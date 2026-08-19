<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->enum('provider', ['amazon', 'google_play', 'steam', 'apple']);
            $table->string('code')->unique();
            $table->string('pin')->nullable();
            $table->decimal('value', 10, 2);
            $table->string('currency')->default('USD');
            $table->integer('fitcoin_cost');
            $table->string('sku')->nullable();
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('purchased_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['provider', 'is_used']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};