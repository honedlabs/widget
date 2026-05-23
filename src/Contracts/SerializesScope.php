<?php

declare(strict_types=1);

namespace Honed\Widget\Contracts;

interface SerializesScope
{
    /**
     * Serialize the scope for storage.
     */
    public function serializeScope(): string;
}
