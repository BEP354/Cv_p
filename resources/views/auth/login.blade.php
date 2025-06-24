@extends('layouts.app')

@section('title', 'Login - PayPal Skrill Converter')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-sign-in-alt text-2xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">Login</h2>
                <p class="text-gray-600 mt-2">Masuk ke akun Anda</p>
            </div>
            
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-gray-400"></i>Email
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                           placeholder="contoh@email.com">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-gray-400"></i>Password
                    </label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                           placeholder="Masukkan password">
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-4 rounded-lg font-semibold text-lg hover:shadow-lg transform hover:scale-105 transition-all">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Daftar di sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
