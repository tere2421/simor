<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\StaffProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Data staff asli Hangry Indonesia
        $staff = [
            // SM
            ['nip' => '30115', 'name' => 'Azidan',                    'role' => 'SM',    'position' => 'Store Manager', 'shift_type' => 'FT', 'email' => 'azidan@hangry.id'],

            // PIC
            ['nip' => '51416', 'name' => 'Suci Hendry Syafriyanto',   'role' => 'PIC',   'position' => 'PIC',           'shift_type' => 'FT', 'email' => 'suci.hendry@hangry.id'],
            ['nip' => '30309', 'name' => 'Inggar Nurdianingsih',      'role' => 'PIC',   'position' => 'PIC',           'shift_type' => 'FT', 'email' => 'inggar@hangry.id'],
            ['nip' => '31641', 'name' => 'Angga Aji Pangestu',        'role' => 'PIC',   'position' => 'PIC',           'shift_type' => 'FT', 'email' => 'angga.aji@hangry.id'],

            // Senior Staff DW
            ['nip' => '51026', 'name' => 'Wahyu Hidayat',             'role' => 'Staff', 'position' => 'Senior Staff',  'shift_type' => 'FT', 'email' => 'wahyu.hidayat@hangry.id'],
            ['nip' => '53638', 'name' => 'Mohamad Damar Shodiq',      'role' => 'Staff', 'position' => 'Senior Staff',  'shift_type' => 'DW', 'email' => 'damar.shodiq@hangry.id'],
            ['nip' => '53845', 'name' => 'Mohamad Dzakie Hymli',      'role' => 'Staff', 'position' => 'Senior Staff',  'shift_type' => 'DW', 'email' => 'dzakie.hymli@hangry.id'],
            ['nip' => '55090', 'name' => 'Nadinda Fitri Daury',       'role' => 'Staff', 'position' => 'Senior Staff',  'shift_type' => 'DW', 'email' => 'nadinda.fitri@hangry.id'],
            ['nip' => '55339', 'name' => 'Erika Nur Afni',            'role' => 'Staff', 'position' => 'Senior Staff',  'shift_type' => 'DW', 'email' => 'erika.nurafni@hangry.id'],
            ['nip' => '55550', 'name' => 'Muhamad Samhuri',           'role' => 'Staff', 'position' => 'Senior Staff',  'shift_type' => 'DW', 'email' => 'muhamad.samhuri@hangry.id'],
            ['nip' => '55478', 'name' => 'Riyan',                     'role' => 'Staff', 'position' => 'Senior Staff',  'shift_type' => 'DW', 'email' => 'riyan@hangry.id'],
            ['nip' => '55935', 'name' => 'Teresia Irma Cahyani',      'role' => 'Staff', 'position' => 'Senior Staff',  'shift_type' => 'DW', 'email' => 'teresia.irma@hangry.id'],
            ['nip' => '51168', 'name' => 'Hari Setio',                'role' => 'Staff', 'position' => 'Senior Staff',  'shift_type' => 'DW', 'email' => 'hari.setio@hangry.id'],
            ['nip' => '53064', 'name' => 'Muhammad Nur Habibi',       'role' => 'Staff', 'position' => 'Senior Staff',  'shift_type' => 'DW', 'email' => 'nur.habibi@hangry.id'],
            ['nip' => '53120', 'name' => 'Afriyan Sanjaya',           'role' => 'Staff', 'position' => 'Senior Staff',  'shift_type' => 'DW', 'email' => 'afriyan.sanjaya@hangry.id'],
            ['nip' => '55140', 'name' => 'Adam Ahsan Jaelani',        'role' => 'Staff', 'position' => 'Senior Staff',  'shift_type' => 'DW', 'email' => 'adam.ahsan@hangry.id'],
            ['nip' => '55480', 'name' => 'Nur Budi Prastiyo',         'role' => 'Staff', 'position' => 'Senior Staff',  'shift_type' => 'DW', 'email' => 'nurbudi.prastiyo@hangry.id'],
            ['nip' => '55588', 'name' => 'Mohamad Afrizal',           'role' => 'Staff', 'position' => 'Senior Staff',  'shift_type' => 'DW', 'email' => 'mohamad.afrizal@hangry.id'],
            ['nip' => '55687', 'name' => 'Rico Harazaki',             'role' => 'Staff', 'position' => 'Senior Staff',  'shift_type' => 'DW', 'email' => 'rico.harazaki@hangry.id'],
        ];

        foreach ($staff as $s) {
            $user = User::create([
                'name'     => $s['name'],
                'email'    => $s['email'],
                'password' => Hash::make('hangry123'),
                'role'     => $s['role'],
            ]);

            StaffProfile::create([
                'user_id'     => $user->id,
                'employee_id' => $s['nip'],
                'name'        => $s['name'],
                'position'    => $s['position'],
                'shift_type'  => $s['shift_type'],
                'is_active'   => true,
                'join_date'   => now()->subMonths(rand(1, 36)),
            ]);
        }
    }
}
