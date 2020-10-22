<?php

namespace DigiTickets\Savvy\Messages;

use Omnipay\Common\Message\RequestInterface;

class RefundRequest extends UnredeemRequest
{
    protected function buildResponse(RequestInterface $request, $response, string $token = null)
    {
        return new RefundResponse($request, $response, $token);
    }
}
