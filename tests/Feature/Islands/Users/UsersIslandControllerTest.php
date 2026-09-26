<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('data', function () {
    it('returns 401 for guests', function () {
        $this->getJson(route('islands.users.data'))->assertUnauthorized();
    });

    it('returns 403 for a disabled user', function () {
        $this->actingAs(User::factory()->disabled()->create())
            ->getJson(route('islands.users.data'))
            ->assertForbidden();
    });

    it('lists users with their status and marks the acting user', function () {
        $admin = User::factory()->create(['name' => 'Admin', 'created_at' => now()->subDays(2)]);
        User::factory()->unverified()->disabled()->create(['name' => 'Bob', 'created_at' => now()->subDay()]);

        $this->actingAs($admin)
            ->getJson(route('islands.users.data'))
            ->assertOk()
            ->assertJsonPath('data.meta.total', 2)
            ->assertJsonPath('data.rows.0.name', 'Admin')
            ->assertJsonPath('data.rows.0.isSelf', true)
            ->assertJsonPath('data.rows.1.name', 'Bob')
            ->assertJsonPath('data.rows.1.isSelf', false)
            ->assertJsonPath('data.rows.1.isDisabled', true)
            ->assertJsonPath('data.rows.1.emailVerified', false);
    });

    it('filters by search term and status', function () {
        $admin = User::factory()->create(['name' => 'Admin']);
        User::factory()->create(['name' => 'Alice', 'email' => 'alice@example.com']);
        User::factory()->disabled()->create(['name' => 'Alina', 'email' => 'alina@example.com']);

        $this->actingAs($admin)
            ->getJson(route('islands.users.data', ['q' => 'ali', 'status' => 'disabled']))
            ->assertOk()
            ->assertJsonPath('data.meta.total', 1)
            ->assertJsonPath('data.rows.0.name', 'Alina');
    });

    it('falls back to the default sort for a column that is not sortable', function () {
        $admin = User::factory()->create(['name' => 'Zed', 'created_at' => now()->subDay()]);
        User::factory()->create(['name' => 'Amy']);

        $this->actingAs($admin)
            ->getJson(route('islands.users.data', ['sort' => 'password']))
            ->assertOk()
            ->assertJsonPath('data.rows.0.name', 'Zed');
    });
});

describe('store', function () {
    it('creates a user with a hashed password and verified email', function () {
        $this->actingAs(User::factory()->create())
            ->postJson(route('islands.users.store'), [
                'name' => 'New Person',
                'email' => 'new@example.com',
                'password' => 'password123',
                'emailVerified' => true,
                'isDisabled' => false,
            ])
            ->assertCreated()
            ->assertJsonPath('data.email', 'new@example.com');

        $user = User::query()->where('email', 'new@example.com')->sole();

        expect(Hash::check('password123', $user->password))->toBeTrue()
            ->and($user->email_verified_at)->not->toBeNull()
            ->and($user->is_disabled)->toBeFalse();
    });

    it('rejects a missing password and a taken email', function () {
        $admin = User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($admin)
            ->postJson(route('islands.users.store'), [
                'name' => 'Someone',
                'email' => 'taken@example.com',
                'password' => '',
                'emailVerified' => true,
                'isDisabled' => false,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'email' => 'The email has already been taken.',
                'password' => 'The password field is required.',
            ]);
    });
});

describe('update', function () {
    it('updates a user and keeps the password when none is given', function () {
        $user = User::factory()->create();
        $originalPassword = $user->password;

        $this->actingAs(User::factory()->create())
            ->putJson(route('islands.users.update', $user), [
                'name' => 'Renamed',
                'email' => $user->email,
                'password' => '',
                'emailVerified' => false,
                'isDisabled' => true,
            ])
            ->assertOk();

        $user->refresh();

        expect($user->name)->toBe('Renamed')
            ->and($user->password)->toBe($originalPassword)
            ->and($user->email_verified_at)->toBeNull()
            ->and($user->is_disabled)->toBeTrue();
    });

    it('does not let users disable themselves', function () {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->putJson(route('islands.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'password' => '',
                'emailVerified' => true,
                'isDisabled' => true,
            ])
            ->assertOk();

        expect($admin->refresh()->is_disabled)->toBeFalse();
    });
});

describe('destroy', function () {
    it('deletes another user', function () {
        $user = User::factory()->create();

        $this->actingAs(User::factory()->create())
            ->deleteJson(route('islands.users.destroy', $user))
            ->assertOk();

        $this->assertModelMissing($user);
    });

    it('returns 422 when users try to delete themselves', function () {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->deleteJson(route('islands.users.destroy', $admin))
            ->assertUnprocessable()
            ->assertJsonPath('message', 'You cannot delete your own account.');

        $this->assertModelExists($admin);
    });
});
