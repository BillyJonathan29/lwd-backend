@extends('layouts.template')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Halo, {{ explode(' ', Auth::user()->full_name)[0] }}! 👋</h1>
    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Berikut adalah ringkasan performa sistem Anda hari ini.</p>
</div>

<!-- 1. KARTU STATISTIK UTAMA -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Anggota -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-blue-50 dark:bg-blue-500/10 text-primary dark:text-blue-400 rounded-xl">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <span class="flex items-center text-xs font-medium text-green-600 bg-green-50 dark:bg-green-500/10 dark:text-green-400 px-2 py-1 rounded-lg">
                Aktif
            </span>
        </div>
        <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Total Anggota LWD</p>
        <div class="flex items-baseline gap-2 mt-1">
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalMembers }}</h3>
            <span class="text-xs text-gray-500 dark:text-slate-500">Orang</span>
        </div>
        <div class="mt-3 flex items-center justify-between text-xs text-gray-500 dark:text-slate-400">
            <span class="flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-blue-500"></div> Web: {{ $softwareMembers }}</span>
            <span class="flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-purple-500"></div> HW: {{ $hardwareMembers }}</span>
        </div>
    </div>

    <!-- Kehadiran -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400 rounded-xl">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
            <span class="flex items-center text-xs font-medium text-green-600 bg-green-50 dark:bg-green-500/10 dark:text-green-400 px-2 py-1 rounded-lg">
                <i data-lucide="trending-up" class="w-3 h-3 mr-1"></i> Stabil
            </span>
        </div>
        <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Tingkat Kehadiran LWD</p>
        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $attendanceRate }}%</h3>
        <div class="w-full bg-gray-100 dark:bg-slate-700 rounded-full h-1.5 mt-4">
            <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $attendanceRate }}%"></div>
        </div>
    </div>

    <!-- Tugas -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 rounded-xl">
                <i data-lucide="file-text" class="w-6 h-6"></i>
            </div>
            <span class="text-xs font-medium text-orange-600 bg-orange-50 dark:bg-orange-500/10 dark:text-orange-400 px-2 py-1 rounded-lg">
                Need Review
            </span>
        </div>
        <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Tugas Belum Dinilai</p>
        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $pendingTasks }}</h3>
        <p class="text-xs text-gray-500 dark:text-slate-400 mt-2">Dari tugas minggu ini</p>
    </div>

    <!-- SP Aktif -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="p-3 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-xl">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            <span class="text-xs font-medium text-red-600 bg-red-50 dark:bg-red-500/10 dark:text-red-400 px-2 py-1 rounded-lg">
                Tindakan
            </span>
        </div>
        <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Anggota Bermasalah (SP)</p>
        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $activeSP }}</h3>
        <p class="text-xs text-gray-500 dark:text-slate-400 mt-2">Terdata bulan ini</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- LEFT COLUMN -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- 2. JADWAL LWD TERDEKAT -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-gray-800 dark:text-white text-lg">Jadwal LWD Terdekat</h3>
                <a href="{{ Route::has('pertemuan') ? route('pertemuan') : '#' }}" class="text-sm font-medium bg-blue-500 hover:bg-blue-600 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 text-white dark:text-blue-400 px-3 py-1 rounded-lg transition-colors">Lihat Semua</a>
            </div>
            
            <div class="space-y-4">
                @forelse($upcomingMeetings as $meeting)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/30 hover:bg-gray-50 dark:hover:bg-slate-800/80 transition-colors gap-4">
                    <div class="flex flex-1 items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                            <i data-lucide="calendar" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-semibold text-gray-800 dark:text-white">{{ $meeting->topic_title }}</h4>
                            <div class="flex flex-wrap items-center gap-3 mt-1.5 text-sm text-gray-500 dark:text-slate-400">
                                <span class="flex items-center gap-1 font-medium text-gray-700 dark:text-slate-300">
                                    <i data-lucide="clock" class="w-4 h-4 text-indigo-500"></i> {{ \Carbon\Carbon::parse($meeting->date)->translatedFormat('d F Y, H:i') }}
                                </span>
                                <span class="flex items-center gap-1 bg-gray-200 dark:bg-slate-700 px-2 py-0.5 rounded text-xs capitalize">
                                    <i data-lucide="folder" class="w-3 h-3"></i> {{ $meeting->category }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <button class="shrink-0 w-full sm:w-auto px-4 py-2 bg-primary text-white bg-blue-500 text-sm font-medium rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all flex justify-center items-center gap-2">
                        <i data-lucide="check-square" class="w-4 h-4"></i> Buka Presensi
                    </button>
                </div>
                @empty
                <div class="text-center py-6 text-gray-500 dark:text-slate-400">
                    <i data-lucide="calendar-x" class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-slate-600"></i>
                    <p>Tidak ada jadwal pertemuan terdekat.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- 3. LOG AKTIVITAS TERBARU -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm p-6 relative overflow-hidden">
            <h3 class="font-bold text-gray-800 dark:text-white text-lg mb-6">Log Aktivitas Terbaru</h3>
            
            <div class="space-y-6 relative before:absolute before:inset-0 before:ml-5 md:before:ml-[2.25rem] before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-200 dark:before:via-slate-700 before:to-transparent">
                
                @forelse($recentActivities as $activity)
                <div class="relative flex items-start gap-4 md:gap-6 group">
                    <div class="flex items-center justify-center w-10 h-10 md:w-12 md:h-12 rounded-full border-4 border-white dark:border-slate-900 bg-{{ $activity['color'] }}-100 dark:bg-{{ $activity['color'] }}-500/20 text-{{ $activity['color'] }}-600 dark:text-{{ $activity['color'] }}-400 shrink-0 shadow-sm z-10 transition-transform group-hover:scale-110">
                        <i data-lucide="{{ $activity['icon'] }}" class="w-5 h-5"></i>
                    </div>
                    <div class="flex-1 p-4 rounded-xl border border-gray-100 dark:border-slate-800 bg-gray-50 dark:bg-slate-800/40 hover:bg-white dark:hover:bg-slate-800 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-bold text-sm text-gray-800 dark:text-white">{{ $activity['title'] }}</h4>
                            <span class="text-xs font-medium text-gray-400 dark:text-slate-500">{{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-slate-300"><span class="font-semibold text-gray-900 dark:text-white">{{ $activity['user'] }}</span> {{ $activity['description'] }}.</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-gray-500 dark:text-slate-400">
                    <p>Belum ada aktivitas terbaru.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div class="space-y-8">
        <!-- 4. ANGGOTA DENGAN PERHATIAN KHUSUS -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="p-6 border-b border-gray-100 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 dark:text-white text-lg">Needs Attention</h3>
                    <span class="px-2.5 py-1 bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400 rounded-lg text-xs font-bold border border-red-200 dark:border-red-500/30">
                        3 Kendala
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Anggota yang membutuhkan tindakan.</p>
            </div>

            <div class="p-6 space-y-4 flex-1">
                @forelse($needsAttention as $item)
                <div class="group flex flex-col gap-3 p-4 rounded-xl border {{ $item['type'] === 'danger' ? 'border-red-100 dark:border-red-500/20 bg-gradient-to-br from-red-50/50 to-white dark:from-red-500/5 dark:to-slate-900' : 'border-orange-100 dark:border-orange-500/20 bg-gradient-to-br from-orange-50/50 to-white dark:from-orange-500/5 dark:to-slate-900' }} hover:shadow-md transition-all">
                    <div class="flex items-center gap-3">
                        <img class="w-10 h-10 rounded-full object-cover border-2 border-white dark:border-slate-800 shadow-sm" src="https://ui-avatars.com/api/?name={{ urlencode($item['user']->full_name) }}&bg={{ $item['type'] === 'danger' ? 'ef4444' : 'f97316' }}&color=fff" alt="{{ $item['user']->full_name }}">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-white">{{ $item['user']->full_name }}</h4>
                            <p class="text-xs {{ $item['type'] === 'danger' ? 'text-red-600 dark:text-red-400' : 'text-orange-600 dark:text-orange-400' }} font-medium mt-0.5 flex items-center gap-1">
                                <i data-lucide="{{ $item['type'] === 'danger' ? 'alert-circle' : 'file-warning' }}" class="w-3 h-3"></i> {{ $item['reason'] }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ $item['type'] === 'danger' ? route('warning_letters') : route('submission') }}" class="w-full px-3 py-2 text-center text-xs font-semibold {{ $item['type'] === 'danger' ? 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 hover:bg-red-600 hover:text-white' : 'bg-orange-100 dark:bg-orange-500/20 text-orange-600 dark:text-orange-400 hover:bg-orange-600 hover:text-white' }} rounded-lg transition-colors border {{ $item['type'] === 'danger' ? 'border-red-200 dark:border-red-500/30' : 'border-orange-200 dark:border-orange-500/30' }} cursor-pointer">
                        {{ $item['type'] === 'danger' ? 'Terbitkan SP' : 'Beri Teguran' }}
                    </a>
                </div>
                @empty
                <div class="text-center py-6 text-gray-500 dark:text-slate-400">
                    <i data-lucide="smile" class="w-10 h-10 mx-auto mb-2 text-green-300"></i>
                    <p>Semua anggota terpantau aman.</p>
                </div>
                @endforelse
            </div>
            
            <div class="p-4 border-t border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/30">
                <a href="#" class="block w-full text-center py-2 text-sm font-medium text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-200/50 dark:hover:bg-slate-700/50 rounded-xl transition-colors">
                    Lihat Semua
                </a>
            </div>
        </div>
    </div>
</div>
@endsection