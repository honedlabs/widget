<?php

namespace Honed\Widget\Exceptions;

use Honed\Widget\Contracts\SerializesScope;
use RuntimeException;

class CannotSerializeScopeException extends RuntimeException
{
    /**
     * Create a new invalid scope exception.
     */
    public function __construct()
    {
        $contract = SerializesScope::class;

        parent::__construct(
            "Unable to serialize the widget scope to a string. You should implement the [{$contract}::class] contract."
        );
    }

    /**
     * Throw a new invalid scope exception.
     *
     * @return never
     * 
     * @throws static
     */
    public static function throw()
    {
        throw new self();
    }
}