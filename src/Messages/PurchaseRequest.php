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
            'currency' => $this->getCurrency(),
            'amount' => (float) $this->getAmount(), // API endpoint crashes if this is not a float!
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

//        return $this->response = $this->buildResponse($this, $rawResponse);
        return $this->response = new PurchaseResponse($this, $rawResponse);
    }
}
