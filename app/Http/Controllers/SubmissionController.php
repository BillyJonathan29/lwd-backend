<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Submission;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SubmissionController extends Controller
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
                         
            // Get all submissions for this meeting
            $submissions = Submission::where('meeting_id', $selectedMeetingId)->get()->keyBy('user_id');
            
            // Map status to users
            $users->map(function ($user) use ($submissions) {
                // If record exists, attach it
                $record = $submissions->get($user->id);
                $user->submission_record = $record;
                return $user;
            });
        }

        return view('submission.index', [
            'title' => 'Evaluasi Tugas Anggota',
            'meetings' => $meetings,
            'selectedMeeting' => $selectedMeeting,
            'users' => $users,
            'breadcrumbs' => [
                ['title' => 'Master Data', 'link' => '#'],
                ['title' => 'Tugas', 'link' => route('submission')]
            ]
        ]);
    }

    public function evaluate(Request $request, Submission $submission)
    {
        $validated = $request->validate([
            'grade' => 'required|integer|min:0|max:100',
            'admin_feedback' => 'nullable|string'
        ]);

        try {
            $submission->update([
                'grade' => $validated['grade'],
                'admin_feedback' => $validated['admin_feedback']
            ]);

            return back()->with('success', 'Penilaian berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Error saving submission evaluation: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan penilaian. Silakan coba lagi.');
        }
    }
}
