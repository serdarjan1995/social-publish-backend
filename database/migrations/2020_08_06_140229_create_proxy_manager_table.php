<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProxyManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proxy_manager', function (Blueprint $table) {
            $table->id();
            $table->string("proxy_name")->nullable();
            $table->string("proxy_location_code")->nullable();
            $table->string("proxy_location_name")->nullable();
            $table->integer("proxy_limit")->nullable();
            $table->boolean('status')->nullable();
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
        Schema::dropIfExists('proxy_manager');
    }
}
