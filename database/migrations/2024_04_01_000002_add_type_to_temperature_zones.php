<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('temperature_zones', function (Blueprint $table) {
            $table->enum('type', ['chiller','freezer','dry_storage','display','other'])
                  ->default('other')
                  ->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('temperature_zones', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
