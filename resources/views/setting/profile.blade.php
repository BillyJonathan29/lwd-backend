@extends('layouts.template')

@section('content')
@php $me = Auth::user(); @endphp

{{-- Success / Error Alerts --}}
@if(session('success_info'))
<div class="mb-5 flex items-center gap-3 px-5 py-3.5 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl text-green-700 dark:text-green-400 text-sm font-medium">
    <i data-lucide="check-circle-2" class="w-5 h-5 shrink-0"></i>
    {{ session('success_info') }}
</div>
@endif
@if(session('success_password'))
<div class="mb-5 flex items-center gap-3 px-5 py-3.5 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 rounded-xl text-green-700 dark:text-green-400 text-sm font-medium">
    <i data-lucide="shield-check" class="w-5 h-5 shrink-0"></i>
    {{ session('success_password') }}
</div>
@endif
@if($errors->any())
<div class="mb-5 flex items-start gap-3 px-5 py-3.5 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-xl text-red-700 dark:text-red-400 text-sm font-medium">
    <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ===== LEFT COLUMN: Identity Card ===== --}}
    <div class="xl:col-span-1 flex flex-col gap-6">

        {{-- Identity Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm overflow-hidden">
            {{-- Cover Banner --}}
            <div class="h-24 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 relative">
                <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/diagmonds.png')]"></div>
            </div>

            {{-- Avatar + Name --}}
            <div class="px-6 pb-6 -mt-10 relative">
                <div class="flex items-end justify-between mb-4">

                    {{-- Hidden upload form --}}
                    <form id="photoForm" action="{{ route('profile.update_photo') }}" method="POST" enctype="multipart/form-data" class="hidden">
                        @csrf
                        <input type="file" id="photoInput" name="photo" accept="image/jpeg,image/png,image/jpg,image/webp">
                    </form>

                    {{-- Clickable circle avatar --}}
                    <div class="relative group cursor-pointer" onclick="document.getElementById('photoInput').click()" title="Klik untuk ganti foto">
                        <div class="w-20 h-20 rounded-full ring-4 ring-white dark:ring-slate-900 shadow-lg overflow-hidden bg-indigo-100 dark:bg-indigo-900/30">
                            <img id="avatarPreview"
                                 src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name) . '&background=6366f1&color=fff&size=128&bold=true' }}"
                                 alt="{{ $user->full_name }}" class="w-full h-full object-cover">
                        </div>
                        {{-- Hover overlay --}}
                        <div class="absolute inset-0 rounded-full bg-black/50 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <i data-lucide="camera" class="w-5 h-5 text-white"></i>
                            <span class="text-white text-[9px] font-bold mt-0.5">Ganti Foto</span>
                        </div>
                        {{-- Edit badge --}}
                        <div class="absolute -bottom-0.5 -right-0.5 w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center ring-2 ring-white dark:ring-slate-900 shadow pointer-events-none">
                            <i data-lucide="pencil" class="w-3 h-3 text-white"></i>
                        </div>
                    </div>
                    @if($user->role === 'Admin')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30">
                            <i data-lucide="shield" class="w-3 h-3 mr-1"></i> Admin
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-700 dark:bg-sky-500/20 dark:text-sky-300 border border-sky-200 dark:border-sky-500/30">
                            <i data-lucide="user" class="w-3 h-3 mr-1"></i> Member
                        </span>
                    @endif
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user->full_name }}</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">NIM: <span class="font-mono font-semibold">{{ $user->nim }}</span></p>

                <div class="mt-4 space-y-2.5 text-sm text-gray-600 dark:text-slate-400">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center">
                            <i data-lucide="cpu" class="w-3.5 h-3.5 text-purple-500"></i>
                        </div>
                        <span class="capitalize">Divisi: <strong class="text-gray-800 dark:text-white">{{ ucfirst($user->subdivision) }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-amber-500"></i>
                        </div>
                        <span>Bergabung: <strong class="text-gray-800 dark:text-white">{{ $user->created_at->translatedFormat('d F Y') }}</strong></span>
                    </div>
                    @if($warningLetters->count() > 0)
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-red-50 dark:bg-red-500/10 flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-red-500"></i>
                        </div>
                        <span>Riwayat SP: <strong class="text-red-600 dark:text-red-400">{{ $warningLetters->count() }} Surat</strong></span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Attendance Stats Card --}}
        @if($me->role !== 'Admin')
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i data-lucide="bar-chart-2" class="w-4 h-4 text-indigo-500"></i> Rekapitulasi Presensi
            </h3>
            @php
                $attendance_pct = $totalMeetings > 0 ? round(($hadir / $totalMeetings) * 100) : 0;
            @endphp
            <div class="relative flex items-center justify-center my-3">
                <svg class="w-28 h-28 -rotate-90" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="currentColor" stroke-width="2.5" class="text-gray-100 dark:text-slate-800"/>
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-dasharray="{{ $attendance_pct }}, 100"
                        class="text-indigo-500 transition-all duration-1000" stroke-linecap="round"/>
                </svg>
                <div class="absolute text-center">
                    <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $attendance_pct }}%</p>
                    <p class="text-[10px] text-gray-400 dark:text-slate-500 font-medium">Hadir</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-3">
                <div class="text-center p-2.5 rounded-xl bg-green-50 dark:bg-green-500/10">
                    <p class="text-lg font-black text-green-600 dark:text-green-400">{{ $hadir }}</p>
                    <p class="text-xs text-green-700/70 dark:text-green-400/70 font-semibold">Hadir</p>
                </div>
                <div class="text-center p-2.5 rounded-xl bg-purple-50 dark:bg-purple-500/10">
                    <p class="text-lg font-black text-purple-600 dark:text-purple-400">{{ $izin }}</p>
                    <p class="text-xs text-purple-700/70 dark:text-purple-400/70 font-semibold">Izin</p>
                </div>
                <div class="text-center p-2.5 rounded-xl bg-blue-50 dark:bg-blue-500/10">
                    <p class="text-lg font-black text-blue-600 dark:text-blue-400">{{ $sakit }}</p>
                    <p class="text-xs text-blue-700/70 dark:text-blue-400/70 font-semibold">Sakit</p>
                </div>
                <div class="text-center p-2.5 rounded-xl bg-red-50 dark:bg-red-500/10">
                    <p class="text-lg font-black text-red-600 dark:text-red-400">{{ $alpa }}</p>
                    <p class="text-xs text-red-700/70 dark:text-red-400/70 font-semibold">Alpa</p>
                </div>
            </div>
        </div>

        {{-- Tugas Stats --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i data-lucide="clipboard-check" class="w-4 h-4 text-indigo-500"></i> Ringkasan Tugas
            </h3>
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-500 dark:text-slate-400">Tugas Dikumpulkan</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $totalSubmitted }} <span class="font-normal text-gray-400">/{{ $totalMeetings }}</span></span>
            </div>
            <div class="w-full h-2 bg-gray-100 dark:bg-slate-800 rounded-full overflow-hidden mb-4">
                @php $submit_pct = $totalMeetings > 0 ? ($totalSubmitted / $totalMeetings) * 100 : 0 @endphp
                <div class="h-2 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all duration-700" style="width: {{ $submit_pct }}%"></div>
            </div>
            <div class="flex justify-between text-center">
                <div>
                    <p class="text-2xl font-extrabold {{ $avgGrade !== null && $avgGrade >= 70 ? 'text-green-600 dark:text-green-400' : 'text-amber-500 dark:text-amber-400' }}">
                        {{ $avgGrade ?? '-' }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-slate-500 font-medium">Rata-rata Nilai</p>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400">{{ $sudahDinilai }}</p>
                    <p class="text-xs text-gray-400 dark:text-slate-500 font-medium">Sudah Dinilai</p>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-gray-400 dark:text-slate-500">{{ $totalSubmitted - $sudahDinilai }}</p>
                    <p class="text-xs text-gray-400 dark:text-slate-500 font-medium">Menunggu</p>
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- ===== RIGHT COLUMN: Forms & Activity ===== --}}
    <div class="xl:col-span-2 flex flex-col gap-6">

        {{-- ---- Update Info Form ---- --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
                    <i data-lucide="user-cog" class="w-4 h-4 text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">Informasi Profil</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400">Perbarui nama, NIM, dan divisi Anda.</p>
                </div>
            </div>
            <form action="{{ route('profile.update_info') }}" method="POST" class="p-6">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">NIM</label>
                        <input type="text" name="nim" value="{{ old('nim', $user->nim) }}" required
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Divisi</label>
                        <select name="subdivision" required
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-sm appearance-none cursor-pointer">
                            <option value="software" {{ $user->subdivision === 'software' ? 'selected' : '' }}>Software</option>
                            <option value="hardware" {{ $user->subdivision === 'hardware' ? 'selected' : '' }}>Hardware</option>
                        </select>
                    </div>
                </div>
                <div class="mt-5 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow-md shadow-indigo-500/25 transition-all inline-flex items-center gap-2 cursor-pointer">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- ---- Change Password Form ---- --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center">
                    <i data-lucide="lock-keyhole" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">Ubah Password</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400">Gunakan minimal 8 karakter untuk keamanan akun.</p>
                </div>
            </div>
            <form action="{{ route('profile.update_password') }}" method="POST" class="p-6">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Password Saat Ini</label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password" required
                                class="w-full px-4 py-2.5 pr-10 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all text-sm @error('current_password') border-red-400 @enderror">
                            <button type="button" onclick="togglePwd('current_password', 'eye1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer"><i data-lucide="eye" id="eye1" class="w-4 h-4"></i></button>
                        </div>
                        @error('current_password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="w-full px-4 py-2.5 pr-10 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all text-sm">
                            <button type="button" onclick="togglePwd('password', 'eye2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer"><i data-lucide="eye" id="eye2" class="w-4 h-4"></i></button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Konfirmasi Password</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full px-4 py-2.5 pr-10 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all text-sm">
                            <button type="button" onclick="togglePwd('password_confirmation', 'eye3')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer"><i data-lucide="eye" id="eye3" class="w-4 h-4"></i></button>
                        </div>
                    </div>
                </div>
                <div class="mt-5 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-sm shadow-md shadow-amber-500/25 transition-all inline-flex items-center gap-2 cursor-pointer">
                        <i data-lucide="shield-check" class="w-4 h-4"></i> Perbarui Password
                    </button>
                </div>
            </form>
        </div>

        {{-- ---- Recent Submissions ---- --}}
        @if($me->role !== 'Admin' && $submissions->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
                    <i data-lucide="clipboard-list" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">Riwayat Tugas Terakhir</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400">5 tugas terbaru yang telah dikumpulkan.</p>
                </div>
            </div>
            <ul class="divide-y divide-gray-100 dark:divide-slate-800">
                @foreach($submissions as $sub)
                <li class="px-6 py-4 flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">{{ $sub->meeting->topic_title ?? 'Tugas' }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                            Dikumpulkan: {{ \Carbon\Carbon::parse($sub->submitted_at)->translatedFormat('d M Y, H:i') }}
                        </p>
                    </div>
                    <div class="shrink-0 text-right">
                        @if($sub->grade !== null)
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full border-2 text-sm font-extrabold {{ $sub->grade >= 70 ? 'border-green-400 text-green-600 bg-green-50 dark:text-green-400 dark:bg-green-500/10' : 'border-red-400 text-red-600 bg-red-50 dark:text-red-400 dark:bg-red-500/10' }}">
                                {{ $sub->grade }}
                            </span>
                        @else
                            <span class="text-xs font-semibold px-2.5 py-1 bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400 rounded-lg border border-yellow-200 dark:border-yellow-500/20">Menunggu</span>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- ---- Warning Letters History ---- --}}
        @if($warningLetters->count() > 0)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-red-50 dark:bg-red-500/10 flex items-center justify-center">
                    <i data-lucide="alert-octagon" class="w-4 h-4 text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">Riwayat Surat Peringatan</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400">Tindakan disiplin yang pernah diterima.</p>
                </div>
            </div>
            <ul class="divide-y divide-gray-100 dark:divide-slate-800">
                @foreach($warningLetters as $sp)
                <li class="px-6 py-4 flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm text-gray-600 dark:text-slate-300">{{ $sp->reason }}</p>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">
                            Diterbitkan: {{ \Carbon\Carbon::parse($sp->issued_at)->translatedFormat('d F Y, H:i') }}
                            &bull; Oleh: <span class="font-semibold">{{ $sp->admin->full_name ?? 'Admin' }}</span>
                        </p>
                    </div>
                    <span class="shrink-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                        {{ $sp->warning_level === 'SP 1' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-500/20' : '' }}
                        {{ $sp->warning_level === 'SP 2' ? 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400 border border-orange-200 dark:border-orange-500/20' : '' }}
                        {{ $sp->warning_level === 'SP 3' ? 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400 border border-red-200 dark:border-red-500/20' : '' }}">
                        {{ $sp->warning_level }}
                    </span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

    </div>{{-- /right column --}}

</div>
@endsection

@section('scripts')
<script>
    // Toggle password visibility
    function togglePwd(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }

    // Avatar preview + auto-submit on file pick
    document.getElementById('photoInput').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        // Live preview before submit
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);

        // Submit form automatically
        document.getElementById('photoForm').submit();
    });
</script>
@endsection
