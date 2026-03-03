<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adverts', function (Blueprint $table) {
            $table->string('reference_number', 120)->nullable()->after('model_id');
            $table->string('case_size_mm', 60)->nullable()->after('condition_id');
            $table->text('service_history')->nullable()->after('case_size_mm');

            $table->boolean('price_negotiable')->default(false)->after('price');
            $table->boolean('accept_traders')->default(false)->after('price_negotiable');
            $table->string('city', 255)->nullable()->after('accept_traders');
            $table->string('postcode', 60)->nullable()->after('city');
            $table->foreignId('meeting_preference_id')->nullable()->after('postcode')
                ->constrained('attribute_options')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('adverts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('meeting_preference_id');
            $table->dropColumn([
                'reference_number',
                'case_size_mm',
                'service_history',
                'price_negotiable',
                'accept_traders',
                'city',
                'postcode',
            ]);
        });
    }
};

