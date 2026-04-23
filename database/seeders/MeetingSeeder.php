<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MeetingSeeder extends Seeder
{
    public function run(): void
    {
        $materiList = [
            "Fundamental HTML: Instalasi, Basic Template, HTML Heading, Paragraf, List, Table, Form.",
            "Fundamental Arduino: Pengenalan Wokwi, Rangkaian Dasar, C++ Setup/Loop, Blink LED.",
            "Fundamental CSS: Type Selector, Text Styling, Display (Grid/Flexbox), Margin, Padding.",
            "Simulasi dengan Tinkercad: Tegangan, arus, Hukum Ohm",
            "Pengenalan CSS Lanjutan: Width, Height, Overflow, hover, dan z-index",
            "Rangkaian Dasar: Seri & paralel, breadboard circuit sederhana",
            "Bootstrap 5: Grid system, komponen dasar, navbar, card, form, responsif",
            "Pengenalan sensor: Suhu (DHT11), cahaya (LDR), jarak (HC-SR04)",
            "Basic Javascript: Sintaks dasar dan Console",
            "Pengenalan aktuator: LED, buzzer, relay, servo motor",
            "Javascript: Manipulasi DOM",
            "Pengenalan Arduino: Board, IDE, struktur program (setup/loop)",
            "Javascript: Event Listener & Handling",
            "Digital output: LED blink, pin control",
            "Git: Init, commit, branch, merge, push",
            "PHP Dasar: Sintaks, variabel, tipe data, echo, Instalasi Laragon",
            "Digital input: Tombol push button, pull-up/pull-down",
            "PHP Dasar: Operator, kondisi (if/else), perulangan",
            "Analog input: Potensiometer, LDR, analogRead()",
            "OOP PHP: Class, object, property, method, constructor",
            "PWM output: Kontrol kecerahan LED, kecepatan motor",
            "OOP lanjut: Inheritance, polymorphism, namespace",
            "Serial communication: Serial.print, Serial Monitor",
            "MySQL: Pengenalan, query dasar (SELECT, INSERT, UPDATE, DELETE)",
            "Mini project IoT: Sistem alarm suhu dengan DHT11 + buzzer",
            "MySQL Lanjutan: Relasi database, JOIN, query lanjutan",
            "LCD I2C: Menampilkan data sensor ke layar",
            "Integrasi PHP + MySQL: CRUD manual",
            "Servo motor: Kendali sudut, project pintu otomatis",
            "Database: Pengenalan Trigger",
            "Sensor ultrasonik HC-SR04: Deteksi jarak",
            "Laravel: Instalasi, Composer, struktur project",
            "Relay module: Kendali perangkat tegangan tinggi",
            "Laravel: Routing & Controller",
            "Library Arduino: Instalasi, penggunaan, contoh umum"
        ];

        // Kata kunci untuk memilah kategori enum yang sesuai
        $hardwareKeywords = ['arduino', 'tegangan', 'rangkaian', 'sensor', 'aktuator', 'digital', 'analog', 'pwm', 'serial', 'lcd', 'servo', 'ultrasonik', 'relay', 'iot'];
        $toolsKeywords = ['git', 'tinkercad', 'wokwi', 'laragon', 'composer'];

        $data = [];
        $startDate = Carbon::now();

        foreach ($materiList as $index => $materi) {

            // 1. Tentukan kategori berdasarkan enum ['web', 'hardware', 'tools']
            $category = 'web'; // Default

            foreach ($hardwareKeywords as $keyword) {
                if (stripos($materi, $keyword) !== false) {
                    $category = 'hardware';
                    break;
                }
            }
            // Jika belum masuk hardware, cek apakah masuk tools
            if ($category === 'web') {
                foreach ($toolsKeywords as $keyword) {
                    if (stripos($materi, $keyword) !== false) {
                        $category = 'tools';
                        break;
                    }
                }
            }

            // 2. Pisahkan judul dan deskripsi
            $title = $materi;
            $description = 'Pembahasan mengenai ' . $materi;

            if (strpos($materi, ':') !== false) {
                $parts = explode(':', $materi, 2);
                $title = trim($parts[0]);
                $description = trim($parts[1]);
            }

            // 3. Setup Tanggal
            $meetingDate = $startDate->copy()->addDays($index * 3);
            $deadlineDate = $meetingDate->copy()->addDays(7);

            $data[] = [
                // Maksimal 150 karakter sesuai database Anda
                'topic_title'         => substr($title, 0, 150),
                'category'            => $category,
                // Menggunakan format date biasa (Y-m-d) karena tipe datanya ->date()
                'date'                => $meetingDate->format('Y-m-d'),
                // Menggunakan format datetime untuk assignment_deadline
                'assignment_deadline' => $deadlineDate->format('Y-m-d H:i:s'),
                'description'         => ucfirst($description),
                'created_at'          => now(),
                'updated_at'          => now(),
            ];
        }

        // 4. Insert data
        foreach ($data as $row) {
            DB::table('meetings')->insert($row);
        }
    }
}
