<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('description');
            $table->string('condition_type', 30);
            $table->unsignedInteger('threshold')->default(1);
            $table->string('metric_key', 60);
            $table->string('badge_icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['condition_type', 'metric_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
