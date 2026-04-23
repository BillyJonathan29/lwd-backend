@extends('layouts.template')

@section('content')
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
    <div class="p-5 border-b border-gray-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 transition-colors duration-300">
        <div class="flex flex-col gap-1">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ $title ?? 'Peringatan Dini (Early Warning System)' }}</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400">Daftar anggota yang memerlukan tindak lanjut karena indisipliner absensi atau tugas.</p>
        </div>
    </div>

    @if($problematicUsers->count() > 0)
    {{-- Search Bar --}}
    <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-900">
        <div class="relative">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
            <input
                type="text"
                id="searchMember"
                placeholder="Cari anggota berdasarkan nama atau NIM..."
                oninput="filterTable()"
                class="w-full pl-10 pr-10 py-2.5 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-red-400/30 focus:border-red-400 transition-all"
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
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider w-1/3">Anggota & Divisi</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider w-1/2">Penyebab / Alasan</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Tindakan Admin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                @foreach ($problematicUsers as $user)
                <tr data-member="1" data-name="{{ strtolower($user->full_name) }}" data-nim="{{ $user->nim }}" class="hover:bg-red-50/30 dark:hover:bg-red-500/5 transition-colors group">
                    <td class="px-6 py-4 align-top">
                        <div class="flex items-center gap-3">
                            <img class="w-10 h-10 rounded-full object-cover border border-red-200 dark:border-red-900/50 shadow-sm" src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&bg=fee2e2&color=b91c1c" alt="">
                            <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->full_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5"><span class="font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">{{ $user->subdivision ?? 'NO DIV' }}</span> &bull; {{ $user->nim }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <ul class="flex flex-col gap-2">
                            @foreach($user->reasons as $reason)
                                <li class="text-xs font-semibold bg-red-50 text-red-700 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20 px-3 py-1.5 rounded-lg w-max flex items-center gap-2">
                                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                                    {{ $reason }}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="px-6 py-4 text-right align-middle">
                        @php
                            $reasonText = implode("\n", $user->reasons);
                        @endphp
                        <button onclick="openSpModal({{ $user->id }}, '{{ addslashes($user->full_name) }}', '{{ addslashes($reasonText) }}')" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-500 rounded-xl shadow-md shadow-red-500/20 transition-all cursor-pointer">
                            <i data-lucide="shield-alert" class="w-4 h-4"></i> Terbitkan SP
                        </button>
                    </td>
                </tr>
                @endforeach
                {{-- No results row --}}
                <tr id="noSearchResult" class="hidden">
                    <td colspan="3" class="px-6 py-10 text-center text-gray-400 dark:text-slate-500">
                        <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2"></i>
                        <p class="text-sm">Tidak ditemukan anggota dengan kata kunci tersebut.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @else
    <div class="p-16 text-center">
        <div class="w-16 h-16 bg-green-100 dark:bg-green-500/10 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="check-circle" class="w-8 h-8"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Semua Aman!</h3>
        <p class="text-gray-500 dark:text-slate-400">Tidak ada anggota yang memenuhi kriteria peringatan dini (Alpa 2x / Tidak Kumpul Tugas).</p>
    </div>
    @endif
</div>
@endsection

@section('modal')
{{-- Modal Penerbitan Surat Peringatan (SP) --}}
<div id="spModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-sm transition-all duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-red-100 dark:border-red-900/30 overflow-hidden">
            <!-- Header Modal -->
            <div class="p-6 bg-red-50 dark:bg-red-900/20 border-b border-red-100 dark:border-red-900/30 flex justify-between items-start">
                <div class="flex gap-4 items-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 flex items-center justify-center flex-shrink-0 shadow-inner">
                        <i data-lucide="alert-octagon" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-red-900 dark:text-red-200">Terbitkan SP</h3>
                        <p class="text-xs text-red-700 dark:text-red-400/80 mt-0.5">Surat Peringatan untuk anggota bermasalah.</p>
                    </div>
                </div>
                <button onclick="closeModal('spModal')" class="text-red-400 hover:text-red-600 dark:hover:text-red-300 cursor-pointer transition-colors p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form id="spForm" action="{{ route('warning_letters.store') }}" method="POST" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="user_id" id="sp_user_id">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Nama Anggota terindikasi</label>
                        <input type="text" id="sp_user_name" readonly class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700/50 rounded-xl outline-none text-sm font-semibold cursor-not-allowed">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Alasan Terbit SP</label>
                        <textarea name="reason" id="sp_reason" rows="3" class="w-full px-4 py-3 bg-red-50/50 dark:bg-red-500/5 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-500/20 rounded-xl outline-none text-sm font-medium focus:ring-2 focus:ring-red-500/20 focus:border-red-400 transition-all"></textarea>
                        <p class="text-[10px] text-gray-500 dark:text-slate-500 mt-1.5">*Alasan terisi otomatis berdasarkan deteksi sistem, tapi Anda dapat mengeditnya sebelum dikirim.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tingkat Peringatan <span class="text-red-500">*</span></label>
                        <select name="warning_level" required class="w-full px-4 py-3 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 outline-none transition-all cursor-pointer font-bold shadow-sm appearance-none">
                            <option value="SP 1">Surat Peringatan 1 (Teguran Lisan/Tulisan)</option>
                            <option value="SP 2">Surat Peringatan 2 (Panggilan)</option>
                            <option value="SP 3">Surat Peringatan 3 (Pencabutan Keanggotaan)</option>
                        </select>
                    </div>
                </div>
                
                <hr class="border-gray-100 dark:border-slate-800">
                
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal('spModal')" class="flex-[1] px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-all font-bold cursor-pointer shadow-sm">Batal</button>
                    <button type="submit" class="flex-[2] px-4 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 focus:ring-4 focus:ring-red-500/30 shadow-lg shadow-red-500/30 transition-all font-bold cursor-pointer inline-flex justify-center items-center">
                        <i data-lucide="send" class="w-4 h-4 mr-2"></i> Kirim SP
                    </button>
                </div>
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

    function openSpModal(userId, userName, reasonText) {
        document.getElementById('sp_user_id').value = userId;
        document.getElementById('sp_user_name').value = userName;
        document.getElementById('sp_reason').value = 'Berdasarkan catatan sistem, anggota bersangkutan:\n- ' + reasonText.replace(/\n/g, '\n- ');
        
        openModal('spModal');
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
