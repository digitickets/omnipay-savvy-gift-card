<?php

namespace DigiTickets\Savvy\Messages;

use DigiTickets\OmnipayAbstractVoucher\VoucherResponseInterface;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

abstract class AbstractSavvyResponse extends AbstractResponse implements VoucherResponseInterface
{
    protected $response;
    protected $success;
    protected $message;
    protected $token;

    abstract protected function init();

    public function __construct(RequestInterface $request, $response, string $token = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->token = $token;

        $this->init();
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

    /**
     * @return string|null
     */
    public function getToken()
    {
        return $this->token;
    }
}
