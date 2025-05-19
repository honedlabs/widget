<?php

namespace Honed\Widget\Contracts;

interface WidgetScopeable
{
    /**
     * Cast the object to a widget scope identifier for the given driver.
     * 
     * @param string $driver
     * @return mixed
     */
    public function toWidgetIdentifier($driver);
}