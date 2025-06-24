@extends('layouts.admin')

@section('title', 'Kelola Exchange Rate - Admin Dashboard')
@section('page-title', 'Kelola Exchange Rate')
@section('page-description', 'Update kurs konversi PayPal/Skrill ke berbagai metode pembayaran')

@push('styles')
<style>
    .rate-card {
        background: linear-gradient(145deg, #ffffff, #f8fafc);
        border-radius: 20px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        border-left: 5px solid transparent;
    }
    
    .rate-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }
    
    .method-icon {
        width: 45px;
        height: 45px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    
    .currency-badge {
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        font-size: 0.875rem;
    }
    
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .edit-input {
        width: 100%;
        padding: 0.5rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }
    
    .edit-input:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-3xl p-8 text-white relative overflow-hidden">
        <div class="relative z-10">
            <h1 class="text-4xl font-bold mb-3 flex items-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mr-4">
                    <i class="fas fa-exchange-alt text-2xl"></i>
                </div>
                Exchange Rate Management
            </h1>
            <p class="text-xl text-blue-100">Kelola kurs konversi dan metode pembayaran berdasarkan rekening admin</p>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="rate-card" style="border-left-color: #3b82f6;">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold text-gray-800">{{ $rates->count() }}</div>
                <div class="text-sm text-gray-500 font-medium">Total Rates</div>
            </div>
            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-exchange-alt text-white"></i>
            </div>
        </div>
    </div>
    
    <div class="rate-card" style="border-left-color: #10b981;">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold text-gray-800">{{ $rates->where('is_active', true)->count() }}</div>
                <div class="text-sm text-gray-500 font-medium">Active Rates</div>
            </div>
            <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-white"></i>
            </div>
        </div>
    </div>
    
    <div class="rate-card" style="border-left-color: #8b5cf6;">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold text-gray-800">{{ $accounts->where('is_active', true)->count() }}</div>
                <div class="text-sm text-gray-500 font-medium">Active Accounts</div>
            </div>
            <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-university text-white"></i>
            </div>
        </div>
    </div>
    
    <div class="rate-card" style="border-left-color: #f59e0b;">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold text-gray-800">{{ $paymentMethods->count() }}</div>
                <div class="text-sm text-gray-500 font-medium">Payment Methods</div>
            </div>
            <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-credit-card text-white"></i>
            </div>
        </div>
    </div>
</div>

<!-- Add New Payment Method Form -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
    <h3 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
        <i class="fas fa-plus mr-3 text-green-500"></i>
        Tambah Metode Pembayaran Baru
    </h3>
    
    <form method="POST" action="{{ route('admin.payment-methods.store') }}" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-tag text-blue-500 mr-2"></i>Nama Metode
                </label>
                <input type="text" name="name" required placeholder="Bank BCA, DANA, dll" 
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-blue-500 transition-colors">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-code text-purple-500 mr-2"></i>Kode
                </label>
                <input type="text" name="code" required placeholder="BCA, DANA, dll" 
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-blue-500 transition-colors">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-layer-group text-indigo-500 mr-2"></i>Tipe
                </label>
                <select name="type" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-blue-500 transition-colors">
                    <option value="">Pilih Tipe</option>
                    <option value="ewallet">💳 E-Wallet</option>
                    <option value="bank">🏦 Bank</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-arrow-down text-green-500 mr-2"></i>Min Amount
                </label>
                <input type="number" name="min_amount" step="1000" min="0" required placeholder="50000" 
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-blue-500 transition-colors">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-arrow-up text-red-500 mr-2"></i>Max Amount
                </label>
                <input type="number" name="max_amount" step="1000" min="0" required placeholder="50000000" 
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-blue-500 transition-colors">
            </div>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="btn-action bg-green-600 text-white hover:bg-green-700">
                <i class="fas fa-save"></i>Tambah Metode
            </button>
        </div>
    </form>
</div>

<!-- Add New Rate Form -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
    <h3 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
        <i class="fas fa-plus mr-3 text-blue-500"></i>
        Tambah Exchange Rate Baru
    </h3>
    
    <form method="POST" action="{{ route('admin.rates.store') }}" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-coins text-yellow-500 mr-2"></i>Dari Currency
                </label>
                <select name="from_currency" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-blue-500 transition-colors">
                    <option value="">Pilih Currency</option>
                    <option value="paypal">💙 PayPal</option>
                    <option value="skrill">❤️ Skrill</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-arrow-right text-green-500 mr-2"></i>Ke Metode
                </label>
                <select name="to_method_id" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-blue-500 transition-colors">
                    <option value="">Pilih Metode</option>
                    @foreach($paymentMethods->groupBy('type') as $type => $methods)
                        <optgroup label="{{ $type === 'ewallet' ? '💳 E-Wallet' : '🏦 Bank' }}">
                            @foreach($methods as $method)
                                <option value="{{ $method->id }}">
                                    {{ $method->name }} 
                                    {{ !$method->is_active ? '(Tidak Aktif)' : '' }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>Rate (IDR)
                </label>
                <input type="number" name="rate" step="1" min="1" required placeholder="15500" 
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-blue-500 transition-colors">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-percentage text-orange-500 mr-2"></i>Fee (%)
                </label>
                <input type="number" name="fee_percentage" step="0.0001" min="0" max="1" required placeholder="0.03" 
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-blue-500 transition-colors">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-hand-holding-usd text-purple-500 mr-2"></i>Admin Fee
                </label>
                <input type="number" name="admin_fee" step="1000" min="0" required placeholder="5000" 
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-blue-500 transition-colors">
            </div>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="btn-action bg-blue-600 text-white hover:bg-blue-700">
                <i class="fas fa-save"></i>Tambah Rate
            </button>
        </div>
    </form>
</div>

<!-- Admin Accounts Reference -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
    <h3 class="text-xl font-bold mb-6 text-gray-800 flex items-center">
        <i class="fas fa-university mr-3 text-purple-500"></i>
        Rekening Admin Tersedia
        <span class="ml-auto text-sm font-normal text-gray-500">Basis untuk metode pembayaran</span>
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($accounts->where('is_active', true) as $account)
        <div class="border-2 border-gray-200 rounded-xl p-4 hover:border-blue-300 transition-colors">
            <div class="flex items-center mb-3">
                <div class="method-icon {{ $account->type === 'paypal' ? 'bg-blue-500' : ($account->type === 'skrill' ? 'bg-red-500' : ($account->type === 'bank' ? 'bg-green-500' : 'bg-purple-500')) }}">
                    <i class="fas {{ $account->type === 'paypal' ? 'fa-paypal' : ($account->type === 'skrill' ? 'fa-wallet' : ($account->type === 'bank' ? 'fa-university' : 'fa-mobile-alt')) }}"></i>
                </div>
                <div class="ml-3">
                    <div class="font-bold text-gray-900">{{ $account->name }}</div>
                    <div class="text-sm text-gray-500">{{ ucfirst($account->type) }}</div>
                </div>
            </div>
            <div class="text-sm text-gray-600">
                <div><strong>Rekening:</strong> {{ $account->account_number }}</div>
                <div><strong>Pemilik:</strong> {{ $account->account_name }}</div>
                @if($account->notes)
                <div class="mt-2 text-xs text-gray-500">{{ $account->notes }}</div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-8 text-gray-500">
            <i class="fas fa-university text-4xl mb-4"></i>
            <p>Belum ada rekening admin aktif</p>
            <a href="{{ route('admin.accounts') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                Tambah rekening admin →
            </a>
        </div>
        @endforelse
    </div>
</div>

<!-- Payment Methods Table -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
        <h3 class="text-xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-credit-card mr-3 text-green-500"></i>
            Kelola Metode Pembayaran
        </h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($paymentMethods as $method)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="method-icon {{ $method->type === 'ewallet' ? 'bg-green-500' : 'bg-blue-500' }}">
                                <i class="fas {{ $method->type === 'ewallet' ? 'fa-mobile-alt' : 'fa-university' }}"></i>
                            </div>
                            <div class="ml-4">
                                <div class="font-bold text-gray-900">{{ $method->name }}</div>
                                <div class="text-sm text-gray-500">{{ $method->code }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="currency-badge {{ $method->type === 'ewallet' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            <i class="fas {{ $method->type === 'ewallet' ? 'fa-mobile-alt' : 'fa-university' }}"></i>
                            {{ $method->type === 'ewallet' ? 'E-Wallet' : 'Bank' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-mono text-sm text-gray-900">
                            Rp {{ number_format($method->min_amount, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-mono text-sm text-gray-900">
                            Rp {{ number_format($method->max_amount, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <form method="POST" action="{{ route('admin.payment-methods.toggle', $method->id) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="currency-badge {{ $method->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }} transition-colors">
                                <i class="fas {{ $method->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                {{ $method->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <form method="POST" action="{{ route('admin.payment-methods.destroy', $method->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus metode ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action bg-red-100 text-red-700 hover:bg-red-200">
                                <i class="fas fa-trash"></i>
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-credit-card text-4xl mb-4"></i>
                        <p class="text-lg">Belum ada metode pembayaran</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Exchange Rates Table -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden" x-data>
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
        <h3 class="text-xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-exchange-alt mr-3 text-blue-500"></i>
            Exchange Rates Management
        </h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dari</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ke</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate (IDR)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee (%)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin Fee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($rates as $rate)
                <tr x-data="{ editing: false, rate: {{ $rate->rate }}, feePercentage: {{ $rate->fee_percentage }}, adminFee: {{ $rate->admin_fee }} }" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="method-icon {{ $rate->from_currency === 'paypal' ? 'bg-blue-500' : 'bg-red-500' }}">
                                <i class="fab {{ $rate->from_currency === 'paypal' ? 'fa-paypal' : 'fa-skrill' }}"></i>
                            </div>
                            <div class="ml-4">
                                <span class="font-bold text-gray-900">{{ strtoupper($rate->from_currency) }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="method-icon {{ $rate->paymentMethod->type === 'ewallet' ? 'bg-green-500' : 'bg-blue-500' }}">
                                <i class="fas {{ $rate->paymentMethod->type === 'ewallet' ? 'fa-mobile-alt' : 'fa-university' }}"></i>
                            </div>
                            <div class="ml-4">
                                <div class="font-bold text-gray-900">{{ $rate->paymentMethod->name }}</div>
                                @if(!$rate->paymentMethod->is_active)
                                    <div class="text-xs text-red-500 font-medium">⚠️ Metode Tidak Aktif</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div x-show="!editing" class="flex items-center">
                            <span class="font-mono text-lg font-bold text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(rate)"></span>
                            <button type="button" @click="editing = true" class="ml-3 btn-action bg-blue-100 text-blue-700 hover:bg-blue-200">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <div x-show="editing" class="flex items-center">
                            <input type="number" x-model="rate" step="1" min="1" required class="edit-input">
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div x-show="!editing" class="flex items-center">
                            <span class="font-mono text-lg font-bold text-gray-900" x-text="(feePercentage * 100).toFixed(2) + '%'"></span>
                        </div>
                        <div x-show="editing" class="flex items-center">
                            <input type="number" x-model="feePercentage" step="0.0001" min="0" max="1" required class="edit-input">
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div x-show="!editing" class="flex items-center">
                            <span class="font-mono text-lg font-bold text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(adminFee)"></span>
                        </div>
                        <div x-show="editing" class="flex items-center">
                            <input type="number" x-model="adminFee" step="1000" min="0" required class="edit-input">
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <form method="POST" action="{{ route('admin.rates.toggle', $rate->id) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="currency-badge {{ $rate->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }} transition-colors">
                                <i class="fas {{ $rate->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                {{ $rate->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div x-show="!editing" class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <button type="button" @click="editing = true" class="btn-action bg-blue-100 text-blue-700 hover:bg-blue-200">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.rates.destroy', $rate->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus rate ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action bg-red-100 text-red-700 hover:bg-red-200">
                                        <i class="fas fa-trash"></i>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                            <div class="text-xs text-gray-500">
                                <div><strong>Update:</strong> {{ $rate->updated_at->format('d M Y H:i') }}</div>
                                @if($rate->updatedBy)
                                    <div><strong>Oleh:</strong> {{ $rate->updatedBy->name }}</div>
                                @endif
                            </div>
                        </div>
                        <div x-show="editing" class="flex items-center space-x-2">
                            <form method="POST" action="{{ route('admin.rates.update', $rate->id) }}" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="rate" :value="rate">
                                <input type="hidden" name="fee_percentage" :value="feePercentage">
                                <input type="hidden" name="admin_fee" :value="adminFee">
                                <button type="submit" class="btn-action bg-green-100 text-green-700 hover:bg-green-200">
                                    <i class="fas fa-check"></i>
                                    Save
                                </button>
                            </form>
                            <button type="button" @click="editing = false; rate = {{ $rate->rate }}; feePercentage = {{ $rate->fee_percentage }}; adminFee = {{ $rate->admin_fee }}" class="btn-action bg-red-100 text-red-700 hover:bg-red-200">
                                <i class="fas fa-times"></i>
                                Cancel
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-exchange-alt text-4xl mb-4"></i>
                        <p class="text-lg">Belum ada exchange rate</p>
                        <p class="text-sm">Tambah rate baru menggunakan form di atas</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh rates every 5 minutes
    setInterval(function() {
        console.log('Auto-refreshing rates...');
        // You can add AJAX refresh logic here if needed
    }, 300000);
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi');
            }
        });
    });
});
</script>
@endpush
