@extends('layouts.admin')

@section('title', 'Kelola Transaksi')
@section('page-title', 'Kelola Transaksi')
@section('page-description', 'Lihat dan proses semua order konversi')

@section('content')
<!-- Filter Section -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <form method="GET" action="{{ route('admin.orders') }}" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        
        <div class="flex-1 min-w-48">
            <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
            <select name="currency" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Currency</option>
                <option value="paypal" {{ request('currency') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                <option value="skrill" {{ request('currency') === 'skrill' ? 'selected' : '' }}>Skrill</option>
            </select>
        </div>
        
        <div class="flex-1 min-w-48">
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Order code, email, nama..." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
            <a href="{{ route('admin.orders') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                <i class="fas fa-times mr-2"></i>Reset
            </a>
        </div>
    </form>
</div>

<!-- Orders Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Konversi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $order->order_code }}</div>
                        <div class="text-sm text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $order->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                        <div class="text-sm text-gray-500">{{ $order->user->phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 {{ $order->from_currency === 'paypal' ? 'bg-blue-500' : 'bg-red-500' }} rounded-lg flex items-center justify-center mr-3">
                                <i class="fas {{ $order->from_currency === 'paypal' ? 'fa-paypal' : 'fa-wallet' }} text-white text-sm"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ ucfirst($order->from_currency) }} ${{ number_format($order->from_amount, 2) }}</div>
                                <div class="text-sm text-gray-500">ke {{ $order->paymentMethod->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->status_badge }}">
                            {{ $order->status_text }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye mr-1"></i>Detail
                        </a>
                        <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus order ini?')">
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
                        <i class="fas fa-inbox text-4xl mb-4"></i>
                        <p>Tidak ada transaksi ditemukan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
