<?php

namespace mathewparet\LaravelInvites\Exceptions;

use mathewparet\LaravelInvites\Exceptions\LaravelInvitesException;

class InvitationNotYetActiveException extends LaravelInvitesException
{
    public function __construct($date)
    {
        parent::__construct("The code will be active only from $date");
    }
}
