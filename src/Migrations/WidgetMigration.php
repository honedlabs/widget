<?php

namespace Honed\Widget\Migrations;

use Illuminate\Database\Migrations\Migration;

abstract class WidgetMigration extends Migration
{
    /**
     * Get the migration connection name.
     *
     * @return string
     */
    public function getConnection()
    {
        /** @var string|null */
        $connection = config('widget.drivers.database.connection');

        // @phpstan-ignore-next-line return.type
        return ($connection === null || $connection === 'null') 
            ? config('database.default')
            : $connection;
    }

    /**
     * Get the migration table name.
     *
     * @return string
     */
    public function getTable()
    {
        /** @var string */
        return config('widget.drivers.database.table', 'widgets');
    }
}
