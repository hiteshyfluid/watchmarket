<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // Drop old stub/legacy tables in dependency order
        Schema::dropIfExists('advert_tag');
        Schema::dropIfExists('advert_images');
        Schema::dropIfExists('adverts');
        Schema::dropIfExists('watch_models');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('attribute_options');

        Schema::enableForeignKeyConstraints();

        // ----------------------------------------------------------------
        // 1. attribute_options — all managed lookup values (condition, etc.)
        // ----------------------------------------------------------------
        Schema::create('attribute_options', function (Blueprint $table) {
            $table->id();
            $table->string('type', 60)->index();   // paper, box, year, gender, condition…
            $table->string('name', 255);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ----------------------------------------------------------------
        // 2. tags
        // ----------------------------------------------------------------
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 120)->unique();
            $table->timestamps();
        });

        // ----------------------------------------------------------------
        // 3. brands — self-referencing for parent brand / model (sub-brand)
        // ----------------------------------------------------------------
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 160)->unique();
            $table->foreignId('parent_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // ----------------------------------------------------------------
        // 4. categories — hierarchical (parent/child)
        // ----------------------------------------------------------------
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 160)->unique();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // ----------------------------------------------------------------
        // 5. adverts — full watch listing schema
        // ----------------------------------------------------------------
        Schema::create('adverts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('title', 255);
            $table->longText('description')->nullable();

            // Taxonomy
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('model_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();

            $table->decimal('price', 12, 2);
            $table->boolean('show_phone')->default(true);

            // Images
            $table->string('main_image')->nullable();

            // Status
            $table->string('status', 30)->default('active'); // draft, active, sold, expired
            $table->date('expiry_date')->nullable();
            $table->boolean('is_sold')->default(false);

            // Watch attributes — all FK to attribute_options
            $table->foreignId('paper_id')->nullable()->constrained('attribute_options')->nullOnDelete();
            $table->foreignId('box_id')->nullable()->constrained('attribute_options')->nullOnDelete();
            $table->foreignId('year_id')->nullable()->constrained('attribute_options')->nullOnDelete();
            $table->foreignId('gender_id')->nullable()->constrained('attribute_options')->nullOnDelete();
            $table->foreignId('condition_id')->nullable()->constrained('attribute_options')->nullOnDelete();
            $table->foreignId('movement_id')->nullable()->constrained('attribute_options')->nullOnDelete();
            $table->foreignId('case_material_id')->nullable()->constrained('attribute_options')->nullOnDelete();
            $table->foreignId('bracelet_material_id')->nullable()->constrained('attribute_options')->nullOnDelete();
            $table->foreignId('dial_colour_id')->nullable()->constrained('attribute_options')->nullOnDelete();
            $table->foreignId('case_diameter_id')->nullable()->constrained('attribute_options')->nullOnDelete();
            $table->foreignId('waterproof_id')->nullable()->constrained('attribute_options')->nullOnDelete();

            $table->timestamps();
        });

        // ----------------------------------------------------------------
        // 6. advert_images — gallery images per advert
        // ----------------------------------------------------------------
        Schema::create('advert_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advert_id')->constrained()->cascadeOnDelete();
            $table->string('image_path', 500);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // ----------------------------------------------------------------
        // 7. advert_tag — pivot
        // ----------------------------------------------------------------
        Schema::create('advert_tag', function (Blueprint $table) {
            $table->foreignId('advert_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['advert_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('advert_tag');
        Schema::dropIfExists('advert_images');
        Schema::dropIfExists('adverts');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('attribute_options');
        Schema::enableForeignKeyConstraints();
    }
};
