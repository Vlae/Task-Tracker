<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_user');
            $table->string('name');
            $table->integer('time_started')->nullable();
            $table->integer('session_duration')->nullable();
            $table->integer('total_duration')->nullable();
            $table->timestamps();
            $table->boolean('is_active')->default('0');
            $table->boolean('is_done')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
