<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Meeting;
use App\Models\WarningLetter;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WarningLetterController extends Controller
{
    public function index()
    {
        $meetings = Meeting::orderBy('date', 'asc')->get();
        
        $users = User::where('role', 'User')
            ->with(['attendances' => function($q) {
                $q->join('meetings', 'attendances.meeting_id', '=', 'meetings.id')
                  ->orderBy('meetings.date', 'asc')
                  ->select('attendances.*');
            }, 'submissions'])
            ->get();

        $problematicUsers = collect();

        foreach ($users as $user) {
            $reasons = [];

            // 1. Check for absent 2 times consecutively
            $consecutiveAbsences = 0;
            $absenceMeetings = [];
            foreach ($meetings as $meeting) {
                $attendance = $user->attendances->where('meeting_id', $meeting->id)->first();
                $isAbsent = !$attendance || $attendance->status === 'absent';
                
                if ($isAbsent) {
                    $consecutiveAbsences++;
                    $absenceMeetings[] = "Pertemuan " . $meeting->id;
                    if ($consecutiveAbsences >= 2) {
                        $reasons[] = "Alpa 2x berturut-turut (" . implode(' & ', array_slice($absenceMeetings, -2)) . ")";
                        break; // Hanya record sekali untuk kriteria berturut-turut ini
                    }
                } else {
                    $consecutiveAbsences = 0;
                    $absenceMeetings = [];
                }
            }

            // 2. Check for missing submissions (deadline passed, no submission)
            foreach ($meetings as $meeting) {
                if ($meeting->assignment_deadline && Carbon::now()->gt(Carbon::parse($meeting->assignment_deadline))) {
                    $submission = $user->submissions->where('meeting_id', $meeting->id)->first();
                    if (!$submission) {
                        $reasons[] = "Tidak mengumpulkan tugas: " . $meeting->topic_title;
                    }
                }
            }

            if (count($reasons) > 0) {
                $user->reasons = $reasons;
                $problematicUsers->push($user);
            }
        }

        return view('warning_letters.index', [
            'title' => 'Early Warning System (SP)',
            'problematicUsers' => $problematicUsers,
            'breadcrumbs' => [
                ['title' => 'Main Menu', 'link' => '#'],
                ['title' => 'Peringatan Dini', 'link' => route('warning_letters')]
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'warning_level' => 'required|in:SP 1,SP 2,SP 3',
            'reason' => 'required|string',
        ]);

        WarningLetter::create([
            'user_id' => $request->user_id,
            'admin_id' => Auth::id(),
            'warning_level' => $request->warning_level,
            'reason' => $request->reason,
        ]);

        return back()->with('success', 'Surat Peringatan (' . $request->warning_level . ') berhasil diterbitkan.');
    }
}
