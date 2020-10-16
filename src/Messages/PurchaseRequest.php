<?php

namespace DigiTickets\Savvy\Messages;

class PurchaseRequest extends AbstractSavvyRequest
{
    protected function getEndpoint()
    {
        return 'TBC';
    }
}
