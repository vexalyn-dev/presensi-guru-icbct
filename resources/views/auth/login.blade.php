<!DOCTYPE html>
<!DOCTYPE html>
<html lang="id" style="height:100%;overflow:hidden;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="pageTitle">Login - {{ config('app.name', 'ICB CT') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            min-height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            background: linear-gradient(135deg, #F1F5F9 0%, #E2E8F0 100%);
            margin: 0;
            overflow-y: auto;
        }

        .auth-container {
            position: relative;
            width: 900px;
            max-width: calc(100% - 32px);
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.18),
                        0 0 0 1px rgba(15, 23, 42, 0.05);
            overflow: hidden;
            display: flex;
            min-height: 560px;
        }

        /* Panel Kiri (Navy) */
        .auth-panel {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 50% !important;
            height: 100% !important;
            transition: transform 0.8s cubic-bezier(0.77, 0, 0.175, 1);
            z-index: 10 !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 40px 35px !important;
            text-align: center !important;
            overflow: hidden !important;
        }

        .auth-panel.login {
            background: linear-gradient(150deg, #080F1E 0%, #0F172A 55%, #162035 100%) !important;
            transform: translateX(0) !important;
        }

        .auth-panel.login::before {
            content: '';
            position: absolute; top: -80px; right: -80px;
            width: 320px; height: 320px; border-radius: 50%;
            background: radial-gradient(circle, rgba(250,204,21,0.09) 0%, transparent 65%);
            pointer-events: none;
        }

        .auth-panel.login::after {
            content: '';
            position: absolute; bottom: -80px; left: -80px;
            width: 280px; height: 280px; border-radius: 50%;
            background: radial-gradient(circle, rgba(99,102,241,0.07) 0%, transparent 65%);
            pointer-events: none;
        }

        .auth-panel.register {
            background: linear-gradient(135deg, #FACC15 0%, #F59E0B 100%);
            transform: translateX(100%);
        }

        .panel-content {
            color: white;
            z-index: 1;
            position: relative;
        }

        .panel-content h1 {
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            letter-spacing: -0.3px;
            line-height: 1.3;
        }

        .panel-content p {
            font-size: 0.88rem;
            color: rgba(255,255,255,0.5);
            margin-bottom: 2.5rem;
            max-width: 280px;
            line-height: 1.65;
        }

        .btn-toggle {
            padding: 13px 40px;
            border: 1.5px solid rgba(255,255,255,0.4);
            background: transparent;
            color: white;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.02em;
        }

        .btn-toggle:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.7);
            transform: translateY(-2px);
        }

        /* Container Form di Kanan */
        .forms-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        /* Individual Forms */
        .form-container {
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            padding: 35px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
            transition: all 0.6s ease-in-out;
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .form-container::-webkit-scrollbar { display: none; }

        /* Login Form - Default Visible */
        #loginForm {
            right: 0;
            left: auto;
            opacity: 1;
            z-index: 2;
        }

        /* Register Form - Default Hidden */
        #registerForm {
            left: 0;
            right: auto;
            opacity: 0;
            z-index: 1;
        }

        #loginForm.hidden  { opacity: 0; z-index: 1; }
        #registerForm.visible { opacity: 1; z-index: 2; }

        .form-header {
            margin-bottom: 2.5rem;
        }

        .form-header h2 {
            font-size: 2.2rem;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .form-header p {
            color: #64748B;
            font-size: 0.95rem;
        }

        .input-group {
            width: 100%;
            margin-bottom: 1.4rem;
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
            border: 1.5px solid #E2E8F0;
            border-radius: 14px;
            font-size: 0.95rem;
            background: #F8FAFC;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #0F172A;
            background: white;
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.08);
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            width: 18px;
            height: 18px;
        }

        /* ── CUSTOM STYLED EYE ICON ── */
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #94A3B8;
            width: 36px;
            height: 36px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: color 0.2s ease, background 0.2s ease, transform 0.15s ease;
            -webkit-tap-highlight-color: transparent;
        }
        .password-toggle:hover {
            color: #0F172A;
            background: rgba(15,23,42,0.06);
        }
        .password-toggle:active {
            transform: translateY(-50%) scale(0.9);
        }
        .password-toggle svg {
            width: 20px;
            height: 20px;
            stroke-width: 1.8;
            flex-shrink: 0;
        }
        /* Beri ruang di kanan input untuk eye button */
        .input-wrapper input.password-input {
            padding-right: 48px !important;
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: #0F172A;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.25);
            margin-top: 0.75rem;
            letter-spacing: 0.02em;
            min-height: 48px;
            touch-action: manipulation;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.35);
            background: #1a2540;
        }

        .error-message {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-left: 4px solid #DC2626;
            color: #DC2626;
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            animation: slideDown 0.3s ease;
        }

        .error-message div {
            margin: 5px 0;
        }

        .error-message div:first-child {
            margin-top: 0;
        }

        .error-message div:last-child {
            margin-bottom: 0;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .social-btn:hover {
            border-color: #FACC15;
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 6px 15px rgba(250, 204, 21, 0.3);
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
            transition: all 0.2s ease;
        }

        .terms-text a:hover {
            text-decoration: underline;
        }


        /* Mobile Header Panel - NAVY/GOLD THEME */
        .mobile-header {
            display: none;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            padding: 40px 24px 36px;
            border-radius: 20px 20px 0 0;
            text-align: center;
            color: white;
            margin-bottom: 0;
            box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.25);
            position: relative;
            overflow: hidden;
        }

        .mobile-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: -50px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(250, 204, 21, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .mobile-header::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -50px;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(250, 204, 21, 0.05) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .mobile-logo-container {
            width: 110px;
            height: 110px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, rgba(250, 204, 21, 0.15) 0%, rgba(250, 204, 21, 0.08) 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2.5px solid rgba(250, 204, 21, 0.4);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
        }

        .mobile-logo-container img {
            width: 85%;
            height: 85%;
            object-fit: contain;
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.3));
        }

        .mobile-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            color: #FACC15;
            text-shadow: 0 3px 8px rgba(0, 0, 0, 0.4);
            letter-spacing: -0.3px;
            position: relative;
            z-index: 1;
        }

        .mobile-header p {
            font-size: 0.9rem;
            opacity: 0.95;
            line-height: 1.7;
            max-width: 300px;
            margin: 0 auto;
            color: #E2E8F0;
            font-weight: 400;
            position: relative;
            z-index: 1;
        }

        /* Register Link Styling */
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #E2E8F0;
        }

        .register-link p {
            color: #64748B;
            font-size: 0.9rem;
            margin: 0;
        }

        .register-link a {
            color: #FACC15;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .register-link a:hover,
        .register-link a:active {
            color: #F59E0B;
            text-decoration: underline;
        }

        /* ════════════════════════════════════════
           MOBILE REDESIGN — MODERN & PREMIUM
           Berlaku untuk semua layar ≤ 768px
           ════════════════════════════════════════ */
        @media (max-width: 768px) {

            /* ── Body & html: full viewport, no scroll bounce ── */
            html, body {
                min-height: 100%;
                height: 100%;
                background: #0A1628;
                margin: 0; padding: 0;
            }
            body {
                align-items: flex-start;
                padding: 0;
                justify-content: flex-start;
            }

            /* ── Auth container: true full-screen ── */
            .auth-container {
                width: 100%;
                max-width: 100%;
                min-height: 100vh;
                min-height: 100svh;
                display: flex;
                flex-direction: column;
                border-radius: 0;
                box-shadow: none;
                border: none;
                overflow: visible;
                background: transparent;
                position: relative;
            }
            .auth-container::before,
            .auth-container::after { display: none !important; }

            /* ── Sembunyikan panel desktop ── */
            .auth-panel { display: none !important; }

            /* ── MOBILE HEADER: navy gradient, lega ── */
            .mobile-header {
                display: flex !important;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 56px 32px 52px;
                background: linear-gradient(160deg, #080F1E 0%, #0F172A 60%, #162035 100%);
                border-radius: 0;
                text-align: center;
                position: relative;
                overflow: hidden;
                flex-shrink: 0;
            }
            .mobile-header::before {
                content: '';
                position: absolute; top: -80px; right: -80px;
                width: 300px; height: 300px; border-radius: 50%;
                background: radial-gradient(circle, rgba(250,204,21,0.09) 0%, transparent 65%);
                pointer-events: none;
            }
            .mobile-header::after {
                content: '';
                position: absolute; bottom: -60px; left: -60px;
                width: 240px; height: 240px; border-radius: 50%;
                background: radial-gradient(circle, rgba(99,102,241,0.07) 0%, transparent 65%);
                pointer-events: none;
            }

            /* Logo box */
            .mobile-logo-container {
                width: 112px !important;
                height: 112px !important;
                margin: 0 auto 24px !important;
                border-radius: 30px !important;
                background: rgba(255,255,255,0.04) !important;
                border: 1.5px solid rgba(250,204,21,0.28) !important;
                box-shadow: 0 20px 48px rgba(0,0,0,0.4),
                            inset 0 1px 0 rgba(255,255,255,0.07) !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                position: relative;
                z-index: 1;
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
            }
            .mobile-logo-container img {
                width: 80% !important;
                height: 80% !important;
                object-fit: contain !important;
                filter: drop-shadow(0 4px 14px rgba(0,0,0,0.35)) !important;
            }

            .mobile-header h1 {
                font-size: 1.4rem !important;
                font-weight: 800 !important;
                color: #FFFFFF !important;
                text-shadow: 0 2px 8px rgba(0,0,0,0.25) !important;
                margin-bottom: 10px !important;
                letter-spacing: -0.3px;
                position: relative; z-index: 1;
            }
            .mobile-header p {
                font-size: 0.85rem !important;
                line-height: 1.65 !important;
                color: rgba(255,255,255,0.45) !important;
                max-width: 270px;
                margin: 0 auto;
                position: relative; z-index: 1;
            }

            /* ── Forms container: flex grow, putih ── */
            .forms-container {
                position: relative;
                width: 100%;
                flex: 1;
                display: flex;
                flex-direction: column;
                background: #FFFFFF;
                border-radius: 32px 32px 0 0;
                margin-top: -28px;
                box-shadow: 0 -4px 32px rgba(15,23,42,0.18);
                z-index: 2;
                overflow: visible;
            }

            /* ── Form card: fill sisa tinggi layar ── */
            .form-container {
                position: relative;
                width: 100%;
                flex: 1;
                min-height: 0;
                max-height: none;
                padding: 40px 28px 52px;
                overflow: visible;
                justify-content: flex-start;
                background: transparent;
                border-radius: 0;
                box-shadow: none;
                margin-top: 0;
                left: auto; right: auto;
                opacity: 1 !important;
                z-index: 2 !important;
            }

            /* Tunjuk-sembunyikan form login/register mobile */
            #loginForm {
                display: flex;
                flex-direction: column;
                opacity: 1 !important;
                z-index: 2 !important;
            }
            #loginForm.hidden { display: none !important; }
            #registerForm { display: none !important; }
            #registerForm.visible { display: flex !important; flex-direction: column; opacity: 1 !important; }

            /* ── Form header: lebih lega ── */
            .form-header {
                margin-bottom: 32px;
                text-align: left;
            }
            .form-header h2 {
                font-size: 1.85rem !important;
                font-weight: 800 !important;
                color: #0F172A;
                margin-bottom: 6px;
                letter-spacing: -0.6px;
            }
            .form-header p {
                font-size: 0.88rem !important;
                color: #64748B !important;
                line-height: 1.55;
            }

            /* ── Input groups: spacing lega ── */
            .input-group { margin-bottom: 20px; }
            .input-group label {
                display: block;
                font-size: 0.82rem !important;
                font-weight: 600;
                color: #1E293B;
                margin-bottom: 8px !important;
                letter-spacing: 0.01em;
            }
            .input-wrapper input {
                height: 54px !important;
                padding: 0 52px 0 48px !important;
                border-radius: 14px !important;
                border: 1.5px solid #E2E8F0 !important;
                background: #F8FAFC !important;
                font-size: 15px !important;
                color: #0F172A;
                box-shadow: none !important;
                transition: border-color 0.2s, box-shadow 0.2s, background 0.2s !important;
            }
            .input-wrapper input:focus {
                border-color: #0F172A !important;
                background: #FFFFFF !important;
                box-shadow: 0 0 0 4px rgba(15,23,42,0.08) !important;
            }
            .input-wrapper input::placeholder { color: #94A3B8 !important; }
            .input-icon {
                left: 16px !important;
                width: 19px !important;
                height: 19px !important;
            }
            .password-toggle {
                right: 10px !important;
                width: 40px !important;
                height: 40px !important;
            }
            .password-toggle svg {
                width: 20px !important;
                height: 20px !important;
            }

            /* ── Remember me row ── */
            .cb-label {
                font-size: 0.88rem !important;
                color: #475569;
            }
            .cb-box {
                width: 20px !important;
                height: 20px !important;
                min-width: 20px !important;
                border-radius: 6px !important;
            }

            /* Remember+LupaPassword row spacing */
            .input-group + div[style*="flex"] {
                margin: 4px 0 24px !important;
            }

            /* ── Submit button: tinggi & menonjol ── */
            .btn-submit {
                width: 100%;
                height: 56px !important;
                border-radius: 16px !important;
                font-size: 1.05rem !important;
                font-weight: 700 !important;
                letter-spacing: 0.03em !important;
                background: #0F172A !important;
                box-shadow: 0 8px 24px rgba(15,23,42,0.28) !important;
                margin-top: 4px !important;
                padding: 0 !important;
                transition: transform 0.15s, box-shadow 0.2s, background 0.2s !important;
            }
            .btn-submit:hover { background: #1a2540 !important; }
            .btn-submit:active { transform: scale(0.98) !important; }

            /* ── Error/success messages ── */
            .error-message, .success-message {
                border-radius: 12px;
                font-size: 0.84rem;
                margin-bottom: 20px;
                padding: 14px 16px;
            }
        }

        /* ── 480px ke bawah ── */
        @media (max-width: 480px) {
            .mobile-header { padding: 48px 24px 44px; }
            .mobile-logo-container { width: 100px !important; height: 100px !important; }
            .form-container { padding: 36px 22px 48px; }
            .form-header { margin-bottom: 28px; }
            .form-header h2 { font-size: 1.65rem !important; }
        }

        /* ── 390px ke bawah ── */
        @media (max-width: 390px) {
            .mobile-header { padding: 40px 20px 36px; }
            .mobile-logo-container { width: 92px !important; height: 92px !important; }
            .form-container { padding: 30px 18px 44px; }
            .form-header h2 { font-size: 1.5rem !important; }
            .input-wrapper input { height: 50px !important; }
            .btn-submit { height: 52px !important; }
        }
        /* ── PAGE TRANSITION LOADING OVERLAY ── */
        #pt-overlay {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(10, 15, 30, 0.82);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 0;
            opacity: 0; pointer-events: none;
            transition: opacity 0.25s ease;
        }
        #pt-overlay.show { opacity: 1; pointer-events: all; }

        /* White card */
        .pt-card {
            background: #fff;
            border-radius: 24px;
            padding: 36px 40px 32px;
            width: 240px;
            text-align: center;
            box-shadow: 0 24px 56px rgba(0,0,0,0.35);
            position: relative;
            overflow: hidden;
        }

        /* Spinner ring (double like dashboard) */
        .pt-ring-wrap {
            position: relative;
            width: 72px; height: 72px;
            margin: 0 auto 20px;
        }
        .pt-ring-outer {
            position: absolute; inset: 0;
            border-radius: 50%;
            border: 4px solid #E2E8F0;
            border-top-color: #0F172A;
            animation: ptSpin 0.9s linear infinite;
        }
        .pt-ring-inner {
            position: absolute; top: 8px; left: 8px; right: 8px; bottom: 8px;
            border-radius: 50%;
            border: 4px solid transparent;
            border-bottom-color: #FACC15;
            animation: ptSpinRev 0.7s linear infinite;
        }
        /* Result icons (hidden by default) */
        .pt-icon-success, .pt-icon-error {
            display: none;
            position: absolute; inset: 0;
            align-items: center; justify-content: center;
        }
        .pt-icon-success svg { width: 72px; height: 72px; }
        .pt-icon-error  svg { width: 72px; height: 72px; }

        /* Bouncing dots */
        .pt-dots {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            margin-bottom: 16px;
        }
        .pt-dots span {
            width: 7px; height: 7px;
            background: #0F172A; border-radius: 50%;
        }
        .pt-dots span:nth-child(1) { animation: ptBounce 0.6s ease-in-out infinite; }
        .pt-dots span:nth-child(2) { animation: ptBounce 0.6s ease-in-out 0.15s infinite; }
        .pt-dots span:nth-child(3) { animation: ptBounce 0.6s ease-in-out 0.3s infinite; }

        .pt-label {
            font-size: 0.88rem; font-weight: 700; color: #0F172A;
            margin-bottom: 3px;
        }
        .pt-sublabel {
            font-size: 0.75rem; color: #94A3B8; font-weight: 400;
        }

        @keyframes ptSpin    { to { transform: rotate(360deg); } }
        @keyframes ptSpinRev { to { transform: rotate(-360deg); } }
        @keyframes ptBounce  {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-6px); }
        }
        @keyframes ptCheckIn {
            0%   { stroke-dashoffset: 60; opacity: 0; }
            100% { stroke-dashoffset: 0;  opacity: 1; }
        }
        @keyframes ptCircleIn {
            0%   { stroke-dashoffset: 180; }
            100% { stroke-dashoffset: 0; }
        }
        @keyframes ptXIn {
            0%   { opacity: 0; transform: scale(0.5); }
            60%  { transform: scale(1.15); }
            100% { opacity: 1; transform: scale(1); }
        }


        /* ── PC DESKTOP: sembunyikan tombol Buat Akun ── */
        @media (min-width: 769px) {
            .btn-toggle {
                display: none !important;
                visibility: hidden !important;
                pointer-events: none !important;
            }
        }

        /* ── CUSTOM CHECKBOX (menggantikan native) ── */
        /* Sembunyikan native checkbox asli */
        .cb-remember-native {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }
        /* Box visual */
        .cb-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            min-width: 22px;
            border: 2px solid #CBD5E1;
            border-radius: 7px;
            background: #fff;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s, transform 0.15s;
            position: relative;
            box-shadow: 0 1px 3px rgba(15,23,42,0.06);
        }
        .cb-box:hover {
            border-color: #0F172A;
            box-shadow: 0 0 0 4px rgba(15,23,42,0.08);
        }
        .cb-box .cb-check {
            opacity: 0;
            transform: scale(0) rotate(-10deg);
            transition: opacity 0.18s, transform 0.22s cubic-bezier(0.34,1.56,0.64,1);
        }
        .cb-box.checked {
            background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 100%);
            border-color: #0F172A;
            box-shadow: 0 4px 14px rgba(15,23,42,0.28);
            animation: cbBounce 0.38s cubic-bezier(0.34,1.56,0.64,1);
        }
        .cb-box.checked .cb-check {
            opacity: 1;
            transform: scale(1) rotate(0deg);
        }
        @keyframes cbBounce {
            0%   { transform: scale(0.8); }
            55%  { transform: scale(1.18); }
            100% { transform: scale(1); }
        }
        .cb-label {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
            user-select: none;
            font-size: 0.85rem;
            font-weight: 500;
            color: #475569;
        }
        .cb-label:hover .cb-box {
            border-color: #0F172A;
            box-shadow: 0 0 0 4px rgba(15,23,42,0.08);
        }

        /* ── HAPUS BROWSER NATIVE EYE ICON ── */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none !important;
            width: 0; height: 0;
        }
        input[type="password"]::-webkit-credentials-auto-fill-button,
        input[type="password"]::-webkit-contacts-auto-fill-button,
        input[type="password"]::-webkit-textfield-decoration-container {
            display: none !important;
            visibility: hidden;
            pointer-events: none;
        }
        /* Edge Chromium */
        input[type="password"]::-webkit-input-placeholder { }
        ::-webkit-credential-manager-button { display: none !important; }
    </style>

