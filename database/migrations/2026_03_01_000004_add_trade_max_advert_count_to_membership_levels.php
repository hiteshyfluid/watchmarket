<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_levels', function (Blueprint $table) {
            $table->unsignedInteger('trade_max_advert_count')
                ->nullable()
                ->after('private_max_advert_price');
        });
    }

    public function down(): void
    {
        Schema::table('membership_levels', function (Blueprint $table) {
            $table->dropColumn('trade_max_advert_count');
        });
    }
};

