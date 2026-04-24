<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Meeting;
use App\Models\Submission;
use App\Models\WarningLetter;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $nearestMeeting = Meeting::where('user_id', $user->id)
            ->where('meeting_date', '>=', now())
            ->orderBy('meeting_date', 'asc')
            ->first();

        $totalMeetings = Meeting::where('user_id', $user->id)->count();

        $presentAttendances = Attendance::where('user_id', $user->id)
            ->where('status', 'hadir')
            ->count();

        $attendancePercentage = $totalMeetings > 0
            ? round(($presentAttendances / $totalMeetings) * 100)
            : 0;

        $deadlineTasks = Submission::where('user_id', $user->id)
            ->whereBetween('deadline_at', [now(), now()->addDays(3)])
            ->get();

        $newWarningLetters = WarningLetter::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->get();

        $notifications = [];

        foreach ($deadlineTasks as $task) {
            $notifications[] = [
                'type' => 'deadline',
                'title' => 'Deadline dekat',
                'message' => 'Ada tugas yang deadline-nya segera habis.',
                'data' => $task,
            ];
        }

        foreach ($newWarningLetters as $sp) {
            $notifications[] = [
                'type' => 'warning_letter',
                'title' => 'SP baru',
                'message' => 'Ada surat peringatan baru.',
                'data' => $sp,
            ];
        }

        return response()->json([
            'message' => 'success',
            'data' => [
                'nearest_meeting' => $nearestMeeting,
                'attendance_progress' => [
                    'present' => $presentAttendances,
                    'total' => $totalMeetings,
                    'percentage' => $attendancePercentage,
                ],
                'notifications' => $notifications,
            ],
        ]);
    }
}