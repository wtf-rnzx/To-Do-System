<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->string('priority', 10)->default('medium')->after('description');
            $table->string('recurrence_type', 20)->nullable()->after('due_date');
            $table->unsignedBigInteger('recurrence_origin_id')->nullable()->after('recurrence_type');
            $table->timestamp('completed_at')->nullable()->after('completed');

            $table->index(['user_id', 'priority']);
            $table->index(['user_id', 'due_date']);
            $table->index(['user_id', 'completed', 'due_date']);
            $table->index(['user_id', 'completed_at']);
            $table->unique(['recurrence_origin_id', 'due_date']);
        });

        Schema::table('todos', function (Blueprint $table) {
            $table->foreign('recurrence_origin_id')
                ->references('id')
                ->on('todos')
                ->nullOnDelete();
        });

        // Replace global unique(title) with per-user unique(user_id, title)
        Schema::table('todos', function (Blueprint $table) {
            $table->dropUnique(['title']);
            $table->unique(['user_id', 'title']);
        });

        // Keep recurrence values constrained in databases that support CHECK.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE todos ADD CONSTRAINT todos_recurrence_type_check CHECK (recurrence_type IN ('daily','weekly','monthly') OR recurrence_type IS NULL)");
            DB::statement("ALTER TABLE todos ADD CONSTRAINT todos_priority_check CHECK (priority IN ('low','medium','high'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE todos DROP CONSTRAINT IF EXISTS todos_recurrence_type_check');
            DB::statement('ALTER TABLE todos DROP CONSTRAINT IF EXISTS todos_priority_check');
        }

        Schema::table('todos', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'title']);
            $table->unique('title');

            $table->dropForeign(['recurrence_origin_id']);
            $table->dropUnique(['recurrence_origin_id', 'due_date']);
            $table->dropIndex(['user_id', 'priority']);
            $table->dropIndex(['user_id', 'due_date']);
            $table->dropIndex(['user_id', 'completed', 'due_date']);
            $table->dropIndex(['user_id', 'completed_at']);

            $table->dropColumn([
                'priority',
                'recurrence_type',
                'recurrence_origin_id',
                'completed_at',
            ]);
        });
    }
};
