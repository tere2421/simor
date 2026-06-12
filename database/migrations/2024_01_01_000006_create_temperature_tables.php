<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('temperature_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('location', 150)->nullable();
            $table->decimal('min_temp', 5, 2);
            $table->decimal('max_temp', 5, 2);
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('temperature_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained('temperature_zones')->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->decimal('temperature', 5, 2);
            $table->boolean('is_abnormal')->default(false);
            $table->string('notes')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('temperature_records');
        Schema::dropIfExists('temperature_zones');
    }
};
