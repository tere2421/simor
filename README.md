# SIMOR — Panduan Instalasi Revisi 4 Fitur

## File yang perlu di-copy ke project

```
simor-revisi/
├── database/
│   ├── migrations/
│   │   └── 2024_03_01_000001_revisi_all_features.php  ← MIGRATION UTAMA
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── ShiftSeeder.php
│       └── ManagerTaskSeeder.php
├── app/
│   ├── Models/
│   │   ├── StockOpname.php
│   │   ├── StockOpnameLine.php
│   │   ├── ManagerTaskList.php
│   │   ├── ManagerTaskRecord.php
│   │   ├── Attendance.php         ← replace model lama
│   │   └── Shift.php              ← replace model lama
│   └── Http/Controllers/
│       ├── StockOpnameController.php
│       ├── ManagerTaskController.php
│       ├── AttendanceController.php  ← replace controller lama
│       └── ShiftController.php       ← replace controller lama
├── resources/views/
│   ├── opnames/           ← folder baru
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── show.blade.php
│   ├── manager-tasks/     ← folder baru
│   │   ├── index.blade.php
│   │   ├── manage.blade.php
│   │   └── form.blade.php
│   ├── attendances/       ← replace semua
│   │   ├── index.blade.php
│   │   └── create.blade.php
│   ├── shifts/            ← folder baru
│   │   ├── index.blade.php
│   │   └── create.blade.php
│   └── layouts/
│       └── _sidebar_nav.blade.php  ← petunjuk update sidebar
└── routes/
    └── web.php            ← replace routes lama
```

---

## Langkah Instalasi

### 1. Copy semua file ke folder project
```bash
# Copy semua file dari ZIP ke C:/laragon/www/simor/
```

### 2. Jalankan migration
```bash
php artisan migrate
```

### 3. Seed data shift dan manager tasks
```bash
# Seed shift codes baru (reset shift lama)
php artisan db:seed --class=ShiftSeeder

# Seed task SM & PIC
php artisan db:seed --class=ManagerTaskSeeder
```

### 4. Update sidebar di layouts/app.blade.php
Buka `resources/views/layouts/app.blade.php`, cari bagian nav sidebar,
ganti isi `<div style="overflow-y:auto;flex:1;...">` dengan isi dari
file `_sidebar_nav.blade.php` yang ada di ZIP.

### 5. Clear cache
```bash
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## Ringkasan Fitur Baru

### 1. Stock Opname Bulanan
- URL: `/opnames`
- Download template CSV → isi stok fisik → upload kembali
- Atau input manual langsung di browser (bulk form semua item sekaligus)
- Approve oleh SM/PIC → stok otomatis diupdate + transaksi adjustment dicatat

### 2. Task Checklist SM & PIC
- URL: `/manager-tasks`
- SM lihat task SM + task PIC; PIC hanya lihat task PIC
- SM bisa tambah/edit/hapus task di `/manager-tasks/manage`
- Judul task bisa diklik jika ada URL (GForm/GSheet/Notion/dll)
- Dikelompokkan per frekuensi: Setiap Hari, Senin-Jumat, Mingguan, Bulanan

### 3. Kehadiran — Kendala & Ketidakhadiran
- URL: `/attendances`
- Kategori: Terlambat, Alpha, Tidak Hadir, Izin, Sakit, Pulang Awal, Masalah Lain
- Terlambat: input jam masuk aktual → menit keterlambatan dihitung otomatis jika ada jadwal
- Summary per bulan dengan filter kategori

### 4. Kode Shift
- URL: `/shifts`
- Format: H + 2 digit durasi + 4 digit jam masuk → contoh: H080800 = 8 jam, masuk 08:00, keluar 16:00
- SM tambah kode shift baru → tersimpan di database → langsung bisa dipakai di jadwal mingguan
- Preview realtime saat mengetik kode

---

## Akun Login

| Role | Email | Password |
|---|---|---|
| SM | azidan@hangry.id | hangry123 |
| PIC | suci.hendry@hangry.id | hangry123 |
| Staff | wahyu.hidayat@hangry.id | hangry123 |
