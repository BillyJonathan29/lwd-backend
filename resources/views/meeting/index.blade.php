@extends('layouts.template')

@section('content')
    <div
        class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
        <div
            class="p-5 border-b border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 transition-colors duration-300">
            <div class="flex flex-col gap-1">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ $title ?? 'Data Pertemuan' }}</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400">Total data: {{ $meetings->total() }} pertemuan
                    terdaftar.</p>
            </div>
            <div class="flex gap-3 w-full sm:w-auto">
                <form id="searchForm" action="{{ Route::has('pertemuan') ? route('pertemuan') : '#' }}" method="GET"
                    class="relative w-full sm:w-64">
                    <i data-lucide="search"
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-slate-500 pointer-events-none"></i>
                    <input
                        type="text"
                        id="searchInput"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search pertemuan..."
                        oninput="debouncedSearch()"
                        class="w-full pl-10 pr-8 py-2.5 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                    @if(request('search'))
                    <a href="{{ Route::has('pertemuan') ? route('pertemuan') : '#' }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 transition-colors">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </a>
                    @endif
                </form>

                <button onclick="openModal('addMeetingModal')"
                    class="bg-primary bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-lg shadow-blue-500/30 cursor-pointer">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Add Pertemuan</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-200 dark:border-slate-800 transition-colors duration-300">
                        <th class="p-4 w-4">
                            <input type="checkbox"
                                class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-slate-600 dark:bg-slate-700 cursor-pointer">
                        </th>
                        <th
                            class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                            Topik & Deskripsi</th>
                        <th
                            class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                            Kategori</th>
                        <th
                            class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                            Tanggal</th>
                        <th
                            class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">
                            Deadline Tugas</th>
                        <th
                            class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">
                            Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse ($meetings as $meeting)
                        <tr class="hover:bg-blue-50/30 dark:hover:bg-slate-800/50 transition-colors group">
                            <td class="p-4">
                                <input type="checkbox"
                                    class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-slate-600 dark:bg-slate-700 cursor-pointer">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                        <i data-lucide="book-open" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $meeting->topic_title }}</p>
                                        <p class="text-xs text-gray-500 dark:text-slate-400 line-clamp-1 mt-0.5"
                                            title="{{ $meeting->description }}">
                                            {{ $meeting->description ?? 'Tidak ada deskripsi' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20 capitalize">
                                    {{ $meeting->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($meeting->date)->translatedFormat('d F Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300 whitespace-nowrap">
                                @if ($meeting->assignment_deadline)
                                    <span
                                        class="text-xs font-medium px-2 py-1 bg-orange-100 text-orange-800 border border-orange-200 dark:bg-orange-500/10 dark:text-orange-400 dark:border-orange-500/20 rounded-md">
                                        {{ \Carbon\Carbon::parse($meeting->assignment_deadline)->translatedFormat('d M Y, H:i') }}
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-slate-500">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <button onclick="openEditModal(this)" data-meeting="{{ $meeting->toJson() }}"
                                        class="flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-primary dark:text-indigo-400 bg-blue-50 dark:bg-indigo-500/10 hover:bg-blue-100 dark:hover:bg-indigo-500/20 rounded-lg transition-all border border-blue-100 dark:border-indigo-500/20 cursor-pointer">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                        <span>Edit</span>
                                    </button>

                                    <button
                                        onclick="openDeleteModal(this)" data-id="{{ $meeting->id }}" data-title="{{ $meeting->topic_title }}"
                                        class="flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-lg transition-all border border-red-100 dark:border-red-500/20 cursor-pointer">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">Tidak ada
                                data pertemuan ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div
            class="bg-gray-50 dark:bg-slate-900/50 px-6 py-4 border-t border-gray-200 dark:border-slate-800 flex items-center justify-between transition-colors duration-300">
            <span class="text-sm text-gray-500 dark:text-slate-400">
                Showing <span class="font-medium text-gray-900 dark:text-white">{{ $meetings->firstItem() ?? 0 }}</span> to
                {{ $meetings->lastItem() ?? 0 }} of {{ $meetings->total() ?? 0 }} results
            </span>
            <div class="flex items-center gap-2">
                {{ $meetings->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    {{-- Modal Tambah --}}
    <div id="addMeetingModal"
        class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-sm transition-all duration-300">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div
                class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800">
                <div
                    class="p-6 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/50 rounded-t-2xl">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Tambah Pertemuan</h3>
                    <button onclick="closeModal('addMeetingModal')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 cursor-pointer transition-colors">
                        <i data-lucide="x"></i>
                    </button>
                </div>
                <form action="{{ route('pertemuan.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Topik
                            Pertemuan</label>
                        <input type="text" name="topic_title" required
                            class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Kategori</label>
                        {{-- UBAH JADI SELECT DROPDOWN SESUAI ENUM DATABASE --}}
                        <select name="category" required
                            class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <option value="web">Web Development</option>
                            <option value="hardware">Hardware / IoT</option>
                            <option value="tools">Tools / Git</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Tanggal &
                            Waktu</label>
                        <input type="datetime-local" name="date" required
                            class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Deadline Tugas
                            (Opsional)</label>
                        <input type="datetime-local" name="assignment_deadline"
                            class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"></textarea>
                    </div>
                    <div class="pt-4 flex gap-3">
                        <button type="button" onclick="closeModal('addMeetingModal')"
                            class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-slate-700 text-gray-600 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 transition-all font-medium cursor-pointer">Batal</button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-primary text-white rounded-xl bg-blue-700 shadow-lg shadow-blue-500/30 transition-all font-medium cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="editMeetingModal"
        class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-sm transition-all duration-300">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div
                class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800">
                <div
                    class="p-6 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/50 rounded-t-2xl">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Edit Pertemuan</h3>
                    <button onclick="closeModal('editMeetingModal')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 cursor-pointer transition-colors">
                        <i data-lucide="x"></i>
                    </button>
                </div>
                <form id="editForm" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Topik
                            Pertemuan</label>
                        <input type="text" name="topic_title" id="edit_topic_title" required
                            class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Kategori</label>
                        <select name="category" id="edit_category" required
                            class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                            <option value="" disabled>-- Pilih Kategori --</option>
                            <option value="web">Web Development</option>
                            <option value="hardware">Hardware / IoT</option>
                            <option value="tools">Tools / Git</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Tanggal &
                            Waktu</label>
                        <input type="datetime-local" name="date" id="edit_date" required
                            class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Deadline Tugas
                            (Opsional)</label>
                        <input type="datetime-local" name="assignment_deadline" id="edit_assignment_deadline"
                            class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Deskripsi</label>
                        <textarea name="description" id="edit_description" rows="3"
                            class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"></textarea>
                    </div>
                    <div class="pt-4 flex gap-3">
                        <button type="button" onclick="closeModal('editMeetingModal')"
                            class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-slate-700 text-gray-600 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 transition-all font-medium cursor-pointer">Batal</button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-primary text-white rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all font-medium cursor-pointer">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Delete --}}
    <div id="deleteMeetingModal"
        class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm transition-all duration-300">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div
                class="relative w-full max-w-sm bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800 p-8">
                <div
                    class="w-16 h-16 bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Hapus Pertemuan?</h3>
                <p class="text-gray-500 dark:text-slate-400 mb-6">Anda yakin ingin menghapus <span id="delete_topic_title"
                        class="font-bold text-gray-800 dark:text-white"></span>? Aksi ini tidak dapat dibatalkan.</p>
                <form id="deleteForm" method="POST" class="flex gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeModal('deleteMeetingModal')"
                        class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-slate-700 text-gray-600 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 font-medium cursor-pointer transition-all">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 shadow-lg shadow-red-500/30 font-medium cursor-pointer transition-all">Hapus</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Auto-search with debounce — submit form 400ms after user stops typing
        let searchTimer;
        function debouncedSearch() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 400);
        }

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function formatDateForInput(dateString) {
            if (!dateString) return '';
            // Ubah yyyy-mm-dd hh:mm:ss menjadi format input yyyy-mm-ddThh:mm
            let t = dateString.replace(' ', 'T');
            if (t.length > 16) {
                t = t.substring(0, 16);
            }
            return t;
        }

        function openEditModal(btn) {
            const meeting = JSON.parse(btn.dataset.meeting);
            document.getElementById('edit_topic_title').value = meeting.topic_title || '';
            document.getElementById('edit_category').value = meeting.category || '';
            document.getElementById('edit_description').value = meeting.description || '';
            document.getElementById('edit_date').value = formatDateForInput(meeting.date);
            document.getElementById('edit_assignment_deadline').value = formatDateForInput(meeting.assignment_deadline);

            document.getElementById('editForm').action = `/pertemuan/${meeting.id}/update`;

            openModal('editMeetingModal');
        }

        function openDeleteModal(btn) {
            document.getElementById('delete_topic_title').innerText = btn.dataset.title;
            document.getElementById('deleteForm').action = `/pertemuan/${btn.dataset.id}/destroy`;

            openModal('deleteMeetingModal');
        }
    </script>
@endsection
