<div id="exportModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-sm transition-all duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 border-b border-indigo-100 dark:border-indigo-900/30 flex justify-between items-start">
                <div class="flex gap-4 items-center">
                    <div class="w-12 h-12 rounded-full bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0 shadow-sm border border-indigo-100 dark:border-indigo-900/50">
                        <i data-lucide="file-output" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-indigo-900 dark:text-indigo-300" id="exportModalTitle">Export Laporan</h3>
                        <p class="text-xs text-indigo-700/80 dark:text-indigo-400/80 mt-0.5">Format profesional dengan Header Institusi PBK.</p>
                    </div>
                </div>
                <button type="button" onclick="closeExportModal()" class="text-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-300 cursor-pointer transition-colors p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form id="exportForm" action="#" method="GET" onsubmit="handleDummyExport(event)" class="p-6 space-y-5">
                <input type="hidden" name="module" id="export_module" value="">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Cakupan Data Ekspor</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex cursor-pointer">
                                <input type="radio" name="export_scope" value="all" checked onchange="updateExportPreview()" class="peer sr-only">
                                <div class="w-full p-4 bg-white dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 rounded-xl peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-500/10 peer-checked:text-indigo-700 dark:peer-checked:text-indigo-300 transition-all text-center">
                                    <i data-lucide="layers" class="w-6 h-6 mx-auto mb-2 opacity-70"></i>
                                    <span class="text-sm font-bold block">1 Semester Penuh</span>
                                    <span class="text-xs opacity-70">Semua Pertemuan</span>
                                </div>
                            </label>
                            
                            <label class="relative flex cursor-pointer">
                                <input type="radio" name="export_scope" value="single" onchange="updateExportPreview()" class="peer sr-only">
                                <div class="w-full p-4 bg-white dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 rounded-xl peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-500/10 peer-checked:text-indigo-700 dark:peer-checked:text-indigo-300 transition-all text-center">
                                    <i data-lucide="calendar-days" class="w-6 h-6 mx-auto mb-2 opacity-70"></i>
                                    <span class="text-sm font-bold block">Satu Pertemuan</span>
                                    <span class="text-xs opacity-70">Pilih Spesifik</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <div id="singleMeetingSelectContainer" class="hidden">
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Pilih Pertemuan</label>
                        <select name="meeting_id" id="export_meeting_id" onchange="updateExportPreview()" class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-800/50 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all cursor-pointer font-medium appearance-none">
                            @foreach(\App\Models\Meeting::orderBy('date', 'desc')->get() as $m)
                                <option value="{{ $m->id }}">Pertemuan {{ $m->id }} - {{ Str::limit($m->topic_title, 40) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-xl border border-gray-100 dark:border-slate-800 flex items-start gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-indigo-500 mt-0.5 flex-shrink-0"></i>
                        <p id="exportPreviewText" class="text-sm text-gray-600 dark:text-slate-400 leading-relaxed font-medium">
                            Menyiapkan ekspor LPJ <strong>Lengkap 1 Semester</strong>. Seluruh data anggota, pertemuan, dan <span id="previewModuleType">presensi</span> akan digabungkan.
                        </p>
                    </div>
                </div>
                
                <hr class="border-gray-100 dark:border-slate-800">
                
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="button" onclick="triggerDownload('excel')" class="px-4 py-3 bg-white dark:bg-slate-800 border-2 border-green-500 text-green-600 dark:text-green-500 rounded-xl hover:bg-green-50 dark:hover:bg-green-500/10 focus:ring-4 focus:ring-green-500/20 transition-all font-bold cursor-pointer inline-flex justify-center items-center shadow-sm">
                        <i data-lucide="file-spreadsheet" class="w-5 h-5 mr-2"></i> Export to Excel
                    </button>
                    
                    <button type="button" onclick="triggerDownload('pdf')" class="px-4 py-3 bg-white dark:bg-slate-800 border-2 border-red-500 text-red-600 dark:text-red-500 rounded-xl hover:bg-red-50 dark:hover:bg-red-500/10 focus:ring-4 focus:ring-red-500/20 transition-all font-bold cursor-pointer inline-flex justify-center items-center shadow-sm">
                        <i data-lucide="file-text" class="w-5 h-5 mr-2"></i> Export to PDF
                    </button>
                </div>
                <div class="text-center mt-2">
                    <button type="button" onclick="closeExportModal()" class="text-xs text-gray-500 hover:text-gray-800 dark:text-slate-500 dark:hover:text-slate-300 font-semibold underline underline-offset-2">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openExportModal(module) {
        document.getElementById('exportModal').classList.remove('hidden');
        document.getElementById('export_module').value = module;
        
        let title = module === 'presensi' ? 'Kehadiran' : 'Tugas Mandiri';
        document.getElementById('exportModalTitle').innerText = 'Export LPJ ' + title;
        document.getElementById('previewModuleType').innerText = title.toLowerCase();
        
        updateExportPreview();
    }

    function closeExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
    }

    function updateExportPreview() {
        const scope = document.querySelector('input[name="export_scope"]:checked').value;
        const selectContainer = document.getElementById('singleMeetingSelectContainer');
        const previewText = document.getElementById('exportPreviewText');
        const moduleType = document.getElementById('export_module').value === 'presensi' ? 'hadir/absen' : 'nilai tugas';
        
        const totalMembers = {{ \App\Models\User::where('role', 'User')->count() }}; 

        if (scope === 'all') {
            selectContainer.classList.add('hidden');
            previewText.innerHTML = `Siap mengekspor rekap <strong>${totalMembers} anggota</strong> untuk rekap satu semester penuh berjalan. Format profesional siap cetak.`;
        } else {
            selectContainer.classList.remove('hidden');
            const selectEl = document.getElementById('export_meeting_id');
            const meetingText = selectEl.options[selectEl.selectedIndex].text;
            previewText.innerHTML = `Siap mengekspor parsial rekap <strong>${totalMembers} anggota</strong> hanya untuk data ${moduleType} pada <strong>${meetingText}</strong>.`;
        }
    }

    function triggerDownload(type) {
        const scope = document.querySelector('input[name="export_scope"]:checked').value;
        const module = document.getElementById('export_module').value;
        const meetingId = document.getElementById('export_meeting_id')?.value ?? '';

        // Hanya presensi yang didukung untuk saat ini
        if (module !== 'presensi') {
            alert('Export untuk modul ini belum tersedia.');
            return;
        }

        // Bangun query params
        const params = new URLSearchParams();
        params.set('export_scope', scope);
        if (scope === 'single' && meetingId) {
            params.set('meeting_id', meetingId);
        }

        // Tentukan URL route
        const baseUrl = type === 'excel'
            ? '{{ route("presensi.export.excel") }}'
            : '{{ route("presensi.export.pdf") }}';

        const fullUrl = baseUrl + '?' + params.toString();

        // Update preview text jadi loading
        const previewText = document.getElementById('exportPreviewText');
        const colorClass = type === 'excel' ? 'text-green-600' : 'text-red-600';
        const icon = type === 'excel' ? 'file-spreadsheet' : 'file-text';
        previewText.innerHTML = `<span class="${colorClass} font-bold flex items-center gap-2"><i data-lucide="${icon}" class="w-4 h-4"></i> Memproses dokumen, harap tunggu...</span>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();

        if (type === 'excel') {
            // Download langsung (CSV)
            window.location.href = fullUrl;
            setTimeout(() => closeExportModal(), 1500);
        } else {
            // Buka PDF di tab baru
            window.open(fullUrl, '_blank');
            setTimeout(() => closeExportModal(), 800);
        }
    }

    function handleDummyExport(event) {
        // Prevent default form submission — export ditangani triggerDownload()
        event.preventDefault();
    }
</script>
