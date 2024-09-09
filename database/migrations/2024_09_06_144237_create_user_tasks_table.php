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
        Schema::create('user_task', function (Blueprint $table) {
            // $table->id();
            $table->id('task_id');
            $table->string('title');
            $table->string('description');
            $table->enum('priority',['low','medium','high']);
            $table->date('due_date');
            $table->enum('status',['pending','in_progress','completed'])->default('pending');
            $table->string('assigned_to')->nullable();
            $table->softDeletes();
            $table->timestamp('created_on')->nullable();
            $table->timestamp('updated_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
