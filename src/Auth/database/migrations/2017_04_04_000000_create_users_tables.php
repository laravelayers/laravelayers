<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_actions')) {
            Schema::create('user_actions', function (Blueprint $table) {
                $table->bigInteger('user_id')->unsigned();
                $table->string('action');
                $table->integer('allowed')->unsigned();
                $table->ipAddress('ip')->nullable();
                $table->unique(array('user_id', 'action'), 'user_id');
                $table->index('action', 'action');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('user_roles')) {
            Schema::create('user_roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->index('name', 'name');
            });
        }

        if (!Schema::hasTable('user_role_actions')) {
            Schema::create('user_role_actions', function (Blueprint $table) {
                $table->integer('role_id')->unsigned();
                $table->string('action');
                $table->integer('allowed')->unsigned();
                $table->unique(array('role_id', 'action'), 'role_id');
                $table->index('action', 'role_action');
                $table->foreign('role_id')->references('id')->on('user_roles')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_actions');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('user_role_actions');
    }
}
