<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kosongkan tabel dulu supaya tidak duplikat kalau di-run berkali-kali
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('attendances')->truncate();
        DB::table('meetings')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = Carbon::now();

        // 2. Buat Data Users (1 Admin, 5 Member)
        $users = [
            // Admin
            [
                'nim' => '20240810015',
                'full_name' => 'Billy Jonathan',
                'password' => Hash::make('password123'),
                'subdivision' => 'software',
                'role' => 'User',
                'created_at' => $now,
            ],
            [
                'nim' => '20240810019',
                'full_name' => 'Billy (Admin PBK)',
                'password' => Hash::make('password123'),
                'subdivision' => 'software',
                'role' => 'Admin',
                'created_at' => $now,
            ],
            // Members
            [
                'nim' => '20240810016',
                'full_name' => 'Rifki',
                'password' => Hash::make('password123'),
                'subdivision' => 'hardware',
                'role' => 'User',
                'created_at' => $now,
            ],
            [
                'nim' => '20240810017',
                'full_name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'subdivision' => 'software',
                'role' => 'User',
                'created_at' => $now,
            ],
            [
                'nim' => '20240810018',
                'full_name' => 'Siti Aminah',
                'password' => Hash::make('password123'),
                'subdivision' => 'software',
                'role' => 'User',
                'created_at' => $now,
            ],
            [
                'nim' => '20240810021',
                'full_name' => 'Andi Wijaya',
                'password' => Hash::make('password123'),
                'subdivision' => 'hardware',
                'role' => 'User',
                'created_at' => $now,
            ],
            [
                'nim' => '20240810020',
                'full_name' => 'Dewi Lestari',
                'password' => Hash::make('password123'),
                'subdivision' => 'software',
                'role' => 'User',
                'created_at' => $now,
            ]
        ];
        DB::table('users')->insert($users);

        // 3. Buat Data Meetings (Jadwal LWD) - Menyesuaikan UI kamu
        $meetings = [
            [
                'topic_title' => 'Fundamental HTML',
                'description' => 'Pengenalan dasar HTML untuk struktur web.',
                'date' => '2026-04-14', // Tanggal disesuaikan dengan screenshot kamu
                'category' => 'web',
                'assignment_deadline' => Carbon::parse('2026-04-14')->addDays(3),
                'created_at' => $now,
            ],
            [
                'topic_title' => 'Dasar Mikrokontroler Arduino',
                'description' => 'Pengenalan komponen hardware dan LED blink.',
                'date' => '2026-04-21',
                'category' => 'hardware',
                'assignment_deadline' => Carbon::parse('2026-04-21')->addDays(3),
                'created_at' => $now,
            ]
        ];
        DB::table('meetings')->insert($meetings);

        // 4. Buat Data Presensi untuk 'Fundamental HTML' (Meeting ID 1)
        // Kita simulasikan: 3 Hadir, 1 Izin, 1 Alpa (Admin tidak perlu absen)
        $attendances = [
            [
                'meeting_id' => 1,
                'user_id' => 2, // Rifki
                'status' => 'present',
                'attended_at' => Carbon::parse('2026-04-14 16:00:00'),
                'gps_location' => '-6.914744, 107.609810',
            ],
            [
                'meeting_id' => 1,
                'user_id' => 3, // Budi
                'status' => 'present',
                'attended_at' => Carbon::parse('2026-04-14 16:05:00'),
                'gps_location' => '-6.914744, 107.609810',
            ],
            [
                'meeting_id' => 1,
                'user_id' => 4, // Siti
                'status' => 'sick', // Kita buat Siti sakit
                'attended_at' => Carbon::parse('2026-04-14 15:30:00'),
                'gps_location' => null,
            ],
            [
                'meeting_id' => 1,
                'user_id' => 5, // Andi
                'status' => 'absent', // Andi alpa
                'attended_at' => null,
                'gps_location' => null,
            ],
            [
                'meeting_id' => 1,
                'user_id' => 6, // Dewi
                'status' => 'present',
                'attended_at' => Carbon::parse('2026-04-14 16:10:00'),
                'gps_location' => '-6.914744, 107.609810',
            ],
        ];
        DB::table('attendances')->insert($attendances);

        // 5. Buat Dummy Data Submissions / Tugas
        $this->call([
            SubmissionSeeder::class
        ]);
    }
}
