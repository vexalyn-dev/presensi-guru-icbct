<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — {{ config('app.name', 'ICB CT') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            min-height: 100vh; height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* ═══════════════════════════════
           DESKTOP LAYOUT
           ═══════════════════════════════ */
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            background: linear-gradient(135deg, #F1F5F9 0%, #E2E8F0 100%);
        }

        .page-wrap {
            display: flex;
            width: 860px;
            max-width: 100%;
            background: #fff;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 24px 60px -10px rgba(15,23,42,0.16),
                        0 0 0 1px rgba(15,23,42,0.05);
            animation: cardIn 0.4s cubic-bezier(0.22,1,0.36,1) both;
        }
        @keyframes cardIn {
            from { opacity:0; transform: translateY(20px) scale(0.98); }
            to   { opacity:1; transform: translateY(0) scale(1); }
        }

        /* ─── LEFT PANEL (navy) ─── */
        .left-panel {
            width: 42%;
            flex-shrink: 0;
            background: linear-gradient(160deg, #080F1E 0%, #0F172A 55%, #162035 100%);
            padding: 52px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: ''; position: absolute; top: -80px; right: -80px;
            width: 280px; height: 280px; border-radius: 50%;
            background: radial-gradient(circle, rgba(250,204,21,0.10) 0%, transparent 65%);
        }
        .left-panel::after {
            content: ''; position: absolute; bottom: -60px; left: -60px;
            width: 220px; height: 220px; border-radius: 50%;
            background: radial-gradient(circle, rgba(99,102,241,0.07) 0%, transparent 65%);
        }
        .logo-box {
            width: 92px; height: 92px; margin: 0 auto 24px;
            border-radius: 24px;
            background: rgba(255,255,255,0.05);
            border: 1.5px solid rgba(250,204,21,0.28);
            box-shadow: 0 16px 40px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.07);
            display: flex; align-items: center; justify-content: center;
            position: relative; z-index: 1;
            backdrop-filter: blur(8px);
        }
        .logo-box img { width: 78%; height: 78%; object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.35)); }
        .left-panel h2 {
            font-size: 1.25rem; font-weight: 800; color: #fff;
            letter-spacing: -0.3px; margin-bottom: 10px;
            position: relative; z-index: 1;
        }
        .left-panel p {
            font-size: 0.83rem; color: rgba(255,255,255,0.45);
            line-height: 1.65; max-width: 220px; margin: 0 auto;
            position: relative; z-index: 1;
        }
        .panel-badge {
            margin-top: 32px;
            display: inline-flex; align-items: center; gap: 7px;
            background: rgba(250,204,21,0.1);
            border: 1px solid rgba(250,204,21,0.22);
            border-radius: 99px;
            padding: 7px 14px;
            font-size: 0.75rem; font-weight: 600; color: #FACC15;
            position: relative; z-index: 1;
        }

        /* ─── RIGHT PANEL (form) ─── */
        .right-panel {
            flex: 1;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
        }
        .form-title {
            font-size: 1.65rem; font-weight: 800; color: #0F172A;
            letter-spacing: -0.5px; margin-bottom: 6px;
        }
        .form-sub {
            font-size: 0.87rem; color: #64748B; line-height: 1.55; margin-bottom: 32px;
        }

        /* Alert */
        .alert {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 13px 15px; border-radius: 12px;
            font-size: 0.83rem; font-weight: 500; line-height: 1.5; margin-bottom: 20px;
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
            padding: 0 50px 0 46px;
            border: 1.5px solid #E2E8F0; border-radius: 14px;
            font-size: 0.92rem; font-family: inherit;
            color: #0F172A; background: #F8FAFC;
            transition: all 0.2s; outline: none;
            -webkit-appearance: none;
        }
        .input-wrap input[readonly] {
            color: #64748B; cursor: not-allowed; background: #F1F5F9;
        }
        .input-wrap input::placeholder { color: #94A3B8; }
        .input-wrap input:focus {
            border-color: #0F172A; background: #fff;
            box-shadow: 0 0 0 4px rgba(15,23,42,0.08);
        }
        .input-icon {
            position: absolute; left: 15px; top: 50%; transform: translateY(-50%);
            width: 18px; height: 18px; color: #94A3B8; pointer-events: none; transition: color 0.2s;
        }
        .input-wrap:focus-within .input-icon { color: #0F172A; }

        /* Eye toggle */
        .toggle-pw {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; color: #94A3B8;
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 8px;
            transition: color 0.2s, background 0.2s, transform 0.15s;
            -webkit-tap-highlight-color: transparent;
        }
        .toggle-pw:hover { color: #0F172A; background: rgba(15,23,42,0.06); }
        .toggle-pw:active { transform: translateY(-50%) scale(0.9); }
        .toggle-pw svg { width: 18px; height: 18px; stroke-width: 1.8; }

        /* Match indicator */
        .pw-match-hint {
            display: none; align-items: center; gap: 7px;
            margin-top: 8px; font-size: 0.8rem; font-weight: 500;
            animation: hintIn 0.25s ease both;
        }
        .pw-match-hint.show { display: flex; }
        .pw-match-hint.match { color: #16A34A; }
        .pw-match-hint.no-match { color: #DC2626; }
        @keyframes hintIn {
            from { opacity:0; transform: translateY(-4px); }
            to   { opacity:1; transform: translateY(0); }
        }
        .input-wrap input.pw-ok  { border-color: #22C55E !important; background: #F0FDF4 !important; }
        .input-wrap input.pw-fail { border-color: #EF4444 !important; background: #FFF5F5 !important; }

        .field-error { font-size: 0.78rem; color: #DC2626; margin-top: 6px; }

        /* Button */
        .btn-submit {
            width: 100%; height: 52px; margin-top: 4px;
            background: #0F172A; color: #fff; border: none; border-radius: 14px;
            font-size: 0.98rem; font-weight: 700; font-family: inherit;
            letter-spacing: 0.02em; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: transform 0.15s, box-shadow 0.2s, background 0.2s;
            box-shadow: 0 6px 20px rgba(15,23,42,0.22);
        }
        .btn-submit:hover { background: #1a2540; box-shadow: 0 10px 28px rgba(15,23,42,0.3); transform: translateY(-1px); }
        .btn-submit:active { transform: scale(0.98); }

        .back-link {
            display: flex; align-items: center; justify-content: center;
            gap: 6px; margin-top: 18px;
            font-size: 0.84rem; font-weight: 600; color: #64748B;
            text-decoration: none; transition: color 0.2s;
        }
        .back-link:hover { color: #0F172A; }

        /* Hapus browser native eye */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear { display: none !important; width:0; height:0; }
        input[type="password"]::-webkit-credentials-auto-fill-button,
        input[type="password"]::-webkit-contacts-auto-fill-button,
        input[type="password"]::-webkit-textfield-decoration-container {
            display: none !important; visibility: hidden; pointer-events: none;
        }

        /* ═══════════════════════════════
           MOBILE LAYOUT ≤ 768px
           ═══════════════════════════════ */
        @media (max-width: 768px) {
            html, body {
                height: 100%; background: #080F1E;
                padding: 0; margin: 0;
                display: flex; flex-direction: column;
            }
            .page-wrap {
                width: 100%; max-width: 100%;
                min-height: 100vh; min-height: 100svh;
                flex-direction: column;
                border-radius: 0; box-shadow: none;
                animation: none;
            }
            .left-panel {
                width: 100%;
                padding: 52px 28px 48px;
                border-radius: 0;
                flex-shrink: 0;
            }
            .logo-box { width: 100px; height: 100px; border-radius: 26px; }
            .logo-box img { width: 80%; height: 80%; }
            .left-panel h2 { font-size: 1.3rem; }
            .left-panel p { font-size: 0.84rem; }
            .panel-badge { margin-top: 24px; }
            .right-panel {
                flex: 1;
                padding: 40px 28px 52px;
                background: #FFFFFF;
                border-radius: 32px 32px 0 0;
                margin-top: -28px;
                box-shadow: 0 -4px 32px rgba(15,23,42,0.18);
                z-index: 2; position: relative;
            }
            .form-title { font-size: 1.75rem; }
            .form-sub { font-size: 0.88rem; margin-bottom: 28px; }
            .field { margin-bottom: 22px; }
            .input-wrap input { height: 56px; border-radius: 14px; font-size: 15px; padding: 0 52px 0 48px; }
            .input-icon { left: 16px; width: 19px; height: 19px; }
            .toggle-pw { right: 10px; width: 40px; height: 40px; }
            .toggle-pw svg { width: 20px; height: 20px; }
            .btn-submit { height: 56px; border-radius: 16px; font-size: 1.02rem; margin-top: 8px; }
            .back-link { margin-top: 24px; font-size: 0.88rem; }
        }
        @media (max-width: 480px) {
            .left-panel { padding: 44px 24px 40px; }
            .logo-box { width: 90px; height: 90px; }
            .right-panel { padding: 34px 22px 48px; }
            .form-title { font-size: 1.6rem; }
        }
        @media (max-width: 390px) {
            .left-panel { padding: 38px 20px 34px; }
            .right-panel { padding: 28px 18px 44px; }
            .form-title { font-size: 1.45rem; }
            .input-wrap input { height: 52px; }
            .btn-submit { height: 52px; }
        }
    </style>
</head>
<body>

<div class="page-wrap">

    <!-- Left Panel -->
    <div class="left-panel">
        @php
            $appSettings = null;
            try { $appSettings = \App\Models\AppSetting::getInstance(); } catch (\Throwable $e) {}
        @endphp
        <div class="logo-box">
            @if($appSettings && $appSettings->app_logo)
                <img src="{{ asset('storage/' . $appSettings->app_logo) }}" alt="Logo">
            @else
                <svg width="36" height="36" fill="none" stroke="#FACC15" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            @endif
        </div>
        <h2>{{ $appSettings->app_name ?? config('app.name', 'ICB CINTA TEKNIKA') }}</h2>
        <p>Sistem Presensi Digital Guru yang modern dan terpercaya.</p>
        <div class="panel-badge">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Reset Password Aman
        </div>
    </div>

    <!-- Right Panel (Form) -->
    <div class="right-panel">
        <p class="form-title">Buat Password Baru</p>
        <p class="form-sub">Masukkan password baru yang kuat. Pastikan mudah diingat dan aman ya.</p>

        @if ($errors->any())
        <div class="alert alert-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
        </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email (readonly) -->
            <div class="field">
                <label for="email">Email</label>
                <div class="input-wrap">
                    <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <input type="email" id="email" name="email" required
                           value="{{ old('email', $request->email) }}"
                           readonly autocomplete="email">
                </div>
                @error('email')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <!-- Password Baru -->
            <div class="field">
                <label for="password">Password Baru</label>
                <div class="input-wrap">
                    <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <input type="password" id="password" name="password"
                           required placeholder="Min. 8 karakter"
                           autocomplete="new-password">
                    <button type="button" class="toggle-pw" onclick="togglePassword('password', this)" tabindex="-1">
                        <svg class="eye" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <!-- Konfirmasi Password -->
            <div class="field">
                <label for="password_confirmation">Konfirmasi Password</label>
                <div class="input-wrap">
                    <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           required placeholder="Ulangi password baru"
                           autocomplete="new-password">
                    <button type="button" class="toggle-pw" onclick="togglePassword('password_confirmation', this)" tabindex="-1">
                        <svg class="eye" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <div class="pw-match-hint match" id="hintMatch">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Password cocok
                </div>
                <div class="pw-match-hint no-match" id="hintNoMatch">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Password tidak cocok
                </div>
                @error('password_confirmation')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="btn-submit">Reset Password</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke halaman login
        </a>
    </div>

</div>

<script>
    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const eye = btn.querySelector('.eye');
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        eye.innerHTML = isHidden
            ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`
            : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
        btn.style.transform = 'translateY(-50%) scale(0.88)';
        setTimeout(() => btn.style.transform = 'translateY(-50%) scale(1)', 150);
    }

    (function() {
        const pw   = document.getElementById('password');
        const conf = document.getElementById('password_confirmation');
        const hintMatch   = document.getElementById('hintMatch');
        const hintNoMatch = document.getElementById('hintNoMatch');
        function check() {
            const p = pw.value, c = conf.value;
            if (!c) {
                hintMatch.classList.remove('show');
                hintNoMatch.classList.remove('show');
                conf.classList.remove('pw-ok','pw-fail');
                return;
            }
            if (p === c && p.length > 0) {
                hintMatch.classList.add('show'); hintNoMatch.classList.remove('show');
                conf.classList.add('pw-ok'); conf.classList.remove('pw-fail');
            } else {
                hintNoMatch.classList.add('show'); hintMatch.classList.remove('show');
                conf.classList.add('pw-fail'); conf.classList.remove('pw-ok');
            }
        }
        pw?.addEventListener('input', check);
        conf?.addEventListener('input', check);
    })();
</script>
</body>
</html>
