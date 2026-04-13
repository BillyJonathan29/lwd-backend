<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();

        $recentUsers = User::latest()->take(5)->get();
        $growth = 12;

        return view('dashboard.index', [
            'title' => 'Dashboard',
            'totalUsers' => $totalUsers,
            'users' => $recentUsers,
            'growth' => $growth,
            'breadcrumbs' => [
                [
                    'title' => 'Dashboard',
                    'link' => route('dashboard')
                ]
            ]
        ]);
    }
}
