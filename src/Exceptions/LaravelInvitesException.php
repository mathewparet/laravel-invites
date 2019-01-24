<?php

namespace mathewparet\LaravelInvites\Exceptions;

use Exception;

class LaravelInvitesException extends Exception
{
    public function __construct($message = '', $code = 0, $previous = null)
    {
        $this->message = strlen($message) > 0 ? $message : get_called_class();
    }
}
