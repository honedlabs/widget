<?php

declare(strict_types=1);

namespace Honed\Widget\Drivers;

use Honed\Widget\Concerns\Resolvable;
use Honed\Widget\Contracts\Driver;
use Illuminate\Support\Arr;

class ArrayDriver implements Driver
{
    use Resolvable;

    /**
     * The store's name.
     *
     * @var string
     */
    protected $name;

    /**
     * The resolved widgets.
     *
     * @var array<string, array<int, array<string, mixed>>>
     */
    protected $widgets = [];

    /**
     * Create a new array driver instance.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function get(mixed $scope): array
    {
        $scope = $this->resolveScope($scope);

        if (! isset($this->widgets[$scope])) {
            return [];
        }

        return $this->widgets[$scope];
    }

    /**
     * {@inheritdoc}
     */
    public function set(mixed $widget, mixed $scope, mixed $data = null, mixed $position = null): void
    {
        $scope = $this->resolveScope($scope);

        if (! isset($this->widgets[$scope])) {
            $this->widgets[$scope] = [];
        }

        $this->widgets[$scope][] = $this->fill(compact('widget', 'scope', 'data', 'position'));
    }

    /**
     * {@inheritdoc}
     */
    public function update(mixed $widget, mixed $scope, mixed $data = null, mixed $position = null): bool
    {
        $scope = $this->resolveScope($scope);
        $widget = $this->resolveWidget($widget);

        if (! isset($this->widgets[$scope])) {
            return false;
        }

        foreach ($this->widgets[$scope] as $index => $item) {
            if ($item['widget'] === $widget) {
                $this->widgets[$scope][$index]['data'] = $data;
                $this->widgets[$scope][$index]['position'] = $position;

                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(mixed $widget, mixed $scope): bool
    {
        $found = $this->find($widget, $scope);

        if (! $found) {
            return false;
        }

        $scope = $this->resolveScope($scope);

        unset($this->widgets[$scope][array_search($found, $this->widgets[$scope])]);

        return true;
    }

    /**
     * Create an array of values to be inserted.
     *
     * @param  array{widget: mixed, scope: mixed, data: mixed, position: mixed}  $values
     * @return array<string, mixed>
     */
    protected function fill(array $values): array
    {
        return [
            'widget' => $this->resolveWidget($values['widget']),
            'scope' => $this->resolveScope($values['scope']),
            'data' => $values['data'],
            'position' => $values['position'],
        ];
    }

    /**
     * Find a widget in the array.
     *
     * @return array<string, mixed>|null
     */
    protected function find(mixed $widget, mixed $scope): ?array
    {
        $scope = $this->resolveScope($scope);
        $widget = $this->resolveWidget($widget);

        if (! isset($this->widgets[$scope])) {
            return null;
        }

        return Arr::first(
            $this->widgets[$scope],
            static fn ($item) => $item['widget'] === $widget,
        );
    }
}
