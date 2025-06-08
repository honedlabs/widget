<?php

namespace Honed\Widget\Exceptions;

use InvalidArgumentException;

class UndefinedDriverException extends InvalidArgumentException
{
    /**
     * Create a new undefined driver exception.
     * 
     * @param  string  $driver
     */
    public function __construct($driver)
    {
        parent::__construct(
            "Driver [{$driver}] is not defined."
        );
    }

    /**
     * Throw a new undefined driver exception.
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