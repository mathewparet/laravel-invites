<?php

namespace mathewparet\LaravelInvites\Exceptions;

use Exception;

class AnEmailCanHaveOnlyOneInvitation extends Exception
{
    public function __construct()
    {
        $this->message = 'An email ID can be invited only once.';
    }
}
