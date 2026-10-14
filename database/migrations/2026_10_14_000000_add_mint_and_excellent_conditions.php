<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['Mint', 'Excellent'] as $name) {
            if (!DB::table('attribute_options')
                ->where('type', 'condition')
                ->where('name', $name)
                ->exists()) {
                DB::table('attribute_options')->insert([
                    'type' => 'condition',
                    'name' => $name,
                    'sort_order' => 0,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Attribute options are curated data and may have existed before this migration.
    }
};
