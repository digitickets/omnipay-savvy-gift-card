<?php

namespace DigiTickets\Savvy\Messages;

class ValidateRequest extends AbstractSavvyRequest
{
    protected function getEndpoint()
    {
        return 'balance';
    }

    public function getData()
    {
        return [
            'requestId' => $this->generateGuid(),
            'adminTeamId' => $this->getAdminTeamId(),
            'merchantId' => $this->getMerchantId(),
            'cardNumber' => $this->getCardNumber(),
            'pin' => $this->getPin(),
            'currency' => $this->getCurrency(),
        ];
    }

    public function sendData($data)
    {
        $responseBody = $this->httpClient->post(
            $this->getUrl(),
            $this->buildHeaders(),
            json_encode($data)
        )
        ->send()
        ->getBody();
        $rawResponse = json_decode($responseBody); // Decode to stdClass
    }
}
