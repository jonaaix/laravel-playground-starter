<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ModuleVisit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ModuleVisitRecorder
{
    public function record(User $user, string $moduleRef): void
    {
        $now = now();

        ModuleVisit::query()->upsert(
            [[
                'user_id' => $user->id,
                'module_ref' => $moduleRef,
                'visit_count' => 1,
                'last_visited_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]],
            ['user_id', 'module_ref'],
            [
                'visit_count' => DB::raw('visit_count + 1'),
                'last_visited_at' => $now,
                'updated_at' => $now,
            ],
        );
    }
}
