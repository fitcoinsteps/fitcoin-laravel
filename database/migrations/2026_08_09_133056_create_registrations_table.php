<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationsTable extends Migration
{
    public function up()
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('email')->unique();
            $table->string('role')->default('user'); // Removed after('email')
            $table->string('phone')->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('employee_code')->nullable();
            $table->string('avatar')->nullable();
            $table->json('registration_data')->nullable();
            $table->timestamp('expires_at');
            $table->boolean('is_verified')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('expires_at');
            $table->index('is_verified');
        });
    }

    public function down()
    {
        Schema::dropIfExists('registrations');
    }
}