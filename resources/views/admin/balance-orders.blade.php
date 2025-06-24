@extends('layouts.admin')

@section('title', 'Pembelian Saldo - Admin Dashboard')
@section('page-title', 'Pembelian Saldo')
@section('page-description', 'Kelola order pembelian saldo PayPal/Skrill')

@push('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #feca57 100%);
        --info-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .metric-card {
        background: linear-gradient(145deg, #ffffff, #f8fafc);
        border-radius: 20px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        border-left: 5px solid transparent;
    }
    
    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary-gradient);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .metric-card:hover::before {
        opacity: 1;
    }
    
    .metric-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }
    
    .filter-toggle {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 15px;
        padding: 1rem 2rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }
    
    .filter-toggle:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
    }
    
    .filter-panel {
        background: white;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        max-height: 0;
        opacity: 0;
        margin-top: 0;
    }
    
    .filter-panel.active {
        max-height: 800px;
        opacity: 1;
        margin-top: 1.5rem;
    }
    
    .animated-input {
        transition: all 0.3s ease;
        border: 2px solid #e2e8f0;
    }
    
    .animated-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        transform: translateY(-1px);
    }
    
    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.5rem;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    .table-modern {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }
    
    .table-modern tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .table-modern tbody tr:hover {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        transform: scale(1.01);
    }
    
    .badge-modern {
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .btn-modern {
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }
    
    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .avatar-modern {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
        background: var(--primary-gradient);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .currency-icon {
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
    
    .floating-action {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 1000;
    }
    
    .pulse-glow {
        animation: pulse-glow 2s infinite;
    }
    
    @keyframes pulse-glow {
        0%, 100% { 
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.4);
            transform: scale(1);
        }
        50% { 
            box-shadow: 0 0 30px rgba(239, 68, 68, 0.8);
            transform: scale(1.05);
        }
    }
    
    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    @media (max-width: 768px) {
        .metric-card {
            padding: 1.5rem;
        }
        
        .filter-toggle {
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
        }
        
        .table-modern {
            font-size: 0.85rem;
        }
        
        .btn-modern {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Enhanced Header with Particles Background -->
<div class="mb-8 relative">
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-3xl p-8 text-white relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-4 -right-4 w-72 h-72 bg-white opacity-10 rounded-full animate-pulse"></div>
            <div class="absolute -bottom-8 -left-8 w-96 h-96 bg-white opacity-5 rounded-full animate-bounce"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-white opacity-5 rounded-full animate-ping"></div>
        </div>
        
        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <h1 class="text-4xl lg:text-5xl font-bold mb-3 flex items-center">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mr-4">
                            <i class="fas fa-coins text-2xl"></i>
                        </div>
                        Balance Orders
                    </h1>
                    <p class="text-xl text-indigo-100 mb-4">Kelola pembelian saldo PayPal & Skrill dengan sistem canggih</p>
                    <div class="flex flex-wrap items-center gap-6 text-sm">
                        <div class="flex items-center bg-white bg-opacity-20 rounded-lg px-3 py-2">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <span>{{ now()->format('l, d F Y') }}</span>
                        </div>
                        <div class="flex items-center bg-white bg-opacity-20 rounded-lg px-3 py-2">
                            <i class="fas fa-clock mr-2"></i>
                            <span id="current-time">{{ now()->format('H:i:s') }}</span>
                        </div>
                        <div class="flex items-center bg-white bg-opacity-20 rounded-lg px-3 py-2">
                            <i class="fas fa-database mr-2"></i>
                            <span>{{ $balanceOrders->total() }} Total Records</span>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="flex flex-wrap gap-3">
                    <button onclick="refreshData()" class="btn-modern bg-white bg-opacity-20 hover:bg-opacity-30 text-white">
                        <i class="fas fa-sync-alt"></i>
                        <span class="hidden sm:inline">Refresh</span>
                    </button>
                    <button onclick="exportData()" class="btn-modern bg-white bg-opacity-20 hover:bg-opacity-30 text-white">
                        <i class="fas fa-download"></i>
                        <span class="hidden sm:inline">Export</span>
                    </button>
                    <button onclick="toggleFilter()" class="btn-modern bg-white bg-opacity-20 hover:bg-opacity-30 text-white">
                        <i class="fas fa-filter"></i>
                        <span class="hidden sm:inline">Filter</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Orders -->
    <div class="metric-card group" style="border-left-color: #3b82f6;">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-shopping-cart text-xl text-white"></i>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-gray-800 mb-1">{{ number_format($totalOrders) }}</div>
                <div class="text-sm text-gray-500 font-medium">Total Orders</div>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-blue-600 font-semibold text-sm flex items-center">
                <i class="fas fa-chart-line mr-1"></i>All Time
            </span>
            <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="w-full h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full"></div>
            </div>
        </div>
    </div>
    
    <!-- Pending Orders -->
    <div class="metric-card group relative" style="border-left-color: #f59e0b;">
        @if($pendingOrders > 0)
        <div class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center pulse-glow">
            {{ $pendingOrders }}
        </div>
        @endif
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-clock text-xl text-white"></i>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-gray-800 mb-1">{{ number_format($pendingOrders) }}</div>
                <div class="text-sm text-gray-500 font-medium">Pending</div>
            </div>
        </div>
        <div class="flex items-center justify-between">
            @if($pendingOrders > 0)
            <span class="text-red-500 font-semibold text-sm flex items-center pulse-glow">
                <i class="fas fa-exclamation-triangle mr-1"></i>Action Needed
            </span>
            @else
            <span class="text-green-500 font-semibold text-sm flex items-center">
                <i class="fas fa-check mr-1"></i>All Clear
            </span>
            @endif
            <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full" 
                     style="width: {{ $totalOrders > 0 ? min(($pendingOrders / $totalOrders) * 100, 100) : 0 }}%"></div>
            </div>
        </div>
    </div>
    
    <!-- Completed Orders -->
    <div class="metric-card group" style="border-left-color: #10b981;">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-check-circle text-xl text-white"></i>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-gray-800 mb-1">{{ number_format($completedOrders) }}</div>
                <div class="text-sm text-gray-500 font-medium">Completed</div>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-green-600 font-semibold text-sm flex items-center">
                <i class="fas fa-trophy mr-1"></i>{{ $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0 }}% Success
            </span>
            <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-green-400 to-emerald-500 rounded-full" 
                     style="width: {{ $totalOrders > 0 ? min(($completedOrders / $totalOrders) * 100, 100) : 0 }}%"></div>
            </div>
        </div>
    </div>
    
    <!-- Total Volume -->
    <div class="metric-card group" style="border-left-color: #8b5cf6;">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-money-bill-wave text-xl text-white"></i>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-gray-800 mb-1">Rp {{ number_format($totalVolume / 1000000, 1) }}M</div>
                <div class="text-sm text-gray-500 font-medium">Total Volume</div>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-purple-600 font-semibold text-sm flex items-center">
                <i class="fas fa-trending-up mr-1"></i>Revenue
            </span>
            <div class="text-xs text-gray-600 font-medium">
                Rp {{ number_format($totalVolume, 0, ',', '.') }}
            </div>
        </div>
    </div>
</div>

<!-- Collapsible Advanced Filter -->
<div class="mb-8">
    <button onclick="toggleFilter()" class="filter-toggle" id="filter-toggle">
        <i class="fas fa-filter"></i>
        <span>Advanced Filters & Search</span>
        <i class="fas fa-chevron-down transition-transform duration-300" id="filter-chevron"></i>
    </button>
    
    <div class="filter-panel" id="filter-panel">
        <form method="GET" action="{{ route('admin.balance-orders') }}" class="p-8">
            <!-- Basic Filters Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-flag text-indigo-500 mr-2"></i>Status
                    </label>
                    <select name="status" class="animated-input w-full px-4 py-3 rounded-xl focus:outline-none">
                        <option value="">🔍 Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>⚡ Processing</option>
                        <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>✅ Success</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>❌ Failed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>🚫 Cancelled</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-money-bill text-green-500 mr-2"></i>Currency
                    </label>
                    <select name="currency" class="animated-input w-full px-4 py-3 rounded-xl focus:outline-none">
                        <option value="">💰 Semua Currency</option>
                        <option value="paypal" {{ request('currency') === 'paypal' ? 'selected' : '' }}>💙 PayPal</option>
                        <option value="skrill" {{ request('currency') === 'skrill' ? 'selected' : '' }}>❤️ Skrill</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Tanggal Mulai
                    </label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                           class="animated-input w-full px-4 py-3 rounded-xl focus:outline-none">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-calendar-check text-purple-500 mr-2"></i>Tanggal Akhir
                    </label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                           class="animated-input w-full px-4 py-3 rounded-xl focus:outline-none">
                </div>
            </div>
            
            <!-- Advanced Filters Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-search text-orange-500 mr-2"></i>Search
                    </label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="🔍 Order code, email, nama user..." 
                           class="animated-input w-full px-4 py-3 rounded-xl focus:outline-none">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-dollar-sign text-green-500 mr-2"></i>Min Amount ($)
                    </label>
                    <input type="number" name="min_amount" value="{{ request('min_amount') }}" 
                           placeholder="💵 Minimum amount" step="0.01"
                           class="animated-input w-full px-4 py-3 rounded-xl focus:outline-none">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-dollar-sign text-red-500 mr-2"></i>Max Amount ($)
                    </label>
                    <input type="number" name="max_amount" value="{{ request('max_amount') }}" 
                           placeholder="💰 Maximum amount" step="0.01"
                           class="animated-input w-full px-4 py-3 rounded-xl focus:outline-none">
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-between pt-6 border-t border-gray-200">
                <div class="text-sm text-gray-600 mb-4 sm:mb-0 flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Menampilkan <span class="font-bold text-indigo-600 mx-1">{{ $balanceOrders->count() }}</span> dari 
                    <span class="font-bold text-indigo-600 mx-1">{{ $balanceOrders->total() }}</span> total orders
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.balance-orders') }}" 
                       class="btn-modern bg-gray-500 hover:bg-gray-600 text-white">
                        <i class="fas fa-times"></i>Reset Filter
                    </a>
                    <button type="submit" 
                            class="btn-modern bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white">
                        <i class="fas fa-search"></i>Apply Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Enhanced Table -->
<div class="table-modern">
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-b border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="mb-4 lg:mb-0">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-table text-indigo-500 mr-3"></i>
                    Balance Orders Management
                </h3>
                <p class="text-gray-600 mt-1">Comprehensive order management system</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center bg-white rounded-xl px-4 py-2 shadow-sm border">
                    <i class="fas fa-sort text-gray-400 mr-2"></i>
                    <select onchange="sortTable(this.value)" class="border-none focus:ring-0 text-sm bg-transparent">
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>📅 Terbaru</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>📅 Terlama</option>
                        <option value="amount_high" {{ request('sort') === 'amount_high' ? 'selected' : '' }}>💰 Amount ↓</option>
                        <option value="amount_low" {{ request('sort') === 'amount_low' ? 'selected' : '' }}>💰 Amount ↑</option>
                    </select>
                </div>
                <div class="flex items-center bg-white rounded-xl px-4 py-2 shadow-sm border">
                    <i class="fas fa-eye text-gray-400 mr-2"></i>
                    <select onchange="changePerPage(this.value)" class="border-none focus:ring-0 text-sm bg-transparent">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 per page</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-receipt text-indigo-500 mr-2"></i>Order Details
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-user text-blue-500 mr-2"></i>Customer
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-shopping-cart text-green-500 mr-2"></i>Purchase
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-money-bill-wave text-purple-500 mr-2"></i>Amount
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-flag text-orange-500 mr-2"></i>Status
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-cogs text-gray-500 mr-2"></i>Actions
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($balanceOrders as $index => $order)
                <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-300">
                    <td class="px-6 py-6 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center text-white font-bold shadow-lg">
                                {{ substr($order->order_code, -2) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-lg font-bold text-gray-900">{{ $order->order_code }}</div>
                                <div class="text-sm text-gray-500 flex items-center mt-1">
                                    <i class="fas fa-clock mr-2 text-blue-400"></i>
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </div>
                                <div class="text-xs text-indigo-600 font-medium mt-1">
                                    {{ $order->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-6 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="avatar-modern">
                                {{ strtoupper(substr($order->user->name, 0, 2)) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-lg font-bold text-gray-900">{{ Str::limit($order->user->name, 20) }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($order->user->email, 25) }}</div>
                                <div class="text-xs text-blue-600 font-medium mt-1 flex items-center">
                                    <i class="fas fa-id-badge mr-1"></i>ID: {{ $order->user->id }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-6 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="currency-icon {{ $order->to_currency === 'paypal' ? 'bg-blue-500' : 'bg-red-500' }}">
                                <i class="fab {{ $order->to_currency === 'paypal' ? 'fa-paypal' : 'fa-skrill' }}"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-lg font-bold text-gray-900">
                                    ${{ number_format($order->to_amount, 2) }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ ucfirst($order->to_currency) }}
                                </div>
                                <div class="text-xs text-purple-600 font-medium mt-1">
                                    Rate: Rp {{ number_format($order->rate ?? 15000, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-6 whitespace-nowrap">
                        <div class="text-xl font-bold text-gray-900">
                            Rp {{ number_format($order->total_idr, 0, ',', '.') }}
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            Subtotal: Rp {{ number_format($order->subtotal ?? 0, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-orange-600 font-medium mt-1">
                            Fee: Rp {{ number_format(($order->total_idr - ($order->subtotal ?? 0)), 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-6 whitespace-nowrap">
                        <div class="relative">
                            @php
                                $statusConfig = [
                                    'pending' => ['bg-yellow-100 text-yellow-800', 'fa-clock', '#f59e0b'],
                                    'processing' => ['bg-blue-100 text-blue-800', 'fa-spinner fa-spin', '#3b82f6'],
                                    'success' => ['bg-green-100 text-green-800', 'fa-check-circle', '#10b981'],
                                    'failed' => ['bg-red-100 text-red-800', 'fa-times-circle', '#ef4444'],
                                    'cancelled' => ['bg-gray-100 text-gray-800', 'fa-ban', '#6b7280']
                                ];
                                $status = $order->status ?? 'pending';
                                $config = $statusConfig[$status] ?? $statusConfig['pending'];
                            @endphp
                            <span class="badge-modern {{ $config[0] }}">
                                <span class="status-dot" style="background-color: {{ $config[2] }}"></span>
                                <i class="fas {{ $config[1] }}"></i>
                                {{ ucfirst($status) }}
                            </span>
                            @if($status === 'pending')
                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full pulse-glow"></div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-6 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.purchase-orders.show', $order->id) }}" 
                               class="btn-modern bg-blue-100 text-blue-700 hover:bg-blue-200">
                                <i class="fas fa-eye"></i>
                                <span class="hidden lg:inline">Detail</span>
                            </a>
                            @if(($order->status ?? 'pending') === 'pending')
                            <button onclick="processOrder({{ $order->id }})" 
                                    class="btn-modern bg-green-100 text-green-700 hover:bg-green-200">
                                <i class="fas fa-play"></i>
                                <span class="hidden lg:inline">Process</span>
                            </button>
                            @endif
                            <div class="relative">
                                <button onclick="toggleDropdown({{ $order->id }})" 
                                        class="btn-modern bg-gray-100 text-gray-700 hover:bg-gray-200">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div id="dropdown-{{ $order->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg z-10 border">
                                    <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 rounded-t-xl">
                                        <i class="fas fa-edit mr-2 text-blue-500"></i>Edit Order
                                    </a>
                                    <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-download mr-2 text-green-500"></i>Download Receipt
                                    </a>
                                    <button onclick="confirmDelete({{ $order->id }}, '{{ $order->order_code }}')" 
                                            class="w-full text-left block px-4 py-3 text-sm text-red-600 hover:bg-red-50 rounded-b-xl">
                                        <i class="fas fa-trash mr-2"></i>Delete Order
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-24 h-24 bg-gradient-to-r from-gray-200 to-gray-300 rounded-full flex items-center justify-center mb-6">
                                <i class="fas fa-inbox text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">No Orders Found</h3>
                            <p class="text-gray-500 text-lg mb-6">Tidak ada order pembelian saldo yang ditemukan dengan filter saat ini</p>
                            <div class="flex space-x-4">
                                <button onclick="clearFilters()" 
                                        class="btn-modern bg-indigo-600 text-white hover:bg-indigo-700">
                                    <i class="fas fa-refresh mr-2"></i>Clear Filters
                                </button>
                                <button onclick="refreshData()" 
                                        class="btn-modern bg-gray-500 text-white hover:bg-gray-600">
                                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Enhanced Pagination -->
    @if($balanceOrders->hasPages())
    <div class="bg-gray-50 px-8 py-6 border-t border-gray-200">
        <div class="flex flex-col sm:flex-row items-center justify-between">
            <div class="text-sm text-gray-600 mb-4 sm:mb-0">
                Showing <span class="font-bold text-indigo-600">{{ $balanceOrders->firstItem() }}</span> to 
                <span class="font-bold text-indigo-600">{{ $balanceOrders->lastItem() }}</span> of 
                <span class="font-bold text-indigo-600">{{ $balanceOrders->total() }}</span> results
            </div>
            <div class="flex items-center space-x-2">
                {{ $balanceOrders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Floating Action Button -->
<div class="floating-action">
    <button onclick="scrollToTop()" class="btn-modern bg-indigo-600 text-white hover:bg-indigo-700 rounded-full w-14 h-14 shadow-lg">
        <i class="fas fa-arrow-up text-xl"></i>
    </button>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 scale-95" id="deleteModalContent">
        <div class="text-center">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-trash text-3xl text-red-500"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Konfirmasi Hapus</h3>
            <p class="text-gray-600 mb-2">Apakah Anda yakin ingin menghapus order:</p>
            <p class="text-lg font-bold text-red-600 mb-6" id="orderCodeToDelete"></p>
            <p class="text-sm text-gray-500 mb-8">⚠️ Tindakan ini tidak dapat dibatalkan!</p>
            
            <div class="flex space-x-4">
                <button onclick="closeDeleteModal()" 
                        class="flex-1 btn-modern bg-gray-500 text-white hover:bg-gray-600">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full btn-modern bg-red-500 text-white hover:bg-red-600">
                        <i class="fas fa-trash mr-2"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Real-time clock update
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID');
        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }
    
    setInterval(updateTime, 1000);
    
    // Initialize filter state
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = Array.from(urlParams.keys()).some(key => 
        ['status', 'currency', 'start_date', 'end_date', 'search', 'min_amount', 'max_amount'].includes(key)
    );
    
    if (hasFilters) {
        document.getElementById('filter-panel').classList.add('active');
        document.getElementById('filter-chevron').classList.add('rotate-180');
    }
});

// Toggle filter panel
function toggleFilter() {
    const panel = document.getElementById('filter-panel');
    const chevron = document.getElementById('filter-chevron');
    
    panel.classList.toggle('active');
    chevron.classList.toggle('rotate-180');
}

// Refresh data with loading state
function refreshData() {
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
    button.disabled = true;
    
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

// Export data
function exportData() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Exporting...';
    button.disabled = true;
    
    setTimeout(() => {
        window.open(`${window.location.pathname}?${params.toString()}`, '_blank');
        button.innerHTML = originalContent;
        button.disabled = false;
    }, 1000);
}

// Sort table
function sortTable(sortBy) {
    const params = new URLSearchParams(window.location.search);
    params.set('sort', sortBy);
    params.delete('page'); // Reset to first page
    window.location.href = `${window.location.pathname}?${params.toString()}`;
}

// Change per page
function changePerPage(perPage) {
    const params = new URLSearchParams(window.location.search);
    params.set('per_page', perPage);
    params.delete('page'); // Reset to first page
    window.location.href = `${window.location.pathname}?${params.toString()}`;
}

// Process order with confirmation
function processOrder(orderId) {
    if (confirm('🚀 Are you sure you want to process this order?')) {
        window.location.href = `/admin/purchase-orders/${orderId}`;
    }
}

// Toggle dropdown with animation
function toggleDropdown(orderId) {
    const dropdown = document.getElementById(`dropdown-${orderId}`);
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    
    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== `dropdown-${orderId}`) {
            d.classList.add('hidden');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

// Clear filters
function clearFilters() {
    window.location.href = window.location.pathname;
}

// Scroll to top
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick^="toggleDropdown"]')) {
        const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
        allDropdowns.forEach(d => d.classList.add('hidden'));
    }
});

// Close filter panel when clicking outside
document.addEventListener('click', function(event) {
    const filterToggle = event.target.closest('#filter-toggle');
    const filterPanel = document.getElementById('filter-panel');
    
    if (!filterToggle && !filterPanel.contains(event.target)) {
        filterPanel.classList.remove('active');
        document.getElementById('filter-chevron').classList.remove('rotate-180');
    }
});

// Auto-refresh stats every 60 seconds
setInterval(function() {
    fetch('/admin/dashboard/refresh-stats')
        .then(response => response.json())
        .then(data => {
            console.log('📊 Stats refreshed successfully');
        })
        .catch(error => console.error('❌ Error refreshing stats:', error));
}, 60000);

// Add loading states to form submissions
document.querySelector('form').addEventListener('submit', function() {
    const submitButton = this.querySelector('button[type="submit"]');
    const originalContent = submitButton.innerHTML;
    
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Filtering...';
    submitButton.disabled = true;
});

// Keyboard shortcuts
document.addEventListener('keydown', function(event) {
    // Ctrl/Cmd + F to toggle filter
    if ((event.ctrlKey || event.metaKey) && event.key === 'f') {
        event.preventDefault();
        toggleFilter();
    }
    
    // Ctrl/Cmd + R to refresh
    if ((event.ctrlKey || event.metaKey) && event.key === 'r') {
        event.preventDefault();
        refreshData();
    }
    
    // Escape to close dropdowns, filter, and modals
    if (event.key === 'Escape') {
        const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
        allDropdowns.forEach(d => d.classList.add('hidden'));
        
        const filterPanel = document.getElementById('filter-panel');
        filterPanel.classList.remove('active');
        document.getElementById('filter-chevron').classList.remove('rotate-180');
        
        // Close delete modal
        closeDeleteModal();
    }
});

// Confirm delete with modal
function confirmDelete(orderId, orderCode) {
    const modal = document.getElementById('deleteModal');
    const modalContent = document.getElementById('deleteModalContent');
    const orderCodeElement = document.getElementById('orderCodeToDelete');
    const deleteForm = document.getElementById('deleteForm');
    
    // Set order code and form action
    orderCodeElement.textContent = orderCode;
    deleteForm.action = `/admin/purchase-orders/${orderId}`;
    
    // Show modal with animation
    modal.classList.remove('hidden');
    setTimeout(() => {
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');
    }, 10);
    
    // Close all dropdowns
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
    allDropdowns.forEach(d => d.classList.add('hidden'));
}

// Close delete modal
function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const modalContent = document.getElementById('deleteModalContent');
    
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeDeleteModal();
    }
});

// Handle delete form submission with loading state
document.getElementById('deleteForm').addEventListener('submit', function(event) {
    const submitButton = this.querySelector('button[type="submit"]');
    const originalContent = submitButton.innerHTML;
    
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menghapus...';
    submitButton.disabled = true;
    
    // Optional: Add a small delay for better UX
    setTimeout(() => {
        // Form will submit naturally
    }, 500);
});
</script>
@endpush
