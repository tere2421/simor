<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('checklist_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_item_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->date('date');
            $table->enum('session', ['pagi', 'siang', 'malam'])->default('pagi');
            $table->boolean('is_done')->default(false);
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['checklist_item_id', 'date', 'session']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('checklist_records');
        Schema::dropIfExists('checklist_items');
    }
};
