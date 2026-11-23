<?php

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Horizon\Horizon;

it('lets a signed-in user into horizon', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $request = Request::create('/horizon');
    $request->setUserResolver(static fn (): User => $user);

    expect(Horizon::check($request))->toBeTrue();
});

it('returns 403 for guests', function () {
    $this->get(route('horizon.index'))->assertForbidden();
});

it('returns 403 for guests in the local environment', function () {
    app()['env'] = 'local';

    $this->get(route('horizon.index'))->assertForbidden();
});

it('returns 403 for a disabled user', function () {
    $this->actingAs(User::factory()->disabled()->create())
        ->get(route('horizon.index'))
        ->assertForbidden();
});
