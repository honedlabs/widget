<?php

namespace Honed\Widget\Exceptions;

use InvalidArgumentException;

class InvalidDriverException extends InvalidArgumentException
{
    /**
     * Create a new invalid driver exception.
     * 
     * @param  string  $driver
     */
    public function __construct($driver)
    {
        parent::__construct(
            "Driver [{$driver}] is not supported.",
        );
    }

    /**
     * Throw a new invalid driver exception.
     *
     * @param  string  $driver
     * @return never
     * 
     * @throws static
     */
    public static function throw($driver)
    {
        throw new self($driver);
    }
}