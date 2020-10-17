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
        // The PIN is not included in the response, so we have to add it.
        $rawResponse->pin = $data['pin'] ?? null;

        // Send all the information to any listeners.
        foreach ($this->getGateway()->getListeners() as $listener) {
            $listener->update('validateRequestSend' /*$this->getListenerAction()*/, $rawResponse);
        }

//        return $this->response = $this->buildResponse($this, $rawResponse);
        return $this->response = new ValidateResponse($this, $rawResponse);
    }
}
