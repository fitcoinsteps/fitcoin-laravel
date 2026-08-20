<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('device_name')->nullable();
            $table->string('fingerprint')->unique();
            $table->string('global_fingerprint')->nullable(); // Removed after('fingerprint')
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('is_trusted')->default(false);
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->index('fingerprint');
            $table->index('global_fingerprint');
        });
    }

    public function down()
    {
        Schema::dropIfExists('devices');
    }
};