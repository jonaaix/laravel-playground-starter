<?php

declare(strict_types=1);

namespace App\Islands\Users\Presenters;

use App\Models\User;

class UserRowPresenter
{
    /**
     * @return array{id: int, name: string, email: string, emailVerified: bool, isDisabled: bool, isSelf: bool, lastLoginAt: ?string, createdAt: ?string}
     */
    public function present(User $user, ?User $currentUser): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'emailVerified' => $user->email_verified_at !== null,
            'isDisabled' => $user->is_disabled,
            'isSelf' => $currentUser !== null && $user->is($currentUser),
            'lastLoginAt' => $user->last_login_at?->toIso8601String(),
            'createdAt' => $user->created_at?->toIso8601String(),
        ];
    }
}
