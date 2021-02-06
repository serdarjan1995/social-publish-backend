<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid('user_id');
            $table->uuid('send_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('message');
            $table->integer('type');
            $table->text('extra_details');
            $table->boolean('status')->nullable()->default(0);
            $table->boolean('read')->nullable()->default(0);
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
        Schema::dropIfExists('notifications');
    }
}
