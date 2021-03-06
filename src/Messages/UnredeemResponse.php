<?php

namespace DigiTickets\Savvy\Messages;

class UnredeemResponse extends AbstractSavvyResponse
{
    private $cardNumber;
    private $authCode;

    protected function init()
    {
        // A responseCode of zero means "success"; anything else means there was a problem.
        $this->success = property_exists($this->response, 'responseCode') && $this->response->responseCode === 0;
        $this->cardNumber = 'No card number';
        $this->authCode = 'No auth code';
        if ($this->success) {
            $this->cardNumber = property_exists($this->response, 'cardNumber') ? $this->response->cardNumber : 'no card number was supplied';
            $this->authCode = property_exists($this->response, 'authCode') ? $this->response->authCode : 'no reference was supplied';
            $this->message = 'Unredeemed';
        } else {
            $this->message = property_exists($this->response, 'responseText') ? $this->response->responseText : 'Unknown error';
        }
    }

    public function getTransactionReference()
    {
        return $this->cardNumber;
    }

    public function getAuthCode()
    {
        return $this->authCode;
    }
}
