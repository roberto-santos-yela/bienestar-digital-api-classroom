<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersRestrictAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_restrict_apps', function (Blueprint $table) {
            $table->primary(['user_id','app_id']);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('app_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
            $table->integer('maximum_usage_time');
            $table->integer('usage_from_hour');
            $table->integer('usage_to_hour');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_restrict_apps');
    }
}
