<?php

declare(strict_types=1);

namespace App\Islands\Users\Writers;

use App\Models\User;

class UserWriter
{
    /**
     * @param  array{name: string, email: string, password: string, emailVerified: bool, isDisabled: bool}  $data
     */
    public function create(array $data): User
    {
        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_disabled' => $data['isDisabled'],
        ]);

        $user->email_verified_at = $data['emailVerified'] ? now() : null;
        $user->save();

        return $user;
    }

    /**
     * @param  array{name: string, email: string, password: ?string, emailVerified: bool, isDisabled: bool}  $data
     */
    public function update(User $user, array $data, User $actingUser): User
    {
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->email_verified_at = $data['emailVerified'] ? ($user->email_verified_at ?? now()) : null;

        // Nobody can lock themselves out; the switch is hidden for the own row as well.
        if (! $user->is($actingUser)) {
            $user->is_disabled = $data['isDisabled'];
        }

        if (filled($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        return $user;
    }

    public function delete(User $user, User $actingUser): bool
    {
        if ($user->is($actingUser)) {
            return false;
        }

        return (bool) $user->delete();
    }
}
