<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

class AttendanceController extends Controller
{
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'meeting_id'   => 'required|exists:meetings,id',
                'gps_location' => 'nullable|string',
                'status'       => 'required|in:present,excused,sick,absent',
            ]);

            // Jika user klik Hadir (present), mereka WAJIB ada di area kampus
            if ($request->status === 'present') {
                if (empty($request->gps_location)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'GPS harus diaktifkan untuk melakukan presensi kehadiran.',
                        'data'    => null,
                    ], 400);
                }

                $coordinates = explode(',', $request->gps_location);
                if (count($coordinates) != 2) {
                    return response()->json(['success' => false, 'message' => 'Format GPS tidak valid.']);
                }

                $userLat = (float) trim($coordinates[0]);
                $userLon = (float) trim($coordinates[1]);

                // Titik Pusat Kampus 2 Uniku
                $kampusLat = -6.975681;
                $kampusLon = 108.477489;

                // Hitung Jarak
                $jarakMeter = $this->calculateDistance($userLat, $userLon, $kampusLat, $kampusLon);

                // Maksimal toleransi jarak (misal: 100 meter dari titik pusat kampus)
                $maxRadius = 100;

                if ($jarakMeter > $maxRadius) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda berada di luar area kampus! (Jarak: ' . round($jarakMeter) . ' meter). Silakan masuk ke area Kampus 2 Uniku.',
                        'data'    => null,
                    ], 403);
                }
            }

            $userId = $request->user()->id;

            $alreadyAttended = Attendance::where('meeting_id', $request->meeting_id)
                ->where('user_id', $userId)
                ->exists();

            if ($alreadyAttended) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan presensi untuk pertemuan ini.',
                    'data'    => null,
                ], 422);
            }

            $attendance = Attendance::create([
                'meeting_id'   => $request->meeting_id,
                'user_id'      => $userId,
                'gps_location' => $request->gps_location,
                'status'       => $request->status,
                'attended_at'  => now(),
            ]);

            $pesan = match ($request->status) {
                'present' => 'Presensi kehadiran berhasil dicatat. Anda berada di area kampus.',
                'excused' => 'Pengajuan izin berhasil dicatat.',
                'sick'    => 'Pemberitahuan sakit berhasil dicatat.',
                default   => 'Data presensi berhasil dicatat.'
            };

            return response()->json([
                'success' => true,
                'message' => $pesan,
                'data'    => $attendance,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage(),
            ], 500);
        }
    }
}
