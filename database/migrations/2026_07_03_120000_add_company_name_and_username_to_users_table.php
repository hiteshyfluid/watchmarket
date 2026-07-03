<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'company_name')) {
                $table->string('company_name')->nullable()->after('last_name');
            }
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('company_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'username']);
        });
    }
};
