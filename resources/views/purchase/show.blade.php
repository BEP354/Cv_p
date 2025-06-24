@extends('layouts.app')

@section('title', 'Detail Pembelian - ' . $order->order_code)

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white bg-opacity-10 rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white bg-opacity-10 rounded-full -ml-12 -mb-12"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-shopping-cart text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold mb-1">Detail Pembelian</h1>
                            <p class="text-green-100 text-lg">Order: {{ $order->order_code }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                            <div class="text-green-200 text-sm">Tanggal Order</div>
                            <div class="text-white font-semibold">{{ $order->created_at->format('d M Y, H:i') }}</div>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                            <div class="text-green-200 text-sm">Saldo Dibeli</div>
                            <div class="text-white font-semibold">${{ number_format($order->to_amount, 2) }} {{ ucfirst($order->to_currency) }}</div>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-green-200 mb-2">Status Pembelian</div>
                    <span class="px-6 py-3 rounded-full text-lg font-bold {{ $order->status_badge }} shadow-lg">
                        {{ $order->status_text }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Left Column - Order & Payment Details -->
        <div class="xl:col-span-2 space-y-8">
            <!-- Order Summary Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-file-invoice-dollar text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Ringkasan Pembelian</h2>
                        <p class="text-gray-600">Detail lengkap transaksi Anda</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="text-sm text-gray-500 mb-1">Saldo yang Dibeli</div>
                            <div class="text-2xl font-bold text-gray-900 flex items-center">
                                <div class="w-8 h-8 {{ $order->to_currency === 'paypal' ? 'bg-blue-500' : 'bg-red-500' }} rounded-lg flex items-center justify-center mr-3">
                                    <i class="fab {{ $order->to_currency === 'paypal' ? 'fa-paypal' : 'fa-skrill' }} text-white text-sm"></i>
                                </div>
                                ${{ number_format($order->to_amount, 2) }} {{ ucfirst($order->to_currency) }}
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="text-sm text-gray-500 mb-1">Email Penerima</div>
                            <div class="font-semibold text-gray-900">{{ $order->recipient_email }}</div>
                        </div>
                        
                        @if($order->notes)
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="text-sm text-gray-500 mb-1">Catatan</div>
                            <div class="font-semibold text-gray-900">{{ $order->notes }}</div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="space-y-4">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-200">
                            <div class="text-sm text-green-600 mb-1">Total Pembayaran</div>
                            <div class="text-3xl font-bold text-green-700">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="text-sm text-gray-500 mb-2">Rincian Biaya</div>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Subtotal:</span>
                                    <span class="font-semibold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Fee ({{ number_format($order->fee_percentage * 100, 2) }}%):</span>
                                    <span class="font-semibold text-orange-600">+ Rp {{ number_format($order->fee_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Admin Fee:</span>
                                    <span class="font-semibold text-orange-600">+ Rp {{ number_format($order->admin_fee, 0, ',', '.') }}</span>
                                </div>
                                <div class="border-t pt-2 flex justify-between font-bold">
                                    <span>Total:</span>
                                    <span class="text-green-600">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Instructions Card -->
            @php
                $adminAccount = \App\Models\AdminAccount::where('type', $order->paymentMethod->type === 'ewallet' ? 'ewallet' : 'bank')
                    ->where('name', 'LIKE', '%' . $order->paymentMethod->name . '%')
                    ->where('is_active', true)
                    ->first();
                
                if (!$adminAccount) {
                    // Fallback ke account pertama yang sesuai type
                    $adminAccount = \App\Models\AdminAccount::where('type', $order->paymentMethod->type === 'ewallet' ? 'ewallet' : 'bank')
                        ->where('is_active', true)
                        ->first();
                }
            @endphp

            @if($order->status === 'pending' && $adminAccount)
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-credit-card text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Instruksi Pembayaran</h2>
                        <p class="text-gray-600">Lakukan pembayaran ke rekening berikut</p>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas {{ $order->paymentMethod->type === 'ewallet' ? 'fa-mobile-alt' : 'fa-university' }} text-white"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">{{ $adminAccount->name }}</div>
                                <div class="text-sm text-gray-600">{{ $order->paymentMethod->name }}</div>
                            </div>
                        </div>
                        <button onclick="copyToClipboard('{{ $adminAccount->account_number }}')" 
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-600 transition-colors">
                            <i class="fas fa-copy mr-1"></i>Copy
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">{{ $order->paymentMethod->type === 'ewallet' ? 'Nomor HP/ID' : 'Nomor Rekening' }}</div>
                            <div class="text-xl font-bold text-gray-900 font-mono bg-white rounded-lg px-4 py-2 border">
                                {{ $adminAccount->account_number }}
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Nama Penerima</div>
                            <div class="text-xl font-bold text-gray-900 bg-white rounded-lg px-4 py-2 border">
                                {{ $adminAccount->account_name }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 bg-white rounded-lg p-4 border">
                        <div class="text-sm text-gray-600 mb-1">Jumlah Transfer</div>
                        <div class="text-2xl font-bold text-green-600 font-mono">
                            Rp {{ number_format($order->total_idr, 0, ',', '.') }}
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Transfer sesuai nominal exact untuk mempercepat verifikasi
                        </div>
                    </div>

                    @if($adminAccount->notes)
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-sticky-note text-yellow-500 mt-1 mr-2"></i>
                            <div>
                                <div class="font-semibold text-yellow-800 mb-1">Catatan Penting:</div>
                                <div class="text-yellow-700 text-sm">{{ $adminAccount->notes }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Payment Steps -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-list-ol mr-2 text-blue-500"></i>
                        Langkah Pembayaran
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">1</div>
                            <div class="text-gray-700">Transfer ke rekening {{ $adminAccount->name }} dengan nominal <strong>Rp {{ number_format($order->total_idr, 0, ',', '.') }}</strong></div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">2</div>
                            <div class="text-gray-700">Screenshot bukti transfer</div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">3</div>
                            <div class="text-gray-700">Upload bukti pembayaran di form di bawah</div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">4</div>
                            <div class="text-gray-700">Tunggu verifikasi admin (maksimal 15 menit)</div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">5</div>
                            <div class="text-gray-700">Saldo {{ ucfirst($order->to_currency) }} akan dikirim ke email Anda</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Upload & Status -->
        <div class="space-y-8">
            <!-- Upload Proof Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-receipt text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Bukti Pembayaran</h2>
                        <p class="text-gray-600 text-sm">Upload screenshot transfer</p>
                    </div>
                </div>
                
                @if($order->status === 'pending')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-yellow-500 text-lg mr-3"></i>
                            <div>
                                <h3 class="font-semibold text-yellow-800">Menunggu Pembayaran</h3>
                                <p class="text-yellow-700 text-sm mt-1">Upload bukti transfer setelah melakukan pembayaran</p>
                            </div>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('purchase.upload-proof', $order->order_code) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Transfer</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition-colors">
                                <input type="file" name="payment_proof" accept="image/*" required
                                       class="hidden" id="file-upload" onchange="previewImage(this)">
                                <label for="file-upload" class="cursor-pointer">
                                    <div id="upload-placeholder">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                        <p class="text-gray-600 font-semibold">Klik untuk upload gambar</p>
                                        <p class="text-gray-500 text-sm mt-1">JPG, PNG maksimal 2MB</p>
                                    </div>
                                    <div id="image-preview" class="hidden">
                                        <img id="preview-img" class="max-w-full h-48 object-cover rounded-lg mx-auto">
                                        <p class="text-green-600 font-semibold mt-2">Gambar siap diupload</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-4 px-6 rounded-xl font-bold text-lg hover:shadow-lg transform hover:scale-105 transition-all">
                            <i class="fas fa-upload mr-2"></i>Upload Bukti Pembayaran
                        </button>
                    </form>
                    
                @elseif($order->payment_proof)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 text-lg mr-3"></i>
                            <div>
                                <h3 class="font-semibold text-green-800">Bukti Pembayaran Diterima</h3>
                                <p class="text-green-700 text-sm mt-1">Sedang diverifikasi oleh admin</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <img src="{{ asset($order->payment_proof) }}" alt="Bukti Pembayaran" 
                             class="max-w-full rounded-xl shadow-lg border mx-auto">
                        <p class="text-gray-600 text-sm mt-3">Bukti pembayaran yang diupload</p>
                    </div>
                @endif
                
                @if($order->admin_notes)
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex items-start">
                            <i class="fas fa-comment-alt text-blue-500 mt-1 mr-3"></i>
                            <div>
                                <h3 class="font-semibold text-blue-800 mb-1">Pesan dari Admin:</h3>
                                <p class="text-blue-700 text-sm">{{ $order->admin_notes }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Status Timeline -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-history mr-2 text-indigo-500"></i>
                    Status Timeline
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-center {{ $order->status === 'pending' ? 'text-yellow-600' : 'text-green-600' }}">
                        <div class="w-4 h-4 {{ $order->status === 'pending' ? 'bg-yellow-500' : 'bg-green-500' }} rounded-full mr-3"></div>
                        <div>
                            <div class="font-semibold">Order Dibuat</div>
                            <div class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                    
                    @if($order->status !== 'pending')
                    <div class="flex items-center {{ in_array($order->status, ['processing', 'success']) ? 'text-green-600' : 'text-gray-400' }}">
                        <div class="w-4 h-4 {{ in_array($order->status, ['processing', 'success']) ? 'bg-green-500' : 'bg-gray-300' }} rounded-full mr-3"></div>
                        <div>
                            <div class="font-semibold">Pembayaran Diterima</div>
                            <div class="text-sm text-gray-500">{{ $order->updated_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                    @endif
                    
                    @if($order->status === 'success')
                    <div class="flex items-center text-green-600">
                        <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                        <div>
                            <div class="font-semibold">Saldo Dikirim</div>
                            <div class="text-sm text-gray-500">{{ $order->completed_at ? $order->completed_at->format('d M Y, H:i') : 'Selesai' }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="text-center">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-8 py-4 bg-gray-600 text-white rounded-xl font-bold text-lg hover:bg-gray-700 transform hover:scale-105 transition-all shadow-lg">
            <i class="fas fa-arrow-left mr-3"></i>Kembali ke Dashboard
        </a>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        toast.innerHTML = '<i class="fas fa-check mr-2"></i>Nomor rekening berhasil disalin!';
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    });
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('upload-placeholder').classList.add('hidden');
            document.getElementById('image-preview').classList.remove('hidden');
            document.getElementById('preview-img').src = e.target.result;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
