<?php

namespace Honed\Widget\Events;

use Illuminate\Queue\SerializesModels;

class WidgetRetrieved
{
    use SerializesModels;

    /**
     * The widget name.
     *
     * @var string
     */
    public $widget;

    /**
     * The scope of the widget check.
     *
     * @var mixed
     */
    public $scope;

    /**
     * The result value of the widget check.
     *
     * @var mixed
     */
    public $value;

    /**
     * Create a new event instance.
     *
     * @param  string  $widget
     * @param  mixed  $scope
     * @param  mixed  $value
     */
    public function __construct($widget, $scope, $value)
    {
        $this->widget = $widget;
        $this->scope = $scope;
        $this->value = $value;
    }
}