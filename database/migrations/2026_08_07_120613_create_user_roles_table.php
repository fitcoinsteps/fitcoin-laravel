<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('userroles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('role_id');
            $table->index('expires_at');
            $table->index('is_deleted');
            $table->index('deleted_at');

            $table->foreign('user_id')->references('id')->on('Users')->cascadeOnDelete();
            $table->foreign('role_id')->references('id')->on('Roles')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('userroles');
    }
};