<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP — {{ config('app.name', 'ICB CT') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            height: 100%; min-height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center; flex-direction: column;
            padding: 24px 16px;
            background: linear-gradient(135deg, #F1F5F9 0%, #E2E8F0 100%);
        }

        .auth-wrapper {
            width: 100%;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
        }

        .page-card {
            position: relative;
            width: 800px; max-width: calc(100% - 32px);
            background: #f8fafc;
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(15,23,42,0.18), 0 0 0 1px rgba(15,23,42,0.05);
            overflow: hidden;
            display: flex; flex-direction: row;
            min-height: 480px;
            margin-top: 22px;
            animation: cardIn 0.45s cubic-bezier(0.22,1,0.36,1) both;
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(28px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Panel Kiri (Navy) ── */
        .card-panel {
            width: 50%; flex-shrink: 0;
            background: linear-gradient(150deg, #050d1a 0%, #0b1a2d 52%, #101f34 100%);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 40px 35px; text-align: center; position: relative; overflow: hidden;
        }
        .card-panel::before {
            content: ''; position: absolute; top: -80px; right: -80px;
            width: 320px; height: 320px; border-radius: 50%;
            background: radial-gradient(circle, rgba(250,204,21,0.09) 0%, transparent 65%);
            pointer-events: none;
        }
        .card-panel::after {
            content: ''; position: absolute; bottom: -80px; left: -80px;
            width: 280px; height: 280px; border-radius: 50%;
            background: radial-gradient(circle, rgba(99,102,241,0.07) 0%, transparent 65%);
            pointer-events: none;
        }
        .icon-box {
            width: 110px; height: 110px; margin: 0 auto 22px; border-radius: 22px;
            background: rgba(255,255,255,0.04); border: 1.5px solid rgba(250,204,21,0.28);
            box-shadow: 0 16px 40px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.07);
            display: flex; align-items: center; justify-content: center;
            position: relative; z-index: 1; backdrop-filter: blur(8px);
        }
        .icon-box img { width: 78%; height: 78%; object-fit: contain; filter: drop-shadow(0 3px 10px rgba(0,0,0,0.3)); }
        .panel-title {
            font-size: 1.5rem; font-weight: 800; color: #fff;
            letter-spacing: -0.3px; position: relative; z-index: 1;
            margin-bottom: 8px; line-height: 1.3;
        }
        .panel-sub {
            font-size: 0.85rem; color: rgba(255,255,255,0.45);
            position: relative; z-index: 1; line-height: 1.65;
            max-width: 240px; margin: 0 auto;
        }

        /* ── Panel Kanan (Form) ── */
        .card-body {
            width: 50%; flex-shrink: 0;
            padding: 42px 52px 36px;
            display: flex; flex-direction: column; justify-content: center;
            background: #f5f5f5; overflow-y: auto;
            scrollbar-width: none; -ms-overflow-style: none;
        }
        .card-body::-webkit-scrollbar { display: none; }

        .section-title {
            font-size: 1.8rem; font-weight: 800; color: #0F172A;
            letter-spacing: -0.5px; margin-bottom: 6px;
        }
        .section-sub {
            font-size: 0.88rem; color: #64748B; line-height: 1.65; margin-bottom: 28px;
        }
        .email-highlight {
            font-weight: 700; color: #0F172A;
            background: #F1F5F9; padding: 1px 8px; border-radius: 5px;
        }

        /* Alert */
        .alert {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 13px 15px; border-radius: 12px;
            font-size: 0.83rem; font-weight: 500; line-height: 1.5; margin-bottom: 20px;
        }
        .alert-error   { background: #FEF2F2; border: 1px solid #FECACA; color: #B91C1C; }
        .alert-success { background: #F0FDF4; border: 1px solid #BBF7D0; color: #15803D; }
        .alert svg { flex-shrink: 0; margin-top: 1px; }

        /* OTP Input Grid */
        .otp-group {
            display: flex; gap: 10px; justify-content: center;
            margin-bottom: 24px;
        }
        .otp-digit {
            width: 52px; height: 62px;
            text-align: center; font-size: 1.6rem; font-weight: 800;
            color: #0F172A; background: #F8FAFC;
            border: 1.5px solid #E2E8F0; border-radius: 8px;
            outline: none; font-family: 'Inter', monospace;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            -webkit-appearance: none; appearance: none;
            caret-color: transparent;
        }
        .otp-digit:focus {
            border-color: #0F172A; background: #fff;
            box-shadow: 0 0 0 4px rgba(15,23,42,0.08);
        }
        .otp-digit.filled { border-color: #0F172A; background: #fff; }
        .otp-digit.error  { border-color: #EF4444 !important; background: #FFF5F5 !important; box-shadow: 0 0 0 3px rgba(239,68,68,0.12) !important; }

        /* Hidden real input */
        #otpHidden { display: none; }

        /* Field error */
        .field-error { font-size: 0.78rem; color: #DC2626; margin-bottom: 16px; text-align: center; }

        /* Button */
        .btn-primary {
            width: 100%; height: 52px;
            background: #0F172A; color: #fff; border: none; border-radius: 12px;
            font-size: 0.98rem; font-weight: 700; font-family: inherit;
            letter-spacing: 0.02em; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: transform 0.15s, box-shadow 0.2s, background 0.2s;
            box-shadow: 0 4px 20px rgba(15,23,42,0.25); margin-top: 0;
            -webkit-tap-highlight-color: transparent; position: relative; overflow: hidden;
        }
        .btn-primary:hover { background: #1a2540; box-shadow: 0 10px 30px rgba(15,23,42,0.35); transform: translateY(-2px); }
        .btn-primary:active { transform: scale(0.98); }
        .btn-primary:disabled { opacity: 0.55; cursor: not-allowed; transform: none; }
        .btn-primary.loading { pointer-events: none; }
        .btn-primary .btn-text { transition: opacity 0.15s; }
        .btn-primary .btn-spinner { display: none; }
        .btn-primary.loading .btn-text { display: none; }
        .btn-primary.loading .btn-spinner { display: flex; }
        .spinner {
            width: 22px; height: 22px;
            border: 2.5px solid rgba(255,255,255,0.3);
            border-top-color: #fff; border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Timer & resend */
        .resend-area {
            text-align: center; margin-top: 18px;
            font-size: 0.84rem; color: #94A3B8;
        }
        .resend-area form { display: inline; }
        .resend-btn {
            background: none; border: none; cursor: pointer;
            font-size: 0.84rem; font-weight: 600; color: #0F172A;
            text-decoration: underline; padding: 0; font-family: inherit;
            transition: color 0.2s;
        }
        .resend-btn:disabled { color: #94A3B8; text-decoration: none; cursor: not-allowed; }
        .resend-btn:not(:disabled):hover { color: #FACC15; }
        #timerText { font-weight: 600; color: #0F172A; }

        /* Back link */
        .back-link {
            display: flex; align-items: center; justify-content: center;
            gap: 6px; margin-top: 16px;
            font-size: 0.84rem; font-weight: 600; color: #64748B;
            text-decoration: none; transition: color 0.2s;
        }
        .back-link:hover { color: #0F172A; }

        /* Desktop credit */
        .auth-credit {
            text-align: center; padding: 14px 16px 20px;
            font-size: 11px; font-family: 'Inter', sans-serif; font-weight: 600;
            color: #94a3b8; text-transform: uppercase; letter-spacing: 0.12em;
            margin-top: 18px; width: 100%;
            display: flex; align-items: center; justify-content: center;
        }
        .auth-credit a { color: #94a3b8; text-decoration: none; transition: color 0.2s ease; margin-left: 4px; }
        .auth-credit a:hover { color: #FACC15; }
        .mobile-credit { display: none; }
        @media (max-width: 768px) {
            html, body { height: 100%; min-height: 100vh; background: #080F1E; padding: 0; margin: 0; display: block; }
            body { display: flex; flex-direction: column; align-items: stretch; }
            .auth-wrapper { display: block; width: 100%; }
            .auth-credit { display: none !important; }
            .mobile-credit {
                display: flex !important; position: fixed; bottom: 0; left: 0; right: 0;
                align-items: center; justify-content: center;
                width: 100%; padding: 10px 16px 20px;
                font-size: 10px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase;
                color: #94a3b8; background: #FFFFFF; z-index: 9999;
            }
            .mobile-credit a { color: #94a3b8; text-decoration: none; margin-left: 4px; }
            .page-card {
                max-width: 100%; width: 100%; min-height: 100vh; min-height: 100svh;
                border-radius: 0; box-shadow: none; display: flex; flex-direction: column;
                animation: none; margin-top: 0;
            }
            .card-panel { display: none !important; }
            .mobile-header-band {
                display: flex !important; flex-direction: column; align-items: center; justify-content: center;
                padding: 56px 28px 52px;
                background: linear-gradient(150deg, #080F1E 0%, #0F172A 60%, #162035 100%);
                text-align: center; position: relative; overflow: hidden; flex-shrink: 0;
            }
            .mobile-header-band::before {
                content: ''; position: absolute; top: -80px; right: -80px;
                width: 300px; height: 300px; border-radius: 50%;
                background: radial-gradient(circle, rgba(250,204,21,0.09) 0%, transparent 65%);
            }
            .mobile-header-band::after {
                content: ''; position: absolute; bottom: -60px; left: -60px;
                width: 240px; height: 240px; border-radius: 50%;
                background: radial-gradient(circle, rgba(99,102,241,0.07) 0%, transparent 65%);
            }
            .mobile-header-band .icon-box { width: 108px !important; height: 108px !important; border-radius: 28px !important; margin-bottom: 22px !important; display: flex !important; }
            .mobile-header-band .icon-box img { width: 80% !important; height: 80% !important; }
            .mobile-header-band .panel-title { font-size: 1.35rem !important; margin-bottom: 8px !important; }
            .mobile-header-band .panel-sub   { font-size: 0.84rem !important; }
            .card-body {
                width: 100%; flex: 1; padding: 40px 28px 80px;
                background: #FFFFFF; border-radius: 32px 32px 0 0;
                margin-top: -28px; box-shadow: 0 -4px 32px rgba(15,23,42,0.18);
                position: relative; z-index: 2; justify-content: flex-start;
            }
            .section-title { font-size: 1.75rem; margin-bottom: 8px; }
            .section-sub   { font-size: 0.88rem; margin-bottom: 28px; }
            .otp-digit { width: 46px; height: 58px; font-size: 1.5rem; border-radius: 8px; }
            .otp-group { gap: 8px; }
            .btn-primary { height: 56px; border-radius: 16px; font-size: 1.02rem; }
        }
        @media (max-width: 480px) {
            .mobile-header-band { padding: 48px 24px 44px; }
            .card-body { padding: 36px 22px 80px; }
            .section-title { font-size: 1.6rem; }
            .otp-digit { width: 42px; height: 54px; font-size: 1.4rem; }
            .otp-group { gap: 7px; }
        }
        @media (max-width: 390px) {
            .mobile-header-band { padding: 38px 20px 34px; }
            .card-body { padding: 28px 18px 80px; }
            .section-title { font-size: 1.45rem; }
            .otp-digit { width: 38px; height: 50px; font-size: 1.25rem; border-radius: 8px; }
            .otp-group { gap: 6px; }
        }
    </style>
</head>
<body>

<div class="auth-wrapper">
<div class="page-card">

    @php
        $appSettings = null;
        try { $appSettings = \App\Models\AppSetting::getInstance(); } catch (\Throwable $e) {}
    @endphp

    <!-- Panel Kiri (Desktop) -->
    <div class="card-panel">
        <div class="icon-box">
            @if($appSettings && $appSettings->app_logo)
                <img src="{{ asset('storage/' . $appSettings->app_logo) }}" alt="Logo">
            @else
                <svg width="44" height="44" fill="none" stroke="#FACC15" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            @endif
        </div>
        <p class="panel-title">{{ $appSettings->app_name ?? config('app.name', 'ICB CINTA TEKNIKA') }}</p>
        <p class="panel-sub">Masukkan kode OTP yang telah dikirimkan ke email Anda.</p>
    </div>

    <!-- Mobile Header Band -->
    <div class="mobile-header-band" style="display:none;">
        <div class="icon-box">
            @if($appSettings && $appSettings->app_logo)
                <img src="{{ asset('storage/' . $appSettings->app_logo) }}" alt="Logo">
            @else
                <svg width="44" height="44" fill="none" stroke="#FACC15" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            @endif
        </div>
        <p class="panel-title">{{ $appSettings->app_name ?? config('app.name', 'ICB CINTA TEKNIKA') }}</p>
        <p class="panel-sub">Verifikasi Kode OTP</p>
    </div>

    <!-- Panel Kanan (Form) -->
    <div class="card-body">
        <p class="section-title">Masukkan OTP</p>
        <p class="section-sub">
            Kode OTP 6 digit telah dikirim ke<br>
            <span class="email-highlight">{{ $email }}</span><br>
            Berlaku selama <strong>10 menit</strong>.
        </p>

        @if (session('status'))
        <div class="alert alert-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>{{ session('status') }}</div>
        </div>
        @endif

        @if (session('resent'))
        <div class="alert alert-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>{{ session('resent') }}</div>
        </div>
        @endif

        @if ($errors->has('otp'))
        <div class="alert alert-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>{{ $errors->first('otp') }}</div>
        </div>
        @endif

        <form method="POST" action="{{ route('password.otp.verify') }}" id="otpForm">
            @csrf

            <!-- 6 kotak OTP -->
            <div class="otp-group" id="otpGroup">
                @for ($i = 1; $i <= 6; $i++)
                    <input type="text" inputmode="numeric" maxlength="1"
                           class="otp-digit {{ $errors->has('otp') ? 'error' : '' }}"
                           id="d{{ $i }}" autocomplete="off"
                           aria-label="Digit OTP ke-{{ $i }}">
                @endfor
            </div>

            <!-- Hidden input yang akan disubmit -->
            <input type="hidden" name="otp" id="otpHidden">

            @if ($errors->has('otp'))
                <p class="field-error">{{ $errors->first('otp') }}</p>
            @endif

            <button type="submit" class="btn-primary" id="verifyBtn" disabled>
                <span class="btn-text">Verifikasi OTP</span>
                <span class="btn-spinner"><div class="spinner"></div></span>
            </button>
        </form>

        <!-- Resend -->
        <div class="resend-area">
            <span>Tidak dapat kode? </span>
            <span id="timerWrap">Kirim ulang dalam <span id="timerText">02:00</span></span>
            <span id="resendWrap" style="display:none;">
                <form method="POST" action="{{ route('password.otp.resend') }}">
                    @csrf
                    <button type="submit" class="resend-btn" id="resendBtn">Kirim ulang kode</button>
                </form>
            </span>
        </div>

        <a href="{{ route('password.request') }}" class="back-link">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Ganti email / kembali
        </a>
    </div>

</div>

    <!-- Credit Desktop -->
    <div class="auth-credit">Developed By&nbsp;<a href="https://vexalyndev.my.id" target="_blank" rel="noopener noreferrer">Vexalyn Dev</a></div>
</div>

<!-- Credit Mobile -->
<div class="mobile-credit">Developed By<a href="https://vexalyndev.my.id" target="_blank" rel="noopener noreferrer">Vexalyn Dev</a></div>

<script>
(function () {
    // Mobile header
    if (window.innerWidth <= 768) {
        var mh = document.querySelector('.mobile-header-band');
        if (mh) mh.style.display = 'flex';
    }

    var digits  = Array.from(document.querySelectorAll('.otp-digit'));
    var hidden  = document.getElementById('otpHidden');
    var btn     = document.getElementById('verifyBtn');
    var form    = document.getElementById('otpForm');

    function getOtp() { return digits.map(function(d){ return d.value; }).join(''); }

    function syncHidden() {
        var val = getOtp();
        hidden.value = val;
        btn.disabled = val.length < 6;
        digits.forEach(function(d) {
            d.classList.toggle('filled', d.value !== '');
        });
    }

    digits.forEach(function (inp, idx) {
        inp.addEventListener('keydown', function (e) {
            // Backspace: hapus digit ini, fokus sebelumnya
            if (e.key === 'Backspace') {
                if (inp.value === '' && idx > 0) {
                    digits[idx - 1].value = '';
                    digits[idx - 1].focus();
                } else {
                    inp.value = '';
                }
                syncHidden();
                e.preventDefault();
            }
            // Arrow left/right
            if (e.key === 'ArrowLeft' && idx > 0) { digits[idx - 1].focus(); e.preventDefault(); }
            if (e.key === 'ArrowRight' && idx < 5) { digits[idx + 1].focus(); e.preventDefault(); }
        });

        inp.addEventListener('input', function (e) {
            // Ambil hanya angka terakhir
            var val = inp.value.replace(/\D/g, '');
            // Kalau paste 6 digit
            if (val.length > 1) {
                var allDigits = val.slice(0, 6).split('');
                allDigits.forEach(function (ch, i) { if (digits[i]) digits[i].value = ch; });
                var last = Math.min(allDigits.length - 1, 5);
                digits[last].focus();
            } else {
                inp.value = val;
                if (val && idx < 5) digits[idx + 1].focus();
            }
            syncHidden();
        });

        inp.addEventListener('paste', function (e) {
            e.preventDefault();
            var pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
            pasted.split('').forEach(function (ch, i) { if (digits[i]) digits[i].value = ch; });
            syncHidden();
            var last = Math.min(pasted.length - 1, 5);
            if (digits[last]) digits[last].focus();
        });

        // Click: select all text di kotak itu
        inp.addEventListener('focus', function () { inp.select(); });
    });

    form.addEventListener('submit', function () {
        btn.classList.add('loading');
    });

    // ── Countdown timer untuk resend ──
    var RESEND_SECONDS = 120;
    var timerWrap  = document.getElementById('timerWrap');
    var resendWrap = document.getElementById('resendWrap');
    var timerText  = document.getElementById('timerText');

    function startTimer(secs) {
        timerWrap.style.display  = '';
        resendWrap.style.display = 'none';
        var remaining = secs;
        var interval = setInterval(function () {
            remaining--;
            var m = String(Math.floor(remaining / 60)).padStart(2, '0');
            var s = String(remaining % 60).padStart(2, '0');
            timerText.textContent = m + ':' + s;
            if (remaining <= 0) {
                clearInterval(interval);
                timerWrap.style.display  = 'none';
                resendWrap.style.display = '';
            }
        }, 1000);
    }

    startTimer(RESEND_SECONDS);

    // Jika ada pesan resent (baru kirim ulang), reset timer
    @if (session('resent'))
        startTimer(RESEND_SECONDS);
    @endif

    // Auto-focus digit pertama
    if (digits[0]) digits[0].focus();
})();
</script>

</body>
</html>
