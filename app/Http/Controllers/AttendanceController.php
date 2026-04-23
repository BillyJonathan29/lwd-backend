<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $meetings = Meeting::orderBy('date', 'desc')->get();
        // Default to latest meeting if none selected
        $selectedMeetingId = $request->query('meeting_id', $meetings->first()?->id);
        
        $selectedMeeting = null;
        $users = collect(); 

        if ($selectedMeetingId) {
            $selectedMeeting = Meeting::find($selectedMeetingId);
            
            // Get all members
            $users = User::where('role', 'User')
                         ->orderBy('full_name')
                         ->get();
                         
            // Get all attendances for this meeting
            $attendances = Attendance::where('meeting_id', $selectedMeetingId)->get()->keyBy('user_id');
            
            // Map status to users
            $users->map(function ($user) use ($attendances) {
                // If record exists, use that status, else "absent"
                $record = $attendances->get($user->id);
                $user->attendance_record = $record;
                $user->attendance_status = $record ? $record->status : 'absent';
                $user->attended_at = $record ? $record->attended_at : null;
                return $user;
            });
        }

        return view('attendance.index', [
            'title' => 'Data Presensi Anggota',
            'meetings' => $meetings,
            'selectedMeeting' => $selectedMeeting,
            'users' => $users,
            'breadcrumbs' => [
                ['title' => 'Master Data', 'link' => '#'],
                ['title' => 'Presensi', 'link' => route('presensi')]
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'meeting_id' => 'required|exists:meetings,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:present,excused,sick,absent',
            'gps_location' => 'nullable|string'
        ]);

        try {
            Attendance::updateOrCreate(
                [
                    'meeting_id' => $validated['meeting_id'],
                    'user_id' => $validated['user_id']
                ],
                [
                    'status' => $validated['status'],
                    'attended_at' => now(), // Admin override uses current timestamp
                    'gps_location' => $validated['gps_location'] ?? 'Manual Override via Admin'
                ]
            );

            return back()->with('success', 'Status presensi berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error saving attendance: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui presensi. Silakan coba lagi.');
        }
    }

    public function destroy(Attendance $presensi)
    {
        try {
            $presensi->delete();
            return back()->with('success', 'Data kehadiran ditarik / di-reset menjadi Alpa.');
        } catch (\Exception $e) {
            Log::error('Error deleting attendance: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus presensi.');
        }
    }

    /**
     * Ambil data LPJ presensi sesuai scope (all/single).
     */
    private function getLpjData(Request $request): array
    {
        $scope = $request->query('export_scope', 'all');
        $meetingId = $request->query('meeting_id');

        $members = User::where('role', 'User')->orderBy('full_name')->get();

        if ($scope === 'single' && $meetingId) {
            $meetings = Meeting::where('id', $meetingId)->orderBy('date')->get();
        } else {
            $meetings = Meeting::orderBy('date')->get();
        }

        // Semua attendance yang relevan
        $attendanceMap = Attendance::whereIn('meeting_id', $meetings->pluck('id'))
            ->get()
            ->groupBy('meeting_id')
            ->map(fn($rows) => $rows->keyBy('user_id'));

        // Susun baris data per anggota
        $rows = [];
        foreach ($members as $member) {
            $row = [
                'nim'         => $member->nim,
                'full_name'   => $member->full_name,
                'subdivision' => $member->subdivision ?? '-',
            ];
            $hadir = $izin = $sakit = $alpa = 0;
            foreach ($meetings as $meeting) {
                $record = $attendanceMap->get($meeting->id)?->get($member->id);
                $status = $record?->status ?? 'absent';
                $row['meeting_' . $meeting->id] = $status;
                match ($status) {
                    'present' => $hadir++,
                    'excused' => $izin++,
                    'sick'    => $sakit++,
                    default   => $alpa++,
                };
            }
            $row['hadir'] = $hadir;
            $row['izin']  = $izin;
            $row['sakit'] = $sakit;
            $row['alpa']  = $alpa;
            $rows[] = $row;
        }

        return compact('meetings', 'members', 'rows', 'scope');
    }

    /**
     * Export LPJ Presensi ke CSV (bisa dibuka di Excel).
     */
    public function exportExcel(Request $request)
    {
        ['meetings' => $meetings, 'rows' => $rows] = $this->getLpjData($request);

        $filename = 'LPJ_Presensi_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($meetings, $rows) {
            $handle = fopen('php://output', 'w');
            // BOM agar Excel baca UTF-8 dengan benar
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header baris 1
            $header = ['NIM', 'Nama Lengkap', 'Divisi'];
            foreach ($meetings as $m) {
                $header[] = Carbon::parse($m->date)->translatedFormat('d M Y') . "\n" . $m->topic_title;
            }
            $header[] = 'Hadir';
            $header[] = 'Izin';
            $header[] = 'Sakit';
            $header[] = 'Alpa';
            fputcsv($handle, $header);

            $statusLabel = [
                'present' => 'Hadir',
                'excused' => 'Izin',
                'sick'    => 'Sakit',
                'absent'  => 'Alpa',
            ];

            foreach ($rows as $row) {
                $line = [$row['nim'], $row['full_name'], $row['subdivision']];
                foreach ($meetings as $m) {
                    $line[] = $statusLabel[$row['meeting_' . $m->id]] ?? '-';
                }
                $line[] = $row['hadir'];
                $line[] = $row['izin'];
                $line[] = $row['sakit'];
                $line[] = $row['alpa'];
                fputcsv($handle, $line);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export LPJ Presensi ke PDF (via HTML print-ready view).
     */
    public function exportPdf(Request $request)
    {
        $data = $this->getLpjData($request);
        return view('attendance.export-pdf', $data);
    }
}
