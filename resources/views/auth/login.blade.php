<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- ===== FAVICON ===== --}}
    @php
        use App\Helpers\Helper;
        $favicon = Helper::getFavicon();
        $bprName = Helper::getBprName();
    @endphp
    <link rel="icon" href="{{ $favicon }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $favicon }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ $favicon }}">
    
    <title>Login - {{ $bprName }}</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
            <!-- Logo -->
            <div class="text-center mb-8">
                {{-- Logo BPR --}}
                @php
                    $logoUrl = Helper::getLogo();
                @endphp
                
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo BPR" class="h-16 w-auto mx-auto mb-3">
                @else
                    <h1 class="text-3xl font-bold text-blue-600">🏦 BPRS</h1>
                    <h2 class="text-xl font-semibold text-gray-800">Amanah Bangsa</h2>
                @endif
                
                <p class="text-gray-500 text-sm mt-2">Sistem KPI & Penilaian Kinerja</p>
            </div>
            
            <!-- Form Login -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" id="username" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-500 @enderror"
                               value="{{ old('username') }}" required autofocus>
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" id="password" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                               required>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">Ingat saya</label>
                        </div>
                        <a href="#" class="text-sm text-blue-600 hover:underline">Lupa password?</a>
                    </div>
                    
                    <button type="submit" class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-200">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </button>
                </div>
            </form>
            
            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} {{ $bprName }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>