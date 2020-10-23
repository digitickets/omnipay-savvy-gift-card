<?php

namespace DigiTickets\Savvy\Messages;

use Omnipay\Common\Message\RequestInterface;

class ValidateRequest extends AbstractSavvyRequest
{
    protected function getEndpoint()
    {
        return 'balance';
    }

    public function getData()
    {
        return array_merge(
            $this->makeRequestContext(),
            [
                'cardNumber' => $this->getCardNumber(),
                'currency' => $this->determineCurrencyNumber(),
                'pin' => $this->getPin(),
            ]
        );
    }

    public function sendData($data)
    {
        $rawResponse = $this->sendMessage($data);
        // The PIN is not included in the response, so we have to add it.
        $rawResponse->pin = $data['pin'] ?? null;

        // Send all the information to any listeners.
        foreach ($this->getGateway()->getListeners() as $listener) {
            $listener->update('validateRequestSend' /*$this->getListenerAction()*/, $rawResponse);
        }

        return $this->response = $this->buildResponse($this, $rawResponse, $this->getToken());
    }

    protected function buildResponse(RequestInterface $request, $response, string $token = null)
    {
        return new ValidateResponse($request, $response, $token);
    }
}
