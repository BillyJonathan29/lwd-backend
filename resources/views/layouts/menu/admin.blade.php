@php
$masterRoutes = [ 'user*'];

$isMasterActive = request()->routeIs($masterRoutes);
@endphp
<p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 sidebar-text whitespace-nowrap">
    Main Menu
</p>

<a href="{{ route('dashboard') }}"
    class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all group overflow-hidden
    {{ request()->routeIs('dashboard') ? 'bg-primary text-white shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <i data-lucide="layout-grid"
        class="w-5 h-5 transition-colors {{ request()->routeIs('dashboard') ? 'text-white' : 'group-hover:text-primary' }}">
    </i>
    <span class="font-medium whitespace-nowrap sidebar-text">Dashboard</span>
</a>

<a href="{{ route('user') }}"
    class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all group overflow-hidden
    {{ request()->routeIs('user*') ? 'bg-primary text-white shadow-lg shadow-blue-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
    <i data-lucide="users"
        class="w-5 h-5 transition-colors {{ request()->routeIs('user*') ? 'text-white' : 'group-hover:text-primary' }}">
    </i>
    <span class="font-medium whitespace-nowrap sidebar-text">Data Users</span>
</a>

<p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-4 sidebar-text whitespace-nowrap">
    Management
</p>

<div class="relative" id="dropdown-container">
    <button onclick="toggleDropdown()"
        class="w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all group overflow-hidden cursor-pointer
        {{ $isMasterActive ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

        <div class="flex items-center gap-3">
            <i data-lucide="database"
                class="w-5 h-5 {{ $isMasterActive ? 'text-primary' : 'group-hover:text-primary' }} transition-colors"></i>
            <span class="font-medium whitespace-nowrap sidebar-text">Master Data</span>
        </div>

        <i data-lucide="chevron-down" id="dropdown-arrow"
            class="w-4 h-4 transition-transform duration-300 sidebar-text {{ $isMasterActive ? 'rotate-180' : '' }}"></i>
    </button>

    <div id="dropdown-menu"
        class="{{ $isMasterActive ? 'flex' : 'hidden' }} flex-col gap-1 mt-1 ml-4 border-l border-slate-700 pl-4 transition-all duration-300">

     


    </div>
</div>


<form method="POST" action="{{ route('logout') }}" class="px-3" onsubmit="return openLogoutModal(event, this);">
    @csrf
    <button type="submit"
        class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-slate-300 hover:bg-red-500/10 hover:text-red-500 transition-all group overflow-hidden cursor-pointer">
        <i data-lucide="log-out" class="w-5 h-5 group-hover:text-red-500 transition-colors"></i>
        <span class="font-medium whitespace-nowrap sidebar-text">Logout</span>
    </button>
</form>