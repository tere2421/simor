<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('employee_id', 20)->unique();
            $table->string('name');
            $table->enum('position', ['Store Manager', 'PIC', 'Senior Staff', 'Junior Staff']);
            $table->enum('shift_type', ['FT', 'DW'])->default('FT')->comment('Full Time / Daily Worker');
            $table->string('phone', 20)->nullable();
            $table->date('join_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('staff_profiles');
    }
};
