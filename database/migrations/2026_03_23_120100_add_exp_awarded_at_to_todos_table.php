<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->timestamp('exp_awarded_at')->nullable()->after('completed_at');
            $table->index(['user_id', 'exp_awarded_at']);
        });
    }

    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'exp_awarded_at']);
            $table->dropColumn('exp_awarded_at');
        });
    }
};
