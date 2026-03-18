<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('progress')->default(0);
            $table->timestamp('unlocked_at')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'achievement_id']);
            $table->index(['user_id', 'unlocked_at']);
            $table->index(['user_id', 'is_visible']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
    }
};
