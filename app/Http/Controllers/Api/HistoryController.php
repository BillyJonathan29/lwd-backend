<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Submission;
use App\Models\WarningLetter;
use Exception;
class HistoryController extends Controller
{
    public function attendanceHistory(Request $request)
    {
        try {
            $user = $request->user();

            $attendances = Attendance::with('meeting:id,topic_title,date')
                ->where('user_id', $user->id)
                ->orderBy('attended_at', 'desc')
                ->get();

            // Menghitung Summary
            $summary = [
                'present'  => $attendances->where('status', 'present')->count(),
                'excused'  => $attendances->where('status', 'excused')->count(),
                'sick'     => $attendances->where('status', 'sick')->count(),
                'absent'   => $attendances->where('status', 'absent')->count(),
                'total'    => $attendances->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Riwayat presensi berhasil diambil.',
                'data'    => [
                    'summary' => $summary,
                    'history' => $attendances
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat presensi: ' . $e->getMessage(),
                'data'    => null,
            ], 500);
        }
    }

    /**
     * 2. Riwayat Pengumpulan Tugas
     */
    public function submissionHistory(Request $request)
    {
        try {
            $user = $request->user();

            $submissions = Submission::with('meeting:id,topic_title,assignment_deadline')
                ->where('user_id', $user->id)
                ->orderBy('submitted_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Riwayat tugas berhasil diambil.',
                'data'    => $submissions,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat tugas.',
                'data'    => null,
            ], 500);
        }
    }

    /**
     * 3. Riwayat Surat Peringatan (SP)
     */
    public function warningLetterHistory(Request $request)
    {
        try {
            $user = $request->user();

            $warnings = WarningLetter::where('user_id', $user->id)
                ->orderBy('issued_at', 'desc')
                ->get(['id', 'warning_level', 'reason', 'issued_at']);

            return response()->json([
                'success' => true,
                'message' => 'Riwayat SP berhasil diambil.',
                'data'    => $warnings,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data SP.',
                'data'    => null,
            ], 500);
        }
    }
}
