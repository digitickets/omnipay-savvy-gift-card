<?php

namespace DigiTickets\Savvy\Messages;

use DigiTickets\OmnipayAbstractVoucher\VoucherResponseInterface;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

class PurchaseResponse extends AbstractResponse implements VoucherResponseInterface
{
    private $success;
    private $message;
    private $cardNumber;
    private $authCode;

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
        $this->cardNumber = 'No card number';
        $this->authCode = 'No auth code';
        if ($this->success) {
            $this->cardNumber = property_exists($this->response, 'cardNumber') ? $this->response->cardNumber : 'no card number was supplied';
            $this->authCode = property_exists($this->response, 'authCode') ? $this->response->authCode : 'no reference was supplied';
            $this->message = 'Redeemed';
        } else {
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
        return $this->message;
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
