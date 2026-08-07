<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('userpermissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('permission_id');
            $table->boolean('allowed')->default(true);
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('permission_id');
            $table->index('allowed');
            $table->index('is_deleted');

            $table->foreign('user_id')->references('id')->on('Users')->cascadeOnDelete();
            $table->foreign('permission_id')->references('id')->on('Permissions')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('userpermissions');
    }
};