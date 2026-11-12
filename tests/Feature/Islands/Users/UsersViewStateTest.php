<?php

use App\Enums\DatagridSectionEnum;
use App\Models\DatatablePreference;
use App\Models\DatatableViewProfile;
use App\Models\User;

describe('preferences', function () {
    it('remembers the column choice per user', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patchJson(route('islands.users.preferences'), ['key' => 'columns', 'value' => '-email'])
            ->assertOk()
            ->assertJsonPath('data.columns', '-email');

        expect(DatatablePreference::query()->sole())
            ->user_id->toBe($user->id)
            ->section->toBe(DatagridSectionEnum::Users->value)
            ->value->toBe('-email');
    });

    it('rejects an unknown column', function () {
        $this->actingAs(User::factory()->create())
            ->patchJson(route('islands.users.preferences'), ['key' => 'columns', 'value' => 'password'])
            ->assertUnprocessable();

        expect(DatatablePreference::query()->count())->toBe(0);
    });

    it('returns 403 for a disabled user', function () {
        $this->actingAs(User::factory()->disabled()->create())
            ->patchJson(route('islands.users.preferences'), ['key' => 'columns', 'value' => '-email'])
            ->assertForbidden();
    });
});

describe('saved views', function () {
    it('saves a view and drops what the schema does not declare', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('islands.users.profiles.store'), [
                'name' => 'Disabled users',
                'payload' => ['status' => 'disabled', 'sort' => 'name', 'password' => 'secret'],
            ])
            ->assertOk()
            ->assertJsonPath('data.profile.name', 'Disabled users');

        expect(DatatableViewProfile::query()->sole())
            ->user_id->toBe($user->id)
            ->section->toBe(DatagridSectionEnum::Users->value)
            ->payload->toBe(['status' => 'disabled', 'sort' => 'name']);
    });

    it('refuses to change or delete another user\'s view', function () {
        $owner = User::factory()->create();

        $this->actingAs($owner)
            ->postJson(route('islands.users.profiles.store'), ['name' => 'Mine', 'payload' => ['status' => 'active']]);

        $ref = DatatableViewProfile::query()->sole()->public_ref;

        $this->actingAs(User::factory()->create())
            ->patchJson(route('islands.users.profiles.update', ['profile' => $ref]), ['name' => 'Taken'])
            ->assertForbidden();

        $this->deleteJson(route('islands.users.profiles.destroy', ['profile' => $ref]))
            ->assertForbidden();

        expect(DatatableViewProfile::query()->sole()->name)->toBe('Mine');
    });

    it('lets the owner rename and delete a view', function () {
        $owner = User::factory()->create();

        $this->actingAs($owner)
            ->postJson(route('islands.users.profiles.store'), ['name' => 'Mine', 'payload' => []]);

        $ref = DatatableViewProfile::query()->sole()->public_ref;

        $this->patchJson(route('islands.users.profiles.update', ['profile' => $ref]), ['name' => 'Renamed'])
            ->assertOk()
            ->assertJsonPath('data.profile.name', 'Renamed');

        $this->deleteJson(route('islands.users.profiles.destroy', ['profile' => $ref]))
            ->assertOk();

        expect(DatatableViewProfile::query()->count())->toBe(0);
    });
});
