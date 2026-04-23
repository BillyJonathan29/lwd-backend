<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    private function userResource(User $user): array
    {
        return [
            'id'          => $user->id,
            'nim'         => $user->nim,
            'full_name'   => $user->full_name,
            'subdivision' => $user->subdivision,
            'role'        => $user->role,
            'profile_photo_url'    => $user->profile_photo_path
                ? asset('storage/' . $user->profile_photo_path)
                : null,
        ];
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data profil berhasil diambil.',
            'data'    => $this->userResource($request->user()),
        ]);
    }


    private function generateToken(User $user, ?string $fcmToken = null): string
    {
        // Revoke semua token lama
        $user->tokens()->delete();

        // Update FCM token jika dikirim
        if ($fcmToken) {
            $user->fcm_token = $fcmToken;
            $user->save();
        }

        return $user->createToken('auth_token')->plainTextToken;
    }


    public function login(Request $request)
    {
        try {
            $request->validate([
                'nim'       => 'required|string|exists:users,nim',
                'password'  => 'required|string',
                'fcm_token' => 'nullable|string',
            ], [
                'nim.required'  => 'NIM wajib diisi.',
                'nim.exists'    => 'NIM tidak terdaftar.',
                'password.required' => 'Password wajib diisi.',
            ]);

            $user = User::where('nim', $request->nim)->first();

            // Cek password
            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'NIM atau password yang Anda masukkan salah.',
                    'data'    => null,
                ], 401);
            }

            // Blokir role Admin
            if ($user->role === 'Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Admin tidak diizinkan mengakses aplikasi ini.',
                    'data'    => null,
                ], 403);
            }

            $token = $this->generateToken($user, $request->fcm_token);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'data'    => [
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                    'user'         => $this->userResource($user),
                ],
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
                'message' => 'Terjadi kesalahan pada server.',
                'data'    => null,
            ], 500);
        }
    }


    public function loginWithGoogle(Request $request)
    {
        try {
            $request->validate([
                'id_token'  => 'required|string',
                'fcm_token' => 'nullable|string',
            ], [
                'id_token.required' => 'Google ID Token wajib dikirim.',
            ]);


            $googleClient = new GoogleClient();
            $googleClient->setClientId(config('services.google.client_id'));
            $payload = $googleClient->verifyIdToken($request->id_token);

            if (! $payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google token tidak valid atau sudah kadaluarsa.',
                    'data'    => null,
                ], 401);
            }

            $googleId    = $payload['sub'];
            $googleEmail = $payload['email'];

            // Pastikan email dari domain institusi
            if (! str_ends_with($googleEmail, '@uniku.ac.id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya email institusi @uniku.ac.id yang diizinkan.',
                    'data'    => null,
                ], 403);
            }

            $nim = explode('@', $googleEmail)[0];

            $user = User::where('nim', $nim)->first();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun dengan NIM ' . $nim . ' tidak terdaftar di sistem.',
                    'data'    => null,
                ], 404);
            }

            if ($user->role === 'Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Admin tidak diizinkan mengakses aplikasi ini.',
                    'data'    => null,
                ], 403);
            }

            // ── Simpan google_id jika belum ada ───────────
            if (! $user->google_id) {
                $user->google_id    = $googleId;
                $user->google_email = $googleEmail;
                $user->save();
            }

            $token = $this->generateToken($user, $request->fcm_token);

            return response()->json([
                'success' => true,
                'message' => 'Login dengan Google berhasil.',
                'data'    => [
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                    'user'         => $this->userResource($user),
                ],
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

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil.',
                'data'    => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat logout.',
                'data'    => null,
            ], 500);
        }
    }


    public function updateFcm(Request $request)
    {
        try {
            $request->validate([
                'fcm_token' => 'required|string',
            ], [
                'fcm_token.required' => 'FCM token wajib diisi.',
            ]);

            $user = $request->user();
            $user->fcm_token = $request->fcm_token;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'FCM token berhasil diperbarui.',
                'data'    => [
                    'fcm_token' => $user->fcm_token,
                ],
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
                'message' => 'Terjadi kesalahan pada server.',
                'data'    => null,
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            $request->validate([
                'full_name'     => 'nullable|string|max:255',
                'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ], [
                'full_name.string'     => 'Nama harus berupa teks.',
                'full_name.max'        => 'Nama maksimal 255 karakter.',
                'profile_photo.image'  => 'File harus berupa gambar.',
                'profile_photo.mimes'  => 'Format foto harus jpg, jpeg, atau png.',
                'profile_photo.max'    => 'Ukuran foto maksimal 2MB.',
            ]);

            // 2. Update Nama (Hanya jika dikirim)
            if ($request->has('full_name')) {
                $user->full_name = $request->full_name;
            }

            // 3. Handle Upload Foto
            if ($request->hasFile('profile_photo')) {
                // Hapus foto lama dari server jika ada
                if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                $path = $request->file('profile_photo')->store('profiles', 'public');
                $user->profile_photo_path = $path;
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui.',
                'data'    => $this->userResource($user),
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
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data'    => null,
            ], 500);
        }
    }
}
