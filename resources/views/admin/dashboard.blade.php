@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Overview platform convert PayPal/Skrill')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
    @php
        $stats = [
            [
                'title' => 'Total Users',
                'value' => $totalUsers,
                'icon' => 'fas fa-users',
                'color' => 'blue',
                'bg' => 'bg-blue-100',
                'iconColor' => 'text-blue-600'
            ],
            [
                'title' => 'Total Orders',
                'value' => $totalOrders,
                'icon' => 'fas fa-shopping-cart',
                'color' => 'green',
                'bg' => 'bg-green-100',
                'iconColor' => 'text-green-600'
            ],
            [
                'title' => 'Pending',
                'value' => $pendingOrders,
                'icon' => 'fas fa-clock',
                'color' => 'yellow',
                'bg' => 'bg-yellow-100',
                'iconColor' => 'text-yellow-600'
            ],
            [
                'title' => 'Processing',
                'value' => $processingOrders,
                'icon' => 'fas fa-spinner',
                'color' => 'purple',
                'bg' => 'bg-purple-100',
                'iconColor' => 'text-purple-600'
            ],
            [
                'title' => 'Success',
                'value' => $successOrders,
                'icon' => 'fas fa-check-circle',
                'color' => 'emerald',
                'bg' => 'bg-emerald-100',
                'iconColor' => 'text-emerald-600'
            ],
        ];
    @endphp
    @foreach ($stats as $stat)
    <div class="bg-gradient-to-br from-{{ $stat['color'] }}-50 to-white rounded-xl shadow-lg transition hover:scale-105 hover:shadow-2xl p-6 border-t-4 border-{{ $stat['color'] }}-400 hover:border-{{ $stat['color'] }}-600 duration-200">
        <div class="flex items-center">
            <div class="w-12 h-12 {{ $stat['bg'] }} rounded-lg flex items-center justify-center shadow-inner">
                <i class="{{ $stat['icon'] }} text-2xl {{ $stat['iconColor'] }}"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-600 font-medium">{{ $stat['title'] }}</p>
                <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ $stat['value'] }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <a href="{{ route('admin.orders') }}" class="group bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow hover:scale-105 duration-200 relative overflow-hidden">
        <span class="absolute right-0 top-0 opacity-10 text-8xl font-black text-blue-100 pointer-events-none">🛒</span>
        <div class="flex items-center z-10 relative">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                <i class="fas fa-shopping-cart text-2xl text-blue-600"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-gray-800">Kelola Transaksi</h3>
                <p class="text-gray-600 text-sm">{{ $pendingOrders }} pending</p>
            </div>
        </div>
    </a>
    
    <a href="{{ route('admin.users') }}" class="group bg-gradient-to-br from-green-50 to-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow hover:scale-105 duration-200 relative overflow-hidden">
        <span class="absolute right-0 top-0 opacity-10 text-8xl font-black text-green-100 pointer-events-none">👥</span>
        <div class="flex items-center z-10 relative">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                <i class="fas fa-users text-2xl text-green-600"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-gray-800">Kelola User</h3>
                <p class="text-gray-600 text-sm">{{ $totalUsers }} users</p>
            </div>
        </div>
    </a>
    
    <a href="{{ route('admin.rates') }}" class="group bg-gradient-to-br from-purple-50 to-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow hover:scale-105 duration-200 relative overflow-hidden">
        <span class="absolute right-0 top-0 opacity-10 text-8xl font-black text-purple-100 pointer-events-none">💹</span>
        <div class="flex items-center z-10 relative">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                <i class="fas fa-chart-line text-2xl text-purple-600"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-gray-800">Kelola Kurs</h3>
                <p class="text-gray-600 text-sm">Update rates</p>
            </div>
        </div>
    </a>
    
    <a href="{{ route('admin.accounts') }}" class="group bg-gradient-to-br from-orange-50 to-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow hover:scale-105 duration-200 relative overflow-hidden">
        <span class="absolute right-0 top-0 opacity-10 text-8xl font-black text-orange-100 pointer-events-none">🏦</span>
        <div class="flex items-center z-10 relative">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                <i class="fas fa-university text-2xl text-orange-600"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-gray-800">Kelola Rekening</h3>
                <p class="text-gray-600 text-sm">Admin accounts</p>
            </div>
        </div>
    </a>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-xl shadow-lg p-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-900">Transaksi Terbaru</h3>
            <p class="text-gray-600">Order yang perlu diproses</p>
        </div>
        <a href="{{ route('admin.orders') }}" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
            Lihat Semua <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
    <div class="overflow-x-auto rounded-lg border border-gray-100">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-blue-100 to-blue-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-widest">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-widest">User</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-widest">Konversi</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-widest">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentOrders as $order)
                <tr class="hover:bg-blue-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-base font-semibold text-gray-900">{{ $order->order_code }}</div>
                        <div class="text-xs text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-base font-bold text-gray-800">{{ $order->user->name }}</div>
                        <div class="text-xs text-gray-500">{{ $order->user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 {{ $order->from_currency === 'paypal' ? 'bg-blue-500' : 'bg-red-500' }} rounded-lg flex items-center justify-center mr-3 shadow">
                                <i class="fas {{ $order->from_currency === 'paypal' ? 'fa-paypal' : 'fa-wallet' }} text-white text-sm"></i>
                            </div>
                            <div>
                                <div class="text-base font-semibold text-gray-900">{{ ucfirst($order->from_currency) }} ${{ number_format($order->from_amount, 2) }}</div>
                                <div class="text-xs text-gray-500">ke {{ $order->paymentMethod->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-base font-bold text-emerald-600">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->status_badge }} shadow-sm">
                            {{ $order->status_text }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-base font-medium">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-900 font-semibold flex items-center">
                            <i class="fas fa-eye mr-2"></i>Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-400">Belum ada transaksi terbaru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection