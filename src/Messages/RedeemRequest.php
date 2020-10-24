<?php

namespace DigiTickets\Savvy\Messages;

use Omnipay\Common\Message\RequestInterface;

class RedeemRequest extends AbstractSavvyRequest
{
    protected function getEndpoint()
    {
        // The endpoints are different between using a PIN and not using one.
        return $this->getUsePIN() === true ? 'redeem' : 'redeemnopin';
    }

    public function getData()
    {
        $result = array_merge(
            $this->makeRequestContext(),
            [
                'cardNumber' => $this->getCardNumber(),
                'currency' => $this->determineCurrencyNumber(),
                'amount' => (float)$this->getAmount(), // API endpoint crashes if this is not a float!
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

        return $this->response = $this->buildResponse($this, $rawResponse, $this->getToken());
    }

    protected function buildResponse(RequestInterface $request, $response, string $token = null)
    {
        return new RedeemResponse($request, $response, $token);
    }
}
