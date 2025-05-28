<?php

namespace Honed\Widget\Contracts;

interface SerializesScope
{
    /**
     * Serialize the scope for storage.
     *
     * @return string
     */
    public function serializeScope();
}
