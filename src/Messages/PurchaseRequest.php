<?php

namespace DigiTickets\Savvy\Messages;

class PurchaseRequest extends AbstractSavvyRequest
{
    protected function getEndpoint()
    {
        return 'redeem';
    }

    public function getData()
    {
        return [
            'requestId' => $this->generateGuid(),
            'adminTeamId' => $this->getAdminTeamId(),
            'merchantId' => $this->getMerchantId(),
            'cardNumber' => $this->getCardNumber(),
            'pin' => $this->getPin(),
            'currency' => $this->determineCurrencyNumber(),
            'amount' => (float) $this->getAmount(), // API endpoint crashes if this is not a float!
        ];
    }

    public function sendData($data)
    {
        $rawResponse = $this->sendMessage($data);

        return $this->response = new PurchaseResponse($this, $rawResponse, $this->getToken());
    }
}
