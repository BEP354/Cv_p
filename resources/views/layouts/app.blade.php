<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PayPal Skrill Converter')</title>
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
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exchange-alt text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">PayPal Skrill</h1>
                            <p class="text-xs text-gray-500">Converter</p>
                        </div>
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-700">Halo, <span class="font-semibold">{{ auth()->user()->name }}</span></span>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    <i class="fas fa-tachometer-alt mr-1"></i>Admin Panel
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    <i class="fas fa-home mr-1"></i>Dashboard
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-sign-out-alt mr-1"></i>Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium">
                                <i class="fas fa-sign-in-alt mr-1"></i>Login
                            </a>
                            <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-lg hover:shadow-lg transition-all font-medium">
                                <i class="fas fa-user-plus mr-1"></i>Daftar
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exchange-alt text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">PayPal Skrill Converter</h3>
                            <p class="text-gray-400 text-sm">Platform Terpercaya</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-4">Platform terpercaya untuk convert PayPal & Skrill ke DANA, ShopeePay, dan berbagai bank di Indonesia dengan rate terbaik dan proses cepat.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-telegram text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-whatsapp text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fas fa-envelope text-xl"></i></a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Layanan</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>Convert PayPal</li>
                        <li>Convert Skrill</li>
                        <li>Transfer ke E-Wallet</li>
                        <li>Transfer ke Bank</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Kontak</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-envelope mr-2"></i>support@paypalconvert.com</li>
                        <li><i class="fab fa-whatsapp mr-2"></i>+62 812-3456-7890</li>
                        <li><i class="fab fa-telegram mr-2"></i>@paypalconvert</li>
                        <li><i class="fas fa-clock mr-2"></i>24/7 Online</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 PayPal Skrill Converter. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
