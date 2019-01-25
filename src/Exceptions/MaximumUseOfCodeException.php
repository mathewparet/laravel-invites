<?php

namespace mathewparet\LaravelInvites\Exceptions;

use mathewparet\LaravelInvites\Exceptions\LaravelInvitesException;

class MaximumUseOfCodeException extends LaravelInvitesException
{
    public function __construct($allowed_count)
    {
        parent::__construct("The code has breached the maximum allowed count of $allowed_count");
    }
}
