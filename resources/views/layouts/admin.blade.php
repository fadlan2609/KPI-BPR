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
    
    <title>@yield('title', Helper::getBprName())</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css'])
    @stack('styles')

    <style>
        /* Dropdown styles */
        .dropdown {
            position: relative;
        }
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 8px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            border: 1px solid #e5e7eb;
            min-width: 220px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
            z-index: 1000;
        }
        .dropdown:hover .dropdown-menu,
        .dropdown:focus-within .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .dropdown-menu a {
            display: block;
            padding: 10px 16px;
            color: #374151;
            text-decoration: none;
            font-size: 14px;
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.15s;
        }
        .dropdown-menu a:last-child {
            border-bottom: none;
        }
        .dropdown-menu a:hover {
            background-color: #eff6ff;
            color: #1d4ed8;
        }
        .dropdown-menu a i {
            width: 20px;
            margin-right: 10px;
            color: #6b7280;
        }
        .dropdown-menu a:hover i {
            color: #1d4ed8;
        }
        .dropdown-menu .active {
            background-color: #eff6ff;
            color: #1d4ed8;
        }
        .dropdown-menu .active i {
            color: #1d4ed8;
        }
        .dropdown-divider {
            height: 1px;
            background: #e5e7eb;
            margin: 4px 12px;
        }
        .dropdown-label {
            padding: 8px 16px 4px;
            font-size: 10px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Logo di navbar */
        .navbar-logo {
            height: 40px;
            width: auto;
            max-width: 120px;
            object-fit: contain;
        }
        .navbar-logo-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: #1F4E79;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    
    <!-- ========== NAVBAR ========== -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
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
                    </div>
                    
                    <!-- Nav Links -->
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        
                        <!-- Dashboard -->
                        <a href="{{ route('admin.dashboard') }}" 
                           class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'border-blue-500 text-gray-900' : '' }}">
                            <i class="fas fa-chart-pie mr-2"></i> Dashboard
                        </a>
                        
                        <!-- ===== INFORMASI BPR DROPDOWN ===== -->
                        <div class="dropdown">
                            <button class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.bpr*') || request()->routeIs('admin.jabatan*') || request()->routeIs('admin.kantor*') || request()->routeIs('admin.pegawai*') ? 'border-blue-500 text-gray-900' : '' }}">
                                <i class="fas fa-building mr-2"></i> Informasi BPR
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div class="dropdown-menu">
                                <div class="dropdown-label">Kelola Data</div>
                                <a href="{{ route('admin.bpr.index') }}" class="{{ request()->routeIs('admin.bpr*') ? 'active' : '' }}">
                                    <i class="fas fa-info-circle"></i> Identitas BPR
                                </a>
                                <a href="{{ route('admin.jabatan.index') }}" class="{{ request()->routeIs('admin.jabatan*') ? 'active' : '' }}">
                                    <i class="fas fa-briefcase"></i> Jabatan Pegawai
                                </a>
                                <a href="{{ route('admin.kantor.index') }}" class="{{ request()->routeIs('admin.kantor*') ? 'active' : '' }}">
                                    <i class="fas fa-store"></i> Daftar Kantor
                                </a>
                                <a href="{{ route('admin.pegawai.index') }}" class="{{ request()->routeIs('admin.pegawai*') ? 'active' : '' }}">
                                    <i class="fas fa-users"></i> Daftar Pegawai
                                </a>
                            </div>
                        </div>
                        
                        <!-- ===== BPR-KPI DROPDOWN ===== -->
                        <div class="dropdown">
                            <button class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.penilaian*') || request()->routeIs('admin.indikator*') || request()->routeIs('admin.predikat*') || request()->routeIs('admin.periode*') ? 'border-blue-500 text-gray-900' : '' }}">
                                <i class="fas fa-tasks mr-2"></i> BPR-KPI
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div class="dropdown-menu">
                                <div class="dropdown-label">Penilaian Kinerja</div>
                                <a href="{{ route('admin.penilaian.progress') }}" class="{{ request()->routeIs('admin.penilaian.progress') ? 'active' : '' }}">
                                    <i class="fas fa-flag-checkered"></i> Memulai KPI
                                </a>
                                <a href="{{ route('admin.periode.index') }}" class="{{ request()->routeIs('admin.periode*') ? 'active' : '' }}">
                                    <i class="fas fa-calendar-alt"></i> Periode Penilaian
                                </a>
                                <a href="{{ route('admin.predikat.index') }}" class="{{ request()->routeIs('admin.predikat*') ? 'active' : '' }}">
                                    <i class="fas fa-trophy"></i> Predikat Kinerja
                                </a>
                                <a href="{{ route('admin.indikator.index') }}" class="{{ request()->routeIs('admin.indikator*') ? 'active' : '' }}">
                                    <i class="fas fa-list-check"></i> Indikator Penilaian
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('admin.penilaian.index') }}" class="{{ request()->routeIs('admin.penilaian.index') ? 'active' : '' }}">
                                    <i class="fas fa-pen"></i> Penilaian Kinerja
                                </a>
                                <a href="{{ route('admin.penilaian.cetak') }}" class="{{ request()->routeIs('admin.penilaian.cetak*') ? 'active' : '' }}">
                                    <i class="fas fa-print"></i> Cetak Hasil Penilaian
                                </a>
                            </div>
                        </div>
                        
                        <!-- ===== GAJI DROPDOWN ===== -->
                        <div class="dropdown">
                            <button class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.gaji*') || request()->routeIs('admin.bonus*') ? 'border-blue-500 text-gray-900' : '' }}">
                                <i class="fas fa-money-bill-wave mr-2"></i> Gaji
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="{{ route('admin.gaji.index') }}" class="{{ request()->routeIs('admin.gaji*') ? 'active' : '' }}">
                                    <i class="fas fa-arrow-up"></i> Kenaikan Gaji
                                </a>
                                <a href="{{ route('admin.bonus.index') }}" class="{{ request()->routeIs('admin.bonus*') ? 'active' : '' }}">
                                    <i class="fas fa-gift"></i> Pembagian Bonus
                                </a>
                            </div>
                        </div>
                        
                        <!-- Cuti -->
                        <a href="{{ route('admin.cuti.index') }}" 
                           class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.cuti*') ? 'border-blue-500 text-gray-900' : '' }}">
                            <i class="fas fa-calendar-alt mr-2"></i> Cuti
                        </a>
                        
                        <!-- Laporan -->
                        <a href="{{ route('admin.laporan.index') }}" 
                           class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.laporan*') ? 'border-blue-500 text-gray-900' : '' }}">
                            <i class="fas fa-file-alt mr-2"></i> Laporan
                        </a>
                        
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
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
    
    <!-- ========== MAIN CONTENT ========== -->
    <main class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                </div>
            @endif
            
            @if(session('warning'))
                <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('warning') }}
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>