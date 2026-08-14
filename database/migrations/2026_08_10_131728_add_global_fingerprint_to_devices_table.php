<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('global_fingerprint')->nullable()->after('fingerprint');
            $table->index('global_fingerprint');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropIndex(['global_fingerprint']);
            $table->dropColumn('global_fingerprint');
        });
    }
};