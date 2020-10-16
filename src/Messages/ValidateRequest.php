<?php

namespace DigiTickets\Savvy\Messages;

class ValidateRequest extends AbstractSavvyRequest
{
    public function getData()
    {
        // @TODO: This needs to build/gather all the data - requestID, adminTeamId, cardNumber, PIN, etc
        return [
            'requestId' => 'REQ_'.time(), // @TODO: Have a global method to return a unique ref.
            'adminTeamId' => 1, // @TODO: From gateway params
            'merchantId' => 'DZ20190907t', // @TODO: From gateway params
            'cardNumber' => '6280399990524260', // @TODO: From card form values
            'pin' => '14261819', // @TODO: From card form values
            'currency' => '978' // @TODO: From gateway params
        ];
    }

    public function sendData($data)
    {
        // @TODO: This needs to call the API endpoint.
        // @TODO: We need to handle the logout having expired (or not existing at all).
        $bearer = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6IkNvbm5lY3REaXJlY3RUb2tlbiIsIk1lcmNoYW50SWQiOiJEWjIwMTkwOTA3dCIsIkFjcXVpcmVySWQiOiIxIiwibmJmIjoxNjAyODQxMzgyLCJleHAiOjE2MDI4NDMxODIsImlhdCI6MTYwMjg0MTM4MiwiaXNzIjoiU2F2dnlDb25uZWN0RGlyZWN0IiwiYXVkIjoiU2F2dnlDb25uZWN0RGlyZWN0In0.vxVP0ODBLs23PdL1_DdYpKzsWk-m1kECUiEY-JqVzQ0QvYwdSDNkLKYs_-Ef-0TyG6kYioJxZ0ovKnJ5wnrcxMn-p-2dh8tJYm4oZk5N2mNfQ08BUI9yZCxMabQygx0lRSP-BLrA0nzYmK0cghsVhn0rxWy6FrqZyyis396h8KBW-2hPEbrdBeTuEq5-09eETbOjucMyX_9qhatGBjaRrOA2WVrGT72ciq-U2CVu6PAhSBYwT8dimFNex3l9qhvNVeN9Zm6b0v3C7JfylAVVADSyhPFIO_-y1u-G5MSPOTw50rm5aaCbITzo0penI8b8iqEhGJZn0bgxu0guXbRhPQ';
        $headers = [
            'Connect-Direct-Subscription-Key' => '586ab003ecb742e3a2dbeb90be14ad92',
            'Authorization' => 'Bearer '.$bearer,
            'Content-Type' => 'application/json'
        ];
        $responseBody = $this->httpClient->post(
            'https://api.savvyconnectdirect.net/sandbox/api/v1/balance',
            $headers,
            $data
        )
        ->send()
        ->getBody();
        $rawResponse = json_decode($responseBody); // Decode to stdClass
    }
}
