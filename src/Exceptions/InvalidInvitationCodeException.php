<?php

namespace mathewparet\LaravelInvites\Exceptions;

use mathewparet\LaravelInvites\Exceptions\LaravelInvitesException;

class InvalidInvitationCodeException extends LaravelInvitesException
{
    public function __construct()
    {
        $this->message = "Invalid invitation code";
    }
}
