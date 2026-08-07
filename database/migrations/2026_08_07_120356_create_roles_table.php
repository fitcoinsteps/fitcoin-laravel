<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('uuid', 36)->unique();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('priority')->nullable();
            $table->boolean('is_system')->default(false);
            $table->string('status')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('deleted_by')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('uuid');
            $table->index('slug');
            $table->index('priority');
            $table->index('is_system');
            $table->index('status');
            $table->index('is_deleted');
            $table->index('updated_at');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};