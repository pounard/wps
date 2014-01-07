<?php

namespace Smvc\Error;

/**
 * 400
 */
class BadRequestError extends LogicError
{
    public function getStatusCode()
    {
        return 400;
    }
}
