<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

class SubmissionController extends Controller
{
    public function store(Request $request)
    {
        try {
            // 1. Validasi Input
            $request->validate([
                'meeting_id'   => 'required|exists:meetings,id',
                'file_or_link' => 'required|string',
                'member_notes' => 'nullable|string',
            ], [
                'meeting_id.required'   => 'Meeting ID wajib diisi.',
                'meeting_id.exists'     => 'Jadwal pertemuan tidak ditemukan.',
                'file_or_link.required' => 'Link atau file tugas wajib dilampirkan.',
            ]);

            $userId = $request->user()->id;

    
            $submission = Submission::updateOrCreate(
                [
                    'meeting_id' => $request->meeting_id,
                    'user_id'    => $userId,
                ],
                [
                    'file_or_link' => $request->file_or_link,
                    'member_notes' => $request->member_notes,
                    'submitted_at' => now(),
                ]
            );

            // Cek apakah ini data baru atau update data lama untuk pesan dinamis
            $isNew = $submission->wasRecentlyCreated;

            return response()->json([
                'success' => true,
                'message' => $isNew ? 'Tugas berhasil dikumpulkan.' : 'Tugas berhasil diperbarui.',
                'data'    => $submission,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data'    => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage(),
                'data'    => null,
            ], 500);
        }
    }
}
