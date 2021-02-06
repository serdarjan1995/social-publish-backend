<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_manager', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name')->nullable();
            $table->string('url')->nullable();
            $table->string('lazy')->nullable();
            $table->string('extension')->nullable();
            $table->float('size', 30, 2)->nullable();
            $table->string('type')->nullable();
            $table->integer('resource_type')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('sub')->default(1)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_manager');
    }
}
