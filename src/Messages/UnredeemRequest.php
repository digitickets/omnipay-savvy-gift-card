<?php

namespace DigiTickets\Savvy\Messages;

use Omnipay\Common\Message\RequestInterface;

class UnredeemRequest extends AbstractSavvyRequest
{
    protected function getEndpoint()
    {
        return 'reverse';
    }

    public function getData()
    {
        return array_merge(
            $this->makeRequestContext(),
            [
                'cardNumber' => $this->getTransactionReference(),
                'currency' => $this->determineCurrencyNumber(),
                'amount' => (float) $this->getAmount(), // API endpoint crashes if this is not a float!
                'authCode' => (int) $this->getAuthCode(), // API endpoint crashes if this is not an integer!
            ]
        );
    }

    public function sendData($data)
    {
        $rawResponse = $this->sendMessage($data);

        return $this->response = $this->buildResponse($this, $rawResponse, $this->getToken());
    }

    protected function buildResponse(RequestInterface $request, $response, string $token = null)
    {
        return new UnredeemResponse($request, $response, $token);
    }
}
