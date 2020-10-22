<?php

namespace DigiTickets\Savvy\Messages;

class ValidateResponse extends AbstractSavvyResponse
{
    protected function init()
    {
        // A responseCode of zero means "success"; anything else means there was a problem.
        $this->success = property_exists($this->response, 'responseCode') && $this->response->responseCode === 0;
        $this->message = 'No error';
        if (!$this->success) {
            $this->message = property_exists($this->response, 'responseText') ? $this->response->responseText : 'Unknown error';
        }
    }
}
