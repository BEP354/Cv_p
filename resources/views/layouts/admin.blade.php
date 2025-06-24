<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - PayPal Skrill Converter')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-900 text-white flex flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold">Admin Panel</h1>
                        <p class="text-xs text-gray-400">PayPal Converter</p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} transition-colors">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.orders') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('admin.orders*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} transition-colors">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Kelola Transaksi</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('admin.users*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} transition-colors">
                            <i class="fas fa-users"></i>
                            <span>Kelola User</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.rates') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('admin.rates*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} transition-colors">
                            <i class="fas fa-chart-line"></i>
                            <span>Kelola Kurs</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.accounts') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('admin.accounts*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} transition-colors">
                            <i class="fas fa-university"></i>
                            <span>Kelola Rekening</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.balance-orders') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('admin.balance-orders*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} transition-colors">
                            <i class="fas fa-coins"></i>
                            <span>Pembelian Saldo</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- User Info -->
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">Administrator</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-white">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-sm text-gray-600">@yield('page-description', 'Kelola platform convert PayPal/Skrill')</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('home') }}" target="_blank" class="text-gray-600 hover:text-blue-600">
                            <i class="fas fa-external-link-alt mr-1"></i>Lihat Website
                        </a>
                        <div class="text-sm text-gray-600">
                            {{ now()->format('d M Y H:i') }}
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <strong>Terjadi kesalahan:</strong>
                        </div>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
