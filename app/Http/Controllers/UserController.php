<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');

            $users = User::when($search, function ($query, $search) {
                return $query->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            })
                ->latest()
                ->paginate(10)
                ->withQueryString();

            return view('user.index', [
                'title' => 'Data User',
                'users' => $users,
                'breadcrumbs' => [
                    [
                        'title' => 'User Management',
                        'link' => route('user')
                    ],
                    [
                        'title' => 'User',
                        'link' => route('user')
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data user.');
        }
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim'         => 'required|string|max:50|unique:users,nim',
            'full_name'   => 'required|string|max:255',
            'password'    => 'required|min:8',
            'subdivision' => 'nullable|string|max:255',
            'role'        => 'required|in:admin,user',
            'fcm_token'   => 'nullable|string',
        ]);

        try {
            User::create([
                'nim'         => $validated['nim'],
                'full_name'   => $validated['full_name'],
                'password'    => Hash::make($validated['password']),
                'subdivision' => $validated['subdivision'] ?? null,
                'role'        => $validated['role'],
                'fcm_token'   => $validated['fcm_token'] ?? null,
            ]);

            return back()->with('success', 'Member baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan member baru. Silakan coba lagi.');
        }
    }


    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nim'         => 'required|string|max:50|unique:users,nim,' . $user->id,
            'full_name'   => 'required|string|max:255',
            'password'    => 'nullable|min:8',
            'subdivision' => 'nullable|string|max:255',
            'role'        => 'required|in:admin,user',
            'fcm_token'   => 'nullable|string',
        ]);

        try {
            $updateData = [
                'nim'         => $validated['nim'],
                'full_name'   => $validated['full_name'],
                'subdivision' => $validated['subdivision'] ?? null,
                'role'        => $validated['role'],
                'fcm_token'   => $validated['fcm_token'] ?? null,
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            return back()->with('success', 'Data member berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data member. Silakan coba lagi.');
        }
    }


    public function destroy(User $user)
    {
        try {
            $user->delete();
            return back()->with('success', 'Member berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus member. Silakan coba lagi.');
        }
    }

    public function show(User $user)
    {
        try {
            // $user->load('courses'); // Dihapus sementara karena relasi causes undefined di model User.

            return view('user.show', [
                'title' => 'User Details',
                'user' => $user,
                'breadcrumbs' => [
                    [
                        'title' => 'User Management',
                        'link' => route('user')
                    ],
                    [
                        'title' => 'User',
                        'link' => route('user')
                    ],
                    [
                        'title' => 'Detail',
                        'link' => route('user.show', $user->id)
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing user details: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat detail member.');
        }
    }
}
