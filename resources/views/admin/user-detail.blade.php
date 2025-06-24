@extends('layouts.admin')

@section('title', 'Detail User')
@section('page-title', 'Detail User: ' . $user->name)
@section('page-description', 'Informasi lengkap user dan riwayat transaksi')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.users') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar User
        </a>
    </div>

    <!-- User Info -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-gray-600">{{ $user->email }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                </span>
                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Personal Info -->
            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-user mr-2 text-blue-500"></i>
                    Informasi Personal
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nama:</span>
                        <span class="font-semibold">{{ $user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-semibold">{{ $user->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Phone:</span>
                        <span class="font-semibold">{{ $user->phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Bergabung:</span>
                        <span class="font-semibold">{{ $user->formatted_created_at }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email Verified:</span>
                        <span class="font-semibold {{ $user->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->formatted_email_verified_at ?? 'Belum Verifikasi' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Account Stats -->
            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
                    Statistik Akun
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Orders:</span>
                        <span class="font-semibold">{{ $user->conversion_orders_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Orders Success:</span>
                        <span class="font-semibold text-green-600">{{ $user->conversionOrders->where('status', 'success')->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Orders Pending:</span>
                        <span class="font-semibold text-yellow-600">{{ $user->conversionOrders->where('status', 'pending')->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Volume:</span>
                        <span class="font-semibold text-blue-600">Rp {{ number_format($user->conversionOrders->where('status', 'success')->sum('total_idr'), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
            <i class="fas fa-cogs mr-2 text-blue-500"></i>
            Kelola User
        </h3>
        
        <div class="flex flex-wrap gap-3">
            <!-- Toggle Status -->
            <form method="POST" action="{{ route('admin.users.toggle-status', $user->id) }}" class="inline">
                @csrf
                <button type="submit" class="bg-{{ $user->is_active ? 'red' : 'green' }}-600 text-white px-4 py-2 rounded-lg hover:bg-{{ $user->is_active ? 'red' : 'green' }}-700 transition-colors">
                    <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} mr-2"></i>
                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} User
                </button>
            </form>

            <!-- Make Admin -->
            @if($user->role !== 'admin')
            <form method="POST" action="{{ route('admin.users.make-admin', $user->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menjadikan user ini sebagai admin?')">
                @csrf
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-crown mr-2"></i>Jadikan Admin
                </button>
            </form>
            @endif

            <!-- View Orders -->
            <a href="{{ route('admin.orders', ['user_id' => $user->id]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-list mr-2"></i>Lihat Orders
            </a>
        </div>
    </div>

    <!-- Recent Orders -->
    @if($user->conversionOrders->count() > 0)
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
            <i class="fas fa-history mr-2 text-blue-500"></i>
            Riwayat Transaksi Terbaru
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Order Code</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">From</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">To</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Amount</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Date</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($user->conversionOrders->take(10) as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-blue-600">{{ $order->order_code }}</td>
                        <td class="px-4 py-3">{{ ucfirst($order->from_currency) }} ${{ number_format($order->from_amount, 2) }}</td>
                        <td class="px-4 py-3">{{ $order->paymentMethod->name }}</td>
                        <td class="px-4 py-3 font-semibold">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $order->status_badge }}">
                                {{ $order->status_text }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $order->formatted_created_at }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
