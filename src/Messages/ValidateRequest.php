<?php

namespace DigiTickets\Savvy\Messages;

use Omnipay\Common\Message\RequestInterface;

class ValidateRequest extends AbstractSavvyRequest
{
    protected function getEndpoint()
    {
        // The engpoints are different between using a PIN and not using one.
        return $this->getUsePIN() === true ? 'balance' : 'balancenopin';
    }

    public function getData()
    {
        $result = array_merge(
            $this->makeRequestContext(),
            [
                'cardNumber' => $this->getCardNumber(),
                'currency' => $this->determineCurrencyNumber(),
            ]
        );
        // If the gateway is using PINs, add it to the data now.
        if ($this->getUsePIN() === true) {
            $result['pin'] = $this->getPin();
        }

        return $result;
    }

    public function sendData($data)
    {
        $rawResponse = $this->sendMessage($data);
        // The PIN is not included in the response, so if the client is using PINs, we have to add it.
        $rawResponse->pin = ($this->getUsePIN() === true && !empty($data['pin'])) ? $data['pin'] : null;

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
