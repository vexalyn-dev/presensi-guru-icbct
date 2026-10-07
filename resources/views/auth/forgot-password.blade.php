<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password — {{ config('app.name', 'ICB CT') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            height: 100%; min-height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── DESKTOP LAYOUT ─── */
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 24px 16px;
            background: linear-gradient(135deg, #F1F5F9 0%, #E2E8F0 100%);
        }

        /* ── Wrapper luar (card + credit) ── */
        .auth-wrapper {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* ── LANDSCAPE CARD (mirip login) ── */
        .page-card {
            position: relative;
            width: 800px;
            max-width: calc(100% - 32px);
            background: #f8fafc;
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(15,23,42,0.18),
                        0 0 0 1px rgba(15,23,42,0.05);
            overflow: hidden;
            display: flex;
            flex-direction: row;
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
            width: 50%;
            flex-shrink: 0;
            background: linear-gradient(150deg, #050d1a 0%, #0b1a2d 52%, #101f34 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 35px;
            text-align: center;
            position: relative;
            overflow: hidden;
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
            width: 110px; height: 110px; margin: 0 auto 22px;
            border-radius: 22px;
            background: rgba(255,255,255,0.04);
            border: 1.5px solid rgba(250,204,21,0.28);
            box-shadow: 0 16px 40px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.07);
            display: flex; align-items: center; justify-content: center;
            position: relative; z-index: 1;
            backdrop-filter: blur(8px);
        }
        .icon-box img { width: 78%; height: 78%; object-fit: contain;
            filter: drop-shadow(0 3px 10px rgba(0,0,0,0.3)); }

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
            width: 50%;
            flex-shrink: 0;
            padding: 42px 52px 36px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #f5f5f5;
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .card-body::-webkit-scrollbar { display: none; }

        .section-title {
            font-size: 1.8rem; font-weight: 800; color: #0F172A;
            letter-spacing: -0.5px; margin-bottom: 6px;
        }
        .section-sub {
            font-size: 0.88rem; color: #64748B;
            line-height: 1.65; margin-bottom: 28px;
        }

        /* Alert */
        .alert {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 13px 15px; border-radius: 12px;
            font-size: 0.83rem; font-weight: 500; line-height: 1.5;
            margin-bottom: 20px;
        }
        .alert-error { background: #FEF2F2; border: 1px solid #FECACA; color: #B91C1C; }
        .alert svg { flex-shrink: 0; margin-top: 1px; }

        /* Field */
        .field { margin-bottom: 20px; }
        .field label {
            display: block; font-size: 0.81rem; font-weight: 600;
            color: #1E293B; margin-bottom: 8px; letter-spacing: 0.01em;
        }
        .input-wrap { position: relative; }
        .input-wrap input {
            width: 100%; height: 48px;
            padding: 0 18px 0 46px;
            border: 1.5px solid #E2E8F0; border-radius: 14px;
            font-size: 0.92rem; font-family: inherit;
            color: #0F172A; background: #F8FAFC;
            transition: all 0.2s; outline: none;
            -webkit-appearance: none;
            appearance: none;
        }
        .input-wrap input::placeholder { color: #94A3B8; }
        .input-wrap input:focus {
            border-color: #0F172A; background: #fff;
            box-shadow: 0 0 0 4px rgba(15,23,42,0.08);
        }
        .input-wrap input.error { border-color: #EF4444; background: #FFF5F5; }
        .input-icon {
            position: absolute; left: 15px; top: 50%;
            transform: translateY(-50%);
            width: 18px; height: 18px; color: #94A3B8;
            pointer-events: none; transition: color 0.2s;
        }
        .input-wrap:focus-within .input-icon { color: #0F172A; }

        /* Button */
        .btn-primary {
            width: 100%; height: 52px;
            background: #0F172A;
            color: #fff; border: none; border-radius: 12px;
            font-size: 0.98rem; font-weight: 700; font-family: inherit;
            letter-spacing: 0.02em; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: transform 0.15s, box-shadow 0.2s, background 0.2s;
            box-shadow: 0 4px 20px rgba(15,23,42,0.25);
            margin-top: 0.75rem;
            -webkit-tap-highlight-color: transparent;
            position: relative; overflow: hidden;
        }
        .btn-primary:hover { background: #1a2540; box-shadow: 0 10px 30px rgba(15,23,42,0.35); transform: translateY(-2px); }
        .btn-primary:active { transform: scale(0.98); }
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

        /* Back link */
        .back-link {
            display: flex; align-items: center; justify-content: center;
            gap: 6px; margin-top: 18px;
            font-size: 0.84rem; font-weight: 600;
            color: #64748B; text-decoration: none; transition: color 0.2s;
        }
        .back-link:hover { color: #0F172A; }

        /* Sent state */
        .sent-state { display: none; text-align: center; }
        .sent-state.active { display: block; animation: fadeUp 0.4s ease both; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .sent-icon {
            width: 72px; height: 72px; margin: 0 auto 18px;
            background: #F0FDF4; border: 1.5px solid #BBF7D0;
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
        }
        .sent-title { font-size: 1.2rem; font-weight: 800; color: #0F172A; margin-bottom: 10px; }
        .sent-desc { font-size: 0.84rem; color: #64748B; line-height: 1.65; margin-bottom: 24px; }
        .sent-email {
            display: inline-block; font-weight: 700; color: #0F172A;
            background: #F1F5F9; padding: 2px 10px; border-radius: 6px;
        }

        /* ── Credit Desktop ── */
        .auth-credit {
            text-align: center;
            padding: 14px 16px 20px;
            font-size: 11px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-top: 18px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-credit a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s ease;
            margin-left: 4px;
        }
        .auth-credit a:hover { color: #FACC15; }

        /* ── Credit Mobile (hidden on desktop) ── */
        .mobile-credit { display: none; }

        /* ════════════════════════════════════════
           MOBILE REDESIGN ≤ 768px
           ════════════════════════════════════════ */
        @media (max-width: 768px) {
            html, body {
                height: 100%; min-height: 100vh;
                background: #080F1E;
                padding: 0; margin: 0;
                display: block;
            }
            body {
                display: flex; flex-direction: column;
                align-items: stretch;
            }

            .auth-wrapper {
                display: block;
                width: 100%;
            }

            /* Desktop credit: sembunyikan */
            .auth-credit { display: none !important; }

            /* Mobile credit: fixed di bawah */
            .mobile-credit {
                display: flex !important;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                align-items: center;
                justify-content: center;
                gap: 0;
                width: 100%;
                padding: 10px 16px 20px;
                font-size: 10px;
                font-weight: 600;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: #94a3b8;
                background: #FFFFFF;
                z-index: 9999;
                will-change: transform;
                transform: translateZ(0);
                -webkit-transform: translateZ(0);
            }
            .mobile-credit a {
                color: #94a3b8;
                text-decoration: none;
                margin-left: 4px;
            }

            /* Page card: full-screen flex column */
            .page-card {
                max-width: 100%; width: 100%;
                min-height: 100vh; min-height: 100svh;
                border-radius: 0;
                box-shadow: none;
                display: flex; flex-direction: column;
                animation: none;
                margin-top: 0;
            }

            /* Sembunyikan panel kiri di mobile */
            .card-panel { display: none !important; }

            /* Buat top band dari card-body header — pakai pseudo mobile header */
            /* Sebenarnya panel sudah hidden, kita inject mobile-header terpisah */
            .mobile-header-band {
                display: flex !important;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 56px 28px 52px;
                background: linear-gradient(150deg, #080F1E 0%, #0F172A 60%, #162035 100%);
                text-align: center;
                position: relative;
                overflow: hidden;
                flex-shrink: 0;
            }
            .mobile-header-band::before {
                content: ''; position: absolute; top: -80px; right: -80px;
                width: 300px; height: 300px; border-radius: 50%;
                background: radial-gradient(circle, rgba(250,204,21,0.09) 0%, transparent 65%);
                pointer-events: none;
            }
            .mobile-header-band::after {
                content: ''; position: absolute; bottom: -60px; left: -60px;
                width: 240px; height: 240px; border-radius: 50%;
                background: radial-gradient(circle, rgba(99,102,241,0.07) 0%, transparent 65%);
                pointer-events: none;
            }
            .mobile-header-band .icon-box {
                width: 108px !important; height: 108px !important;
                border-radius: 28px !important; margin-bottom: 22px !important;
                display: flex !important;
            }
            .mobile-header-band .icon-box img { width: 80% !important; height: 80% !important; }
            .mobile-header-band .panel-title { font-size: 1.35rem !important; margin-bottom: 8px !important; }
            .mobile-header-band .panel-sub  { font-size: 0.84rem !important; }

            /* Card body: overlap header, flex:1 */
            .card-body {
                width: 100%;
                flex: 1;
                padding: 40px 28px 80px;
                background: #FFFFFF;
                border-radius: 32px 32px 0 0;
                margin-top: -28px;
                box-shadow: 0 -4px 32px rgba(15,23,42,0.18);
                position: relative; z-index: 2;
                justify-content: flex-start;
            }

            .section-title { font-size: 1.75rem; margin-bottom: 8px; }
            .section-sub   { font-size: 0.88rem; margin-bottom: 32px; }

            .field { margin-bottom: 22px; }
            .field label { font-size: 0.83rem; margin-bottom: 9px; }
            .input-wrap input {
                height: 56px; border-radius: 14px;
                font-size: 15px; padding: 0 18px 0 48px;
            }
            .input-icon { left: 16px; width: 19px; height: 19px; }

            .btn-primary { height: 56px; border-radius: 16px; font-size: 1.02rem; }
            .back-link { margin-top: 24px; font-size: 0.88rem; }
            .sent-icon { width: 84px; height: 84px; border-radius: 24px; }
            .sent-title { font-size: 1.35rem; }
            .sent-desc  { font-size: 0.88rem; margin-bottom: 32px; }
        }

        @media (max-width: 480px) {
            .mobile-header-band { padding: 48px 24px 44px; }
            .mobile-header-band .icon-box { width: 96px !important; height: 96px !important; }
            .card-body { padding: 36px 22px 80px; }
            .section-title { font-size: 1.6rem; }
        }

        @media (max-width: 390px) {
            .mobile-header-band { padding: 40px 20px 36px; }
            .mobile-header-band .icon-box { width: 88px !important; height: 88px !important; }
            .card-body { padding: 30px 18px 80px; }
            .section-title { font-size: 1.45rem; }
            .input-wrap input { height: 52px; }
            .btn-primary { height: 52px; }
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
                <svg width="40" height="40" fill="none" stroke="#FACC15" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            @endif
        </div>
        <p class="panel-title">{{ $appSettings->app_name ?? config('app.name', 'ICB CINTA TEKNIKA') }}</p>
        <p class="panel-sub">Reset password akun Anda dengan mudah dan aman.</p>
    </div>

    <!-- Mobile Header Band (hanya tampil di mobile) -->
    <div class="mobile-header-band" style="display:none;">
        <div class="icon-box">
            @if($appSettings && $appSettings->app_logo)
                <img src="{{ asset('storage/' . $appSettings->app_logo) }}" alt="Logo">
            @else
                <svg width="40" height="40" fill="none" stroke="#FACC15" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            @endif
        </div>
        <p class="panel-title">{{ $appSettings->app_name ?? config('app.name', 'ICB CINTA TEKNIKA') }}</p>
        <p class="panel-sub">Reset Password Akun Anda</p>
    </div>

    <!-- Panel Kanan (Form) -->
    <div class="card-body">

        {{-- Form state --}}
        <div id="formState" @if(session('status')) hidden @endif>
            <p class="section-title">Lupa Password?</p>
            <p class="section-sub">Masukkan email terdaftar dan kami akan mengirimkan kode OTP untuk mereset password Anda.</p>

            @if ($errors->any())
            <div class="alert alert-error">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
            </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" id="fpForm">
                @csrf
                <div class="field">
                    <label for="email">Alamat Email</label>
                    <div class="input-wrap">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <input type="email" id="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="nama@sekolah.sch.id"
                               class="{{ $errors->has('email') ? 'error' : '' }}"
                               required autofocus autocomplete="email">
                    </div>
                </div>

                <button type="submit" class="btn-primary" id="fpBtn">
                    <span class="btn-text">Kirim Kode OTP</span>
                    <span class="btn-spinner"><div class="spinner"></div></span>
                </button>
            </form>

            <a href="{{ route('login') }}" class="back-link">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke halaman login
            </a>
        </div>

        {{-- Sent state --}}
        <div id="sentState" class="sent-state {{ session('status') ? 'active' : '' }}">
            <div class="sent-icon">
                <svg width="36" height="36" fill="none" stroke="#16A34A" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="sent-title">Kode OTP Terkirim!</p>
            <p class="sent-desc">
                Kode OTP reset password telah dikirim ke<br>
                <span class="sent-email" id="sentEmail">{{ old('email') }}</span><br><br>
                Silakan cek inbox atau folder spam Anda, lalu masukkan kode tersebut di halaman berikutnya.
            </p>

            <a href="{{ route('login') }}" class="btn-primary" style="text-decoration:none;">
                <span>Kembali ke Login</span>
            </a>

            <a href="#" onclick="document.getElementById('sentState').classList.remove('active');document.getElementById('formState').style.display='';return false;"
               class="back-link" style="margin-top:16px;">
                Kirim ulang ke email lain
            </a>
        </div>

    </div>
    {{-- END .page-card --}}
</div>

    <!-- Credit Desktop -->
    <div class="auth-credit">Developed By&nbsp;<a href="https://vexalyndev.my.id" target="_blank" rel="noopener noreferrer">Vexalyn Dev</a></div>
</div>
{{-- END .auth-wrapper --}}

<!-- Credit Mobile (fixed, di luar semua container) -->
<div class="mobile-credit">Developed By<a href="https://vexalyndev.my.id" target="_blank" rel="noopener noreferrer">Vexalyn Dev</a></div>

<script>
    // Tampilkan mobile-header-band hanya di mobile
    (function() {
        if (window.innerWidth <= 768) {
            var mh = document.querySelector('.mobile-header-band');
            if (mh) mh.style.display = 'flex';
        }
    })();

    var _hasStatus = <?php echo session('status') ? 'true' : 'false'; ?>;
    if (_hasStatus) {
        document.getElementById('formState').removeAttribute('hidden');
        document.getElementById('formState').style.display = 'none';
        document.getElementById('sentState').classList.add('active');
    }

    document.getElementById('fpForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('fpBtn');
        const emailVal = document.getElementById('email').value;
        document.getElementById('sentEmail').textContent = emailVal;
        btn.classList.add('loading');
    });
</script>
</body>
</html>
