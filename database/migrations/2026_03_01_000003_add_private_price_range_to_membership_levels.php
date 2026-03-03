<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_levels', function (Blueprint $table) {
            $table->decimal('private_min_advert_price', 10, 2)->nullable()->after('seller_type');
            $table->decimal('private_max_advert_price', 10, 2)->nullable()->after('private_min_advert_price');
        });
    }

    public function down(): void
    {
        Schema::table('membership_levels', function (Blueprint $table) {
            $table->dropColumn(['private_min_advert_price', 'private_max_advert_price']);
        });
    }
};

