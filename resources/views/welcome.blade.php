@extends('layouts.app')

@section('title', 'PayPal Skrill Converter - Platform Terpercaya Convert PayPal & Skrill')

@section('content')
<!-- Hero Section -->
<div class="relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-purple-50 -z-10"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                Convert PayPal & Skrill ke
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                    DANA, ShopeePay & Bank
                </span>
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                Platform terpercaya untuk convert saldo PayPal dan Skrill ke rekening Indonesia dengan rate terbaik dan proses super cepat
            </p>
            
            @guest
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-xl text-lg font-semibold hover:shadow-xl transform hover:scale-105 transition-all">
                    <i class="fas fa-rocket mr-2"></i>Mulai Convert Sekarang
                </a>
                <a href="{{ route('login') }}" class="bg-white text-gray-700 px-8 py-4 rounded-xl text-lg font-semibold border-2 border-gray-200 hover:border-blue-500 hover:text-blue-600 transition-all">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            </div>
            @else
            <div class="flex justify-center">
                <a href="{{ route('dashboard') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-xl text-lg font-semibold hover:shadow-xl transform hover:scale-105 transition-all">
                    <i class="fas fa-tachometer-alt mr-2"></i>Ke Dashboard
                </a>
            </div>
            @endguest
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Mengapa Pilih Kami?</h2>
            <p class="text-xl text-gray-600">Keunggulan yang membuat kami berbeda</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow">
                <div class="w-16 h-16 bg-gradient-to-r from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-shield-alt text-2xl text-white"></i>
                </div>
                <h3 class="text-xl font-semibold mb-4">100% Aman & Terpercaya</h3>
                <p class="text-gray-600">Transaksi dijamin aman dengan sistem keamanan berlapis dan track record terpercaya</p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-bolt text-2xl text-white"></i>
                </div>
                <h3 class="text-xl font-semibold mb-4">Proses Super Cepat</h3>
                <p class="text-gray-600">Dana masuk ke rekening dalam hitungan menit, tidak perlu menunggu lama</p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-400 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-chart-line text-2xl text-white"></i>
                </div>
                <h3 class="text-xl font-semibold mb-4">Rate Terbaik</h3>
                <p class="text-gray-600">Dapatkan kurs terbaik dan fee terendah untuk setiap transaksi convert</p>
            </div>
        </div>
    </div>
</div>

<!-- Payment Methods Section -->
<div class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Metode Pembayaran</h2>
            <p class="text-xl text-gray-600">Berbagai pilihan metode pembayaran yang didukung</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-5 gap-6 mb-12">
            <!-- From -->
            <div class="col-span-2 md:col-span-2">
                <h3 class="text-lg font-semibold text-center mb-4 text-gray-700">Dari</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl p-6 text-center shadow-md hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <i class="fab fa-paypal text-white text-xl"></i>
                        </div>
                        <p class="font-semibold text-gray-800">PayPal</p>
                        <p class="text-sm text-gray-500">USD</p>
                    </div>
                    
                    <div class="bg-white rounded-xl p-6 text-center shadow-md hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-wallet text-white text-xl"></i>
                        </div>
                        <p class="font-semibold text-gray-800">Skrill</p>
                        <p class="text-sm text-gray-500">USD</p>
                    </div>
                </div>
            </div>
            
            <!-- Arrow -->
            <div class="hidden md:flex items-center justify-center">
                <i class="fas fa-arrow-right text-3xl text-gray-400"></i>
            </div>
            
            <!-- To -->
            <div class="col-span-2 md:col-span-2">
                <h3 class="text-lg font-semibold text-center mb-4 text-gray-700">Ke</h3>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($paymentMethods->take(4) as $method)
                    <div class="bg-white rounded-lg p-4 text-center shadow-md hover:shadow-lg transition-shadow">
                        <div class="w-10 h-10 {{ $method->type === 'ewallet' ? 'bg-green-500' : 'bg-blue-500' }} rounded-lg flex items-center justify-center mx-auto mb-2">
                            <i class="fas {{ $method->type === 'ewallet' ? 'fa-mobile-alt' : 'fa-university' }} text-white"></i>
                        </div>
                        <p class="font-medium text-sm text-gray-800">{{ $method->name }}</p>
                    </div>
                    @endforeach
                </div>
                <p class="text-center text-sm text-gray-500 mt-2">+ {{ $paymentMethods->count() - 4 }} lainnya</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Success Section -->
<div class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Transaksi Berhasil Terbaru</h2>
            <p class="text-xl text-gray-600">Lihat transaksi yang baru saja berhasil diproses</p>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dari</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ke</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentSuccess as $success)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $success->order_code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 {{ $success->from_currency === 'PayPal' ? 'bg-blue-500' : 'bg-red-500' }} rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas {{ $success->from_currency === 'PayPal' ? 'fa-paypal' : 'fa-wallet' }} text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $success->from_currency }}</div>
                                        <div class="text-sm text-gray-500">${{ number_format($success->from_amount, 2) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $success->to_method }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-green-600">Rp {{ number_format($success->total_idr, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $success->user_initial }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($success->completed_at)->diffForHumans() }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4"></i>
                                <p>Belum ada transaksi berhasil</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- How it Works Section -->
<div class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Cara Kerja</h2>
            <p class="text-xl text-gray-600">Proses convert yang mudah dan cepat</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-2xl font-bold">1</div>
                <h3 class="text-xl font-semibold mb-4">Daftar Akun</h3>
                <p class="text-gray-600">Buat akun dengan data yang valid hanya dalam 1 menit</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-2xl font-bold">2</div>
                <h3 class="text-xl font-semibold mb-4">Buat Order</h3>
                <p class="text-gray-600">Pilih metode dan masukkan jumlah yang ingin diconvert</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-2xl font-bold">3</div>
                <h3 class="text-xl font-semibold mb-4">Transfer</h3>
                <p class="text-gray-600">Kirim PayPal/Skrill sesuai instruksi yang diberikan</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-2xl font-bold">4</div>
                <h3 class="text-xl font-semibold mb-4">Terima Dana</h3>
                <p class="text-gray-600">Dana masuk ke rekening Anda dalam hitungan menit</p>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl p-12 text-white">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Siap Untuk Convert?</h2>
            <p class="text-xl mb-8 opacity-90">Bergabung dengan ribuan user yang sudah mempercayai layanan kami</p>
            
            @guest
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-4 rounded-xl text-lg font-semibold hover:shadow-xl transform hover:scale-105 transition-all">
                    <i class="fas fa-user-plus mr-2"></i>Daftar Gratis Sekarang
                </a>
                <a href="{{ route('login') }}" class="border-2 border-white text-white px-8 py-4 rounded-xl text-lg font-semibold hover:bg-white hover:text-blue-600 transition-all">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            </div>
            @else
            <a href="{{ route('dashboard') }}" class="bg-white text-blue-600 px-8 py-4 rounded-xl text-lg font-semibold hover:shadow-xl transform hover:scale-105 transition-all inline-block">
                <i class="fas fa-tachometer-alt mr-2"></i>Mulai Convert
            </a>
            @endguest
        </div>
    </div>
</div>
@endsection
