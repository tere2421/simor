<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('name');
            $table->string('unit', 50);
            $table->decimal('current_stock', 10, 2)->default(0);
            $table->decimal('min_stock_threshold', 10, 2);
            $table->string('storage_location', 100)->nullable();
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('ingredients');
    }
};
