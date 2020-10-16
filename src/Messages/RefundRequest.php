<?php

namespace DigiTickets\Savvy\Messages;

class RefundRequest extends AbstractSavvyRequest
{
    protected function getEndpoint()
    {
        return 'TBC';
    }
}
