<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        // Cek apakah IP sedang terblokir
        $key = 'login:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->route('login')
                ->with('blocked', true)
                ->with('retryAfter', $seconds);
        }

        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string|max:100',
            'password' => 'required|string|min:6|max:100',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.max' => 'Username maksimal 100 karakter.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.max' => 'Password maksimal 100 karakter.',
        ]);

        // ==================== CEK RATE LIMIT ====================
        $key = 'login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);

            throw ValidationException::withMessages([
                'username' => "🔒 Terlalu banyak percobaan login. Silakan coba lagi dalam {$minutes} menit.",
            ]);
        }
        // =======================================================

        // ==================== CASE-SENSITIVE USERNAME ====================
        $user = User::whereRaw('BINARY username = ?', [$request->username])->first();

        // Cek user ada
        if (!$user) {
            RateLimiter::hit($key, 60); // Tambah counter

            $sisa = 5 - RateLimiter::attempts($key);
            $sisa = max(0, $sisa);

            Log::warning('Login gagal - username tidak ditemukan', [
                'username' => $request->username,
                'ip' => $request->ip(),
                'sisa_percobaan' => $sisa,
            ]);

            throw ValidationException::withMessages([
                'username' => "Username atau password salah. Sisa kesempatan: {$sisa}x",
            ]);
        }

        // Cek password
        if (!Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60);

            $sisa = 5 - RateLimiter::attempts($key);
            $sisa = max(0, $sisa);

            Log::warning('Login gagal - password salah', [
                'username' => $request->username,
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'sisa_percobaan' => $sisa,
            ]);

            throw ValidationException::withMessages([
                'username' => "Username atau password salah. Sisa kesempatan: {$sisa}x",
            ]);
        }

        // Cek user aktif
        if (!$user->is_active) {
            Log::warning('Login gagal - akun tidak aktif', [
                'username' => $request->username,
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);

            throw ValidationException::withMessages([
                'username' => 'Akun Anda tidak aktif. Hubungi admin untuk mengaktifkan.',
            ]);
        }

        // ==================== LOGIN SUKSES ====================
        // Hapus rate limit counter
        RateLimiter::clear($key);

        // Login user
        Auth::login($user, $request->filled('remember'));

        // Regenerate session
        $request->session()->regenerate();

        // Update last login
        try {
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);
        } catch (\Exception $e) {
            // Skip kalau kolom tidak ada
        }

        // Log sukses
        Log::info('User login sukses', [
            'user_id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'ip' => $request->ip(),
        ]);

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            Log::info('User logout', [
                'user_id' => $user->id,
                'username' => $user->username,
                'ip' => $request->ip(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda berhasil logout.');
    }

    /**
     * Redirect berdasarkan role
     */
    protected function redirectBasedOnRole($user)
    {
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('pegawai.dashboard'));
    }
}