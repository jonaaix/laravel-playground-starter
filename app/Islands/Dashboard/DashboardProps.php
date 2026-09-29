<?php

declare(strict_types=1);

namespace App\Islands\Dashboard;

use Illuminate\Http\Request;

class DashboardProps
{
    /**
     * @return array{userName: string}
     */
    public function build(Request $request): array
    {
        return [
            'userName' => (string) $request->user()?->name,
        ];
    }
}
