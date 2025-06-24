@extends('layouts.app')

@section('title', 'Kelola Order Pembelian')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl p-8 text-white">
        <h1 class="text-3xl font-bold mb-2">Kelola Order Pembelian</h1>
        <p class="text-green-100">Kelola semua order pembelian saldo PayPal/Skrill dari customer</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                <select name="currency" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Currency</option>
                    <option value="paypal" {{ request('currency') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                    <option value="skrill" {{ request('currency') === 'skrill' ? 'selected' : '' }}>Skrill</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Order code, nama, email..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Daftar Order Pembelian</h2>
        </div>
        
        @if($orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembelian</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $order->order_code }}</div>
                                <div class="text-sm text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $order->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 {{ $order->to_currency === 'paypal' ? 'bg-blue-500' : 'bg-red-500' }} rounded-lg flex items-center justify-center mr-3">
                                        <i class="fab {{ $order->to_currency === 'paypal' ? 'fa-paypal' : 'fa-skrill' }} text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">${{ number_format($order->to_amount, 2) }} {{ ucfirst($order->to_currency) }}</div>
                                        <div class="text-sm text-gray-500">via {{ $order->paymentMethod->name }}</div>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.purchase-orders.show', $order->id) }}" class="text-green-600 hover:text-green-900 font-medium">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada order pembelian</h3>
                <p class="text-gray-500">Order pembelian akan muncul di sini</p>
            </div>
        @endif
    </div>
</div>
@endsection
