<?php

declare(strict_types=1);

namespace Honed\Widget\Concerns;

/**
 * @internal
 */
trait InteractsWithDatabase
{
    /**
     * Get the connection name for widgets.
     */
    public function getConnectionName(): string
    {
        /** @var string|null */
        $connection = config('widget.drivers.database.connection');

        /** @var string */
        return ($connection === null || $connection === 'null') ? config('database.default') : $connection;
    }

    /**
     * Get the migration table name for widgets.
     */
    public function getTableName(): string
    {
        /** @var string|null */
        $table = config('widget.drivers.database.table');

        /** @var string */
        return ($table === null || $table === 'null') ? 'widgets' : $table;
    }
}
