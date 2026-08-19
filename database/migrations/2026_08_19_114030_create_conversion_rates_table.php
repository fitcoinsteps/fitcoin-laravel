<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversion_rates', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // gift_card, crypto
            $table->string('currency'); // USD, USDT, BTC
            $table->string('provider')->nullable(); // amazon, google_play
            $table->decimal('fitcoins_per_unit', 10, 2);
            $table->decimal('min_fitcoins', 10, 2)->default(0);
            $table->decimal('max_fitcoins', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('effective_from');
            $table->timestamp('effective_to')->nullable();
            $table->timestamps();
            
            $table->index(['type', 'currency', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversion_rates');
    }
};