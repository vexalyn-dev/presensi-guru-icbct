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
            display: grid;
            place-items: center;
            padding: 24px 16px;
            background: linear-gradient(135deg, #F1F5F9 0%, #E2E8F0 100%);
        }

        .page-card {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 32px 64px -12px rgba(15,23,42,0.16),
                        0 0 0 1px rgba(15,23,42,0.05);
            overflow: hidden;
            animation: cardIn 0.45s cubic-bezier(0.22,1,0.36,1) both;
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(28px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Top navy band */
        .card-top {
            background: linear-gradient(150deg, #080F1E 0%, #0F172A 60%, #162035 100%);
            padding: 40px 40px 36px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .card-top::before {
            content: ''; position: absolute; top: -80px; right: -80px;
            width: 280px; height: 280px; border-radius: 50%;
            background: radial-gradient(circle, rgba(250,204,21,0.1) 0%, transparent 65%);
        }
        .card-top::after {
            content: ''; position: absolute; bottom: -60px; left: -60px;
            width: 200px; height: 200px; border-radius: 50%;
            background: radial-gradient(circle, rgba(99,102,241,0.07) 0%, transparent 65%);
        }
        .icon-box {
            width: 72px; height: 72px; margin: 0 auto 18px;
            border-radius: 20px;
            background: rgba(255,255,255,0.05);
            border: 1.5px solid rgba(250,204,21,0.28);
            box-shadow: 0 16px 40px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.07);
            display: flex; align-items: center; justify-content: center;
            position: relative; z-index: 1;
            backdrop-filter: blur(8px);
        }
        .icon-box img { width: 78%; height: 78%; object-fit: contain;
            filter: drop-shadow(0 3px 10px rgba(0,0,0,0.3)); }
        .card-top h1 {
            font-size: 1.1rem; font-weight: 800; color: #fff;
            letter-spacing: -0.2px; position: relative; z-index: 1; margin-bottom: 4px;
        }
        .card-top p {
            font-size: 0.78rem; color: rgba(255,255,255,0.42);
            position: relative; z-index: 1;
        }

        /* Body */
        .card-body { padding: 36px 40px 32px; }

        .section-title {
            font-size: 1.3rem; font-weight: 800; color: #0F172A;
            letter-spacing: -0.4px; margin-bottom: 6px;
        }
        .section-sub {
            font-size: 0.84rem; color: #64748B;
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
            width: 100%; height: 52px;
            padding: 0 18px 0 46px;
            border: 1.5px solid #E2E8F0; border-radius: 14px;
            font-size: 0.92rem; font-family: inherit;
            color: #0F172A; background: #F8FAFC;
            transition: all 0.2s; outline: none;
            -webkit-appearance: none;
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
            width: 100%; height: 54px;
            background: #0F172A;
            color: #fff; border: none; border-radius: 14px;
            font-size: 0.98rem; font-weight: 700; font-family: inherit;
            letter-spacing: 0.02em; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: transform 0.15s, box-shadow 0.2s, background 0.2s;
            box-shadow: 0 6px 20px rgba(15,23,42,0.22);
            -webkit-tap-highlight-color: transparent;
            position: relative; overflow: hidden;
        }
        .btn-primary:hover { background: #1a2540; box-shadow: 0 10px 28px rgba(15,23,42,0.3); transform: translateY(-1px); }
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
            gap: 6px; margin-top: 20px;
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
            width: 76px; height: 76px; margin: 0 auto 20px;
            background: #F0FDF4; border: 1.5px solid #BBF7D0;
            border-radius: 22px;
            display: flex; align-items: center; justify-content: center;
        }
        .sent-title { font-size: 1.2rem; font-weight: 800; color: #0F172A; margin-bottom: 10px; }
        .sent-desc { font-size: 0.84rem; color: #64748B; line-height: 1.65; margin-bottom: 28px; }
        .sent-email {
            display: inline-block; font-weight: 700; color: #0F172A;
            background: #F1F5F9; padding: 2px 10px; border-radius: 6px;
        }

        /* Footer */
        .card-footer {
            padding: 14px 40px 18px;
            border-top: 1px solid #F1F5F9; text-align: center;
        }
        .footer-text { font-size: 0.72rem; color: #94A3B8; }
        .footer-text span { color: #F59E0B; font-weight: 600; }

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

            /* Page card: full-screen flex column */
            .page-card {
                max-width: 100%; width: 100%;
                min-height: 100vh; min-height: 100svh;
                border-radius: 0;
                box-shadow: none;
                display: flex; flex-direction: column;
                animation: none;
            }

            /* Top band: lebih tinggi di mobile */
            .card-top {
                padding: 56px 28px 52px;
                flex-shrink: 0;
                border-radius: 0;
            }
            .icon-box {
                width: 108px; height: 108px;
                border-radius: 28px; margin-bottom: 22px;
            }
            .icon-box img { width: 80%; height: 80%; }
            .card-top h1 { font-size: 1.35rem; margin-bottom: 8px; }
            .card-top p  { font-size: 0.84rem; }

            /* White card: overlap header, flex:1 agar penuh */
            .card-body {
                flex: 1;
                padding: 40px 28px 52px;
                background: #FFFFFF;
                border-radius: 32px 32px 0 0;
                margin-top: -28px;
                box-shadow: 0 -4px 32px rgba(15,23,42,0.18);
                position: relative; z-index: 2;
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

            /* Footer tenggelam di bawah */
            .card-footer {
                padding: 16px 28px 28px;
                border-top: 1px solid #F1F5F9;
                background: #FFFFFF;
                flex-shrink: 0;
            }
        }

        @media (max-width: 480px) {
            .card-top { padding: 48px 24px 44px; }
            .icon-box { width: 96px; height: 96px; }
            .card-body { padding: 36px 22px 48px; }
            .section-title { font-size: 1.6rem; }
        }

        @media (max-width: 390px) {
            .card-top { padding: 40px 20px 36px; }
            .icon-box { width: 88px; height: 88px; }
            .card-body { padding: 30px 18px 44px; }
            .section-title { font-size: 1.45rem; }
            .input-wrap input { height: 52px; }
            .btn-primary { height: 52px; }
        }
    </style>
</head>
<body>

<div class="page-card">

    <!-- Top Band -->
    <div class="card-top">
        @php
            $appSettings = null;
            try { $appSettings = \App\Models\AppSetting::getInstance(); } catch (\Throwable $e) {}
        @endphp
        <div class="icon-box">
            @if($appSettings && $appSettings->app_logo)
                <img src="{{ asset('storage/' . $appSettings->app_logo) }}" alt="Logo">
            @else
                <svg width="32" height="32" fill="none" stroke="#FACC15" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            @endif
        </div>
        <h1>{{ $appSettings->app_name ?? config('app.name', 'ICB CINTA TEKNIKA') }}</h1>
        <p>Reset Password Akun Anda</p>
    </div>

    <!-- Body -->
    <div class="card-body">

        {{-- Form state --}}
        <div id="formState" @if(session('status')) style="display:none" @endif>
            <p class="section-title">Lupa Password?</p>
            <p class="section-sub">Masukkan email terdaftar dan kami akan mengirimkan link untuk mereset password Anda.</p>

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
                    <span class="btn-text">Kirim Link Reset</span>
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
            <p class="sent-title">Email Terkirim!</p>
            <p class="sent-desc">
                Link reset password telah dikirim ke<br>
                <span class="sent-email" id="sentEmail">{{ old('email') }}</span><br><br>
                Silakan cek inbox atau folder spam Anda.
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

    <!-- Footer -->
    <div class="card-footer">
        <p class="footer-text">© {{ date('Y') }} <span>{{ config('app.name', 'ICB CT') }}</span> · Sistem Presensi Digital</p>
    </div>
</div>

<script>
    @if(session('status'))
        document.getElementById('formState').style.display = 'none';
        document.getElementById('sentState').classList.add('active');
    @endif

    document.getElementById('fpForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('fpBtn');
        const emailVal = document.getElementById('email').value;
        document.getElementById('sentEmail').textContent = emailVal;
        btn.classList.add('loading');
    });
</script>
</body>
</html>
