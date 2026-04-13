<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('user.index', [
            'title' => 'All User',
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
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role'     => 'required|in:admin,user',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        return back()->with('success', 'Member baru berhasil ditambahkan.');
    }


    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,user',
        ]);

        $user->update($validated);

        return back()->with('success', 'Data member berhasil diperbarui.');
    }


    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', 'Member berhasil dihapus.');
    }

    public function show(User $user)
    {
        $user->load('courses'); // Load the user's courses

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
    }
}
