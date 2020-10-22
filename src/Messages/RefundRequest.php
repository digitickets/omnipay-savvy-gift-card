<?php

namespace DigiTickets\Savvy\Messages;

class RefundRequest extends AbstractSavvyRequest
{
    protected function getEndpoint()
    {
        return 'reverse';
    }

    public function getData()
    {
        return [
            'requestId' => $this->generateGuid(),
            'adminTeamId' => $this->getAdminTeamId(),
            'merchantId' => $this->getMerchantId(),
            'cardNumber' => $this->getTransactionReference(),
            'currency' => $this->currencyCodeToNumber($this->getCurrency()), // @TODO: We need a common "getCurrencyNumber()" method.
            'amount' => (float) $this->getAmount(), // API endpoint crashes if this is not a float!
            'authCode' => (int) $this->getAuthCode(), // API endpoint crashes if this is not an integer!
        ];
    }

    public function sendData($data)
    {
        $rawResponse = $this->sendMessage($data);

        return $this->response = new RefundResponse($this, $rawResponse, $this->getToken());
    }
}
