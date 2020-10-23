<?php

namespace DigiTickets\Savvy\Messages;

class ValidateResponse extends AbstractSavvyResponse
{
    private $balance;

    protected function init()
    {
        // A responseCode of zero means "success"; anything else means there was a problem.
        $this->success = property_exists($this->response, 'responseCode') && $this->response->responseCode === 0;
        $this->message = 'No error';
        $this->balance = property_exists($this->response, 'balance') ? $this->response->balance : 0;
        if (!$this->success) {
            $this->message = property_exists($this->response, 'responseText') ? $this->response->responseText : 'Unknown error';
            $this->balance = 0;
        }
    }

    public function getBalance()
    {
        return $this->balance;
    }
}
