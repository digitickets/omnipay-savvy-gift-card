<?php

namespace DigiTickets\Savvy\Messages;

class RedeemRequest extends AbstractSavvyRequest
{
    protected function getEndpoint()
    {
        return 'TBC';
    }
}
