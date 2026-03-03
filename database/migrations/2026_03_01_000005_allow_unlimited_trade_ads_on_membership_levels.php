<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE membership_levels MODIFY trade_max_advert_count INT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE membership_levels MODIFY trade_max_advert_count INT UNSIGNED NULL');
    }
};

