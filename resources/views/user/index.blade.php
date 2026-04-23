@extends('layouts.template')

@section('content')
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
    <div class="p-5 border-b border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 transition-colors duration-300">
        <div class="flex flex-col gap-1">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ $title ?? 'Data User' }}</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400">Total data: {{ $users->total() }} user terdaftar.</p>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            <form id="searchForm" action="{{ route('user') }}" method="GET" class="relative w-full sm:w-64">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-slate-500 pointer-events-none"></i>
                <input
                    type="text"
                    id="searchInput"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search users..."
                    oninput="debouncedSearch()"
                    class="w-full pl-10 pr-8 py-2.5 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                >
                @if(request('search'))
                <a href="{{ route('user') }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 transition-colors">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </a>
                @endif
            </form>

            <button onclick="openModal('addMemberModal')"
                class="bg-primary bg-blue-700 hover:bg-blue-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-lg shadow-blue-500/30 cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Add Member</span>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-200 dark:border-slate-800 transition-colors duration-300">
                    <th class="p-4 w-4">
                        <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-slate-600 dark:bg-slate-700">
                    </th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">User Info</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Role & Team</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-center">FCM Token</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Joined Date</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                @forelse ($users as $user)
                <tr class="hover:bg-blue-50/30 dark:hover:bg-slate-800/50 transition-colors group">
                    <td class="p-4">
                        <input type="checkbox"
                            class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-slate-600 dark:bg-slate-700">
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('user.show', $user->id) }}"
                            class="flex items-center gap-4 hover:opacity-80 transition-opacity">
                            <img class="w-10 h-10 rounded-full object-cover ring-2 ring-white dark:ring-slate-800 shadow-sm"
                                src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name ?? 'User') }}&background=2563eb&color=fff"
                                alt="{{ $user->full_name }}">
                            <div>
                                <p class="text-sm font-semibold text-blue-600 dark:text-indigo-400 hover:underline">{{ $user->full_name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-slate-400">{{ $user->nim }}</p>
                            </div>
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-900 dark:text-white font-medium capitalize">{{ $user->role }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">{{ $user->subdivision ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($user->fcm_token)
                        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20">
                            Available
                        </div>
                        @else
                        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/20">
                            None
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-slate-400">
                        {{ $user->created_at?->format('M d, Y') ?? 'null' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-3">
                            <button onclick="openEditModal({{ $user->toJson() }})"
                                class="flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-primary dark:text-indigo-400 bg-blue-50 dark:bg-indigo-500/10 hover:bg-blue-100 dark:hover:bg-indigo-500/20 rounded-lg transition-all border border-blue-100 dark:border-indigo-500/20 cursor-pointer">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                <span>Edit</span>
                            </button>

                            <button onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->full_name) }}')"
                                class="flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-lg transition-all border border-red-100 dark:border-red-500/20 cursor-pointer">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-slate-400">Tidak ada data user ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-gray-50 dark:bg-slate-900/50 px-6 py-4 border-t border-gray-200 dark:border-slate-800 flex items-center justify-between transition-colors duration-300">
        <span class="text-sm text-gray-500 dark:text-slate-400">
            Showing <span class="font-medium text-gray-900 dark:text-white">{{ $users->firstItem() ?? 0 }}</span> to
            {{ $users->lastItem() ?? 0 }} of {{ $users->total() ?? 0 }} results
        </span>
        <div class="flex items-center gap-2">
            {{ $users->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection

@section('modal')
{{-- Modal tambah --}}
<div id="addMemberModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-sm transition-all duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800">
            <div class="p-6 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/50 rounded-t-2xl">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Add New Member</h3>
                <button onclick="closeModal('addMemberModal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 cursor-pointer transition-colors"><i
                        data-lucide="x"></i></button>
            </div>
            <form action="{{ route('user.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">NIM</label>
                    <input type="text" name="nim" required
                        class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Full Name</label>
                    <input type="text" name="full_name" required
                        class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Subdivision</label>
                    <input type="text" name="subdivision" required
                        class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Role</label>
                    <select name="role" required
                        class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all cursor-pointer">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">FCM Token</label>
                    <input type="text" name="fcm_token"
                        class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeModal('addMemberModal')"
                        class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-slate-700 text-gray-600 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 transition-all font-medium cursor-pointer">Cancel</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-primary text-white rounded-xl bg-blue-700 shadow-lg shadow-blue-500/30 transition-all font-medium cursor-pointer">Save
                        Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal edit --}}
<div id="editMemberModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-sm transition-all duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800">
            <div class="p-6 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/50 rounded-t-2xl">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Edit Member</h3>
                <button onclick="closeModal('editMemberModal')" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 cursor-pointer transition-colors"><i
                        data-lucide="x"></i></button>
            </div>
            <form id="editForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">NIM</label>
                    <input type="text" name="nim" id="edit_nim" required
                        class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Full Name</label>
                    <input type="text" name="full_name" id="edit_full_name" required
                        class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Subdivision</label>
                    <input type="text" name="subdivision" id="edit_subdivision" required
                        class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Role</label>
                    <select name="role" id="edit_role" required
                        class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all cursor-pointer">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">FCM Token</label>
                    <input type="text" name="fcm_token" id="edit_fcm_token"
                        class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Password <span class="text-xs text-gray-400 font-normal">(Kosongkan jika tidak diubah)</span></label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-gray-800 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeModal('editMemberModal')"
                        class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-slate-700 text-gray-600 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 transition-all font-medium cursor-pointer">Cancel</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-primary text-white rounded-xl bg-blue-700 shadow-lg shadow-blue-500/30 transition-all font-medium cursor-pointer">Update
                        Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal delete --}}
<div id="deleteMemberModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm transition-all duration-300">
    <div class="flex items-center justify-center min-h-screen p-4 text-center">
        <div class="relative w-full max-w-sm bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-800 p-8">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="alert-triangle" class="w-8 h-8"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Delete Member?</h3>
            <p class="text-gray-500 dark:text-slate-400 mb-6">Are you sure you want to delete <span id="delete_user_name"
                    class="font-bold text-gray-800 dark:text-white"></span>? This action cannot be undone.</p>
            <form id="deleteForm" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeModal('deleteMemberModal')"
                    class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-slate-700 text-gray-600 dark:text-slate-300 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 font-medium cursor-pointer transition-all">Cancel</button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 shadow-lg shadow-red-500/30 font-medium cursor-pointer transition-all">Delete
                    Now</button>
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

    function openEditModal(user) {
        document.getElementById('edit_nim').value = user.nim || '';
        document.getElementById('edit_full_name').value = user.full_name || '';
        document.getElementById('edit_subdivision').value = user.subdivision || '';
        document.getElementById('edit_role').value = user.role || '';
        document.getElementById('edit_fcm_token').value = user.fcm_token || '';

        document.getElementById('editForm').action = `/user/${user.id}/update`;

        openModal('editMemberModal');
    }

    function openDeleteModal(id, name) {
        document.getElementById('delete_user_name').innerText = name;
        document.getElementById('deleteForm').action = `/user/${id}/destroy`;

        openModal('deleteMemberModal');
    }
</script>
@endsection