<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaskLogIndixes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('step');
            $table->string('status');
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('task_log_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('task_log_id');
            $table->integer('entry_number')->default(0);
            $table->string('severity');
            $table->text('message');
            $table->json('context')->nullable();
            $table->timestamps(3);
        });

        Schema::create('task_logs_relations', function (Blueprint $table) {
            $table->string('task_log_id');
            $table->string('task_logs_relation_id');
            $table->string('task_logs_relation_type');
            $table->timestamps();
        });

        Schema::table('task_logs', function (Blueprint $table) {
            $table->index(['type']);
            $table->index(['status']);
            $table->index(['created_at']);
        });
    }

    /**git
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_logs');
        Schema::dropIfExists('task_log_entries');
        Schema::dropIfExists('task_logs_relations');
    }
}
