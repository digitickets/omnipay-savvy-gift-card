<?php

namespace DigiTickets\Savvy\Messages;

use DigiTickets\OmnipayAbstractVoucher\VoucherResponseInterface;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

class ValidateResponse extends AbstractResponse implements VoucherResponseInterface
{
    private $success;
    private $message;

    public function __construct(RequestInterface $request, $response)
    {
        $this->request = $request;
        $this->response = $response;

        $this->init();
    }

    private function init()
    {
        // A responseCode of zero means "success"; anything else means there was a problem.
        $this->success = property_exists($this->response, 'responseCode') && $this->response->responseCode === 0;
        $this->message = 'No error';
        if (!$this->success) {
            $this->message = property_exists($this->response, 'responseText') ? $this->response->responseText : 'Unknown error';
        }
    }

    public function isSuccessful(): bool
    {
        return $this->success;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        $this->message;
    }
}
