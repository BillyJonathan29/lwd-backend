<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile page with activity summary.
     */
    public function index()
    {
        $user = Auth::user()->load(['attendances.meeting', 'submissions.meeting']);

        // --- Attendance Summary ---
        $attendances = $user->attendances;
        $totalMeetings = \App\Models\Meeting::count();
        $hadir  = $attendances->where('status', 'present')->count();
        $izin   = $attendances->where('status', 'excused')->count();
        $sakit  = $attendances->where('status', 'sick')->count();
        $alpa   = $totalMeetings - $hadir - $izin - $sakit;
        $alpa   = max($alpa, 0);

        // --- Submission Summary ---
        $submissions    = $user->submissions;
        $totalSubmitted = $submissions->count();
        $sudahDinilai   = $submissions->whereNotNull('grade')->count();
        $avgGrade       = $sudahDinilai > 0
            ? round($submissions->whereNotNull('grade')->avg('grade'), 1)
            : null;

        // --- Warning Letters ---
        $warningLetters = \App\Models\WarningLetter::where('user_id', $user->id)
            ->orderBy('issued_at', 'desc')
            ->get();

        return view('setting.profile', [
            'title'         => 'Profil Saya',
            'user'          => $user,
            'totalMeetings' => $totalMeetings,
            'hadir'         => $hadir,
            'izin'          => $izin,
            'sakit'         => $sakit,
            'alpa'          => $alpa,
            'totalSubmitted'=> $totalSubmitted,
            'sudahDinilai'  => $sudahDinilai,
            'avgGrade'      => $avgGrade,
            'submissions'   => $submissions->sortByDesc('submitted_at')->take(5),
            'warningLetters'=> $warningLetters,
            'breadcrumbs'   => [
                ['title' => 'Pengaturan', 'link' => '#'],
                ['title' => 'Profil Saya', 'link' => route('profile')],
            ],
        ]);
    }

    /**
     * Update profile info (full_name, nim, subdivision).
     */
    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'full_name'   => 'required|string|max:100',
            'nim'         => 'required|string|max:20|unique:users,nim,' . $user->id,
            'subdivision' => 'required|in:software,hardware',
        ]);

        $user->update($validated);

        return back()->with('success_info', 'Informasi profil berhasil diperbarui.');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.'])->withInput();
        }

        Auth::user()->update(['password' => $request->password]);

        return back()->with('success_password', 'Password berhasil diubah.');
    }

    /**
     * Upload / replace profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = Auth::user();

        // Delete old photo if exists
        if ($user->profile_photo_path) {
            \Storage::disk('public')->delete($user->profile_photo_path);
        }

        $path = $request->file('photo')->store('profile-photos', 'public');
        $user->update(['profile_photo_path' => $path]);

        return back()->with('success_info', 'Foto profil berhasil diperbarui.');
    }
}
