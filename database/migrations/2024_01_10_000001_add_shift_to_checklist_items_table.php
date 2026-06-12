<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('checklist_items', function (Blueprint $table) {
            // shift: pagi | siang | malam | all (berlaku semua shift)
            $table->enum('shift', ['pagi', 'siang', 'malam', 'all'])
                  ->default('all')
                  ->after('order');
        });
    }

    public function down(): void
    {
        Schema::table('checklist_items', function (Blueprint $table) {
            $table->dropColumn('shift');
        });
    }
};
