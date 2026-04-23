@extends('layouts.template')

@section('content')
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
    <div class="p-5 border-b border-gray-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 transition-colors duration-300">
        <div class="flex flex-col gap-1">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ $title ?? 'Data Presensi Anggota' }}</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400">Pilih pertemuan untuk melihat/mengubah kehadiran anggota.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <button type="button" onclick="openExportModal('presensi')" class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border-2 border-indigo-100 dark:border-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors font-semibold text-sm shadow-sm group">
                <i data-lucide="file-output" class="w-4 h-4 transition-transform group-hover:-translate-y-0.5"></i> Export LPJ
            </button>
            <form action="{{ route('presensi') }}" method="GET" class="relative group w-full sm:w-72">
                <i data-lucide="filter" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-hover:text-primary transition-colors"></i>
                <select name="meeting_id" onchange="this.form.submit()" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all appearance-none cursor-pointer">
                    @forelse($meetings as $meeting)
                        <option value="{{ $meeting->id }}" {{ $selectedMeeting?->id == $meeting->id ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($meeting->date)->translatedFormat('d M Y') }} - {{ Str::limit($meeting->topic_title, 40) }}
                        </option>
                    @empty
                        <option value="">Belum ada jadwal pertemuan</option>
                    @endforelse
                </select>
                <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            </form>
        </div>
    </div>

    @if($selectedMeeting)
    <div class="bg-blue-50/50 dark:bg-indigo-500/5 p-4 border-b border-blue-100 dark:border-indigo-500/10 flex flex-col sm:flex-row gap-4 justify-between items-center">
        <div>
            <h3 class="font-bold text-blue-900 dark:text-indigo-300">Topik: {{ $selectedMeeting->topic_title }}</h3>
            <p class="text-sm text-blue-700 dark:text-indigo-400/80 mt-0.5"><i data-lucide="clock" class="inline w-3 h-3 mr-1"></i> {{ \Carbon\Carbon::parse($selectedMeeting->date)->translatedFormat('l, d F Y H:i') }}</p>
        </div>
        <div class="flex gap-2">
            @php
                $hadir = $users->where('attendance_status', 'present')->count();
                $izin = $users->where('attendance_status', 'excused')->count();
                $sakit = $users->where('attendance_status', 'sick')->count();
                $alpa = $users->where('attendance_status', 'absent')->count();
            @endphp
            <span class="px-2.5 py-1 bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400 rounded-lg text-xs font-bold border border-green-200 dark:border-green-500/30">Hadir: {{ $hadir }}</span>
            <span class="px-2.5 py-1 bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400 rounded-lg text-xs font-bold border border-purple-200 dark:border-purple-500/30">Izin: {{ $izin }}</span>
            <span class="px-2.5 py-1 bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400 rounded-lg text-xs font-bold border border-blue-200 dark:border-blue-500/30">Sakit: {{ $sakit }}</span>
            <span class="px-2.5 py-1 bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400 rounded-lg text-xs font-bold border border-red-200 dark:border-red-500/30">Alpa: {{ $alpa }}</span>
        </div>
    </div>

    {{-- Search Bar --}}
    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-900">
        <div class="relative">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input
                type="text"
                id="searchMember"
                placeholder="Cari anggota berdasarkan nama atau NIM..."
                oninput="filterTable()"
                class="w-full pl-10 pr-10 py-2.5 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition-all"
            >
            <button onclick="clearSearch()" id="clearSearchBtn" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 transition-colors cursor-pointer">
                <i data-lucide="x-circle" class="w-4 h-4"></i>
            </button>
        </div>
        <p id="searchResultInfo" class="text-xs text-gray-400 dark:text-slate-500 mt-1.5 hidden"></p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-200 dark:border-slate-800 transition-colors duration-300">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Anggota</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-center">Status Kehadiran</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Waktu Kunci Data</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Tindakan Admin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                @foreach ($users as $user)
                <tr data-member="1" data-name="{{ strtolower($user->full_name) }}" data-nim="{{ $user->nim }}" class="hover:bg-blue-50/30 dark:hover:bg-slate-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-slate-700" src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&bg=f8fafc&color=334155" alt="">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->full_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">{{ $user->nim }} &bull; {{ $user->subdivision ?? 'No Div' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($user->attendance_status === 'present')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-green-100 text-green-700 border border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20">
                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 mr-1.5"></i> Hadir
                            </span>
                        @elseif($user->attendance_status === 'excused')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-purple-100 text-purple-700 border border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20">
                                <i data-lucide="mail-warning" class="w-3.5 h-3.5 mr-1.5"></i> Izin
                            </span>
                        @elseif($user->attendance_status === 'sick')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20">
                                <i data-lucide="cross" class="w-3.5 h-3.5 mr-1.5"></i> Sakit
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-red-100 text-red-700 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20">
                                <i data-lucide="x-circle" class="w-3.5 h-3.5 mr-1.5"></i> Alpa
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300">
                        @if($user->attended_at)
                            {{ \Carbon\Carbon::parse($user->attended_at)->translatedFormat('d M H:i') }}
                        @else
                            <span class="text-gray-400 dark:text-slate-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2 text-left">
                            <button onclick="openOverrideModal({{ $user->id }}, '{{ addslashes($user->full_name) }}', '{{ $user->attendance_status }}')" class="flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-gray-800 hover:bg-black dark:bg-slate-700 dark:hover:bg-slate-600 rounded-lg shadow-sm transition-colors cursor-pointer">
                                <i data-lucide="pen-tool" class="w-3.5 h-3.5"></i> Override
                            </button>
                            
                            @if($user->attendance_record)
                            <button onclick="openResetModal({{ $user->attendance_record->id }})" title="Reset Ke Alpa (Hapus Data)" class="flex items-center justify-center p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 border border-transparent hover:border-red-100 dark:hover:border-red-500/20 rounded-lg transition-colors cursor-pointer">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            </button>
                            @else
                            <div class="w-[30px]"></div> <!-- spacer to align buttons when no reset option -->
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
                {{-- No results row --}}
                <tr id="noSearchResult" class="hidden">
                    <td colspan="4" class="px-6 py-10 text-center text-gray-400 dark:text-slate-500">
                        <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2"></i>
                        <p class="text-sm">Tidak ditemukan anggota dengan kata kunci tersebut.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @else
    <div class="p-12 text-center text-gray-500 dark:text-slate-400">
        <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-slate-600"></i>
        <p>Silakan buat atau pilih pertemuan LWD terlebih dahulu.</p>
    </div>
    @endif
</div>
@endsection

@section('modal')
{{-- Include generic Export Modal Component --}}
@include('components.export-modal')

{{-- Modal Override / Check-In Manual --}}
<div id="overrideModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-sm transition-all duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-sm bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800">
            <div class="p-6 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/50 rounded-t-2xl">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Override Presensi</h3>
                <button onclick="closeModal('overrideModal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 cursor-pointer transition-colors">
                    <i data-lucide="x"></i>
                </button>
            </div>
            <form action="{{ route('presensi.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="meeting_id" id="ov_meeting_id" value="{{ $selectedMeeting?->id }}">
                <input type="hidden" name="user_id" id="ov_user_id" value="">
                
                <div class="p-3 bg-blue-50 dark:bg-indigo-500/10 border border-blue-100 dark:border-indigo-500/20 rounded-xl mb-4">
                    <p class="text-xs text-blue-600 dark:text-indigo-400 font-medium">Anggota Terpilih:</p>
                    <p id="ov_user_name" class="font-bold text-blue-900 dark:text-indigo-300 mt-0.5">-</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Status Kehadiran <span class="text-red-500">*</span></label>
                    <select name="status" id="ov_status" required class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all cursor-pointer">
                        <option value="present">Hadir (Manual Check-In)</option>
                        <option value="excused">Izin</option>
                        <option value="sick">Sakit</option>
                        <option value="absent">Alpa</option>
                    </select>
                </div>
                
                <div class="pt-2 flex gap-3">
                    <button type="button" onclick="closeModal('overrideModal')" class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-slate-700 text-gray-600 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 transition-all font-medium cursor-pointer">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-gray-800 text-white rounded-xl hover:bg-black dark:bg-primary dark:hover:bg-blue-600 shadow-lg transition-all font-medium cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Reset Presensi (Destroy) --}}
<div id="resetModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm transition-all duration-300">
    <div class="flex items-center justify-center min-h-screen p-4 text-center">
        <div class="relative w-full max-w-sm bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800 p-8">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="rotate-ccw" class="w-8 h-8"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Reset Ke Alpa?</h3>
            <p class="text-gray-500 dark:text-slate-400 mb-6 text-sm">Rekam presensi yang sudah masuk akan dihapus. Pengguna akan berstatus <strong>Alpa</strong> atau belum submit.</p>
            <form id="resetForm" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeModal('resetModal')" class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-slate-700 text-gray-600 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 font-medium cursor-pointer transition-all">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 shadow-lg shadow-red-500/30 font-medium cursor-pointer transition-all">Reset Presensi</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openOverrideModal(userId, userName, currentStatus) {
        document.getElementById('ov_user_id').value = userId;
        document.getElementById('ov_user_name').innerText = userName;
        document.getElementById('ov_status').value = currentStatus;
        
        openModal('overrideModal');
    }

    function openResetModal(attendanceId) {
        document.getElementById('resetForm').action = `/presensi/${attendanceId}/destroy`;
        openModal('resetModal');
    }

    function filterTable() {
        const input = document.getElementById('searchMember');
        const query = input.value.trim().toLowerCase();
        const rows = document.querySelectorAll('tbody tr[data-member]');
        const noResult = document.getElementById('noSearchResult');
        const clearBtn = document.getElementById('clearSearchBtn');
        const infoEl = document.getElementById('searchResultInfo');

        clearBtn.classList.toggle('hidden', query === '');

        let visibleCount = 0;

        rows.forEach(row => {
            const name = (row.dataset.name || '').toLowerCase();
            const nim  = (row.dataset.nim  || '').toLowerCase();
            const match = name.includes(query) || nim.includes(query);
            row.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });

        noResult.classList.toggle('hidden', visibleCount > 0);

        if (query !== '') {
            infoEl.textContent = `Menampilkan ${visibleCount} dari ${rows.length} anggota`;
            infoEl.classList.remove('hidden');
        } else {
            infoEl.classList.add('hidden');
        }
    }

    function clearSearch() {
        const input = document.getElementById('searchMember');
        input.value = '';
        input.focus();
        filterTable();
    }
</script>
@endsection
