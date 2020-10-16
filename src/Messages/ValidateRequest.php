<?php

namespace DigiTickets\Savvy\Messages;

class ValidateRequest extends AbstractSavvyRequest
{
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
        // @TODO: We need to handle the logout having expired (or not existing at all).
        // @TODO: It should be possible to move the headers stuff out to a central method.
        $bearer = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6IkNvbm5lY3REaXJlY3RUb2tlbiIsIk1lcmNoYW50SWQiOiJEWjIwMTkwOTA3dCIsIkFjcXVpcmVySWQiOiIxIiwibmJmIjoxNjAyODQ4MjA4LCJleHAiOjE2MDI4NTAwMDgsImlhdCI6MTYwMjg0ODIwOCwiaXNzIjoiU2F2dnlDb25uZWN0RGlyZWN0IiwiYXVkIjoiU2F2dnlDb25uZWN0RGlyZWN0In0.H2PNu9ccPzVX7Y9Em3StfHabs2YGUVYfcNN-ErIOPhAD46xneMN4RJc0WaI21lLBG3S9yc5RcO2goupVEgYe4Cb2DO2r1XcS9lLGZ0lkeyBcOx0vOI3HscuHjZvPVdLzD2raNOJ2TDevXiKS8GmNjyHeq7imTBWoEUTXVTUY611lbgifgnAH8H7ovWk7Rh1OgCJ68XPQ6FXNZ0aHE5A3DqyYkMnVIEYRRczJo_rsZ8gR6e78Q5W8igWdN-05BvGj-8SsrYgLjtj6-ND1srjkUrEGwLJrnTqk4G3muCixbi-aZfV4wPKRvSaMOubFVcoGcUSvElfhh2M-bGHCXDBaWQ';
        $headers = [
            'Connect-Direct-Subscription-Key' => $this->getConnectDirectSubscriptionKey(),
            'Authorization' => 'Bearer '.$bearer,
            'Content-Type' => 'application/json'
        ];
        $responseBody = $this->httpClient->post(
            $this->getUrl(),
            $headers,
            json_encode($data)
        )
        ->send()
        ->getBody();
        $rawResponse = json_decode($responseBody); // Decode to stdClass
    }
}
