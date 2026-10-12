<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $bprFirst = \App\Models\BPR::first();
        $favicon = $bprFirst && $bprFirst->logo
            ? asset('storage/' . $bprFirst->logo)
            : asset('favicon.ico');
        $bprName = $bprFirst->nama_bpr ?? 'BPRS Amanah Bangsa';
    @endphp
    <link rel="icon" href="{{ $favicon }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $favicon }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ $favicon }}">

    <title>@yield('title', $bprName)</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
    @stack('styles')

    <style>
        /* ============================================================
           DROPDOWN MENU
           ============================================================ */
        .dropdown { position: relative; }
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
        .dropdown-menu a:last-child { border-bottom: none; }
        .dropdown-menu a:hover {
            background-color: #eff6ff;
            color: #1d4ed8;
        }
        .dropdown-menu a i {
            width: 20px;
            margin-right: 10px;
            color: #6b7280;
        }
        .dropdown-menu a:hover i { color: #1d4ed8; }
        .dropdown-menu .active {
            background-color: #eff6ff;
            color: #1d4ed8;
        }
        .dropdown-menu .active i { color: #1d4ed8; }
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
        .navbar-logo { height: 40px; width: auto; max-width: 150px; object-fit: contain; }
        .navbar-logo-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: #1F4E79;
        }
        .logo-text { font-size: 20px; font-weight: 800; color: #1F4E79; }
        .logo-text span { color: #2563eb; }
        .user-dropdown { position: relative; }
        .user-dropdown .dropdown-menu {
            right: 0;
            left: auto;
            min-width: 180px;
        }
        .user-dropdown .dropdown-menu a {
            padding: 8px 16px;
        }
        .user-dropdown .dropdown-menu a i {
            width: 18px;
            margin-right: 8px;
        }

        /* ============================================================
           NOTIFIKASI DROPDOWN
           ============================================================ */
        .notif-dropdown { position: relative; }
        .notif-dropdown .dropdown-menu {
            right: 0;
            left: auto;
            min-width: 340px;
            max-width: 400px;
            padding: 0;
        }
        .notif-header {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            background: #f9fafb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notif-header-title {
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .notif-header-action {
            font-size: 11px;
            color: #1d4ed8;
            font-weight: 600;
            text-decoration: none;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
        }
        .notif-header-action:hover { text-decoration: underline; }
        .notif-list {
            max-height: 400px;
            overflow-y: auto;
        }
        .notif-item {
            display: block;
            padding: 12px 16px;
            border-bottom: 1px solid #f9fafb;
            text-decoration: none;
            transition: background 0.15s;
        }
        .notif-item:hover { background: #f9fafb; }
        .notif-item.unread { background: #eff6ff; }
        .notif-item.unread:hover { background: #dbeafe; }
        .notif-item-inner {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .notif-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
        }
        .notif-icon-blue { background: #dbeafe; color: #1d4ed8; }
        .notif-icon-red { background: #fee2e2; color: #b91c1c; }
        .notif-icon-yellow { background: #fef3c7; color: #b45309; }
        .notif-icon-green { background: #d1fae5; color: #047857; }
        .notif-icon-purple { background: #ede9fe; color: #6d28d9; }
        .notif-icon-pink { background: #fce7f3; color: #be185d; }
        .notif-icon-gray { background: #f3f4f6; color: #6b7280; }
        .notif-content { flex: 1; min-width: 0; }
        .notif-title {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 2px 0;
            line-height: 1.3;
        }
        .notif-message {
            font-size: 12px;
            color: #6b7280;
            margin: 0 0 4px 0;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .notif-time {
            font-size: 10px;
            color: #9ca3af;
            margin: 0;
        }
        .notif-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #2563eb;
            flex-shrink: 0;
            margin-top: 6px;
        }
        .notif-empty {
            padding: 40px 20px;
            text-align: center;
        }
        .notif-empty-icon {
            font-size: 36px;
            margin-bottom: 8px;
            opacity: 0.5;
        }
        .notif-empty-text {
            font-size: 13px;
            color: #9ca3af;
            margin: 0;
        }
        .notif-footer {
            padding: 10px 16px;
            border-top: 1px solid #f3f4f6;
            background: #f9fafb;
            text-align: center;
        }
        .notif-footer a {
            font-size: 12px;
            color: #1d4ed8;
            font-weight: 600;
            text-decoration: none;
        }
        .notif-footer a:hover { text-decoration: underline; }
        .notif-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 700;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
        }
        .notif-btn {
            position: relative;
            padding: 6px 10px;
            border-radius: 8px;
            color: #4b5563;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.15s;
            font-size: 18px;
        }
        .notif-btn:hover { background: #f3f4f6; color: #1d4ed8; }

        /* ============================================================
           ADMIN ALERT — CLEAN STYLE
           ============================================================ */
        .admin-alert {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.1);
            position: relative;
            animation: adminAlertSlide 0.3s ease-out;
        }

        @keyframes adminAlertSlide {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .admin-alert-inner {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 20px;
            position: relative;
            z-index: 2;
        }

        .admin-alert-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.2);
        }

        .admin-alert-icon i {
            color: white;
            font-size: 18px;
        }

        .admin-alert-content {
            flex: 1;
            min-width: 0;
            padding-top: 2px;
        }

        .admin-alert-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.8;
            margin: 0 0 2px 0;
        }

        .admin-alert-message {
            font-size: 14px;
            font-weight: 600;
            line-height: 1.5;
            margin: 0;
        }

        .admin-alert-list {
            font-size: 13px;
            line-height: 1.6;
            margin: 4px 0 0 0;
            padding-left: 18px;
            list-style: disc;
        }

        .admin-alert-list li {
            margin-bottom: 2px;
        }

        .admin-alert-close {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
            opacity: 0.6;
            transition: all 0.2s;
            flex-shrink: 0;
            font-size: 14px;
        }

        .admin-alert-close:hover {
            opacity: 1;
            background: rgba(0, 0, 0, 0.05);
        }

        .admin-alert-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            opacity: 0.3;
            animation: adminAlertProgress linear forwards;
        }

        @keyframes adminAlertProgress {
            from { width: 100%; }
            to { width: 0%; }
        }

        .admin-alert[data-auto-dismiss="6000"] .admin-alert-progress {
            animation-duration: 6s;
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
                        @php
                            $bprNavbar = \App\Models\BPR::first();
                            $logoUrl = $bprNavbar && $bprNavbar->logo ? asset('storage/' . $bprNavbar->logo) : null;
                        @endphp

                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $bprName }}" class="navbar-logo">
                        @else
                            <span class="logo-text">🏦 <span>BPRS</span></span>
                            <span class="text-xl font-bold text-gray-800 ml-1">Amanah Bangsa</span>
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
                            <button class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.bpr*') || request()->routeIs('admin.jabatan*') || request()->routeIs('admin.kantor*') || request()->routeIs('admin.pegawai*') || request()->routeIs('admin.riwayat-jabatan*') ? 'border-blue-500 text-gray-900' : '' }}">
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
                                <a href="{{ route('admin.riwayat-jabatan.index') }}" class="{{ request()->routeIs('admin.riwayat-jabatan*') ? 'active' : '' }}">
                                    <i class="fas fa-history"></i> Riwayat Jabatan
                                    @php
                                        $pendingRiwayat = \App\Models\PermintaanEditRiwayat::where('status', 'pending')->count();
                                    @endphp
                                    @if($pendingRiwayat > 0)
                                        <span class="ml-2 px-1.5 py-0.5 bg-red-500 text-white text-xs rounded-full font-bold">
                                            {{ $pendingRiwayat }}
                                        </span>
                                    @endif
                                </a>
                                <a href="{{ route('admin.reward.index') }}" class="{{ request()->routeIs('admin.reward*') ? 'active' : '' }}">
                                    <i class="fas fa-gift" style="color: #22c55e;"></i> Reward
                                </a>
                                <a href="{{ route('admin.punishment.index') }}" class="{{ request()->routeIs('admin.punishment*') ? 'active' : '' }}">
                                    <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i> Punishment
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

                <!-- ===== USER MENU ===== -->
                <div class="flex items-center space-x-4">

                    <!-- ===== NOTIFIKASI DROPDOWN ===== -->
                    @php
                        $unreadCount = auth()->user()->unreadNotifications->count();
                        $recentNotifs = auth()->user()->notifications()->take(10)->get();
                    @endphp

                    <div class="notif-dropdown dropdown">
                        <button class="notif-btn">
                            <i class="fas fa-bell"></i>
                            @if($unreadCount > 0)
                                <span class="notif-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                            @endif
                        </button>

                        <div class="dropdown-menu">
                            <div class="notif-header">
                                <span class="notif-header-title">Notifikasi</span>
                                @if($unreadCount > 0)
                                    <form action="{{ route('admin.notifications.read-all') }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="notif-header-action">Tandai semua dibaca</button>
                                    </form>
                                @endif
                            </div>

                            <div class="notif-list">
                                @forelse($recentNotifs as $notif)
                                    @php
                                        $data = $notif->data;
                                        $iconClass = 'notif-icon-gray';
                                        $iconEmoji = '🔔';

                                        if (($data['type'] ?? '') == 'cuti_baru') {
                                            $iconClass = 'notif-icon-green';
                                            $iconEmoji = '📋';
                                        } elseif (($data['type'] ?? '') == 'surat_dokter_uploaded') {
                                            $iconClass = 'notif-icon-blue';
                                            $iconEmoji = '📎';
                                        } elseif (($data['type'] ?? '') == 'surat_dokter_rejected') {
                                            $iconClass = 'notif-icon-red';
                                            $iconEmoji = '❌';
                                        } elseif (($data['type'] ?? '') == 'reminder_surat_dokter') {
                                            $iconClass = 'notif-icon-yellow';
                                            $iconEmoji = '⚠️';
                                        }
                                    @endphp

                                    <a href="{{ $data['url'] ?? '#' }}"
                                       class="notif-item {{ $notif->read_at ? '' : 'unread' }}">
                                        <div class="notif-item-inner">
                                            <div class="notif-icon {{ $iconClass }}">{{ $iconEmoji }}</div>
                                            <div class="notif-content">
                                                <p class="notif-title">{{ $data['title'] ?? 'Notifikasi' }}</p>
                                                <p class="notif-message">{{ $data['message'] ?? '' }}</p>
                                                <p class="notif-time">{{ $notif->created_at->diffForHumans() }}</p>
                                            </div>
                                            @if(!$notif->read_at)
                                                <div class="notif-dot"></div>
                                            @endif
                                        </div>
                                    </a>
                                @empty
                                    <div class="notif-empty">
                                        <div class="notif-empty-icon">🔕</div>
                                        <p class="notif-empty-text">Belum ada notifikasi</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="notif-footer">
                                <a href="{{ route('admin.notifications.index') }}">Lihat semua notifikasi →</a>
                            </div>
                        </div>
                    </div>
                    <!-- ===== END NOTIFIKASI ===== -->

                    <a href="{{ route('admin.profile') }}" class="text-gray-700 hover:text-blue-600 text-sm flex items-center transition">
                        <i class="fas fa-user-circle mr-1 text-lg"></i>
                        <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                    </a>

                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 hidden sm:inline">
                        {{ ucfirst(Auth::user()->role) }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700 text-sm transition">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="hidden sm:inline"> Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- ========== MAIN CONTENT ========== -->
    <main class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ============================================================
                 FLASH MESSAGES — CLEAN STYLE
                 ============================================================ --}}
            @php
                $flashTypes = [
                    'success' => [
                        'icon' => 'check-circle',
                        'label' => 'Berhasil',
                        'bg' => 'linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)',
                        'border' => '#22c55e',
                        'icon_bg' => 'linear-gradient(135deg, #22c55e 0%, #15803d 100%)',
                        'text' => '#166534',
                    ],
                    'error' => [
                        'icon' => 'exclamation-circle',
                        'label' => 'Error',
                        'bg' => 'linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%)',
                        'border' => '#ef4444',
                        'icon_bg' => 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)',
                        'text' => '#991b1b',
                    ],
                    'warning' => [
                        'icon' => 'exclamation-triangle',
                        'label' => 'Perhatian',
                        'bg' => 'linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%)',
                        'border' => '#f59e0b',
                        'icon_bg' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                        'text' => '#92400e',
                    ],
                    'info' => [
                        'icon' => 'info-circle',
                        'label' => 'Informasi',
                        'bg' => 'linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)',
                        'border' => '#3b82f6',
                        'icon_bg' => 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)',
                        'text' => '#1e40af',
                    ],
                ];
            @endphp

            @foreach($flashTypes as $type => $style)
                @if(session($type))
                    <div class="admin-alert mb-4"
                         style="background: {{ $style['bg'] }}; border-left: 5px solid {{ $style['border'] }};"
                         data-auto-dismiss="6000">

                        <div class="admin-alert-inner">
                            <div class="admin-alert-icon" style="background: {{ $style['icon_bg'] }};">
                                <i class="fas fa-{{ $style['icon'] }}"></i>
                            </div>

                            <div class="admin-alert-content">
                                <p class="admin-alert-label" style="color: {{ $style['text'] }};">
                                    {{ $style['label'] }}
                                </p>
                                <p class="admin-alert-message" style="color: {{ $style['text'] }};">
                                    {{ session($type) }}
                                </p>
                            </div>

                            <button type="button"
                                    class="admin-alert-close"
                                    style="color: {{ $style['text'] }};"
                                    onclick="this.closest('.admin-alert').remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="admin-alert-progress" style="background: {{ $style['border'] }};"></div>
                    </div>
                @endif
            @endforeach

            {{-- VALIDATION ERRORS --}}
            @if($errors->any())
                <div class="admin-alert mb-4"
                     style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-left: 5px solid #ef4444;">

                    <div class="admin-alert-inner">
                        <div class="admin-alert-icon" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>

                        <div class="admin-alert-content">
                            <p class="admin-alert-label" style="color: #991b1b;">
                                Terjadi Kesalahan
                            </p>
                            <ul class="admin-alert-list" style="color: #991b1b;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <button type="button"
                                class="admin-alert-close"
                                style="color: #991b1b;"
                                onclick="this.closest('.admin-alert').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/app.js'])

    {{-- Auto dismiss alert --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.admin-alert[data-auto-dismiss]').forEach(function(alert) {
                const duration = parseInt(alert.dataset.autoDismiss) || 6000;

                setTimeout(function() {
                    alert.style.transition = 'all 0.3s ease-out';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';

                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                }, duration);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>