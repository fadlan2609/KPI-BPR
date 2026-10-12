<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="{{ $seconds }};url={{ route('login') }}">
    <title>Login Terblokir</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .blocked-card {
            background: #fff;
            border-radius: 16px;
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(239, 68, 68, 0.15);
            border: 1px solid #fecaca;
        }
        .blocked-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }
        .blocked-title {
            font-size: 24px;
            font-weight: 800;
            color: #991b1b;
            margin-bottom: 8px;
        }
        .blocked-subtitle {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .blocked-timer {
            background: #fef2f2;
            border: 2px solid #fecaca;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .timer-label {
            font-size: 12px;
            font-weight: 600;
            color: #991b1b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .timer-value {
            font-size: 40px;
            font-weight: 800;
            color: #b91c1c;
            font-variant-numeric: tabular-nums;
            line-height: 1;
        }
        .timer-unit {
            font-size: 16px;
            font-weight: 600;
            color: #b91c1c;
            margin-left: 8px;
        }
        .timer-progress {
            background: #fee2e2;
            height: 6px;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 12px;
        }
        .timer-progress-bar {
            background: linear-gradient(90deg, #ef4444, #b91c1c);
            height: 100%;
            width: 100%;
            animation: progress linear forwards;
            animation-duration: {{ $seconds }}s;
        }
        @keyframes progress {
            from { width: 100%; }
            to { width: 0%; }
        }
        .blocked-actions {
            display: flex;
            gap: 8px;
            flex-direction: column;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.15s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            color: #fff;
        }
        .btn-primary:hover { opacity: 0.9; }
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }
        .btn-secondary:hover { background: #e5e7eb; }
        .blocked-info {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #fecaca;
            font-size: 12px;
            color: #9ca3af;
            line-height: 1.6;
        }
    </style>
</head>
<body>

<div class="blocked-card">
    <div class="blocked-icon">🔒</div>

    <h1 class="blocked-title">Login Terblokir Sementara</h1>
    <p class="blocked-subtitle">
        Anda telah melakukan percobaan login terlalu banyak.<br>
        Silakan tunggu sebelum mencoba lagi.
    </p>

    <div class="blocked-timer">
        <div class="timer-label">Bisa coba lagi dalam</div>
        <div>
            <span class="timer-value" id="timer">{{ $seconds }}</span>
            <span class="timer-unit">detik</span>
        </div>
        <div class="timer-progress">
            <div class="timer-progress-bar"></div>
        </div>
    </div>

    <div class="blocked-actions">
        <a href="{{ route('login') }}" class="btn btn-primary" id="btn-refresh">
            🔄 Refresh Halaman Sekarang
        </a>
        <a href="{{ url('/') }}" class="btn btn-secondary">
            🏠 Kembali ke Beranda
        </a>
    </div>

    <div class="blocked-info">
        <strong>Kenapa ini terjadi?</strong><br>
        Untuk keamanan, sistem membatasi 5 percobaan login per menit per alamat IP.
        <br><br>
        Kalau Anda lupa password, hubungi admin untuk reset.
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let seconds = {{ $seconds }};
        const timerEl = document.getElementById('timer');
        const btnRefresh = document.getElementById('btn-refresh');

        const interval = setInterval(function() {
            seconds--;

            if (seconds <= 0) {
                clearInterval(interval);
                timerEl.textContent = '0';
                timerEl.style.color = '#047857';
                btnRefresh.innerHTML = '✅ Klik untuk Login';
                btnRefresh.style.background = 'linear-gradient(135deg, #047857 0%, #065f46 100%)';

                // Auto refresh setelah 2 detik
                setTimeout(function() {
                    window.location.href = '{{ route('login') }}';
                }, 2000);
            } else {
                timerEl.textContent = seconds;
            }
        }, 1000);
    });
</script>

</body>
</html>