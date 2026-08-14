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
       // database/migrations/xxxx_xx_xx_xxxxxx_create_social_accounts_table.php
Schema::create('social_accounts', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->string('provider');     // 'google', 'apple'
    $table->string('provider_id');  // the user's ID from the provider
    $table->timestamps();

    $table->unique(['provider', 'provider_id']);
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
}); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
