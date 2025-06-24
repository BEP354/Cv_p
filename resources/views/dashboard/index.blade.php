@extends('layouts.app')

@section('title', 'Dashboard - PayPal Skrill Converter')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white">
        <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}!</h1>
        <p class="text-blue-100">Platform lengkap untuk jual beli saldo PayPal dan Skrill dengan berbagai metode pembayaran Indonesia</p>
        @if(!auth()->user()->email_verified_at)
            <div class="mt-4 bg-yellow-500 bg-opacity-20 border border-yellow-300 rounded-lg p-3">
                <p class="text-yellow-100">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Email Anda belum diverifikasi. Silakan cek email untuk verifikasi akun.
                </p>
            </div>
        @endif
    </div>

    <!-- Service Toggle -->
    <div class="bg-white rounded-2xl shadow-lg p-8" x-data="{ activeTab: 'sell' }">
        <div class="flex items-center justify-center mb-8">
            <div class="bg-gray-100 p-1 rounded-xl flex">
                <button @click="activeTab = 'sell'" 
                        :class="activeTab === 'sell' ? 'bg-white text-blue-600 shadow-md' : 'text-gray-600 hover:text-gray-800'"
                        class="px-6 py-3 rounded-lg font-semibold transition-all flex items-center space-x-2">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>Jual Saldo PayPal/Skrill</span>
                </button>
                <button @click="activeTab = 'buy'" 
                        :class="activeTab === 'buy' ? 'bg-white text-green-600 shadow-md' : 'text-gray-600 hover:text-gray-800'"
                        class="px-6 py-3 rounded-lg font-semibold transition-all flex items-center space-x-2">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Beli Saldo PayPal/Skrill</span>
                </button>
            </div>
        </div>

        <!-- Sell PayPal/Skrill Section -->
        <div x-show="activeTab === 'sell'" x-data="sellCalculator()">
            <div class="flex items-center mb-8">
                <div class="w-12 h-12 bg-gradient-to-r from-red-400 to-red-600 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-exchange-alt text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Jual PayPal / Skrill</h2>
                    <p class="text-gray-600">Convert saldo PayPal/Skrill Anda ke Rupiah</p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('conversion.store') }}" @submit="return validateSellForm()">
                @csrf
                <input type="hidden" name="transaction_type" value="sell">
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- From Currency -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                <i class="fas fa-wallet mr-2 text-gray-400"></i>Dari Saldo
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="from_currency" value="paypal" x-model="fromCurrency" @change="calculateSellRate()" required
                                           class="sr-only peer">
                                    <div class="bg-white border-2 border-gray-200 rounded-xl p-4 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300 transition-all">
                                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                                            <i class="fab fa-paypal text-white text-xl"></i>
                                        </div>
                                        <p class="font-semibold text-gray-800">PayPal</p>
                                        <p class="text-sm text-gray-500">USD</p>
                                    </div>
                                </label>
                                
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="from_currency" value="skrill" x-model="fromCurrency" @change="calculateSellRate()" required
                                           class="sr-only peer">
                                    <div class="bg-white border-2 border-gray-200 rounded-xl p-4 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300 transition-all">
                                        <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                                            <i class="fas fa-wallet text-white text-xl"></i>
                                        </div>
                                        <p class="font-semibold text-gray-800">Skrill</p>
                                        <p class="text-sm text-gray-500">USD</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                <i class="fas fa-dollar-sign mr-2 text-gray-400"></i>Jumlah (USD)
                            </label>
                            <input type="number" name="from_amount" x-model="amount" @input="calculateSellRate()" 
                                   step="0.01" min="1" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                                   placeholder="Masukkan jumlah dalam USD">
                        </div>
                        
                        <!-- To Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                <i class="fas fa-credit-card mr-2 text-gray-400"></i>Terima di
                            </label>
                            <select name="to_method_id" x-model="toMethodId" @change="calculateSellRate()" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Pilih metode penerima</option>
                                @foreach($paymentMethods->groupBy('type') as $type => $methods)
                                    <optgroup label="{{ $type === 'ewallet' ? 'E-Wallet' : 'Bank' }}">
                                        @foreach($methods as $method)
                                            @if($method->is_active)
                                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                                            @else
                                                <option value="{{ $method->id }}" disabled>{{ $method->name }} - Sedang Perbaikan</option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Right Column - Rate Display -->
                    <div>
                        <div x-show="sellRateData" class="bg-gradient-to-br from-red-50 to-pink-50 rounded-xl p-6 h-full border border-red-100">
                            <h3 class="font-semibold mb-6 text-gray-800 flex items-center">
                                <i class="fas fa-calculator mr-2 text-red-500"></i>
                                Detail Penjualan
                            </h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-red-200">
                                    <span class="text-gray-600">Rate Jual:</span>
                                    <span class="font-semibold text-gray-800" x-text="sellRateData ? 'Rp ' + numberFormat(sellRateData.rate) : '-'"></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-red-200">
                                    <span class="text-gray-600">Gross IDR:</span>
                                    <span class="font-semibold text-gray-800" x-text="sellRateData ? 'Rp ' + numberFormat(sellRateData.gross_idr) : '-'"></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-red-200">
                                    <span class="text-gray-600">Fee (<span x-text="sellRateData ? sellRateData.fee_percentage : 0"></span>%):</span>
                                    <span class="font-semibold text-red-600" x-text="sellRateData ? '- Rp ' + numberFormat(sellRateData.fee_amount) : '-'"></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-red-200">
                                    <span class="text-gray-600">Admin Fee:</span>
                                    <span class="font-semibold text-red-600" x-text="sellRateData ? '- Rp ' + numberFormat(sellRateData.admin_fee) : '-'"></span>
                                </div>
                                <div class="flex justify-between items-center py-4 bg-green-50 rounded-lg px-4 mt-4">
                                    <span class="text-lg font-semibold text-green-800">Anda Terima:</span>
                                    <span class="text-2xl font-bold text-green-600" x-text="sellRateData ? 'Rp ' + numberFormat(sellRateData.total_idr) : '-'"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div x-show="!sellRateData" class="bg-gray-50 rounded-xl p-6 h-full flex items-center justify-center">
                            <div class="text-center text-gray-500">
                                <i class="fas fa-calculator text-4xl mb-4"></i>
                                <p>Pilih mata uang, metode, dan jumlah untuk melihat kalkulasi</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recipient Details for Sell -->
                <div class="border-t border-gray-200 pt-8">
                    <h3 class="text-lg font-semibold mb-6 text-gray-800 flex items-center">
                        <i class="fas fa-user mr-2 text-blue-500"></i>
                        Detail Penerima Rupiah
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Penerima</label>
                            <input type="text" name="recipient_name" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Nama sesuai rekening/akun">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Rekening/HP</label>
                            <input type="text" name="recipient_account" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Nomor rekening atau HP">
                        </div>
                    </div>
                    
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email PayPal/Skrill Pengirim</label>
                        <input type="email" name="sender_email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Email yang akan mengirim PayPal/Skrill">
                    </div>
                    
                    <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-pink-600 text-white py-4 px-6 rounded-xl font-semibold text-lg hover:shadow-lg transform hover:scale-105 transition-all">
                        <i class="fas fa-paper-plane mr-2"></i>Jual Saldo PayPal/Skrill
                    </button>
                </div>
            </form>
        </div>

        <!-- Buy PayPal/Skrill Section -->
        <div x-show="activeTab === 'buy'" x-data="buyCalculator()">
            <div class="flex items-center mb-8">
                <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-green-600 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-shopping-cart text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Beli PayPal / Skrill</h2>
                    <p class="text-gray-600">Beli saldo PayPal/Skrill dengan Rupiah</p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('purchase.store') }}" @submit="return validateBuyForm()">
                @csrf
                <input type="hidden" name="transaction_type" value="buy">
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Payment Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                <i class="fas fa-credit-card mr-2 text-gray-400"></i>Bayar Dengan
                            </label>
                            <select name="from_method_id" x-model="fromMethodId" @change="calculateBuyRate()" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Pilih metode pembayaran</option>
                                @foreach($paymentMethods->groupBy('type') as $type => $methods)
                                    <optgroup label="{{ $type === 'ewallet' ? 'E-Wallet' : 'Bank Transfer' }}">
                                        @foreach($methods as $method)
                                            @if($method->is_active)
                                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- To Currency -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                <i class="fas fa-wallet mr-2 text-gray-400"></i>Beli Saldo
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="to_currency" value="paypal" x-model="toCurrency" @change="calculateBuyRate()" required
                                           class="sr-only peer">
                                    <div class="bg-white border-2 border-gray-200 rounded-xl p-4 text-center peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-gray-300 transition-all">
                                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                                            <i class="fab fa-paypal text-white text-xl"></i>
                                        </div>
                                        <p class="font-semibold text-gray-800">PayPal</p>
                                        <p class="text-sm text-gray-500">USD</p>
                                    </div>
                                </label>
                                
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="to_currency" value="skrill" x-model="toCurrency" @change="calculateBuyRate()" required
                                           class="sr-only peer">
                                    <div class="bg-white border-2 border-gray-200 rounded-xl p-4 text-center peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-gray-300 transition-all">
                                        <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                                            <i class="fas fa-wallet text-white text-xl"></i>
                                        </div>
                                        <p class="font-semibold text-gray-800">Skrill</p>
                                        <p class="text-sm text-gray-500">USD</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                <i class="fas fa-dollar-sign mr-2 text-gray-400"></i>Jumlah Saldo (USD)
                            </label>
                            <input type="number" name="to_amount" x-model="buyAmount" @input="calculateBuyRate()" 
                                   step="1" min="10" max="1000" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-lg"
                                   placeholder="Masukkan jumlah USD yang ingin dibeli">
                            <p class="text-sm text-gray-500 mt-2">Minimum $10, Maximum $1000</p>
                        </div>
                        
                        <!-- Quick Amount Buttons -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Jumlah Cepat</label>
                            <div class="grid grid-cols-4 gap-3">
                                <button type="button" @click="setBuyAmount(25)" 
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-center hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all">
                                    $25
                                </button>
                                <button type="button" @click="setBuyAmount(50)" 
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-center hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all">
                                    $50
                                </button>
                                <button type="button" @click="setBuyAmount(100)" 
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-center hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all">
                                    $100
                                </button>
                                <button type="button" @click="setBuyAmount(200)" 
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-center hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all">
                                    $200
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column - Price Display -->
                    <div>
                        <div x-show="buyRateData" class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 h-full border border-green-100">
                            <h3 class="font-semibold mb-6 text-gray-800 flex items-center">
                                <i class="fas fa-calculator mr-2 text-green-500"></i>
                                Detail Pembelian
                            </h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-green-200">
                                    <span class="text-gray-600">Saldo Dibeli:</span>
                                    <span class="font-semibold text-gray-800" x-text="buyRateData ? '$' + buyRateData.amount : '-'"></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-green-200">
                                    <span class="text-gray-600">Rate Beli:</span>
                                    <span class="font-semibold text-gray-800" x-text="buyRateData ? 'Rp ' + numberFormat(buyRateData.rate) : '-'"></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-green-200">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span class="font-semibold text-gray-800" x-text="buyRateData ? 'Rp ' + numberFormat(buyRateData.subtotal) : '-'"></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-green-200">
                                    <span class="text-gray-600">Fee (<span x-text="buyRateData ? buyRateData.fee_percentage : 0"></span>%):</span>
                                    <span class="font-semibold text-orange-600" x-text="buyRateData ? '+ Rp ' + numberFormat(buyRateData.fee_amount) : '-'"></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-green-200">
                                    <span class="text-gray-600">Admin Fee:</span>
                                    <span class="font-semibold text-orange-600" x-text="buyRateData ? '+ Rp ' + numberFormat(buyRateData.admin_fee) : '-'"></span>
                                </div>
                                <div class="flex justify-between items-center py-4 bg-blue-50 rounded-lg px-4 mt-4">
                                    <span class="text-lg font-semibold text-blue-800">Total Bayar:</span>
                                    <span class="text-2xl font-bold text-blue-600" x-text="buyRateData ? 'Rp ' + numberFormat(buyRateData.total_idr) : '-'"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div x-show="!buyRateData" class="bg-gray-50 rounded-xl p-6 h-full flex items-center justify-center">
                            <div class="text-center text-gray-500">
                                <i class="fas fa-calculator text-4xl mb-4"></i>
                                <p>Pilih metode pembayaran, mata uang, dan jumlah untuk melihat total</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- PayPal Details for Buy -->
                <div class="border-t border-gray-200 pt-8">
                    <h3 class="text-lg font-semibold mb-6 text-gray-800 flex items-center">
                        <i class="fab fa-paypal mr-2 text-blue-500"></i>
                        Detail Penerima Saldo
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email PayPal/Skrill Penerima</label>
                            <input type="email" name="recipient_email" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="email@example.com">
                            <p class="text-sm text-gray-500 mt-1">Saldo akan dikirim ke email ini</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Email</label>
                            <input type="email" name="recipient_email_confirmation" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="email@example.com">
                        </div>
                    </div>
                    
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                  placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                    
                    <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-4 px-6 rounded-xl font-semibold text-lg hover:shadow-lg transform hover:scale-105 transition-all">
                        <i class="fas fa-shopping-cart mr-2"></i>Beli Saldo PayPal/Skrill
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center mb-8">
            <div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-purple-600 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-history text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Transaksi Terbaru</h2>
                <p class="text-gray-600">Riwayat transaksi jual beli Anda</p>
            </div>
        </div>
        
        @if($orders && $orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaksi</th>
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
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->transaction_type === 'sell' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $order->transaction_type === 'sell' ? 'Jual' : 'Beli' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 {{ ($order->from_currency === 'paypal' || $order->to_currency === 'paypal') ? 'bg-blue-500' : 'bg-red-500' }} rounded-lg flex items-center justify-center mr-3">
                                        <i class="fab {{ ($order->from_currency === 'paypal' || $order->to_currency === 'paypal') ? 'fa-paypal' : 'fa-skrill' }} text-white text-sm"></i>
                                    </div>
                                    <div>
                                        @if($order->transaction_type === 'sell')
                                            <!-- Untuk transaksi JUAL (conversion_orders) -->
                                            <div class="text-sm font-medium text-gray-900">
                                                ${{ number_format($order->from_amount, 2) }} {{ ucfirst($order->from_currency) }}
                                            </div>
                                            <div class="text-sm text-gray-500">ke {{ $order->paymentMethod->name }}</div>
                                        @else
                                            <!-- Untuk transaksi BELI (purchase_orders) -->
                                            <div class="text-sm font-medium text-gray-900">
                                                ${{ number_format($order->to_amount, 2) }} {{ ucfirst($order->to_currency) }}
                                            </div>
                                            <div class="text-sm text-gray-500">dari {{ $order->paymentMethod->name }}</div>
                                        @endif
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
                                @if($order->transaction_type === 'sell')
                                    <a href="{{ route('conversion.show', $order->order_code) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </a>
                                @else
                                    <a href="{{ route('purchase.show', $order->order_code) }}" class="text-green-600 hover:text-green-900 font-medium">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada transaksi</h3>
                <p class="text-gray-500">Mulai transaksi pertama Anda sekarang!</p>
            </div>
        @endif
    </div>
</div>

<script>
function sellCalculator() {
    return {
        fromCurrency: '',
        toMethodId: '',
        amount: '',
        sellRateData: null,
        
        async calculateSellRate() {
            if (!this.fromCurrency || !this.toMethodId || !this.amount || this.amount < 1) {
                this.sellRateData = null;
                return;
            }
            
            try {
                const response = await fetch('{{ route("conversion.rate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        from_currency: this.fromCurrency,
                        to_method_id: this.toMethodId,
                        amount: this.amount,
                        type: 'sell'
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.sellRateData = data;
                } else {
                    alert(data.error || 'Terjadi kesalahan');
                    this.sellRateData = null;
                }
            } catch (error) {
                console.error('Error:', error);
                this.sellRateData = null;
            }
        },
        
        numberFormat(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        },
        
        validateSellForm() {
            if (!this.sellRateData) {
                alert('Silakan hitung rate terlebih dahulu');
                return false;
            }
            return true;
        }
    }
}

function buyCalculator() {
    return {
        fromMethodId: '',
        toCurrency: '',
        buyAmount: '',
        buyRateData: null,
        
        setBuyAmount(amount) {
            this.buyAmount = amount;
            this.calculateBuyRate();
        },
        
        async calculateBuyRate() {
            if (!this.fromMethodId || !this.toCurrency || !this.buyAmount || this.buyAmount < 10) {
                this.buyRateData = null;
                return;
            }
            
            try {
                const response = await fetch('{{ route("purchase.rate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        from_method_id: this.fromMethodId,
                        to_currency: this.toCurrency,
                        amount: this.buyAmount,
                        type: 'buy'
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.buyRateData = data;
                } else {
                    alert(data.error || 'Terjadi kesalahan');
                    this.buyRateData = null;
                }
            } catch (error) {
                console.error('Error:', error);
                this.buyRateData = null;
            }
        },
        
        numberFormat(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        },
        
        validateBuyForm() {
            if (!this.buyRateData) {
                alert('Silakan hitung harga terlebih dahulu');
                return false;
            }
            return true;
        }
    }
}
</script>
@endsection
