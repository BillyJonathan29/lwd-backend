@extends('layouts.template')

@section('content')
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
    <div class="p-5 border-b border-gray-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 transition-colors duration-300">
        <div class="flex flex-col gap-1">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ $title ?? 'Evaluasi Tugas Anggota' }}</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400">Pilih pertemuan untuk melihat dan menilai tugas LWD.</p>
        </div>
        
        <div class="w-full md:w-96">
            <form action="{{ route('submission') }}" method="GET" class="relative group">
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
            <h3 class="font-bold text-blue-900 dark:text-indigo-300">Tugas: {{ $selectedMeeting->topic_title }}</h3>
            <p class="text-sm text-blue-700 dark:text-indigo-400/80 mt-0.5">
                <i data-lucide="clock" class="inline w-3 h-3 mr-1"></i> Deadline Tugas: 
                @if($selectedMeeting->assignment_deadline)
                    {{ \Carbon\Carbon::parse($selectedMeeting->assignment_deadline)->translatedFormat('l, d F Y H:i') }}
                @else
                    <span class="italic">Belum diatur</span>
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            @php
                $sudahKumpul = $users->filter(fn($u) => $u->submission_record)->count();
                $belumKumpul = $users->count() - $sudahKumpul;
                $sudahDinilai = $users->filter(fn($u) => $u->submission_record && $u->submission_record->grade !== null)->count();
            @endphp
            <span class="px-2.5 py-1 bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400 rounded-lg text-xs font-bold border border-green-200 dark:border-green-500/30">Terkumpul: {{ $sudahKumpul }}</span>
            <span class="px-2.5 py-1 bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400 rounded-lg text-xs font-bold border border-red-200 dark:border-red-500/30">Belum: {{ $belumKumpul }}</span>
            <span class="px-2.5 py-1 bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400 rounded-lg text-xs font-bold border border-blue-200 dark:border-blue-500/30">Dinilai: {{ $sudahDinilai }}</span>
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
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-center">Status / Waktu</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-center">Nilai</th>
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
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">{{ $user->nim }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($user->submission_record)
                            <div class="flex flex-col items-center gap-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 border border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20">
                                    <i data-lucide="check" class="w-3 h-3 mr-1"></i> Sudah Kumpul
                                </span>
                                @php
                                    $isLate = $selectedMeeting->assignment_deadline && \Carbon\Carbon::parse($user->submission_record->submitted_at)->gt(\Carbon\Carbon::parse($selectedMeeting->assignment_deadline));
                                @endphp
                                @if($isLate)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700 border border-orange-200 dark:bg-orange-500/10 dark:text-orange-400 dark:border-orange-500/20 mt-1"> Terlambat </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20 mt-1"> Tepat Waktu </span>
                                @endif
                                <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-1">{{ \Carbon\Carbon::parse($user->submission_record->submitted_at)->format('d M H:i') }}</p>
                            </div>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700">
                                Belum Kumpul
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($user->submission_record)
                            @if($user->submission_record->grade !== null)
                                <div class="inline-flex flex-col items-center justify-center w-12 h-12 rounded-full border-4 shadow-sm {{ $user->submission_record->grade >= 70 ? 'border-green-500 text-green-600 bg-green-50 dark:bg-green-500/10 dark:text-green-400' : 'border-red-400 text-red-600 bg-red-50 dark:bg-red-500/10 dark:text-red-400' }}">
                                    <span class="text-sm font-bold">{{ $user->submission_record->grade }}</span>
                                </div>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/20">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 mr-1.5"></i> Menunggu
                                </span>
                            @endif
                        @else
                            <span class="text-gray-400 dark:text-slate-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($user->submission_record)
                            <button onclick="openReviewModal({{ $user->submission_record->id }}, '{{ addslashes($user->full_name) }}', '{{ htmlspecialchars($user->submission_record->file_or_link ?? '') }}', '{{ htmlspecialchars($user->submission_record->member_notes ?? '') }}', '{{ $user->submission_record->grade }}', '{{ htmlspecialchars($user->submission_record->admin_feedback ?? '') }}')" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 dark:bg-indigo-600 dark:hover:bg-indigo-500 rounded-lg shadow-sm transition-colors cursor-pointer w-28">
                                @if($user->submission_record->grade !== null)
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Ubah Nilai
                                @else
                                    <i data-lucide="star" class="w-3.5 h-3.5"></i> Beri Nilai
                                @endif
                            </button>
                        @else
                            <button disabled class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-400 bg-gray-100 dark:bg-slate-800 dark:text-slate-500 rounded-lg cursor-not-allowed w-28">
                                <i data-lucide="lock" class="w-3.5 h-3.5"></i> Terkunci
                            </button>
                        @endif
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
{{-- Modal Evaluasi Tugas --}}
<div id="reviewModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-sm transition-all duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800">
            <div class="p-6 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/50 rounded-t-2xl">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Evaluasi Tugas</h3>
                <button onclick="closeModal('reviewModal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 cursor-pointer transition-colors">
                    <i data-lucide="x"></i>
                </button>
            </div>
            <form id="evaluationForm" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')
                
                <div class="p-4 bg-blue-50 dark:bg-indigo-500/10 border border-blue-100 dark:border-indigo-500/20 rounded-xl">
                    <p class="text-xs text-blue-600 dark:text-indigo-400 font-medium uppercase tracking-wider">Review Untuk:</p>
                    <p id="rev_user_name" class="text-lg font-bold text-blue-900 dark:text-indigo-300 mt-0.5">-</p>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">File / Link Tugas</label>
                        <div class="flex items-center gap-2">
                            <input type="text" id="rev_link" readonly class="w-full px-4 py-2 bg-gray-50 dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl outline-none text-sm">
                            <a id="rev_link_btn" href="#" target="_blank" class="flex-shrink-0 px-4 py-2 bg-gray-800 text-white rounded-xl hover:bg-black dark:bg-slate-700 dark:hover:bg-slate-600 shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-gray-800 inline-flex items-center">
                                <i data-lucide="external-link" class="w-4 h-4 mr-2"></i> Buka
                            </a>
                        </div>
                        <div id="rev_image_container" class="hidden mt-3 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden bg-gray-50 dark:bg-slate-800 shadow-inner flex justify-center items-center h-48">
                            <img id="rev_image_preview" src="" alt="Pratinjau Tugas" class="max-w-full max-h-full object-contain">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">Catatan dari Anggota</label>
                        <div id="rev_member_notes" class="w-full p-4 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-600 dark:text-slate-300 rounded-xl text-sm min-h-[60px]">
                            -
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-slate-700 border-dashed">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">Nilai <span class="text-red-500">*</span></label>
                        <input type="number" name="grade" id="rev_grade" required min="0" max="100" placeholder="0-100" class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-bold text-center text-lg">
                    </div>
                    
                    <div class="md:col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">Feedback Admin <span class="text-gray-400 font-normal text-xs">(Opsional)</span></label>
                        <textarea name="admin_feedback" id="rev_admin_feedback" rows="3" placeholder="Berikan feedback membangun untuk anggota ini..." class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"></textarea>
                    </div>
                </div>
                
                <div class="pt-2 flex gap-3">
                    <button type="button" onclick="closeModal('reviewModal')" class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-slate-700 text-gray-600 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 transition-all font-medium cursor-pointer">Batal</button>
                    <button type="submit" class="flex-[2] px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 dark:bg-indigo-600 dark:hover:bg-indigo-500 shadow-lg shadow-blue-500/30 transition-all font-medium cursor-pointer inline-flex justify-center items-center">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i> Simpan Penilaian
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

    function openReviewModal(submissionId, userName, link, memberNotes, currentGrade, adminFeedback) {
        document.getElementById('rev_user_name').innerText = userName;
        
        const linkInput = document.getElementById('rev_link');
        const linkBtn = document.getElementById('rev_link_btn');
        linkInput.value = link;
        
        // Handle URL vs just text
        if (link.startsWith('http://') || link.startsWith('https://')) {
            linkBtn.href = link;
            linkBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            linkBtn.removeAttribute('disabled');
        } else {
            linkBtn.href = '#';
            linkBtn.classList.add('opacity-50', 'cursor-not-allowed');
            linkBtn.setAttribute('disabled', 'true');
        }

        const imgContainer = document.getElementById('rev_image_container');
        const imgPreview = document.getElementById('rev_image_preview');

        // Check if link is an image URL
        const isImage = link.match(/\.(jpeg|jpg|gif|png|webp|svg|bmp)([\?#].*)?$/i) !== null;
        
        if (isImage) {
            imgPreview.src = link;
            imgContainer.classList.remove('hidden');
        } else {
            imgPreview.src = '';
            imgContainer.classList.add('hidden');
        }

        document.getElementById('rev_member_notes').innerHTML = memberNotes ? memberNotes.replace(/\n/g, '<br>') : '<span class="italic text-gray-400">Tidak ada catatan.</span>';
        
        document.getElementById('rev_grade').value = currentGrade !== '' ? currentGrade : '';
        document.getElementById('rev_admin_feedback').value = adminFeedback;
        
        document.getElementById('evaluationForm').action = `/submission/${submissionId}/evaluate`;
        
        openModal('reviewModal');
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
