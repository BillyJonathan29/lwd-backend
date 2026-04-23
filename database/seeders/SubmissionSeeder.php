<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Meeting;
use App\Models\User;

class SubmissionSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan tabel submissions kosong sebelum di-seed (opsional tapi aman)
        DB::table('submissions')->truncate();

        // Ambil meeting pertama untuk di-assign tugasnya (Fundamental HTML dari seeder)
        $meeting = Meeting::first();

        // Jika tidak ada meeting, batalkan
        if (!$meeting) return;

        // Ambil beberapa user (member)
        $users = User::where('role', 'User')->get();
        if ($users->count() < 3) return;

        // Mendapatkan deadline dari pertemuan untuk simulasi terlambat / tepat waktu
        $deadline = Carbon::parse($meeting->assignment_deadline);

        $submissions = [
            // User 1: Kumpul tepat waktu & Sudah dinilai
            [
                'meeting_id' => $meeting->id,
                'user_id' => $users[0]->id, // Rifki
                'file_or_link' => 'https://github.com/rifki/fundamental-html-task',
                'submitted_at' => $deadline->copy()->subDays(1), // 1 hari sebelum deadline
                'member_notes' => 'Halo min, ini tugas HTML saya. Saya taruh di repo github ya. Terima kasih!',
                'grade' => 85,
                'admin_feedback' => 'Sangat bagus! Struktur kode sudah rapih, namun perhatikan penggunaan tag semantiknya (section, article, dll) agar lebih optimal. Lanjutkan kerjanya!',
            ],
            // User 2: Kumpul terlambat & Sudah dinilai
            [
                'meeting_id' => $meeting->id,
                'user_id' => $users[1]->id, // Budi Santoso
                'file_or_link' => 'https://docs.google.com/document/d/tugas-budi',
                'submitted_at' => $deadline->copy()->addDays(1), // 1 hari telat
                'member_notes' => 'Maaf telat kumpul bang, kemarin terkendala internet mati.',
                'grade' => 65,
                'admin_feedback' => 'Tugas sudah masuk dan oke secara basic. Tetapi karena telat, nilainya dikurangi. Besok perhatikan deadlinenya ya.',
            ],
            // User 3: Kumpul tepat waktu & Belum dinilai (Menunggu)
            [
                'meeting_id' => $meeting->id,
                'user_id' => $users[2]->id, // Siti Aminah
                'file_or_link' => 'https://i.pinimg.com/736x/a2/48/9a/a2489a9ebb904e86bdf97719f3f286a2.jpg',
                'submitted_at' => $deadline->copy()->subHours(5), // 5 jam sebelum deadline
                'member_notes' => 'Saya deploy langsung di Vercel bang. Boleh di cek langsung hasilnya.',
                'grade' => null,
                'admin_feedback' => null,
            ]
        ];

        DB::table('submissions')->insert($submissions);

        // Sisanya (user_id 4 dan 5 misalnya) dibiarkan kosong agar berstatus "Belum Kumpul" di UI.
    }
}
