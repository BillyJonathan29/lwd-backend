<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');

            $meetings = Meeting::when($search, function ($query, $search) {
                return $query->where('topic_title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            })
                ->latest('date')
                ->paginate(10)
                ->withQueryString();

            return view('meeting.index', [
                'title' => 'Data Pertemuan',
                'meetings' => $meetings,
                'breadcrumbs' => [
                    [
                        'title' => 'Master Data',
                        'link' => '#'
                    ],
                    [
                        'title' => 'Pertemuan',
                        'link' => route('pertemuan')
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching meetings: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data pertemuan.');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic_title'         => 'required|string|max:255',
            'description'         => 'nullable|string',
            'date'                => 'required|date',
            'category'            => 'required|string|max:50',
            'assignment_deadline' => 'nullable|date',
        ]);

        try {
            Meeting::create($validated);
            return back()->with('success', 'Pertemuan baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating meeting: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan pertemuan baru. Silakan coba lagi.');
        }
    }

    public function update(Request $request, Meeting $pertemuan)
    {
        $validated = $request->validate([
            'topic_title'         => 'required|string|max:255',
            'description'         => 'nullable|string',
            'date'                => 'required|date',
            'category'            => 'required|string|max:50',
            'assignment_deadline' => 'nullable|date',
        ]);

        try {
            $pertemuan->update($validated);
            return back()->with('success', 'Data pertemuan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating meeting: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data pertemuan. Silakan coba lagi.');
        }
    }

    public function destroy(Meeting $pertemuan)
    {
        try {
            $pertemuan->delete();
            return back()->with('success', 'Pertemuan berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting meeting: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus pertemuan. Silakan coba lagi.');
        }
    }
}
