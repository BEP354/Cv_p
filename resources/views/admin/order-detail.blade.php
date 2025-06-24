@extends('layouts.app')

@section('title', 'Detail Order Konversi - Admin')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Order #{{ $order->order_code }}</h1>
                <p class="text-gray-600">Transaksi Jual {{ ucfirst($order->from_currency) }}</p>
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
                    <i class="fas fa-exchange-alt mr-2 text-green-500"></i>
                    Detail Transaksi
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Dari:</span>
                        <span class="font-semibold">${{ number_format($order->from_amount, 2) }} {{ ucfirst($order->from_currency) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Rate:</span>
                        <span class="font-semibold">Rp {{ number_format($order->rate, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Gross IDR:</span>
                        <span class="font-semibold">Rp {{ number_format($order->gross_idr, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fee:</span>
                        <span class="font-semibold text-red-600">- Rp {{ number_format($order->fee_amount + $order->admin_fee, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t pt-3">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold">Total Transfer:</span>
                            <span class="text-xl font-bold text-green-600">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recipient Info -->
            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="font-semibold mb-4 text-gray-800 flex items-center">
                    <i class="fas fa-user mr-2 text-purple-500"></i>
                    Data Penerima
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nama:</span>
                        <span class="font-semibold">{{ $order->recipient_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Rekening/HP:</span>
                        <span class="font-semibold">{{ $order->recipient_account }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Metode:</span>
                        <span class="font-semibold">{{ $order->paymentMethod->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email Pengirim:</span>
                        <span class="font-semibold">{{ $order->sender_email }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Account Info -->
    @if($adminAccount)
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-8">
        <h3 class="text-xl font-semibold mb-6 text-blue-800 flex items-center">
            <i class="fas fa-university mr-2"></i>
            Rekening Admin untuk Transfer
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
                <h4 class="font-semibold text-gray-800 mb-4">Instruksi Transfer</h4>
                <div class="space-y-2 text-sm text-gray-700">
                    <p>• Transfer tepat sebesar: <strong>Rp {{ number_format($order->total_idr, 0, ',', '.') }}</strong></p>
                    <p>• Ke rekening: <strong>{{ $order->recipient_name }}</strong></p>
                    <p>• Nomor: <strong>{{ $order->recipient_account }}</strong></p>
                    <p>• Bank/E-wallet: <strong>{{ $order->paymentMethod->name }}</strong></p>
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
                <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}">
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
                <h4 class="font-semibold mb-4">Upload Bukti Transfer (Admin)</h4>
                <form method="POST" action="{{ route('admin.orders.upload-proof', $order->id) }}" enctype="multipart/form-data">
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
