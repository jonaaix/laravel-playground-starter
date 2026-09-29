<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ModuleVisitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'module_ref', 'visit_count', 'last_visited_at'])]
class ModuleVisit extends Model
{
    /** @use HasFactory<ModuleVisitFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'visit_count' => 'integer',
            'last_visited_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
