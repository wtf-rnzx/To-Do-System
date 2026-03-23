<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('total_exp')->default(0)->after('weekly_goal');
            $table->string('current_rank', 50)->default('novice')->after('total_exp');
            $table->unsignedTinyInteger('rank_progress_pct')->default(0)->after('current_rank');

            $table->index('total_exp');
            $table->index('current_rank');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['total_exp']);
            $table->dropIndex(['current_rank']);
            $table->dropColumn(['total_exp', 'current_rank', 'rank_progress_pct']);
        });
    }
};
