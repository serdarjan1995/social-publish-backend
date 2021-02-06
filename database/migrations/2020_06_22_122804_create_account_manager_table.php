<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('account_manager', function (Blueprint $table): void  {
            $table->id();
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('account_manager');
            $table->foreignId('social_network_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('account_category')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('login_type')->nullable();
            $table->integer('can_post')->nullable();
            $table->string('name');
            $table->string('username')->nullable();
            $table->mediumText('auth_token')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('account_url')->nullable();
            $table->integer('status')->nullable();
            $table->mediumText('data')->nullable();
            $table->string('profile_id')->nullable();
            $table->json('watermark_details')->nullable();
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
        Schema::dropIfExists('account_manager');
    }
}
