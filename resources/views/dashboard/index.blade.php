@extends('layouts.template')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋</h1>
        <p class="text-gray-500">Berikut adalah ringkasan performa sistem Anda hari ini.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-50 text-primary rounded-xl">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <span class="flex items-center text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-lg">
                    <i data-lucide="trending-up" class="w-3 h-3 mr-1"></i> +12%
                </span>
            </div>
            <p class="text-sm font-medium text-gray-500">Total Users</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ \App\Models\User::count() }}</h3>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                    <i data-lucide="layout-template" class="w-6 h-6"></i>
                </div>
                <span class="text-xs font-medium text-gray-400">Target: 20</span>
            </div>
            <p class="text-sm font-medium text-gray-500">Active Projects</p>
            <h3 class="text-2xl font-bold text-gray-800">12</h3>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-orange-50 text-orange-600 rounded-xl">
                    <i data-lucide="credit-card" class="w-6 h-6"></i>
                </div>
                <span class="flex items-center text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded-lg">
                    <i data-lucide="trending-down" class="w-3 h-3 mr-1"></i> -2%
                </span>
            </div>
            <p class="text-sm font-medium text-gray-500">Monthly Revenue</p>
            <h3 class="text-2xl font-bold text-gray-800">Rp 4.2M</h3>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                    <i data-lucide="message-square" class="w-6 h-6"></i>
                </div>
                <div class="flex -space-x-2">
                    <img class="w-7 h-7 rounded-full border-2 border-white"
                        src="https://ui-avatars.com/api/?name=A&bg=2563eb&color=fff" alt="">
                    <img class="w-7 h-7 rounded-full border-2 border-white"
                        src="https://ui-avatars.com/api/?name=B&bg=1e293b&color=fff" alt="">
                </div>
            </div>
            <p class="text-sm font-medium text-gray-500">New Tickets</p>
            <h3 class="text-2xl font-bold text-gray-800">5</h3>
        </div>
    </div>

    {{-- <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-800 text-lg">Platform Growth</h3>
                <select class="text-sm border-none focus:ring-0 text-gray-500 bg-transparent cursor-pointer">
                    <option>Last 7 Days</option>
                    <option>Last 30 Days</option>
                </select>
            </div>

            <div
                class="h-64 w-full bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center">
                <div class="text-center">
                    <i data-lucide="bar-chart-3" class="w-10 h-10 text-gray-300 mx-auto mb-2"></i>
                    <p class="text-gray-400 text-sm">Integrasikan Chart.js</p>
                </div>
            </div>
        </div>


        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <h3 class="font-bold text-gray-800 text-lg mb-6">Recent Members</h3>
            <div class="space-y-6">
                @foreach ($users->take(5) as $u)
                    <div class="flex items-center gap-4">
                        <img class="w-10 h-10 rounded-full object-cover"
                            src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background=f1f5f9&color=64748b"
                            alt="">
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-gray-800">{{ $u->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $u->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('user') }}"
                class="block w-full text-center mt-8 py-2 text-sm font-semibold text-primary hover:bg-blue-50 rounded-xl transition-colors">
                View All Members
            </a>
        </div>
    </div> --}}
@endsection