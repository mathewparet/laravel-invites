<?php

namespace mathewparet\LaravelInvites\Exceptions;

use Exception;

class InvalidEmailIdException extends Exception
{
    public function __construct()
    {
        $this->message = 'Invalid email ID';
    }
}
