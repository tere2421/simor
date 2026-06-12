<?php

namespace Database\Seeders;

use App\Models\ManagerTaskList;
use App\Models\User;
use Illuminate\Database\Seeder;

class ManagerTaskSeeder extends Seeder
{
    public function run(): void
    {
        $smUser  = User::where('role', 'SM')->first();
        $creator = $smUser?->id ?? 1;

        ManagerTaskList::query()->delete();

        $tasks = [
            // ── DAILY — both ──────────────────────────────────────────
            ['role_target'=>'both', 'frequency'=>'daily',     'order'=>1, 'category'=>'Monitoring',  'title'=>'Check Brand Complaint',               'url'=>null],
            ['role_target'=>'both', 'frequency'=>'daily',     'order'=>2, 'category'=>'Monitoring',  'title'=>'Check CE Tracker',                    'url'=>'https://core.ishangry.com/web#action=1125&model=hangry.ce.tracker&view_type=list&cids=1&menu_id=734'],
            ['role_target'=>'both', 'frequency'=>'daily',     'order'=>3, 'category'=>'Monitoring',  'title'=>'Check Waste Alert',                   'url'=>'https://erp.ishangry.com/report/waste'],
            ['role_target'=>'both', 'frequency'=>'daily',     'order'=>4, 'category'=>'Pengadaan',   'title'=>'Stock Card',                          'url'=>'https://docs.google.com/spreadsheets/d/1dl4ATVJ_Kty2F3CJJRJx9V3HhRPIjgH38cs2USWmWPI/edit?gid=346178651#gid=346178651'],

            // ── DAILY — PIC ───────────────────────────────────────────
            ['role_target'=>'PIC',  'frequency'=>'daily',     'order'=>4, 'category'=>'Operasional', 'title'=>'Pembuatan CP (Customer Proposal)',     'url'=>'https://core.ishangry.com/web#menu_id=299&cids=1&action=507&model=purchase.request&view_type=list'],
            ['role_target'=>'PIC',  'frequency'=>'daily',     'order'=>5, 'category'=>'Kualitas',    'title'=>'Oil Test Strip & Monitoring',          'url'=>'https://docs.google.com/forms/d/e/1FAIpQLSenpqAjDC21p1MQQF_IUbn3kcAcflLsbJRT7kn7zQ_DPmDPoA/viewform'],
            ['role_target'=>'PIC',  'frequency'=>'daily',     'order'=>6, 'category'=>'Operasional', 'title'=>'DSTO (Daily Stock Take Out)',           'url'=>'https://erp.ishangry.com/report/stock/daily'],
            ['role_target'=>'PIC',  'frequency'=>'daily',     'order'=>1, 'category'=>'Food Safety', 'title'=>'Foto Shelflife - Oddo Thawing All Chiller',              'url'=>null],
            ['role_target'=>'PIC',  'frequency'=>'daily',     'order'=>2, 'category'=>'Food Safety', 'title'=>'Foto Penyusunan WIP Sesuai Oddo Thawing',               'url'=>null],
            ['role_target'=>'PIC',  'frequency'=>'daily',     'order'=>3, 'category'=>'Food Safety', 'title'=>'Foto Penyusunan Resting MC - Marinated Whole Chicken',  'url'=>null],
            ['role_target'=>'PIC',  'frequency'=>'daily',     'order'=>4, 'category'=>'Food Safety', 'title'=>'Foto Penyusunan Resting WIP - Marinated Red Chicken',   'url'=>null],
            ['role_target'=>'PIC',  'frequency'=>'daily',     'order'=>5, 'category'=>'Food Safety', 'title'=>'Foto Shelflife - Open Pack Freezer & Chiller (all)',    'url'=>null],
            ['role_target'=>'PIC',  'frequency'=>'daily',     'order'=>6, 'category'=>'Food Safety', 'title'=>'Foto Holding Time - Open Pack Suhu Ruang (all)',        'url'=>null],
            ['role_target'=>'PIC',  'frequency'=>'daily',     'order'=>7, 'category'=>'Monitoring',  'title'=>'Monitoring Waste Variance Harian',                      'url'=>null],
            ['role_target'=>'PIC',  'frequency'=>'daily',     'order'=>8, 'category'=>'Operasional', 'title'=>'GFORM Shelflife Harian',                                'url'=>null],

            // ── MONDAY — SM ───────────────────────────────────────────
            ['role_target'=>'SM',   'frequency'=>'monday',    'order'=>1, 'category'=>'Laporan',     'title'=>'Reason MRT (Market Research & Trend)',  'url'=>null],
            ['role_target'=>'SM',   'frequency'=>'monday',    'order'=>2, 'category'=>'SDM',         'title'=>'Absensi Sosialisasi & Quiz Sosialisasi MSO', 'url'=>null],

            // ── MONDAY — both ─────────────────────────────────────────
            ['role_target'=>'both', 'frequency'=>'monday',    'order'=>3, 'category'=>'Pengadaan',   'title'=>'Pembuatan PR Vendor',                  'url'=>'https://core.ishangry.com/web#menu_id=299&cids=1&action=507&model=purchase.request&view_type=list'],

            // ── MONDAY — PIC ──────────────────────────────────────────
            ['role_target'=>'PIC',  'frequency'=>'monday',    'order'=>4, 'category'=>'Pengadaan',   'title'=>'Konfirmasi Dry',                       'url'=>'https://docs.google.com/spreadsheets/d/1Gz1D2vWPeqjRb20qHn8UiNYILTLs-dfnz6_MYc5OhJg/edit?gid=1719354853#gid=1719354853'],

            // ── TUESDAY — PIC ─────────────────────────────────────────
            ['role_target'=>'PIC',  'frequency'=>'tuesday',   'order'=>1, 'category'=>'Operasional', 'title'=>'QTY Minyak Jelantah',                  'url'=>'https://docs.google.com/spreadsheets/d/1OjfR-EySPYRLkwL4viThaSe-v5fvYmZB0D2WVmI-osE/edit?gid=250248878#gid=250248878&range=AK12'],

            // ── TUESDAY — SM ──────────────────────────────────────────
            ['role_target'=>'SM',   'frequency'=>'tuesday',   'order'=>2, 'category'=>'Equipment',   'title'=>'Cek Equipment Troubleshoot & Temuan Rentokil', 'url'=>'https://asia.myrentokil.com/service/visits'],
            ['role_target'=>'SM',   'frequency'=>'tuesday',   'order'=>3, 'category'=>'Laporan',     'title'=>'Cek Hasil Variance DSTO',              'url'=>null],

            // ── WEDNESDAY — SM ────────────────────────────────────────
            ['role_target'=>'SM',   'frequency'=>'wednesday', 'order'=>1, 'category'=>'Audit',       'title'=>'Cek Hasil Audit',                      'url'=>null],
            ['role_target'=>'SM',   'frequency'=>'wednesday', 'order'=>2, 'category'=>'Platform',    'title'=>'Cek Platform Management',              'url'=>null],
            ['role_target'=>'SM',   'frequency'=>'wednesday', 'order'=>3, 'category'=>'Platform',    'title'=>'Cek Platform Rating',                  'url'=>null],
            ['role_target'=>'SM',   'frequency'=>'wednesday', 'order'=>4, 'category'=>'Platform',    'title'=>'Cek DS (Delivery Service)',             'url'=>null],

            // ── THURSDAY — SM ─────────────────────────────────────────
            ['role_target'=>'SM',   'frequency'=>'thursday',  'order'=>1, 'category'=>'Analitik',    'title'=>'Analisa KPI',                          'url'=>null],
            ['role_target'=>'SM',   'frequency'=>'thursday',  'order'=>2, 'category'=>'SDM',         'title'=>'Cek Manpower Ideal',                   'url'=>null],

            // ── WEEKLY — PIC ──────────────────────────────────────────
            ['role_target'=>'PIC',  'frequency'=>'weekly',    'order'=>1, 'category'=>'Audit',       'title'=>'Master Audit Improvement Knock Out',   'url'=>null],
            ['role_target'=>'PIC',  'frequency'=>'weekly',    'order'=>2, 'category'=>'Audit',       'title'=>'Commitments (Audit Continuous)',        'url'=>null],
            ['role_target'=>'PIC',  'frequency'=>'weekly',    'order'=>3, 'category'=>'Laporan',     'title'=>'Follow Up Temuan Visit Management',     'url'=>null],
            ['role_target'=>'PIC',  'frequency'=>'weekly',    'order'=>4, 'category'=>'Laporan',     'title'=>'Monitoring OPEX Mingguan',              'url'=>null],

            // ── MONTHLY — PIC ─────────────────────────────────────────
            ['role_target'=>'PIC',  'frequency'=>'monthly',   'order'=>1, 'category'=>'Stok',        'title'=>'Stock Opname Bulanan',                 'url'=>null],
            ['role_target'=>'PIC',  'frequency'=>'monthly',   'order'=>2, 'category'=>'Laporan',     'title'=>'Rekap Waste Variance Bulanan',         'url'=>null],
            ['role_target'=>'PIC',  'frequency'=>'monthly',   'order'=>3, 'category'=>'SDM',         'title'=>'Evaluasi Kinerja Staff Bulanan',        'url'=>null],
        ];

        foreach ($tasks as $task) {
            ManagerTaskList::create([...$task, 'is_active' => true, 'created_by' => $creator]);
        }

        $this->command->info('✅ ManagerTaskSeeder: ' . count($tasks) . ' tasks berhasil di-seed.');
    }
}