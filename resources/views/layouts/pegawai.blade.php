<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- ===== FAVICON ===== --}}
    @php
        use App\Helpers\Helper;
        $favicon = Helper::getFavicon();
    @endphp
    <link rel="icon" href="{{ $favicon }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $favicon }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ $favicon }}">
    
    <title>@yield('title', Helper::getBprName() . ' - Pegawai')</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    @stack('styles')

    <style>
        .navbar-logo {
            height: 40px;
            width: auto;
            max-width: 120px;
            object-fit: contain;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    
    <!-- Navbar -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-6">
                    {{-- ===== LOGO BPR ===== --}}
                    @php
                        $bpr = \App\Models\BPR::first();
                        $logoUrl = $bpr && $bpr->logo ? asset('storage/' . $bpr->logo) : null;
                    @endphp
                    
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo BPR" class="navbar-logo">
                    @else
                        <span class="text-xl font-bold text-blue-600">🏦 BPRS</span>
                        <span class="text-xl font-bold text-gray-800"> Amanah Bangsa</span>
                    @endif
                    
                    <span class="ml-4 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Pegawai</span>
                    
                    {{-- Menu Pegawai --}}
                    <div class="flex space-x-4 ml-4">
                        <a href="{{ route('pegawai.dashboard') }}" 
                           class="text-sm {{ request()->routeIs('pegawai.dashboard') ? 'text-blue-600 font-semibold' : 'text-gray-600 hover:text-blue-600' }}">
                            <i class="fas fa-home mr-1"></i> Dashboard
                        </a>
                        <a href="{{ route('pegawai.self-assessment.index') }}" 
                           class="text-sm {{ request()->routeIs('pegawai.self-assessment*') ? 'text-blue-600 font-semibold' : 'text-gray-600 hover:text-blue-600' }}">
                            <i class="fas fa-user-check mr-1"></i> Self Assessment
                        </a>
                        @if(Auth::user()->pegawai && Auth::user()->pegawai->bawahanLangsung->count() > 0)
                            <a href="{{ route('pegawai.penilaian-bawahan.index') }}" 
                               class="text-sm {{ request()->routeIs('pegawai.penilaian-bawahan*') ? 'text-blue-600 font-semibold' : 'text-gray-600 hover:text-blue-600' }}">
                                <i class="fas fa-users mr-1"></i> Penilaian Bawahan
                                <span class="ml-1 px-2 py-0.5 text-xs bg-red-500 text-white rounded-full">
                                    {{ Auth::user()->pegawai->bawahanLangsung->count() }}
                                </span>
                            </a>
                        @endif
                        <a href="{{ route('pegawai.hasil-penilaian') }}" 
                           class="text-sm {{ request()->routeIs('pegawai.hasil-penilaian') ? 'text-blue-600 font-semibold' : 'text-gray-600 hover:text-blue-600' }}">
                            <i class="fas fa-file-alt mr-1"></i> Hasil Penilaian
                        </a>
                        <a href="{{ route('pegawai.cuti') }}" 
                           class="text-sm {{ request()->routeIs('pegawai.cuti*') ? 'text-blue-600 font-semibold' : 'text-gray-600 hover:text-blue-600' }}">
                            <i class="fas fa-calendar-alt mr-1"></i> Cuti
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                        {{ Auth::user()->role }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700 text-sm">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                    <i class="fas fa-info-circle mr-2"></i> {{ session('info') }}
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>