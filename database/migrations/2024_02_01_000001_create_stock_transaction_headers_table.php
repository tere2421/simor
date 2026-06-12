<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabel header transaksi — satu record = satu sesi input
        Schema::create('stock_transaction_headers', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 30)->unique(); // TRX-20250521-001
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->enum('type', ['in', 'out']);
            $table->date('transaction_date');
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // Tambah kolom header_id ke stock_transactions yang sudah ada
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->foreignId('header_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('stock_transaction_headers')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropForeign(['header_id']);
            $table->dropColumn('header_id');
        });
        Schema::dropIfExists('stock_transaction_headers');
    }
};
