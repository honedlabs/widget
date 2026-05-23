<?php

declare(strict_types=1);

namespace Honed\Widget\Contracts;

interface Driver
{
    /**
     * Get all widgets for a given scope.
     *
     * @return array<int,mixed>
     */
    public function get(mixed $scope): array;

    /**
     * Set a widget for a given scope and widget name.
     */
    public function set(mixed $widget, mixed $scope, mixed $data = null, mixed $position = null): void;

    /**
     * Update the data and position of a widget for a given scope and widget.
     */
    public function update(mixed $widget, mixed $scope, mixed $data = null, mixed $position = null): bool;

    /**
     * Delete a widget for a given scope and widget.
     */
    public function delete(mixed $widget, mixed $scope): bool;

    /**
     * Purge all widgets by scope from storage.
     */
    // public function purge(mixed $scope): void;
}
