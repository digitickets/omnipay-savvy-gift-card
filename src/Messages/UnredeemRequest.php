<?php

namespace DigiTickets\Savvy\Messages;

class UnredeemRequest extends AbstractSavvyRequest
{
    protected function getEndpoint()
    {
        return 'TBC';
    }
}
