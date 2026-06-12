<?php

namespace Database\Seeders;

use App\Models\ChecklistItem;
use App\Models\ChecklistRecord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChecklistItemSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ChecklistRecord::truncate();
        ChecklistItem::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $items = [

            // ── SEMUA SHIFT ──────────────────────────────────────────
            ['shift' => 'all',   'order' =>  1, 'name' => 'Cek suhu chiller (target: 0–4°C) dan catat di SIMOR',             'description' => 'Wajib dicatat tiap shift'],
            ['shift' => 'all',   'order' =>  2, 'name' => 'Cek suhu freezer (target: -18 s/d -22°C) dan catat di SIMOR',     'description' => 'Wajib dicatat tiap shift'],
            ['shift' => 'all',   'order' =>  3, 'name' => 'Meja persiapan bahan baku dibersihkan dengan disinfektan',         'description' => null],
            ['shift' => 'all',   'order' =>  4, 'name' => 'Lantai dapur disapu dan dipel',                                    'description' => null],
            ['shift' => 'all',   'order' =>  5, 'name' => 'Tempat sampah dicek — ganti plastik jika penuh',                   'description' => null],
            ['shift' => 'all',   'order' =>  6, 'name' => 'Wastafel & sabun cuci tangan tersedia',                            'description' => 'Food safety priority'],
            ['shift' => 'all',   'order' =>  7, 'name' => 'Staf memakai seragam, apron, dan hair net',                        'description' => null],
            ['shift' => 'all',   'order' =>  8, 'name' => 'Peralatan masak (wajan, spatula) bersih sebelum digunakan',        'description' => null],

            // ── SHIFT PAGI (Opening) ──────────────────────────────────
            ['shift' => 'pagi',  'order' =>  1, 'name' => 'Buka kunci & pastikan semua lampu dan peralatan berfungsi',        'description' => 'Opening checklist'],
            ['shift' => 'pagi',  'order' =>  2, 'name' => 'Cek stok bahan baku — catat yang kritis di SIMOR',                'description' => null],
            ['shift' => 'pagi',  'order' =>  3, 'name' => 'Siapkan bahan baku harian dari freezer (thawing terencana)',       'description' => 'FIFO — produk lama duluan'],
            ['shift' => 'pagi',  'order' =>  4, 'name' => 'Cek tanggal kadaluarsa semua bahan yang akan digunakan hari ini',  'description' => 'Buang bahan expired'],
            ['shift' => 'pagi',  'order' =>  5, 'name' => 'Hidupkan mesin kasir dan cek koneksi printer struk',               'description' => null],
            ['shift' => 'pagi',  'order' =>  6, 'name' => 'Siapkan packaging: box, cup, sauce cup, flag, sleeve',            'description' => null],
            ['shift' => 'pagi',  'order' =>  7, 'name' => 'Area driver & parkir dibersihkan',                                 'description' => null],
            ['shift' => 'pagi',  'order' =>  8, 'name' => 'Toilet dibersihkan — isi form checklist toilet',                   'description' => null],

            // ── SHIFT SIANG (Middle) ─────────────────────────────────
            ['shift' => 'siang', 'order' =>  1, 'name' => 'Lakukan serah terima stok dengan shift pagi',                     'description' => 'Catat selisih stok'],
            ['shift' => 'siang', 'order' =>  2, 'name' => 'Restok bahan baku yang menipis dari gudang/freezer',              'description' => null],
            ['shift' => 'siang', 'order' =>  3, 'name' => 'Bersihkan area display dan etalase',                              'description' => null],
            ['shift' => 'siang', 'order' =>  4, 'name' => 'Periksa kondisi sauce & bumbu WIP — buang jika tidak layak',      'description' => 'Peak hour — food safety kritis'],
            ['shift' => 'siang', 'order' =>  5, 'name' => 'Cek ketersediaan packaging (midday restock)',                     'description' => null],
            ['shift' => 'siang', 'order' =>  6, 'name' => 'Toilet dicek kembali saat peak hour',                             'description' => null],
            ['shift' => 'siang', 'order' =>  7, 'name' => 'Sampah dikosongkan setelah peak hour siang',                      'description' => null],

            // ── SHIFT MALAM (Closing) ────────────────────────────────
            ['shift' => 'malam', 'order' =>  1, 'name' => 'Hitung stok akhir hari dan input di SIMOR',                       'description' => 'Wajib akurat untuk seeder besok'],
            ['shift' => 'malam', 'order' =>  2, 'name' => 'Sisa bahan baku diberi label tanggal & disimpan dengan benar',    'description' => 'FIFO + label waktu buka'],
            ['shift' => 'malam', 'order' =>  3, 'name' => 'Bersihkan kompor, hood, dan exhaust fan',                         'description' => null],
            ['shift' => 'malam', 'order' =>  4, 'name' => 'Cuci dan sterilkan semua peralatan masak',                        'description' => null],
            ['shift' => 'malam', 'order' =>  5, 'name' => 'Matikan semua peralatan listrik kecuali freezer & chiller',       'description' => null],
            ['shift' => 'malam', 'order' =>  6, 'name' => 'Toilet closing: dibersihkan dan dikunci',                         'description' => null],
            ['shift' => 'malam', 'order' =>  7, 'name' => 'Cek APAR — pastikan tidak kadaluarsa dan tekanan normal',         'description' => null],
            ['shift' => 'malam', 'order' =>  8, 'name' => 'Jalur evakuasi tidak terhalang',                                  'description' => null],
            ['shift' => 'malam', 'order' =>  9, 'name' => 'Kunci semua pintu dan pastikan alarm aktif',                      'description' => 'Closing final'],
        ];

        foreach ($items as $item) {
            ChecklistItem::create(array_merge($item, ['is_active' => true]));
        }
    }
}