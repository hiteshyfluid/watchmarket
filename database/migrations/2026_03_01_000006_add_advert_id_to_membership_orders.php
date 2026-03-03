<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_orders', function (Blueprint $table) {
            $table->foreignId('advert_id')
                ->nullable()
                ->after('membership_subscription_id')
                ->constrained('adverts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('membership_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('advert_id');
        });
    }
};

