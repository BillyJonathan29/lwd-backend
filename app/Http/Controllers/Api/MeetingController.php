<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Models\Meeting;

class MeetingController extends Controller
{

    private function getAllowedCategories(string $subdivision): array
    {
        return match ($subdivision) {
            'software' => ['web', 'tools'],
            'hardware' => ['hardware', 'tools'],
            default    => [],
        };
    }

    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $allowedCategories = $this->getAllowedCategories($user->subdivision);


            if (empty($allowedCategories)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subdivision User tidak dikenali',
                    'data' => null,
                ], 400);
            }

            $meetings = Meeting::whereIn('category', $allowedCategories)
                ->orderBy('date', 'asc')
                ->get([
                    'id',
                    'topic_title',
                    'category',
                    'date',
                    'assignment_deadline'
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Daftar jadwal berhasil diambil.',
                'data'    => $meetings,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.' . $e->getMessage(),
                'data'    => null,
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $user              = $request->user();
            $allowedCategories = $this->getAllowedCategories($user->subdivision);

            $meeting = Meeting::find($id);

            // Jadwal tidak ditemukan
            if (! $meeting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal tidak ditemukan.',
                    'data'    => null,
                ], 404);
            }

            // Cek apakah kategori meeting sesuai divisi user
            if (! in_array($meeting->category, $allowedCategories)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke jadwal ini.',
                    'data'    => null,
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail jadwal berhasil diambil.',
                'data'    => $meeting,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'data'    => null,
            ], 500);
        }
    }
}
