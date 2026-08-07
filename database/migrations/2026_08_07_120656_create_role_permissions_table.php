<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rolepermissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('role_id');
            $table->index('permission_id');
            $table->index('is_deleted');

            $table->foreign('role_id')->references('id')->on('Roles')->cascadeOnDelete();
            $table->foreign('permission_id')->references('id')->on('Permissions')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rolepermissions');
    }
};