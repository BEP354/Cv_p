@extends('layouts.admin')

@section('title', 'Kelola Rekening')
@section('page-title', 'Kelola Rekening')
@section('page-description', 'Kelola rekening admin untuk menerima pembayaran')

@section('content')
<!-- Add Account Form -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h3 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
        <i class="fas fa-plus mr-2 text-blue-500"></i>
        Tambah Rekening Admin
    </h3>
    
    <form method="POST" action="{{ route('admin.accounts.store') }}" class="space-y-4">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Rekening</label>
                <select name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Pilih Jenis</option>
                    <option value="paypal">PayPal</option>
                    <option value="skrill">Skrill</option>
                    <option value="bank">Bank</option>
                    <option value="ewallet">E-Wallet</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama/Bank</label>
                <input type="text" name="name" required placeholder="Contoh: PayPal Admin, BCA, DANA" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email/Nomor Rekening</label>
                <input type="text" name="account_number" required placeholder="Email atau nomor rekening" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pemilik</label>
                <input type="text" name="account_name" required placeholder="Nama pemilik rekening" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <input type="text" name="notes" placeholder="Catatan tambahan (opsional)" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-save mr-2"></i>Tambah Rekening
            </button>
        </div>
    </form>
</div>

<!-- Accounts List -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-xl font-semibold text-gray-800 flex items-center">
            <i class="fas fa-university mr-2 text-blue-500"></i>
            Daftar Rekening Admin
        </h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama/Bank</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rekening</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemilik</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($accounts as $account)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 {{ $account->type === 'paypal' ? 'bg-blue-500' : ($account->type === 'skrill' ? 'bg-red-500' : ($account->type === 'bank' ? 'bg-green-500' : 'bg-purple-500')) }} rounded-lg flex items-center justify-center mr-3">
                                <i class="fas {{ $account->type === 'paypal' ? 'fa-paypal' : ($account->type === 'skrill' ? 'fa-wallet' : ($account->type === 'bank' ? 'fa-university' : 'fa-mobile-alt')) }} text-white text-sm"></i>
                            </div>
                            <span class="font-medium">{{ ucfirst($account->type) }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $account->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $account->account_number }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $account->account_name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $account->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <form method="POST" action="{{ route('admin.accounts.toggle', $account->id) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="text-{{ $account->is_active ? 'red' : 'green' }}-600 hover:text-{{ $account->is_active ? 'red' : 'green' }}-900">
                                <i class="fas fa-{{ $account->is_active ? 'ban' : 'check' }} mr-1"></i>{{ $account->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.accounts.destroy', $account->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus rekening ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash mr-1"></i>Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-university text-4xl mb-4"></i>
                        <p>Belum ada rekening admin</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
