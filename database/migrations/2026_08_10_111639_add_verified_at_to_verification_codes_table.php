<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('verification_codes', function (Blueprint $table) {
            $table->timestamp('verified_at')->nullable()->after('used_at');
        });
    }

    public function down()
    {
        Schema::table('verification_codes', function (Blueprint $table) {
            $table->dropColumn('verified_at');
        });
    }
};