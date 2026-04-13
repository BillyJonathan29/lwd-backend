
@extends('layouts.template')

@section('content')
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
        <div class="flex flex-col gap-1">
            <h2 class="text-lg font-bold text-gray-800">{{ $title }}</h2>
            <p class="text-sm text-gray-500">Total data: {{ $users->total() }} user terdaftar.</p>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            <form action="{{ route('user') }}" method="GET" class="relative w-full sm:w-64">
                <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 focus:outline-none group cursor-pointer">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 group-hover:text-primary transition-colors">
                    </i>
                </button>

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..."
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
            </form>

            <button onclick="openModal('addMemberModal')"
                class="bg-primary hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-lg shadow-blue-500/30 cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Add Member</span>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-200">
                    <th class="p-4 w-4">
                        <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
                    </th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">User Info</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role & Team</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Joined Date</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Action
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($users as $user)
                <tr class="hover:bg-blue-50/30 transition-colors group">
                    <td class="p-4">
                        <input type="checkbox"
                            class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('user.show', $user->id) }}"
                            class="flex items-center gap-4 hover:opacity-80 transition-opacity">
                            <img class="w-10 h-10 rounded-full object-cover ring-2 ring-white shadow-sm"
                                src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=2563eb&color=fff"
                                alt="{{ $user->name }}">
                            <div>
                                <p class="text-sm font-semibold text-blue-600 hover:underline">{{ $user->name }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium capitalize">{{ $user->role }}</td>
                    <td class="px-6 py-4">
                        <div
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                            <span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-1.5"></span> Active
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $user->created_at?->format('M d, Y') ?? 'null' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-3">
                            <button onclick="openEditModal({{ $user->toJson() }})"
                                class="flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-primary bg-blue-50 hover:bg-blue-100 rounded-lg transition-all border border-blue-100 cursor-pointer">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                <span>Edit</span>
                            </button>

                            <button onclick="openDeleteModal({{ $user->id }}, '{{ $user->name }}')"
                                class="flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-all border border-red-100 cursor-pointer">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">Tidak ada data user ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <span class="text-sm text-gray-500">
            Showing <span class="font-medium text-gray-900">{{ $users->firstItem() }}</span> to
            {{ $users->lastItem() }} of {{ $users->total() }} results
        </span>
        <div class="flex items-center gap-2">
            {{ $users->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection

@section('modal')
{{-- Modal tambah --}}
<div id="addMemberModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Add New Member</h3>
                <button onclick="closeModal('addMemberModal')" class="text-gray-400 hover:text-gray-600 cursor-pointer"><i
                        data-lucide="x"></i></button>
            </div>
            <form action="{{ route('user.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all cursor-pointer">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeModal('addMemberModal')"
                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition-all font-medium cursor-pointer">Cancel</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-primary text-white rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all font-medium cursor-pointer">Save
                        Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal edit --}}
<div id="editMemberModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Edit Member</h3>
                <button onclick="closeModal('editMemberModal')" class="text-gray-400 hover:text-gray-600 cursor-pointer"><i
                        data-lucide="x"></i></button>
            </div>
            <form id="editForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" id="edit_name" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" id="edit_email" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" id="edit_role"
                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all cursor-pointer">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeModal('editMemberModal')"
                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition-all font-medium cursor-pointer">Cancel</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-primary text-white rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all font-medium cursor-pointer">Update
                        Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal delete --}}
<div id="deleteMemberModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4 text-center">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-xl p-8">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="alert-triangle" class="w-8 h-8"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Delete Member?</h3>
            <p class="text-gray-500 mb-6">Are you sure you want to delete <span id="delete_user_name"
                    class="font-bold text-gray-800"></span>? This action cannot be undone.</p>
            <form id="deleteForm" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeModal('deleteMemberModal')"
                    class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 font-medium cursor-pointer">Cancel</button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 shadow-lg shadow-red-500/30 font-medium cursor-pointer">Delete
                    Now</button>
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

    function openEditModal(user) {
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_role').value = user.role;

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