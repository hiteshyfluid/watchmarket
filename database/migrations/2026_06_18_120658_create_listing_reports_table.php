<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('listing_reports', function (Blueprint $table) {
            $table->id();
            $table->string('listing_url')->nullable();
            $table->string('issue_type');
            $table->text('description');
            $table->string('serial_number')->nullable();
            $table->string('reporter_name')->nullable();
            $table->string('reporter_email');
            $table->enum('status', ['new', 'reviewed', 'resolved'])->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_reports');
    }
};
