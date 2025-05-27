<?php

namespace Honed\Widget\Contracts;

interface Driver
{
    /**
     * Get all widgets for a given scope and group.
     *
     * @param  string|null  $scope
     * @param  string|null  $group
     * @return array<int,mixed>
     */
    public function get($scope, $group = null);

    /**
     * Determine if a widget exists for a given scope, widget name and group.
     *
     * @param  string  $widget
     * @param  string  $scope
     * @param  string|null  $group
     * @return bool
     */
    public function exists($widget, $scope, $group = null);

    /**
     * Set a widget for a given scope, widget name and group.
     *
     * @param  string  $widget
     * @param  string  $scope
     * @param  string|null  $group
     * @param  int  $order
     * @return void
     */
    public function set($widget, $scope, $group = null, $order = 0);

    /**
     * Update the order of a widget for a given scope, widget name and group.
     *
     * @param  string  $widget
     * @param  string  $scope
     * @param  string|null  $group
     * @param  int  $order
     * @return bool
     */
    public function update($widget, $scope, $group = null, $order = 0);

    /**
     * Delete a widget for a given scope, widget name and group.
     *
     * @param  string  $widget
     * @param  string  $scope
     * @param  string|null  $group
     * @return void
     */
    public function delete($widget, $scope, $group = null);

    /**
     * Purge all widgets by name from storage.
     *
     * @param  string|iterable<int, string>  ...$widgets
     * @return void
     */
    // public function purge(...$widgets);
}
