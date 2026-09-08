@extends('layouts.admin')

@section('title', 'Profile Admin')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Profile Admin</h1>
            <p class="text-sm text-gray-500">Kelola informasi akun dan password Anda</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Edit Profile -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-user-edit text-blue-600 mr-2"></i> Edit Profil
            </h2>

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <input type="text" value="{{ ucfirst($user->role) }}" disabled
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <i class="fas fa-save mr-2"></i> Update Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- Ganti Password -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-key text-red-600 mr-2"></i> Ganti Password
            </h2>

            <form action="{{ route('admin.profile.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition @error('current_password') border-red-500 @enderror pr-10">
                            <button type="button" onclick="togglePassword('current_password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition @error('password') border-red-500 @enderror pr-10">
                            <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan simbol</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition pr-10">
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        <i class="fas fa-key mr-2"></i> Ganti Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.nextElementSibling.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush
@endsection