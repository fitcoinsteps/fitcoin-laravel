<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('uuid', 36)->unique();
            $table->string('module')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('group_name')->nullable();
            $table->boolean('is_system')->default(false);
            $table->bigInteger('deleted_by')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('uuid');
            $table->index('slug');
            $table->index('module');
            $table->index('group_name');
            $table->index('is_system');
            $table->index('is_deleted');
            $table->index('updated_at');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};