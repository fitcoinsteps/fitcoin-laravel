<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crypto_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('fitcoins_spent', 10, 2);
            $table->decimal('crypto_amount', 10, 8);
            $table->string('crypto_currency', 10);
            $table->string('wallet_address', 255);
            $table->string('network', 20);
            $table->string('transaction_hash')->nullable();
            $table->decimal('admin_fee', 10, 2)->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('crypto_currency');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crypto_withdrawals');
    }
};