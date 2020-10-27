<?php

namespace DigiTickets\Savvy\Messages;

class RedeemResponse extends AbstractSavvyResponse
{
    private $cardNumber;
    private $authCode;
    /**
     * We have to expose the amount because if the gateway returns a response code of 30 and the merchant says that's
     * okay, it means the gateway has taken as much off the card as it could (which is obviously different to the
     * amount requested) and we have converted the response code to 0 (meaning success). The response then looks like
     * it was successful, but the merchant must compare the amount with the amount requested, and if different, handle
     * it.
     * @var float
     */
    private $amount;

    protected function init()
    {
        // A responseCode of zero means "success"; anything else means there was a problem.
        // A responseCode of 30 is a special case. It means there were insufficient funds on the voucher and the provider
        // took as much off as it could, but then _we_ reverted the transaction and we'll report it as a failure.
        $responseCode = property_exists($this->response, 'responseCode') ? $this->response->responseCode : -1;
        $cardNumber = property_exists($this->response, 'cardNumber') ? $this->response->cardNumber : 'no card number was supplied';
        $this->success = $responseCode === 0;
        $this->cardNumber = 'No card number';
        $this->authCode = 'No auth code';
        $this->amount = property_exists($this->response, 'amount') ? $this->response->amount : 0;
        if ($this->success) {
            $this->cardNumber = $cardNumber;
            $this->authCode = property_exists($this->response, 'authCode') ? $this->response->authCode : 'no reference was supplied';
            $this->message = 'Redeemed';
        } else {
            $this->message = property_exists($this->response, 'responseText') ? $this->response->responseText : 'Unknown error';
            if ($responseCode === 30) {
                $this->message = 'Insufficient funds on Gift Card';
                $this->cardNumber = $cardNumber;
            }
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

    public function getAmount()
    {
        return $this->amount;
    }
}
