<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make fields that are required for a *published* advert nullable so
        // partial drafts can be saved at any wizard step.
        DB::statement('ALTER TABLE adverts
            MODIFY COLUMN title        VARCHAR(255)    NULL,
            MODIFY COLUMN price        DECIMAL(12,2)   NULL DEFAULT 0,
            MODIFY COLUMN description  TEXT            NULL,
            MODIFY COLUMN brand_id     BIGINT UNSIGNED NULL,
            MODIFY COLUMN model_id     BIGINT UNSIGNED NULL,
            MODIFY COLUMN paper_id     BIGINT UNSIGNED NULL,
            MODIFY COLUMN box_id       BIGINT UNSIGNED NULL,
            MODIFY COLUMN year_id      BIGINT UNSIGNED NULL,
            MODIFY COLUMN condition_id BIGINT UNSIGNED NULL
        ');
    }

    public function down(): void
    {
        DB::statement("UPDATE adverts SET title='' WHERE title IS NULL");
        DB::statement("UPDATE adverts SET price=0 WHERE price IS NULL");
        DB::statement("UPDATE adverts SET description='' WHERE description IS NULL");
        DB::statement('ALTER TABLE adverts
            MODIFY COLUMN title       VARCHAR(255)  NOT NULL,
            MODIFY COLUMN price       DECIMAL(12,2) NOT NULL DEFAULT 0,
            MODIFY COLUMN description TEXT          NOT NULL
        ');
    }
};
