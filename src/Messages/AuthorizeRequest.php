<?php

namespace DigiTickets\Savvy\Messages;

use Omnipay\Common\Message\RequestInterface;

class AuthorizeRequest extends ValidateRequest
{
    protected function buildResponse(RequestInterface $request, $response, string $token = null)
    {
        return new AuthorizeResponse($request, $response, $token);
    }
}
