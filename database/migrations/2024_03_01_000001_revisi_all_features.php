<?php
// ══════════════════════════════════════════════════════
// JALANKAN SATU PER SATU — copy tiap class ke file terpisah
// atau jalankan semua via php artisan migrate
// ══════════════════════════════════════════════════════

// FILE 1: database/migrations/2024_03_01_000001_create_stock_opname_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── 1. STOCK OPNAME ──────────────────────────────────────
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('opname_code', 30)->unique(); // OP-2025-06
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->date('opname_date');
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->string('period', 20); // 2025-06 (YYYY-MM)
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('stock_opname_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opname_id')->constrained('stock_opnames')->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->restrictOnDelete();
            $table->decimal('system_stock', 10, 2);   // stok di sistem sebelum opname
            $table->decimal('actual_stock', 10, 2);   // stok fisik hasil hitung
            $table->decimal('difference', 10, 2)->storedAs('actual_stock - system_stock');
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // ── 2. MANAGER TASK LIST (SM & PIC checklist) ────────────
        Schema::create('manager_task_lists', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url')->nullable();        // link ke GForm/GSheet
            $table->enum('role_target', ['SM', 'PIC', 'both'])->default('both');
            $table->enum('frequency', [
                'daily', 'monday', 'tuesday', 'wednesday',
                'thursday', 'friday', 'weekly', 'monthly'
            ])->default('daily');
            $table->string('category')->nullable();   // pengelompokan visual
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('manager_task_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('manager_task_lists')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->date('date');
            $table->boolean('is_done')->default(false);
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->unique(['task_id', 'user_id', 'date'], 'unique_task_user_date');
        });

        // ── 3. REVAMP ATTENDANCE ──────────────────────────────────
        Schema::table('attendances', function (Blueprint $table) {
            // Ubah status — fokus ke masalah/ketidakhadiran
            $table->enum('status', [
                'terlambat',
                'alpha',
                'tidak_hadir',
                'izin',
                'sakit',
                'pulang_awal',
                'masalah_lain',
            ])->default('tidak_hadir')->change();

            $table->time('check_in_actual')->nullable()->after('check_out')
                  ->comment('Untuk terlambat: jam masuk aktual');
            $table->integer('late_minutes')->nullable()->after('check_in_actual')
                  ->comment('Menit keterlambatan');
            $table->string('problem_description')->nullable()->after('late_minutes')
                  ->comment('Deskripsi masalah / alasan detail');
        });

        // ── 4. REVAMP SHIFT CODES ─────────────────────────────────
        Schema::table('shifts', function (Blueprint $table) {
            $table->string('code', 10)->unique()->nullable()->after('id')
                  ->comment('Format: H + 2digit durasi + 4digit jam masuk. Contoh: H080800');
            $table->tinyInteger('duration_hours')->nullable()->after('code')
                  ->comment('Durasi kerja dalam jam, dari 2 digit pertama kode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_lines');
        Schema::dropIfExists('stock_opnames');
        Schema::dropIfExists('manager_task_records');
        Schema::dropIfExists('manager_task_lists');
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['check_in_actual', 'late_minutes', 'problem_description']);
        });
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['code', 'duration_hours']);
        });
    }
};