</head>

<body>

<!-- ── PAGE TRANSITION OVERLAY ── -->
<div id="pt-overlay">
    <div class="pt-card">
        <!-- Spinner state -->
        <div id="pt-state-loading">
            <div class="pt-ring-wrap">
                <div class="pt-ring-outer"></div>
                <div class="pt-ring-inner"></div>
            </div>
            <div class="pt-dots">
                <span></span><span></span><span></span>
            </div>
            <p class="pt-label">Memeriksa akun…</p>
            <p class="pt-sublabel">Mohon tunggu sebentar</p>
        </div>

        <!-- Success state -->
        <div id="pt-state-success" style="display:none;">
            <div class="pt-ring-wrap" style="margin-bottom:20px;">
                <svg viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:72px;height:72px;">
                    <circle cx="36" cy="36" r="32" stroke="#22C55E" stroke-width="4"
                            stroke-dasharray="201" stroke-dashoffset="201"
                            style="animation: ptCircleIn 0.45s ease forwards;"/>
                    <path d="M20 36 L31 47 L52 25" stroke="#22C55E" stroke-width="4"
                          stroke-linecap="round" stroke-linejoin="round"
                          stroke-dasharray="60" stroke-dashoffset="60"
                          style="animation: ptCheckIn 0.4s ease 0.3s forwards;"/>
                </svg>
            </div>
            <p class="pt-label" style="color:#16A34A;">Berhasil masuk!</p>
            <p class="pt-sublabel">Mengalihkan ke dashboard…</p>
        </div>

        <!-- Error state -->
        <div id="pt-state-error" style="display:none;">
            <div class="pt-ring-wrap" style="margin-bottom:20px;">
                <svg viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg"
                     style="width:72px;height:72px;animation:ptXIn 0.35s ease forwards;">
                    <circle cx="36" cy="36" r="32" stroke="#EF4444" stroke-width="4"/>
                    <path d="M24 24 L48 48 M48 24 L24 48" stroke="#EF4444" stroke-width="4"
                          stroke-linecap="round"/>
                </svg>
            </div>
            <p class="pt-label" style="color:#DC2626;" id="pt-error-title">Email atau password salah</p>
            <p class="pt-sublabel" id="pt-error-sub">Silakan coba lagi</p>
        </div>
    </div>
