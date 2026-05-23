<?php

declare(strict_types=1);

namespace Tests\Feature\Casts;

use App\Models\User;
use Honed\Widget\Casts\ScopeCast;
use Honed\Widget\Contracts\SerializesScope;
use Honed\Widget\Facades\Widgets;
use Illuminate\Database\Eloquent\Relations\Relation;
use RuntimeException;
use stdClass;

beforeEach(function () {
    $this->cast = new ScopeCast();
});

afterEach(function () {
    Relation::requireMorphMap(false);
    Widgets::useMorphMap(false);
});

it('handles scope serializers', function () {

    $scope = new class() implements SerializesScope
    {
        public function serializeScope(): string
        {
            return 'abc';
        }
    };

    expect($this->cast->set(new User(), 'scope', $scope, []))->toBe('abc');
});

it('handles null values', function () {
    expect($this->cast->set(new User(), 'scope', null, []))->toBe('__laravel_null');
});

it('handles string values', function () {
    $scope = 'user|1';

    expect($this->cast->set(new User(), 'scope', $scope, []))->toBe($scope);
});

it('handles numeric values', function () {
    $scope = 1;

    expect($this->cast->set(new User(), 'scope', $scope, []))->toBe('1');
});

it('handles models', function () {
    $user = User::factory()->create();

    expect($this->cast->set(new User(), 'scope', $user, []))->toBe(User::class.'|'.$user->getKey());
});

it('handles models with morph map', function () {
    Relation::enforceMorphMap([
        'user' => User::class,
    ]);

    Widgets::useMorphMap();

    $user = User::factory()->create();

    expect($this->cast->set(new User(), 'scope', $user, []))->toBe('user|'.$user->id);
});

it('throws an exception when the scope is not serializable', function () {
    $this->cast->set(new User(), 'scope', new stdClass(), []);
})->throws(RuntimeException::class);
