<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Meeting;
use App\Models\Attendance;
use App\Models\Submission;
use App\Models\WarningLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Kartu Statistik Utama
        $totalMembers = User::where('role', 'User')->count(); // Anggota aktif
        $softwareMembers = User::where('role', 'User')
                                ->where(function($q) {
                                    $q->where('subdivision', 'Software')->orWhere('subdivision', 'Web');
                                })->count(); 
        $hardwareMembers = User::where('role', 'User')
                                ->where(function($q) {
                                    $q->where('subdivision', 'Hardware')->orWhere('subdivision', 'IoT');
                                })->count();
                                
        // Real attendance rate calculation
        $pastMeetingsCount = Meeting::where('date', '<', now())->count();
        $totalPotentialAttendances = $totalMembers * $pastMeetingsCount;
        $actualAttendances = Attendance::where('status', 'present')->count();
        
        $attendanceRate = ($totalPotentialAttendances > 0) 
            ? round(($actualAttendances / $totalPotentialAttendances) * 100) 
            : 0;

        $pendingTasks = Submission::whereNull('grade')->count(); 
        $activeSP = WarningLetter::count(); 

        // 2. Jadwal LWD Terdekat
        $upcomingMeetings = Meeting::where('date', '>=', now())
                                   ->orderBy('date', 'asc')
                                   ->take(3)
                                   ->get();

        // 3. Log Aktivitas Terbaru (Merged)
        $activities = collect();

        Attendance::with(['user', 'meeting'])->orderBy('attended_at', 'desc')->take(5)->get()->each(function($att) use ($activities) {
            $activities->push([
                'type' => 'attendance',
                'title' => $att->status === 'present' ? 'Hadir Presensi' : ($att->status === 'excused' ? 'Izin Presensi' : 'Status Presensi'),
                'user' => $att->user->full_name,
                'description' => "Menandai status " . $att->status . " pada " . ($att->meeting->topic_title ?? 'Pertemuan'),
                'time' => $att->attended_at,
                'icon' => $att->status === 'present' ? 'user-check' : 'user-x',
                'color' => $att->status === 'present' ? 'blue' : 'orange'
            ]);
        });

        Submission::with(['user', 'meeting'])->orderBy('created_at', 'desc')->take(5)->get()->each(function($sub) use ($activities) {
            $activities->push([
                'type' => 'submission',
                'title' => 'Tugas Dikumpulkan',
                'user' => $sub->user->full_name,
                'description' => "Mengumpulkan tugas " . ($sub->meeting->topic_title ?? 'Pertemuan'),
                'time' => $sub->created_at,
                'icon' => 'file-check-2',
                'color' => 'green'
            ]);
        });

        WarningLetter::with(['user'])->orderBy('issued_at', 'desc')->take(5)->get()->each(function($sp) use ($activities) {
            $activities->push([
                'type' => 'warning',
                'title' => 'SP Diterbitkan',
                'user' => $sp->user->full_name,
                'description' => "Menerima " . $sp->warning_level . ": " . Str::limit($sp->reason, 30),
                'time' => $sp->issued_at,
                'icon' => 'alert-triangle',
                'color' => 'red'
            ]);
        });

        $recentActivities = $activities->sortByDesc('time')->take(5);

        // 4. Needs Attention (Problematic Users)
        $meetings = Meeting::where('date', '<', now())->orderBy('date', 'asc')->get();
        $users = User::where('role', 'User')->with(['attendances', 'submissions'])->get();
        $needsAttention = collect();

        foreach ($users as $user) {
            $reason = null;
            $type = null;

            // Check 2x Alpa Berturut
            $consecutiveAbsences = 0;
            foreach ($meetings as $meeting) {
                $att = $user->attendances->where('meeting_id', $meeting->id)->first();
                if (!$att || $att->status === 'absent') {
                    $consecutiveAbsences++;
                    if ($consecutiveAbsences >= 2) {
                        $reason = "2x Alpa Beruntun";
                        $type = 'danger';
                        break;
                    }
                } else {
                    $consecutiveAbsences = 0;
                }
            }

            // If not danger, check for missing tasks
            if (!$reason) {
                $missingTasks = 0;
                foreach ($meetings as $meeting) {
                    if ($meeting->assignment_deadline && now()->gt($meeting->assignment_deadline)) {
                        if (!$user->submissions->where('meeting_id', $meeting->id)->first()) {
                            $missingTasks++;
                        }
                    }
                }
                if ($missingTasks >= 2) {
                    $reason = "$missingTasks Tugas Belum Kumpul";
                    $type = 'warning';
                }
            }

            if ($reason) {
                $needsAttention->push([
                    'user' => $user,
                    'reason' => $reason,
                    'type' => $type
                ]);
            }
        }

        $needsAttention = $needsAttention->take(3);

        return view('dashboard.index', [
            'title' => 'Dashboard Overview',
            'totalMembers' => $totalMembers,
            'softwareMembers' => $softwareMembers,
            'hardwareMembers' => $hardwareMembers,
            'attendanceRate' => $attendanceRate,
            'pendingTasks' => $pendingTasks,
            'activeSP' => $activeSP,
            'upcomingMeetings' => $upcomingMeetings,
            'recentActivities' => $recentActivities,
            'needsAttention' => $needsAttention,
            'breadcrumbs' => [
                [
                    'title' => 'Dashboard',
                    'link' => route('dashboard')
                ]
            ]
        ]);
    }
}
