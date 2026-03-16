<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name');
            $table->string('action', 50);       // login | logout | created | updated | deleted | etc.
            $table->string('module', 50);        // auth | todos | users | profile
            $table->text('description');
            $table->string('ip_address', 45)->nullable();  // supports IPv6
            $table->text('user_agent')->nullable();
            $table->json('properties')->nullable();         // extra context (old/new values, etc.)
            $table->timestamp('created_at')->useCurrent();

            // Foreign key — SET NULL so logs survive user deletion
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            // Indexes for filters and sorting
            $table->index('user_id');
            $table->index('action');
            $table->index('module');
            $table->index('created_at');
            $table->index(['user_id', 'created_at']);  // composite for user-scoped queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
