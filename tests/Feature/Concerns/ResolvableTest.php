<?php

declare(strict_types=1);

use App\Models\User;
use App\Widgets\TeamMembersWidget;
use App\Widgets\UserCountWidget;
use Honed\Widget\Concerns\Resolvable;

beforeEach(function () {
    $this->artisan('widget:cache');

    $this->class = new class()
    {
        use Resolvable;
    };
});

it('resolves scopes', function () {
    $user = User::factory()->create();

    expect($this->class)
        ->resolveScope($user)->toBe(User::class.'|'.$user->getKey());
});

it('resolves widgets', function () {
    expect($this->class)
        ->resolveWidget(UserCountWidget::class)->toBe('count')
        ->resolveWidget(TeamMembersWidget::class)->toBe(TeamMembersWidget::class);
});
