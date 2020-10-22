<?php

namespace DigiTickets\Savvy\Messages;

use Omnipay\Common\Message\RequestInterface;

class PurchaseRequest extends RedeemRequest
{
    protected function buildResponse(RequestInterface $request, $response, string $token = null)
    {
        return new PurchaseResponse($request, $response, $token);
    }
}