</div>

    <div class="auth-container">
        <!-- Mobile Header Panel -->
        <div class="mobile-header" id="mobileHeader" style="display: none;">
            <div class="mobile-logo-container">
                @php
                    $appSettings = null;
                    try {
                        $appSettings = \App\Models\AppSetting::getInstance();
                    } catch (\Throwable $e) {
                        $appSettings = null;
                    }
                @endphp
                @if($appSettings && $appSettings->app_logo)
                    <img src="{{ asset('storage/' . $appSettings->app_logo) }}" alt="Logo">
                @else
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                @endif
            </div>
            <h1 id="mobileTitle">SMK ICB Cinta Teknika</h1>
            <p id="mobileDesc">Sistem presensi digital sekolah yang mudah, cepat, dan terpercaya.</p>
        </div>

        <!-- Sliding Panel (KIRI) -->
        <div class="auth-panel login" id="authPanel">
            @php
                $appSettingsPanel = null;
                try { $appSettingsPanel = \App\Models\AppSetting::getInstance(); } catch (\Throwable $e) {}
            @endphp

            {{-- Logo tunggal di tengah --}}
            <div style="width:130px;height:130px;display:flex;align-items:center;justify-content:center;margin-bottom:24px;position:relative;z-index:1;">
                @if($appSettingsPanel && $appSettingsPanel->app_logo)
                    <img src="{{ asset('storage/' . $appSettingsPanel->app_logo) }}" alt="Logo" style="width:100%;height:100%;object-fit:contain;filter:drop-shadow(0 4px 16px rgba(0,0,0,0.3));">
                @else
                    <svg width="80" height="80" fill="none" stroke="#FACC15" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                @endif
            </div>

            <div class="panel-content">
                <h1 id="panelTitle">{{ $appSettingsPanel->app_name ?? 'ICB CINTA TEKNIKA' }}</h1>
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
                    <div class="error-message">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="success-message">{{ session('success') }}</div>
                @endif

                {{-- AJAX error box --}}
                <div id="login-error-box" style="display:none;align-items:flex-start;gap:10px;background:#FEF2F2;border:1px solid #FECACA;border-left:3px solid #DC2626;color:#B91C1C;padding:12px 14px;border-radius:12px;margin-bottom:16px;font-size:0.84rem;font-weight:500;line-height:1.5;animation:slideDown 0.3s ease;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="login-error-text">Email atau password salah.</span>
                </div>

                <form method="POST" action="{{ route('login.post') }}" id="loginFormElement" novalidate>
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
                            <input type="password" id="login-password" name="password" placeholder="••••••••" required
                                class="password-input" autocomplete="current-password">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <button type="button" class="password-toggle"
                                onclick="togglePassword('login-password', this)">
                                <svg class="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Lupa Password -->
                    <div style="display:flex;align-items:center;justify-content:space-between;margin:0.25rem 0 0.85rem;">
                        <label class="cb-label" onclick="toggleRemember()">
                            <div class="cb-box" id="cbBox">
                                <svg class="cb-check" width="13" height="13" viewBox="0 0 13 13" fill="none">
                                    <path d="M2 6.5L5 9.5L11 3.5" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <input type="checkbox" name="remember" id="remember" class="cb-remember-native" {{ old('remember') ? 'checked' : '' }}>
                            Ingat Saya
                        </label>
                        <a href="{{ route('password.request') }}"
                           style="font-size:0.84rem;font-weight:600;color:#0F172A;text-decoration:none;transition:opacity 0.2s;"
                           onmouseover="this.style.opacity='0.55'" onmouseout="this.style.opacity='1'">
                            Lupa Password?
                        </a>
                    </div>

                    <button type="submit" class="btn-submit" id="loginSubmitBtn">Masuk</button>
                </form>
            </div>
            {{-- END #loginForm --}}

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
                                required class="password-input">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <button type="button" class="password-toggle"
                                onclick="togglePassword('register-password', this)">
                                <svg class="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="register-password-confirm">Konfirmasi Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="register-password-confirm" name="password_confirmation"
                                placeholder="••••••••" required class="password-input">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <button type="button" class="password-toggle"
                                onclick="togglePassword('register-password-confirm', this)">
                                <svg class="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Daftar Sekarang</button>

                    <p class="terms-text">
                        Dengan mendaftar, Anda menyetujui <a href="#">Syarat &amp; Ketentuan</a> serta <a
                            href="#">Kebijakan Privasi</a> kami.
                    </p>
                </form>

                <div class="divider"><span>atau daftar dengan</span></div>

                <div class="register-link">
                    <p>Sudah punya akun? <a href="#" onclick="toggleAuth(); return false;">Login sekarang</a></p>
                </div>
            </div>
            {{-- END #registerForm --}}

        </div>
        {{-- END .forms-container --}}
    </div>
    {{-- END .auth-container --}}

    <script>
        function toggleAuth() {
            const panel = document.getElementById('authPanel');
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const panelTitle = document.getElementById('panelTitle');
            const panelText = document.getElementById('panelText');
            const toggleBtn = document.getElementById('toggleBtn');
            const mobileTitle = document.getElementById('mobileTitle');
            const mobileDesc = document.getElementById('mobileDesc');
            const formsContainer = document.querySelector('.forms-container');
            const authContainer = document.querySelector('.auth-container');

            if (loginForm.classList.contains('hidden')) {
                // Show Login
                panel.classList.remove('register');
                panel.classList.add('login');
                if (authContainer) authContainer.classList.remove('register-mode');
                loginForm.classList.remove('hidden');
                registerForm.classList.remove('visible');
                
                // Update mobile header text
                if (mobileTitle) mobileTitle.textContent = 'SMK ICB Cinta Teknika';
                if (mobileDesc) mobileDesc.textContent = 'Sistem presensi digital sekolah yang mudah, cepat, dan terpercaya.';
                
                panelTitle.textContent = 'Selamat Datang Di Website ICB CINTA TEKNIKA';
                panelText.textContent = 'Aplikasi Presensi Guru ICB Cinta Teknika';
                toggleBtn.textContent = 'Buat Akun';
                document.getElementById('pageTitle').textContent = "Login - {{ config('app.name', 'ICB CT') }}";
                
                // Scroll to top of form
                if (formsContainer) {
                    formsContainer.scrollTop = 0;
                }
                if (loginForm) {
                    loginForm.scrollTop = 0;
                }
            } else {
                // Show Register
                panel.classList.remove('login');
                panel.classList.add('register');
                if (authContainer) authContainer.classList.add('register-mode');
                loginForm.classList.add('hidden');
                registerForm.classList.add('visible');
                
                // Update mobile header text
                if (mobileTitle) mobileTitle.textContent = 'Daftar Akun Baru';
                if (mobileDesc) mobileDesc.textContent = 'Buat akun untuk mulai menggunakan aplikasi';
                
                panelTitle.textContent = 'Daftar Akun Baru';
                panelText.textContent = 'Silahkan register untuk mulai menggunakan aplikasi';
                toggleBtn.textContent = 'Sudah Punya Akun?';
                document.getElementById('pageTitle').textContent = "Register - {{ config('app.name', 'ICB CT') }}";
                
                // Scroll to top of form
                if (formsContainer) {
                    formsContainer.scrollTop = 0;
                }
                if (registerForm) {
                    registerForm.scrollTop = 0;
                }
            }
            
            // Scroll page to top on mobile (for better UX)
            window.scrollTo(0, 0);
        }

        // Toggle Password Visibility
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const eyeIcon = button.querySelector('.eye-icon');

            if (input.type === 'password') {
                input.type = 'text';
                // Change to eye-off icon
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                    </path>
                `;
            } else {
                input.type = 'password';
                // Change back to eye icon
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                    </path>
                `;
            }
        }

        // ── Custom Checkbox Toggle ──────────────────────────
        // Init state on load
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('login-email');
            if (emailInput) emailInput.focus();

            // Sync checkbox display state with native input
            const native = document.getElementById('remember');
            const box    = document.getElementById('cbBox');
            if (native && box && native.checked) {
                box.classList.add('checked');
            }
        });

        function toggleRemember() {
            const native = document.getElementById('remember');
            const box    = document.getElementById('cbBox');
            if (!native || !box) return;
            native.checked = !native.checked;
            if (native.checked) {
                box.classList.add('checked');
            } else {
                box.classList.remove('checked');
            }
        }

        // ── AJAX Login dengan Overlay Feedback ────────────────
        (function() {
            var form     = document.getElementById('loginFormElement');
            var overlay  = document.getElementById('pt-overlay');
            var stLoad   = document.getElementById('pt-state-loading');
            var stOk     = document.getElementById('pt-state-success');
            var stErr    = document.getElementById('pt-state-error');
            var errTitle = document.getElementById('pt-error-title');
            var errSub   = document.getElementById('pt-error-sub');
            var errBox   = document.getElementById('login-error-box');
            var errText  = document.getElementById('login-error-text');

            if (!form || !overlay) return;

            function showState(name) {
                stLoad.style.display = name === 'loading' ? 'block' : 'none';
                stOk.style.display   = name === 'success' ? 'block' : 'none';
                stErr.style.display  = name === 'error'   ? 'block' : 'none';
            }

            function hideOverlay(cb) {
                overlay.style.transition = 'opacity 0.3s ease';
                overlay.classList.remove('show');
                setTimeout(function() {
                    showState('loading'); // reset untuk next attempt
                    if (cb) cb();
                }, 320);
            }

            function showFormError(msg) {
                if (errBox) {
                    if (errText) errText.textContent = msg;
                    errBox.style.display = 'flex';
                    errBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
                document.getElementById('login-password')?.focus();
                document.getElementById('login-password')?.select();
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Sembunyikan error box lama
                if (errBox) errBox.style.display = 'none';

                var email = document.getElementById('login-email')?.value.trim();
                var pass  = document.getElementById('login-password')?.value;
                if (!email || !pass) return;

                // Tampilkan overlay + spinner
                showState('loading');
                overlay.classList.add('show');

                var fd = new FormData(form);
                var csrfToken = document.querySelector('input[name="_token"]')?.value || '';

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: fd
                })
                .then(function(res) {
                    return res.json().then(function(data) {
                        return { status: res.status, data: data };
                    });
                })
                .then(function(result) {
                    var status = result.status;
                    var data   = result.data;

                    if (status === 200 && data.success && data.redirect) {
                        // ✅ Sukses — set sessionStorage untuk welcome modal SEBELUM redirect
                        if (data.welcome_key) {
                            sessionStorage.setItem(data.welcome_key, '1');
                        } else if (data.show_welcome && data.user_id) {
                            sessionStorage.setItem('show_welcome_' + data.user_id, '1');
                        }
                        showState('success');
                        setTimeout(function() {
                            window.location.href = data.redirect;
                        }, 1300);

                    } else {
                        // ❌ Gagal — tampilkan X merah → tutup overlay → error di form
                        var msg = 'Email atau password salah.';
                        if (data.errors) {
                            var k = Object.keys(data.errors)[0];
                            msg = data.errors[k][0] || msg;
                        } else if (data.message) {
                            msg = data.message;
                        }
                        if (errTitle) errTitle.textContent = msg;
                        showState('error');
                        setTimeout(function() {
                            hideOverlay(function() { showFormError(msg); });
                        }, 1500);
                    }
                })
                .catch(function() {
                    // Network error — tutup overlay & fallback
                    hideOverlay(function() {
                        showFormError('Koneksi bermasalah. Coba lagi.');
                    });
                });
            });
        })();
    </script>
</body>

</html>
