<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgencyUserPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agency_user_permissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('agency_parent_id');
            $table->foreign('agency_parent_id')->references('id')->on('users');
            $table->uuid('agency_user_id');
            $table->foreign('agency_user_id')->references('id')->on('users');
            $table->foreignId('account_id')->constrained('account_manager')->onDelete('cascade');
            $table->integer('permissions');
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
        Schema::dropIfExists('agency_user_permissions');
    }
}
