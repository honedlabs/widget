<?php

namespace Honed\Widget\Events;

use Illuminate\Queue\SerializesModels;

class WidgetDeleted
{
    use SerializesModels;

    /**
     * The widget name.
     *
     * @var string
     */
    public $widget;

    /**
     * The scope of the feature deletion.
     *
     * @var mixed
     */
    public $scope;

    /**
     * Create a new event instance.
     *
     * @param  string  $widget
     * @param  mixed  $scope
     */
    public function __construct($widget, $scope)
    {
        $this->widget = $widget;
        $this->scope = $scope;
    }
}