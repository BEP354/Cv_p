@extends('layouts.admin')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')
@section('page-description', 'Lihat dan kelola semua user platform')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-2xl text-blue-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Total Users</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-user-check text-2xl text-green-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Active Users</p>
                <p class="text-2xl font-bold text-gray-800">{{ $activeUsers }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-user-plus text-2xl text-purple-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Hari Ini</p>
                <p class="text-2xl font-bold text-gray-800">{{ $todayUsers }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-crown text-2xl text-orange-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600">Admin</p>
                <p class="text-2xl font-bold text-gray-800">{{ $adminUsers }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <form method="GET" action="{{ route('admin.users') }}" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
            <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Role</option>
                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>
        
        <div class="flex-1 min-w-48">
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        
        <div class="flex-1 min-w-48">
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, email, phone..." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
            <a href="{{ route('admin.users') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                <i class="fas fa-times mr-2"></i>Reset
            </a>
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaksi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-semibold">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                        <div class="text-sm text-gray-500">{{ $user->phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->conversion_orders_count }} orders
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye mr-1"></i>Detail
                        </a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggle-status', $user->id) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="text-{{ $user->is_active ? 'red' : 'green' }}-600 hover:text-{{ $user->is_active ? 'red' : 'green' }}-900">
                                <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} mr-1"></i>{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-users text-4xl mb-4"></i>
                        <p>Tidak ada user ditemukan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
