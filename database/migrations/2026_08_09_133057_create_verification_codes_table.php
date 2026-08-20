<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerificationCodesTable extends Migration
{
    public function up()
    {
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('registration_id')->nullable();
            $table->string('type', 50);
            $table->string('via', 50);
            $table->string('code', 10);
            $table->string('token')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamp('verified_at')->nullable(); // Removed after('used_at')
            $table->integer('attempts')->default(0);
            $table->boolean('is_revoked')->default(0);
            $table->timestamps();

            $table->index('user_id');
            $table->index('registration_id');
            $table->index('type');
            $table->index('via');
            $table->index('expires_at');
            $table->index('is_revoked');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('verification_codes');
    }
}