@php
    $masterRoutes = ['user*', 'pertemuan*', 'presensi*', 'submission*'];

    $isMasterActive = request()->routeIs($masterRoutes);
@endphp
<p
    class="px-3 text-xs font-semibold text-gray-500 dark:text-slate-500 uppercase tracking-wider mb-2 sidebar-text whitespace-nowrap">
    Main Menu
</p>

<a href="{{ route('dashboard') }}"
    class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all group overflow-hidden
    {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-white' }}">
    <i data-lucide="layout-grid"
        class="w-5 h-5 transition-colors {{ request()->routeIs('dashboard') ? 'text-white' : 'group-hover:text-indigo-600 dark:group-hover:text-indigo-400' }}">
    </i>
    <span class="font-medium whitespace-nowrap sidebar-text">Dashboard</span>
</a>

<p
    class="px-3 text-xs font-semibold text-gray-500 dark:text-slate-500 uppercase tracking-wider mb-2 mt-4 sidebar-text whitespace-nowrap">
    Management
</p>

<div class="relative" id="dropdown-container">
    <button onclick="toggleDropdown()"
        class="w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all group overflow-hidden cursor-pointer
        {{ $isMasterActive ? 'bg-gray-100 dark:bg-slate-800 text-indigo-600 dark:text-white' : 'text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-white' }}">

        <div class="flex items-center gap-3">
            <i data-lucide="database"
                class="w-5 h-5 {{ $isMasterActive ? 'text-indigo-600 dark:text-indigo-400' : 'group-hover:text-indigo-600 dark:group-hover:text-indigo-400' }} transition-colors"></i>
            <span class="font-medium whitespace-nowrap sidebar-text">Master Data</span>
        </div>

        <i data-lucide="chevron-down" id="dropdown-arrow"
            class="w-4 h-4 transition-transform duration-300 sidebar-text {{ $isMasterActive ? 'rotate-180' : '' }}"></i>
    </button>

    <div id="dropdown-menu"
        class="{{ $isMasterActive ? 'flex' : 'hidden' }} flex-col gap-1 mt-1 ml-4 border-l border-gray-200 dark:border-slate-700 pl-4 transition-all duration-300">

        <a href="{{ Route::has('pertemuan') ? route('pertemuan') : '#' }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all group overflow-hidden
            {{ request()->routeIs('pertemuan*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-slate-800' }}">
            <i data-lucide="calendar"
                class="w-4 h-4 {{ request()->routeIs('pertemuan*') ? 'text-indigo-600 dark:text-indigo-400' : 'group-hover:text-indigo-600 dark:group-hover:text-white' }}"></i>
            <span class="font-medium text-sm whitespace-nowrap sidebar-text">Data Pertemuan</span>
        </a>

        <a href="{{ Route::has('presensi') ? route('presensi') : '#' }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all group overflow-hidden
            {{ request()->routeIs('presensi*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-slate-800' }}">
            <i data-lucide="check-square"
                class="w-4 h-4 {{ request()->routeIs('presensi*') ? 'text-indigo-600 dark:text-indigo-400' : 'group-hover:text-indigo-600 dark:group-hover:text-white' }}"></i>
            <span class="font-medium text-sm whitespace-nowrap sidebar-text">Data Presensi</span>
        </a>

        <a href="{{ Route::has('submission') ? route('submission') : '#' }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all group overflow-hidden
            {{ request()->routeIs('submission*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-slate-800' }}">
            <i data-lucide="clipboard-list"
                class="w-4 h-4 {{ request()->routeIs('submission*') ? 'text-indigo-600 dark:text-indigo-400' : 'group-hover:text-indigo-600 dark:group-hover:text-white' }}"></i>
            <span class="font-medium text-sm whitespace-nowrap sidebar-text">Data Tugas</span>
        </a>

    </div>
</div>
<a href="{{ Route::has('user') ? route('user') : '#' }}"
    class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all group overflow-hidden
            {{ request()->routeIs('user*') ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-slate-800' }}">
    <i data-lucide="users"
        class="w-5 h-5 {{ request()->routeIs('user*') ? 'text-indigo-600 dark:text-indigo-400' : 'group-hover:text-indigo-600 dark:group-hover:text-white' }}"></i>
    <span class="font-medium text-sm whitespace-nowrap sidebar-text">Data User</span>
</a>

<p
    class="px-3 text-xs font-semibold text-gray-500 dark:text-slate-500 uppercase tracking-wider mb-2 mt-4 sidebar-text whitespace-nowrap">
    Sistem Tindak Lanjut
</p>

<a href="{{ Route::has('warning_letters') ? route('warning_letters') : '#' }}"
    class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all group overflow-hidden
    {{ request()->routeIs('warning_letters*') ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 font-bold' : 'text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-red-400' }}">
    <i data-lucide="alert-triangle"
        class="w-5 h-5 {{ request()->routeIs('warning_letters*') ? 'text-red-600 dark:text-red-400' : 'group-hover:text-red-600 dark:group-hover:text-red-500' }} transition-colors"></i>
    <span class="font-medium whitespace-nowrap sidebar-text">Peringatan Dini</span>
</a>


<p
    class="px-3 text-xs font-semibold text-gray-500 dark:text-slate-500 uppercase tracking-wider mb-2 mt-4 sidebar-text whitespace-nowrap">
    Pengaturan
</p>
<a href="{{ Route::has('profile') ? route('profile') : '#' }}"
    class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all group overflow-hidden
    {{ request()->routeIs('profile*') ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 font-bold' : 'text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-red-400' }}">
    <i data-lucide="circle-user"
        class="w-5 h-5 {{ request()->routeIs('profile*') ? 'text-red-600 dark:text-red-400' : 'group-hover:text-red-600 dark:group-hover:text-red-500' }} transition-colors"></i>
    <span class="font-medium whitespace-nowrap sidebar-text">Profile</span>
</a>

<form method="POST" action="{{ route('logout') }}" class="px-3 mt-auto pt-6"
    onsubmit="return openLogoutModal(event, this);">
    @csrf
    <button type="submit"
        class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 dark:text-slate-300 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10 dark:hover:text-red-500 transition-all group overflow-hidden cursor-pointer">
        <i data-lucide="log-out"
            class="w-5 h-5 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors"></i>
        <span class="font-medium whitespace-nowrap sidebar-text">Logout</span>
    </button>
</form>
