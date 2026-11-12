<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datatable_view_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('public_ref', 10)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('section', 60);
            $table->string('name', 60);
            $table->json('payload');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['section', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datatable_view_profiles');
    }
};
