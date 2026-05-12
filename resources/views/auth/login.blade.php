<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="pageTitle">Login - {{ config('app.name', 'ICB CT') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #FFFFFF;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            position: relative;
            width: 900px;
            max-width: 95%;
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.15);
            overflow: hidden;
            display: flex;
            min-height: 600px;
        }

        /* Panel Kiri (Biru/Kuning) */
        .auth-panel {
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            transition: transform 0.8s cubic-bezier(0.77, 0, 0.175, 1);
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 35px;
            text-align: center;
        }

        .auth-panel.login {
            background: #0F172A;
            transform: translateX(0);
        }

        .auth-panel.register {
            background: #FACC15;
            transform: translateX(100%);
        }

        .logo-container {
            width: 90px;
            height: 90px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .panel-content {
            color: white;
            z-index: 1;
        }

        .panel-content h1 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
        }

        .panel-content p {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .btn-toggle {
            padding: 14px 45px;
            border: 2px solid white;
            background: transparent;
            color: white;
            border-radius: 14px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.4s ease;
        }

        .btn-toggle:hover {
            background: white;
            color: #0F172A;
            transform: translateY(-3px);
        }

        /* Container Form di Kanan */
        .forms-container {
            position: absolute;
            top: 0;
            left: 0;
            /* Ubah dari right:0 ke left:0 */
            width: 100%;
            /* Ubah dari 50% ke 100% */
            height: 100%;
            overflow: hidden;
        }

        /* Individual Forms */
        .form-container {
            position: absolute;
            top: 0;
            width: 50%;
            /* Tetap 50% */
            height: 100%;
            padding: 35px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
            transition: all 0.6s ease-in-out;
            overflow-y: auto;
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE/Edge */
        }

        .form-container::-webkit-scrollbar {
            display: none;
        }


        /* Login Form - Default Visible */
        #loginForm {
            right: 0;
            opacity: 1;
            z-index: 2;
        }

        /* Register Form - Default Hidden (di kanan, outside view) */
        #registerForm {
            left: 0;
            opacity: 0;
            z-index: 1;
        }

        /* Saat Toggle ke Register */
        #loginForm.hidden {
            opacity: 0;
            z-index: 1;
        }

        #registerForm.visible {
            opacity: 1;
            z-index: 2;
            justify-content: flex-start;
        }

        #registerForm.visible .form-header {
            margin-bottom: 1rem;
        }

        #registerForm.visible .input-group {
            margin-bottom: 0.75rem;
        }

        #registerForm.visible .divider {
            margin: 1rem 0;
        }

        #registerForm.visible .terms-text {
            margin-top: 1rem;
        }

        .form-header {
            margin-bottom: 2rem;
        }

        .form-header h2 {
            font-size: 2rem;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #64748B;
            font-size: 0.95rem;
        }

        .input-group {
            width: 100%;
            margin-bottom: 1.25rem;
        }

        .input-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #0F172A;
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #E2E8F0;
            border-radius: 12px;
            font-size: 0.95rem;
            background: #F8FAFC;
            font-family: 'Inter', sans-serif;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #0F172A;
            background: white;
            box-shadow: 0 0 0 5px rgba(15, 23, 42, 0.08);
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            width: 20px;
            height: 20px;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: #0F172A;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s ease;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.25);
            margin-top: 0.75rem;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.35);
            background: #1E3A8A;
        }

        .error-message {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
        }

        .success-message {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            color: #16A34A;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
            color: #94A3B8;
            font-size: 0.85rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, #E2E8F0, transparent);
        }

        .divider span {
            padding: 0 1.25rem;
        }

        .social-login {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .social-btn {
            width: 50px;
            height: 50px;
            border: 2px solid #E2E8F0;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.4s ease;
            background: white;
            text-decoration: none;
        }

        .social-btn:hover {
            border-color: #FACC15;
            transform: translateY(-5px) scale(1.1);
        }

        .social-btn img,
        .social-btn svg {
            width: 24px;
            height: 24px;
        }

        .terms-text {
            font-size: 0.75rem;
            color: #94A3B8;
            text-align: center;
            margin-top: 1.5rem;
            line-height: 1.6;
        }

        .terms-text a {
            color: #FACC15;
            font-weight: 600;
            text-decoration: none;
        }

        .password-strength {
            margin-top: 0.75rem;
        }

        .strength-bar {
            height: 4px;
            background: #E2E8F0;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
        }

        .strength-fill.weak {
            width: 25%;
            background: #EF4444;
        }

        .strength-fill.fair {
            width: 50%;
            background: #F59E0B;
        }

        .strength-fill.good {
            width: 75%;
            background: #3B82F6;
        }

        .strength-fill.strong {
            width: 100%;
            background: #10B981;
        }

        .strength-text {
            font-size: 0.75rem;
            font-weight: 600;
            text-align: right;
        }

        .strength-text.weak {
            color: #EF4444;
        }

        .strength-text.fair {
            color: #F59E0B;
        }

        .strength-text.good {
            color: #3B82F6;
        }

        .strength-text.strong {
            color: #10B981;
        }

        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
                height: auto;
            }

            .auth-panel {
                display: none;
            }

            .forms-container {
                position: relative;
                width: 100%;
                height: auto;
            }

            .form-container {
                position: relative;
                width: 100%;
                opacity: 1 !important;
                z-index: 2 !important;
            }

            #registerForm {
                display: none;
            }

            #registerForm.visible {
                display: flex;
            }
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <!-- Sliding Panel (KIRI) -->
        <div class="auth-panel login" id="authPanel">
            <div class="logo-container">
                @php $appSettings = \App\Models\AppSetting::getInstance(); @endphp
                @if($appSettings->app_logo)
                    <img src="{{ asset('storage/' . $appSettings->app_logo) }}" alt="Logo">
                @else
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                @endif
            </div>
            <div class="panel-content">
                <h1 id="panelTitle">Selamat Datang Di Website ICB CINTA TEKNIKA</h1>
                <p id="panelText">Sistem Absensi Guru Termodern & Terpercaya</p>
                <button class="btn-toggle" id="toggleBtn" onclick="toggleAuth()">Buat Akun</button>
            </div>
        </div>

        <!-- Forms Container (KANAN) -->
        <div class="forms-container">
            <!-- Login Form -->
            <div class="form-container" id="loginForm">
                <div class="form-header">
                    <h2>Login</h2>
                    <p>Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                @if ($errors->any())
                    <div class="error-message">{{ $errors->first() }}</div>
                @endif

                @if (session('success'))
                    <div class="success-message">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="input-group">
                        <label for="login-email">Email</label>
                        <div class="input-wrapper">
                            <input type="email" id="login-email" name="email" placeholder="nama@email.com" required
                                value="{{ old('email') }}">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                </path>
                            </svg>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="login-password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="login-password" name="password" placeholder="••••••••" required>
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Masuk</button>
                </form>

                <div class="divider"><span>atau masuk dengan</span></div>

                <div class="social-login">
                    <a href="{{ url('auth/google') }}" class="social-btn" title="Google">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google">
                    </a>
                    <a href="{{ url('auth/facebook') }}" class="social-btn" title="Facebook">
                        <svg viewBox="0 0 24 24" fill="#1877F2">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                    <a href="{{ url('auth/github') }}" class="social-btn" title="GitHub">
                        <svg viewBox="0 0 24 24" fill="#171717">
                            <path
                                d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                        </svg>
                    </a>
                    <a href="{{ url('auth/twitter') }}" class="social-btn" title="Twitter">
                        <svg viewBox="0 0 24 24" fill="#1DA1F2">
                            <path
                                d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Register Form -->
            <div class="form-container" id="registerForm">
                <div class="form-header">
                    <h2>Register</h2>
                    <p>Lengkapi form di bawah untuk membuat akun baru</p>
                </div>

                @if ($errors->any())
                    <div class="error-message">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('register.post') }}">
                    @csrf
                    <div class="input-group">
                        <label for="register-name">Nama Lengkap</label>
                        <div class="input-wrapper">
                            <input type="text" id="register-name" name="name" placeholder="Vexalyn Dev" required
                                value="{{ old('name') }}">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="register-email">Email</label>
                        <div class="input-wrapper">
                            <input type="email" id="register-email" name="email" placeholder="nama@email.com" required
                                value="{{ old('email') }}">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                </path>
                            </svg>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="register-password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="register-password" name="password" placeholder="••••••••"
                                required oninput="checkPasswordStrength()">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                        <div class="password-strength">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strengthFill"></div>
                            </div>
                            <p class="strength-text" id="strengthText"></p>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="register-password-confirm">Konfirmasi Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="register-password-confirm" name="password_confirmation"
                                placeholder="••••••••" required>
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Daftar Sekarang</button>

                    <p class="terms-text">
                        Dengan mendaftar, Anda menyetujui <a href="#">Syarat & Ketentuan</a> serta <a href="#">Kebijakan
                            Privasi</a> kami.
                    </p>
                </form>

                <div class="divider"><span>atau daftar dengan</span></div>

                <div class="social-login">
                    <a href="{{ url('auth/google') }}" class="social-btn" title="Google">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google">
                    </a>
                    <a href="{{ url('auth/facebook') }}" class="social-btn" title="Facebook">
                        <svg viewBox="0 0 24 24" fill="#1877F2">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                    <a href="{{ url('auth/github') }}" class="social-btn" title="GitHub">
                        <svg viewBox="0 0 24 24" fill="#171717">
                            <path
                                d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                        </svg>
                    </a>
                    <a href="{{ url('auth/twitter') }}" class="social-btn" title="Twitter">
                        <svg viewBox="0 0 24 24" fill="#1DA1F2">
                            <path
                                d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleAuth() {
            const panel = document.getElementById('authPanel');
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const panelTitle = document.getElementById('panelTitle');
            const panelText = document.getElementById('panelText');
            const toggleBtn = document.getElementById('toggleBtn');

            if (loginForm.classList.contains('hidden')) {
                // Show Login
                panel.classList.remove('register');
                panel.classList.add('login');
                loginForm.classList.remove('hidden');
                registerForm.classList.remove('visible');
                panelTitle.textContent = 'Selamat Datang Di Website ICB CINTA TEKNIKA';
                panelText.textContent = 'Sistem Absensi Guru Termodern & Terpercaya';
                toggleBtn.textContent = 'Buat Akun';
                document.getElementById('pageTitle').textContent = 'Login - {{ config('app.name', 'ICB CT') }}';
            } else {
                // Show Register
                panel.classList.remove('login');
                panel.classList.add('register');
                loginForm.classList.add('hidden');
                registerForm.classList.add('visible');
                panelTitle.textContent = 'Daftar Akun Baru';
                panelText.textContent = 'Silahkan register untuk mulai menggunakan sistem absensi';
                toggleBtn.textContent = 'Sudah Punya Akun?';
                document.getElementById('pageTitle').textContent = 'Register - {{ config('app.name', 'ICB CT') }}';
            }
        }

        function checkPasswordStrength() {
            const password = document.getElementById('register-password').value;
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');

            if (!password) {
                strengthFill.className = 'strength-fill';
                strengthText.className = 'strength-text';
                strengthText.textContent = '';
                return;
            }

            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            strengthFill.className = 'strength-fill';
            strengthText.className = 'strength-text';

            if (strength <= 1) {
                strengthFill.classList.add('weak');
                strengthText.textContent = 'Lemah';
                strengthText.classList.add('weak');
            } else if (strength === 2) {
                strengthFill.classList.add('fair');
                strengthText.textContent = 'Cukup';
                strengthText.classList.add('fair');
            } else if (strength === 3 || strength === 4) {
                strengthFill.classList.add('good');
                strengthText.textContent = 'Bagus';
                strengthText.classList.add('good');
            } else if (strength === 5) {
                strengthFill.classList.add('strong');
                strengthText.textContent = 'Sangat Kuat';
                strengthText.classList.add('strong');
            }
        }
    </script>
</body>

</html>