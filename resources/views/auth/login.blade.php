<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- ===== FAVICON ===== --}}
    @php
        use App\Helpers\AppHelper;
        $favicon = AppHelper::getFavicon();
        $bprName = AppHelper::getBprName();
        $logoUrl = AppHelper::getLogoOrDefault();
    @endphp
    <link rel="icon" href="{{ $favicon }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $favicon }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ $favicon }}">
    
    <title>Login - {{ $bprName }}</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
            padding: 20px;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 400px;
            width: 100%;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo img {
            max-height: 70px;
            width: auto;
            margin-bottom: 10px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .login-logo h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1a365d;
            margin-top: 8px;
            margin-bottom: 0;
        }
        .login-logo p {
            color: #718096;
            font-size: 14px;
            margin-top: 5px;
        }
        .login-form .form-group {
            margin-bottom: 20px;
        }
        .login-form label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #4a5568;
            margin-bottom: 5px;
        }
        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }
        .login-form input:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66,153,225,0.2);
        }
        .login-form .btn-login {
            width: 100%;
            padding: 12px;
            background: #4299e1;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .login-form .btn-login:hover {
            background: #3182ce;
        }
        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #a0aec0;
        }
        .login-error {
            background: #fed7d7;
            color: #c53030;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .login-remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #4a5568;
        }
        .login-remember input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #4299e1;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <!-- Logo -->
            <div class="login-logo">
                @if($logoUrl && file_exists(public_path(str_replace(asset(''), '', $logoUrl))))
                    <img src="{{ $logoUrl }}" alt="{{ $bprName }}">
                    <h1>{{ $bprName }}</h1>
                @else
                    <h1>🏦 {{ $bprName }}</h1>
                @endif
                <p>Sistem KPI & Penilaian Kinerja</p>
            </div>
            
            <!-- Error Messages -->
            @if($errors->any())
                <div class="login-error">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif
            
            <!-- Form Login -->
            <form method="POST" action="{{ route('login') }}" class="login-form">
                @csrf
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" 
                           value="{{ old('username') }}" required autofocus
                           placeholder="Masukkan username">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required
                           placeholder="Masukkan password">
                </div>
                
                <div class="form-group" style="display:flex; justify-content:space-between; align-items:center;">
                    <label class="login-remember">
                        <input type="checkbox" name="remember"> Ingat saya
                    </label>
                    <a href="#" style="color:#4299e1; font-size:14px; text-decoration:none;">Lupa password?</a>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </button>
            </form>
            
            <!-- Footer -->
            <div class="login-footer">
                &copy; {{ date('Y') }} {{ $bprName }}. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>