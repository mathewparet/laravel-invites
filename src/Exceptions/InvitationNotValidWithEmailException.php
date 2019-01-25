<?php

namespace mathewparet\LaravelInvites\Exceptions;

use mathewparet\LaravelInvites\Exceptions\LaravelInvitesException;

class InvitationNotValidWithEmailException extends LaravelInvitesException
{
    public function __construct($email, $valid_email)
    {
        if ($email)
            parent::__construct("This invitation code is not valid with this $email");
        else
            parent::__construct("This invitation code is valid only for $valid_email");
    }
}
