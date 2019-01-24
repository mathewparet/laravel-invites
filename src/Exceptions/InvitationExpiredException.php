<?php

namespace mathewparet\LaravelInvites\Exceptions;

use mathewparet\LaravelInvites\Exceptions\LaravelInvitesException;

class InvitationExpiredException extends LaravelInvitesException
{
    public function __construct($date)
    {
        parent::__construct("The code expired at $date");
    }
}
