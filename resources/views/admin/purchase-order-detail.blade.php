@extends('layouts.app')

@section('title', 'Detail Order Pembelian - Admin')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Order #{{ $order->order_code }}</h1>
                <p class="text-gray-600">Transaksi Beli {{ ucfirst($order->to_currency) }}</p>
            </div>
            <div>
                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $order->status_badge }}">
                    {{ $order->status_text }}
                </span>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Info -->
            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Informasi Order
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Order Code:</span>
                        <span class="font-semibold">{{ $order->order_code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">User:</span>
                        <span class="font-semibold">{{ $order->user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-semibold">{{ $order->user->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal:</span>
                        <span class="font-semibold">{{ $order->created_at->format('d M Y H:i') }}</span>
                    </div>
                    @if($order->completed_at)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Selesai:</span>
                        <span class="font-semibold">{{ $order->completed_at->format('d M Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Transaction Details -->
            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-shopping-cart mr-2 text-green-500"></i>
                    Detail Pembelian
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Saldo Dibeli:</span>
                        <span class="font-semibold">${{ number_format($order->to_amount, 2) }} {{ ucfirst($order->to_currency) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Rate:</span>
                        <span class="font-semibold">Rp {{ number_format($order->rate, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-semibold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fee:</span>
                        <span class="font-semibold text-orange-600">+ Rp {{ number_format($order->fee_amount + $order->admin_fee, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t pt-3">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold">Total Bayar:</span>
                            <span class="text-xl font-bold text-blue-600">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recipient Info -->
            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-envelope mr-2 text-purple-500"></i>
                    Data Penerima Saldo
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email Penerima:</span>
                        <span class="font-semibold">{{ $order->recipient_email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Metode Bayar:</span>
                        <span class="font-semibold">{{ $order->paymentMethod->name }}</span>
                    </div>
                    @if($order->notes)
                    <div class="mt-3">
                        <span class="text-gray-600">Catatan:</span>
                        <p class="font-semibold mt-1">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Account Info -->
    @if($adminAccount)
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-8">
        <h3 class="text-xl font-semibold mb-6 text-green-800 flex items-center">
            <i class="fas fa-university mr-2"></i>
            Rekening Admin untuk Menerima Pembayaran
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <h4 class="font-semibold text-gray-800 mb-4">{{ $adminAccount->name }}</h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nama Penerima:</span>
                        <span class="font-semibold">{{ $adminAccount->account_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nomor Rekening:</span>
                        <span class="font-semibold font-mono">{{ $adminAccount->account_number }}</span>
                    </div>
                    @if($adminAccount->notes)
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            {{ $adminAccount->notes }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <h4 class="font-semibold text-gray-800 mb-4">User Harus Transfer</h4>
                <div class="space-y-2 text-sm text-gray-700">
                    <p>• Jumlah: <strong>Rp {{ number_format($order->total_idr, 0, ',', '.') }}</strong></p>
                    <p>• Ke rekening: <strong>{{ $adminAccount->account_name }}</strong></p>
                    <p>• Nomor: <strong>{{ $adminAccount->account_number }}</strong></p>
                    <p>• Bank/E-wallet: <strong>{{ $adminAccount->name }}</strong></p>
                    <p>• Setelah transfer, kirim saldo ke: <strong>{{ $order->recipient_email }}</strong></p>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="bg-red-50 border border-red-200 rounded-2xl p-8">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
            <div>
                <h3 class="font-semibold text-red-800">Rekening Admin Tidak Ditemukan</h3>
                <p class="text-red-700">Belum ada rekening admin yang aktif untuk metode pembayaran {{ $order->paymentMethod->name }}. Silakan tambahkan di menu Admin Accounts.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Payment Proof -->
    @if($order->payment_proof)
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h3 class="text-xl font-semibold mb-6 text-gray-800 flex items-center">
            <i class="fas fa-image mr-2 text-blue-500"></i>
            Bukti Pembayaran dari User
        </h3>
        <div class="text-center">
            <img src="{{ asset($order->payment_proof) }}" alt="Bukti Pembayaran" class="max-w-md mx-auto rounded-lg shadow-md">
        </div>
    </div>
    @endif

    <!-- Admin Actions -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h3 class="text-xl font-semibold mb-6 text-gray-800 flex items-center">
            <i class="fas fa-cogs mr-2 text-blue-500"></i>
            Aksi Admin
        </h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Update Status -->
            <div>
                <h4 class="font-semibold mb-4">Update Status Order</h4>
                <form method="POST" action="{{ route('admin.purchase-orders.update-status', $order->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="success" {{ $order->status === 'success' ? 'selected' : '' }}>Success</option>
                            <option value="failed" {{ $order->status === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <textarea name="admin_notes" rows="3" placeholder="Catatan admin (opsional)"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $order->admin_notes }}</textarea>
                    </div>
                    
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-all">
                        <i class="fas fa-save mr-2"></i>Update Status
                    </button>
                </form>
            </div>
            
            <!-- Upload Proof -->
            @if(in_array($order->status, ['pending', 'processing']))
            <div>
                <h4 class="font-semibold mb-4">Upload Bukti Kirim Saldo (Admin)</h4>
                <form method="POST" action="{{ route('admin.purchase-orders.upload-proof', $order->id) }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <input type="file" name="payment_proof" accept="image/*" required
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    
                    <div class="mb-4">
                        <textarea name="upload_notes" rows="2" placeholder="Catatan upload (opsional)"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition-all">
                        <i class="fas fa-upload mr-2"></i>Upload Bukti
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    @if($order->admin_notes)
    <!-- Admin Notes -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-8">
        <h3 class="text-xl font-semibold mb-4 text-blue-800 flex items-center">
            <i class="fas fa-comment mr-2"></i>
            Catatan Admin
        </h3>
        <p class="text-blue-800 whitespace-pre-line">{{ $order->admin_notes }}</p>
    </div>
    @endif
</div>
@endsection
