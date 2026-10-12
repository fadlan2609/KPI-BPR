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

    <title>@yield('title', $bprName . ' - Pegawai')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
    @stack('styles')

    <style>
        .navbar-logo { height: 40px; width: auto; max-width: 120px; object-fit: contain; }
        .dropdown { position: relative; }
        .dropdown-menu {
            position: absolute; top: 100%; left: 0; margin-top: 8px;
            background: white; border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            border: 1px solid #e5e7eb; min-width: 220px;
            opacity: 0; visibility: hidden; transform: translateY(-10px);
            transition: all 0.2s ease; z-index: 1000;
        }
        .dropdown:hover .dropdown-menu,
        .dropdown:focus-within .dropdown-menu {
            opacity: 1; visibility: visible; transform: translateY(0);
        }
        .dropdown-menu a {
            display: block; padding: 10px 16px; color: #374151;
            text-decoration: none; font-size: 14px;
            border-bottom: 1px solid #f3f4f6; transition: all 0.15s;
        }
        .dropdown-menu a:last-child { border-bottom: none; }
        .dropdown-menu a:hover { background-color: #eff6ff; color: #1d4ed8; }
        .dropdown-menu a i { width: 20px; margin-right: 10px; color: #6b7280; }
        .dropdown-menu a:hover i { color: #1d4ed8; }
        .dropdown-menu .active { background-color: #eff6ff; color: #1d4ed8; }
        .dropdown-menu .active i { color: #1d4ed8; }
        .dropdown-divider { height: 1px; background: #e5e7eb; margin: 4px 12px; }
        .dropdown-label {
            padding: 8px 16px 4px; font-size: 10px; font-weight: 600;
            color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .nav-item {
            display: inline-flex; align-items: center; padding: 8px 12px;
            font-size: 13px; color: #4b5563; text-decoration: none;
            border-radius: 6px; transition: all 0.15s; font-weight: 500;
        }
        .nav-item:hover { background-color: #f3f4f6; color: #1d4ed8; }
        .nav-item.active { color: #1d4ed8; font-weight: 600; background-color: #eff6ff; }
        .nav-item i { margin-right: 6px; }
        .badge-notif {
            display: inline-block; background: #ef4444; color: white;
            font-size: 10px; font-weight: 700; padding: 2px 6px;
            border-radius: 10px; margin-left: 4px; min-width: 18px; text-align: center;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        /* Foto profil navbar */
        .avatar-navbar {
            width: 36px; height: 36px;
            border-radius: 9999px;
            overflow: hidden;
            border: 2px solid #3b82f6;
            transition: all 0.15s;
            display: flex; align-items: center; justify-content: center;
            background: #2563eb;
        }
        .avatar-navbar:hover { border-color: #1d4ed8; transform: scale(1.05); }
        .avatar-navbar img { width: 100%; height: 100%; object-fit: cover; display: block; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

    @php
        // Ambil pegawai yang login untuk foto profil navbar
        $pegawaiNavbar = Auth::user()->pegawai ?? null;
        $fotoProfilNavbar = $pegawaiNavbar->foto_profil ?? null;
        $hasFotoProfil = $fotoProfilNavbar && file_exists(storage_path('app/public/' . $fotoProfilNavbar));

        if ($hasFotoProfil) {
            $navCx = ($pegawaiNavbar->foto_crop_x ?? 0.5) * 100;
            $navCy = ($pegawaiNavbar->foto_crop_y ?? 0.5) * 100;
            $navCz = $pegawaiNavbar->foto_crop_zoom ?? 1;
            $navCr = $pegawaiNavbar->foto_crop_rotate ?? 0;
        }
    @endphp

    <nav class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                {{-- ===== KIRI: LOGO + MENU ===== --}}
                <div class="flex items-center space-x-4">

                    @php
                        $bprNavbar = \App\Models\BPR::first();
                        $logoUrl = $bprNavbar && $bprNavbar->logo ? asset('storage/' . $bprNavbar->logo) : null;
                    @endphp

                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo BPR" class="navbar-logo">
                    @else
                        <div class="flex items-center">
                            <span class="text-xl font-bold text-blue-600">🏦 BPRS</span>
                            <span class="text-xl font-bold text-gray-800 ml-1 hidden sm:inline">Amanah Bangsa</span>
                        </div>
                    @endif

                    <span class="hidden sm:inline-block px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full font-semibold">
                        Pegawai
                    </span>

                    @php
                        $userPegawai = Auth::user()->pegawai;
                        $jumlahBawahan = $userPegawai ? $userPegawai->bawahanLangsung()->count() : 0;
                        $cutiPending = $userPegawai
                            ? \App\Models\CutiApproval::where('approver_id', $userPegawai->id)
                                ->where('status', 'pending')
                                ->count()
                            : 0;
                    @endphp

                    <div class="hidden lg:flex items-center space-x-1 ml-4">

                        {{-- Dashboard --}}
                        <a href="{{ route('pegawai.dashboard') }}"
                           class="nav-item {{ request()->routeIs('pegawai.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>

                        {{-- Self Assessment --}}
                        <a href="{{ route('pegawai.self-assessment.index') }}"
                           class="nav-item {{ request()->routeIs('pegawai.self-assessment*') ? 'active' : '' }}">
                            <i class="fas fa-user-check"></i> Self Assessment
                        </a>

                        {{-- Penilaian Bawahan (SELALU TAMPIL kalau punya bawahan) --}}
                        @if($userPegawai && $jumlahBawahan > 0)
                            <a href="{{ route('pegawai.penilaian-bawahan.index') }}"
                               class="nav-item {{ request()->routeIs('pegawai.penilaian-bawahan*') ? 'active' : '' }}">
                                <i class="fas fa-users"></i> Penilaian Bawahan
                                <span class="badge-notif">{{ $jumlahBawahan }}</span>
                            </a>
                        @endif

                        {{-- Hasil Penilaian --}}
                        <a href="{{ route('pegawai.hasil-penilaian') }}"
                           class="nav-item {{ request()->routeIs('pegawai.hasil-penilaian') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i> Hasil Penilaian
                        </a>

                        {{-- Riwayat Jabatan --}}
                        <a href="{{ route('pegawai.riwayat-jabatan.index') }}"
                           class="nav-item {{ request()->routeIs('pegawai.riwayat-jabatan*') ? 'active' : '' }}">
                            <i class="fas fa-briefcase"></i> Riwayat Jabatan
                        </a>

                        {{-- ===== REWARD & PUNISHMENT DROPDOWN ===== --}}
                        <div class="dropdown">
                            <button class="nav-item {{ request()->routeIs('pegawai.reward*') || request()->routeIs('pegawai.punishment*') || request()->routeIs('pegawai.atasan-reward*') || request()->routeIs('pegawai.atasan-punishment*') ? 'active' : '' }}">
                                <i class="fas fa-trophy"></i> R/P
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div class="dropdown-menu">
                                <div class="dropdown-label">Reward & Punishment Saya</div>
                                <a href="{{ route('pegawai.reward.index') }}" class="{{ request()->routeIs('pegawai.reward*') ? 'active' : '' }}">
                                    <i class="fas fa-gift" style="color: #22c55e;"></i> Reward Saya
                                </a>
                                <a href="{{ route('pegawai.punishment.index') }}" class="{{ request()->routeIs('pegawai.punishment*') ? 'active' : '' }}">
                                    <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i> Punishment Saya
                                </a>

                                @if($userPegawai && $jumlahBawahan > 0)
                                    <div class="dropdown-divider"></div>
                                    <div class="dropdown-label">Input untuk Bawahan</div>
                                    <a href="{{ route('pegawai.atasan-reward.index') }}" class="{{ request()->routeIs('pegawai.atasan-reward*') ? 'active' : '' }}">
                                        <i class="fas fa-user-shield" style="color: #22c55e;"></i> Input Reward
                                    </a>
                                    <a href="{{ route('pegawai.atasan-punishment.index') }}" class="{{ request()->routeIs('pegawai.atasan-punishment*') ? 'active' : '' }}">
                                        <i class="fas fa-user-shield" style="color: #ef4444;"></i> Input Punishment
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Cuti Dropdown --}}
                        <div class="dropdown">
                            <button class="nav-item {{ request()->routeIs('pegawai.cuti*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt"></i> Cuti
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div class="dropdown-menu">
                                <div class="dropdown-label">Manajemen Cuti</div>
                                <a href="{{ route('pegawai.cuti.index') }}"
                                   class="{{ request()->routeIs('pegawai.cuti.index') ? 'active' : '' }}">
                                    <i class="fas fa-list"></i> Cuti Saya
                                </a>
                                <a href="{{ route('pegawai.cuti.approval.index') }}"
                                   class="{{ request()->routeIs('pegawai.cuti.approval*') ? 'active' : '' }}">
                                    <i class="fas fa-check-circle"></i> Approval Cuti
                                    @if($cutiPending > 0)
                                        <span class="badge-notif">{{ $cutiPending }}</span>
                                    @endif
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('pegawai.cuti.saldo') }}">
                                    <i class="fas fa-wallet"></i> Saldo Cuti
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== KANAN: USER MENU ===== --}}
                <div class="flex items-center space-x-3">

                    {{-- BELL --}}
                    <div class="relative" id="notif-wrapper" style="position: relative;">
                        <button onclick="toggleNotif()"
                                class="relative text-gray-500 hover:text-blue-600 p-2 rounded-full hover:bg-blue-50 transition"
                                style="position: relative;">
                            <i class="fas fa-bell text-lg"></i>
                            <span id="notif-badge"
                                  style="display: none; position: absolute; top: -4px; left: -4px; background: #ef4444; color: white; font-size: 11px; font-weight: 700; min-width: 20px; height: 20px; border-radius: 9999px; align-items: center; justify-content: center; padding: 0 5px; border: 2px solid white; box-shadow: 0 2px 6px rgba(239,68,68,0.4); line-height: 1;">
                                0
                            </span>
                        </button>

                        <div id="notif-dropdown"
                             class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-hidden">
                            <div class="px-4 py-3 border-b bg-gray-50 flex justify-between items-center">
                                <h3 class="font-semibold text-gray-800 text-sm">
                                    <i class="fas fa-bell mr-1 text-blue-600"></i> Notifikasi
                                </h3>
                                <button onclick="markAllRead()" class="text-xs text-blue-600 hover:underline">
                                    Tandai semua
                                </button>
                            </div>
                            <div id="notif-list" class="overflow-y-auto max-h-72">
                                <div class="p-4 text-center text-gray-400 text-sm">
                                    <i class="fas fa-spinner fa-spin"></i> Memuat...
                                </div>
                            </div>
                            <div class="border-t bg-gray-50 px-4 py-2 text-center">
                                <a href="{{ route('pegawai.notifications.index') }}"
                                   class="text-xs text-blue-600 hover:underline">
                                    Lihat Semua Notifikasi
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('pegawai.profil.index') }}" class="hidden sm:block text-right hover:opacity-75">
                        <div class="text-sm font-semibold text-gray-800 hover:text-blue-600">
                            {{ Auth::user()->name }}
                        </div>
                        <div class="text-xs text-gray-500">{{ Auth::user()->role }}</div>
                    </a>

                    {{-- ===== AVATAR FOTO PROFIL (GANTI HURUF INISIAL) ===== --}}
                    <a href="{{ route('pegawai.profil.index') }}"
                       class="avatar-navbar"
                       title="Profil Saya">
                        @if($hasFotoProfil)
                            <img src="{{ route('storage.file', ['path' => $fotoProfilNavbar]) }}?v={{ time() }}"
                                 alt="{{ $pegawaiNavbar->nama }}"
                                 style="object-position: {{ $navCx }}% {{ $navCy }}%;
                                        transform: scale({{ $navCz }}) rotate({{ $navCr }}deg);">
                        @else
                            <span class="text-white font-bold text-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        @endif
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="text-gray-500 hover:text-red-600 text-sm transition p-2 rounded-full hover:bg-red-50"
                                title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Menu Mobile --}}
            <div class="lg:hidden border-t border-gray-200 py-2 overflow-x-auto">
                <div class="flex space-x-1 min-w-max">
                    <a href="{{ route('pegawai.dashboard') }}" class="nav-item {{ request()->routeIs('pegawai.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="{{ route('pegawai.self-assessment.index') }}" class="nav-item {{ request()->routeIs('pegawai.self-assessment*') ? 'active' : '' }}">
                        <i class="fas fa-user-check"></i> Self Assessment
                    </a>
                    @if($userPegawai && $jumlahBawahan > 0)
                        <a href="{{ route('pegawai.penilaian-bawahan.index') }}" class="nav-item {{ request()->routeIs('pegawai.penilaian-bawahan*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Penilaian
                        </a>
                    @endif
                    <a href="{{ route('pegawai.hasil-penilaian') }}" class="nav-item {{ request()->routeIs('pegawai.hasil-penilaian') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i> Hasil
                    </a>
                    <a href="{{ route('pegawai.riwayat-jabatan.index') }}" class="nav-item {{ request()->routeIs('pegawai.riwayat-jabatan*') ? 'active' : '' }}">
                        <i class="fas fa-briefcase"></i> Mutasi
                    </a>
                    <a href="{{ route('pegawai.reward.index') }}" class="nav-item {{ request()->routeIs('pegawai.reward*') ? 'active' : '' }}">
                        <i class="fas fa-gift"></i> Reward
                    </a>
                    <a href="{{ route('pegawai.punishment.index') }}" class="nav-item {{ request()->routeIs('pegawai.punishment*') ? 'active' : '' }}">
                        <i class="fas fa-exclamation-triangle"></i> Punishment
                    </a>
                    @if($userPegawai && $jumlahBawahan > 0)
                        <a href="{{ route('pegawai.atasan-reward.index') }}" class="nav-item {{ request()->routeIs('pegawai.atasan-reward*') ? 'active' : '' }}">
                            <i class="fas fa-user-shield"></i> Input R
                        </a>
                        <a href="{{ route('pegawai.atasan-punishment.index') }}" class="nav-item {{ request()->routeIs('pegawai.atasan-punishment*') ? 'active' : '' }}">
                            <i class="fas fa-user-shield"></i> Input P
                        </a>
                    @endif
                    <a href="{{ route('pegawai.cuti.index') }}" class="nav-item {{ request()->routeIs('pegawai.cuti.index') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i> Cuti
                    </a>
                    <a href="{{ route('pegawai.cuti.approval.index') }}" class="nav-item {{ request()->routeIs('pegawai.cuti.approval*') ? 'active' : '' }}">
                        <i class="fas fa-check-circle"></i> Approval
                    </a>
                    <a href="{{ route('pegawai.profil.index') }}" class="nav-item {{ request()->routeIs('pegawai.profil*') ? 'active' : '' }}">
                        <i class="fas fa-user"></i> Profil
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded shadow-sm flex items-start">
                    <i class="fas fa-check-circle mr-3 mt-0.5"></i>
                    <div class="flex-1">{{ session('success') }}</div>
                    <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded shadow-sm flex items-start">
                    <i class="fas fa-exclamation-circle mr-3 mt-0.5"></i>
                    <div class="flex-1">{{ session('error') }}</div>
                    <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
            @if(session('warning'))
                <div class="mb-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 px-4 py-3 rounded shadow-sm flex items-start">
                    <i class="fas fa-exclamation-triangle mr-3 mt-0.5"></i>
                    <div class="flex-1">{{ session('warning') }}</div>
                    <button onclick="this.parentElement.remove()" class="text-yellow-700 hover:text-yellow-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
            @if(session('info'))
                <div class="mb-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 px-4 py-3 rounded shadow-sm flex items-start">
                    <i class="fas fa-info-circle mr-3 mt-0.5"></i>
                    <div class="flex-1">{{ session('info') }}</div>
                    <button onclick="this.parentElement.remove()" class="text-blue-700 hover:text-blue-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded shadow-sm">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle mr-3 mt-0.5"></i>
                        <div class="flex-1">
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="list-disc list-inside mt-1 text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center text-sm text-gray-500">
                <div>&copy; {{ date('Y') }} {{ $bprName }}. All rights reserved.</div>
                <div>Sistem KPI v1.0</div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite(['resources/js/app.js'])

    <script>
        function toggleNotif() {
            const dropdown = document.getElementById('notif-dropdown');
            dropdown.classList.toggle('hidden');
            if (!dropdown.classList.contains('hidden')) loadNotif();
        }
        function loadNotif() {
            fetch('{{ route("pegawai.notifications.latest") }}')
                .then(r => r.json())
                .then(data => {
                    const badge = document.getElementById('notif-badge');
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                        badge.style.display = 'flex';
                    } else { badge.style.display = 'none'; }
                    const list = document.getElementById('notif-list');
                    if (data.notifications.length === 0) {
                        list.innerHTML = `<div class="p-6 text-center text-gray-400 text-sm"><i class="fas fa-bell-slash text-2xl mb-1 block"></i>Tidak ada notifikasi</div>`;
                        return;
                    }
                    list.innerHTML = data.notifications.map(n => `
                        <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer ${n.is_read ? '' : 'bg-blue-50'}" onclick="openNotif('${n.id}', '${n.url}')">
                            <div class="flex items-start space-x-2">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center ${n.type === 'cuti_disetujui' || n.type === 'reward' ? 'bg-green-100 text-green-600' : n.type === 'cuti_ditolak' || n.type === 'punishment' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600'}">
                                    <i class="fas fa-${n.icon} text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-800 ${n.is_read ? '' : 'font-bold'}">${n.title}</p>
                                    <p class="text-xs text-gray-600 mt-0.5 line-clamp-2">${n.message}</p>
                                    <p class="text-xs text-gray-400 mt-1">${n.time_ago}</p>
                                </div>
                            </div>
                        </div>`).join('');
                }).catch(err => console.error(err));
        }
        function openNotif(id, url) {
            fetch(`{{ url('pegawai/notifications') }}/${id}/read`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(r => r.json()).then(data => {
                if (data.url) window.location.href = data.url;
                else if (url && url !== 'null' && url !== '#') window.location.href = url;
            }).catch(() => {
                if (url && url !== 'null' && url !== '#') window.location.href = url;
            });
        }
        function markAllRead() {
            fetch('{{ route("pegawai.notifications.read-all") }}', {
                method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => loadNotif());
        }
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('notif-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('notif-dropdown')?.classList.add('hidden');
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            function refreshBadge() {
                fetch('{{ route("pegawai.notifications.latest") }}')
                    .then(r => r.json()).then(data => {
                        const badge = document.getElementById('notif-badge');
                        if (data.unread_count > 0) {
                            badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                            badge.style.display = 'flex';
                        } else { badge.style.display = 'none'; }
                    }).catch(() => {});
            }
            refreshBadge();
            setInterval(refreshBadge, 60000);
        });
    </script>

    @stack('scripts')
</body>
</html>