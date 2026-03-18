<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('todos', 'due_date')) {
            Schema::table('todos', function (Blueprint $table) {
                $table->date('due_date')->nullable()->after('completed');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('todos', 'due_date')) {
            Schema::table('todos', function (Blueprint $table) {
                $table->dropColumn('due_date');
            });
        }

    }
};
