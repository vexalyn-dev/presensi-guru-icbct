{{-- PWA Install Prompt — White/Navy, modern & clean --}}
<div id="pwa-prompt" aria-label="Install aplikasi" role="dialog" style="
    position: fixed;
    bottom: 96px;
    right: 16px;
    z-index: 9000;
    width: 300px;
    max-width: calc(100vw - 32px);
    transform: translateX(calc(100% + 32px));
    transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.4s ease;
    opacity: 0;
    pointer-events: none;
    will-change: transform, opacity;
">
    <div style="
        background: #ffffff;
        border-radius: 24px;
        padding: 20px;
        box-shadow:
            0 2px 4px rgba(15,23,42,0.04),
            0 8px 24px rgba(15,23,42,0.10),
            0 32px 64px rgba(15,23,42,0.12);
        border: 1px solid rgba(226,232,240,0.8);
        position: relative;
        overflow: hidden;
    ">
        {{-- Top subtle line accent --}}
        <div style="
            position: absolute; top: 0; left: 20px; right: 20px; height: 2px;
            background: linear-gradient(90deg, transparent, #0f172a 30%, #334155 70%, transparent);
            border-radius: 0 0 2px 2px;
        "></div>

        {{-- Close --}}
        <button id="pwa-close" aria-label="Tutup" style="
            position: absolute; top: 14px; right: 14px;
            width: 26px; height: 26px;
            background: #f1f5f9;
            border: none; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: #64748b;
            transition: background 0.2s, color 0.2s;
        "
        onmouseover="this.style.background='#e2e8f0';this.style.color='#0f172a'"
        onmouseout="this.style.background='#f1f5f9';this.style.color='#64748b'">
            <svg width="11" height="11" viewBox="0 0 12 12" fill="none">
                <path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>

        {{-- App icon + name --}}
        <div style="display: flex; align-items: center; gap: 13px; margin-bottom: 14px; padding-right: 28px;">
            <div style="
                width: 52px; height: 52px; border-radius: 16px; flex-shrink: 0;
                background: #0f172a;
                display: flex; align-items: center; justify-content: center;
                box-shadow: 0 4px 12px rgba(15,23,42,0.2);
                overflow: hidden;
            ">
                @php $appSettings = \App\Models\AppSetting::getInstance(); @endphp
                @if($appSettings && $appSettings->app_logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($appSettings->app_logo))
                    <img src="{{ asset('storage/' . $appSettings->app_logo) }}" style="width:36px;height:36px;object-fit:contain;">
                @else
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                @endif
            </div>
            <div>
                <p style="font-size: 0.85rem; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.25; letter-spacing: -0.01em;">
                    {{ $appSettings->app_name ?? 'Presensi ICB CT' }}
                </p>
                <p style="font-size: 0.72rem; color: #64748b; margin: 3px 0 0; line-height: 1.3;">
                    Install sebagai aplikasi
                </p>
            </div>
        </div>

        {{-- Divider --}}
        <div style="height: 1px; background: #f1f5f9; margin: 0 0 14px;"></div>

        {{-- Description --}}
        <p style="
            font-size: 0.76rem;
            color: #475569;
            line-height: 1.6;
            margin: 0 0 14px;
        ">
            Akses lebih cepat, tampilan fullscreen, dan bekerja offline tanpa perlu browser.
        </p>

        {{-- Feature chips --}}
        <div style="display: flex; gap: 6px; margin-bottom: 16px; flex-wrap: wrap;">
            @foreach([
                ['Fullscreen', 'M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3'],
                ['Offline',    'M1 6s4-6 11-6 11 6 11 6-4 6-11 6-11-6-11-6z M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6'],
                ['Cepat',      'M13 2L3 14h9l-1 8 10-12h-9l1-8z'],
            ] as [$label, $icon])
            <span style="
                display: inline-flex; align-items: center; gap: 5px;
                font-size: 0.68rem; font-weight: 600;
                padding: 4px 10px; border-radius: 999px;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                color: #334155;
                letter-spacing: 0.01em;
            ">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $icon }}"/>
                </svg>
                {{ $label }}
            </span>
            @endforeach
        </div>

        {{-- Install button --}}
        <button id="pwa-install-btn" style="
            width: 100%;
            padding: 11px;
            background: #0f172a;
            border: none; border-radius: 14px;
            font-size: 0.82rem; font-weight: 700;
            color: #ffffff;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            letter-spacing: -0.01em;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 12px rgba(15,23,42,0.18);
        "
        onmouseover="this.style.background='#1e293b';this.style.boxShadow='0 6px 20px rgba(15,23,42,0.28)'"
        onmouseout="this.style.background='#0f172a';this.style.boxShadow='0 4px 12px rgba(15,23,42,0.18)'"
        onmousedown="this.style.transform='scale(0.97)'"
        onmouseup="this.style.transform=''">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Install Sekarang
        </button>

        {{-- iOS hint --}}
        <p id="pwa-ios-hint" style="
            display: none;
            font-size: 0.68rem;
            color: #94a3b8;
            text-align: center;
            margin: 10px 0 0;
            line-height: 1.5;
        ">
            Di iOS: ketuk <strong style="color:#0f172a">Share</strong> → <strong style="color:#0f172a">Add to Home Screen</strong>
        </p>
    </div>
</div>

<script>
(function () {
    var STORAGE_KEY  = 'pwa_prompt_dismissed';
    var DISMISS_DAYS = 3;

    var prompt     = document.getElementById('pwa-prompt');
    var closeBtn   = document.getElementById('pwa-close');
    var installBtn = document.getElementById('pwa-install-btn');
    var iosHint    = document.getElementById('pwa-ios-hint');

    if (!prompt) return;

    // Jangan tampil kalau sudah dismiss baru-baru ini
    var dismissed = localStorage.getItem(STORAGE_KEY);
    if (dismissed && (Date.now() - parseInt(dismissed)) < DISMISS_DAYS * 86400 * 1000) return;

    // Jangan tampil kalau sudah installed (standalone mode)
    if (window.matchMedia('(display-mode: standalone)').matches) return;
    if (window.navigator.standalone === true) return;

    var deferredPrompt = null;

    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;
    });

    var isIos = /iphone|ipad|ipod/i.test(navigator.userAgent) && !window.MSStream;
    if (isIos && iosHint) {
        iosHint.style.display = 'block';
        if (installBtn) installBtn.style.display = 'none';
    }

    function show() {
        prompt.style.pointerEvents = 'all';
        requestAnimationFrame(function () {
            prompt.style.transform = 'translateX(0)';
            prompt.style.opacity   = '1';
        });
    }

    function hide(onDone) {
        prompt.style.transform    = 'translateX(calc(100% + 32px))';
        prompt.style.opacity      = '0';
        prompt.style.pointerEvents = 'none';
        if (onDone) setTimeout(onDone, 500);
    }

    // Muncul setelah 3 detik
    setTimeout(show, 3000);

    closeBtn && closeBtn.addEventListener('click', function () {
        hide(function () {
            localStorage.setItem(STORAGE_KEY, Date.now().toString());
        });
    });

    installBtn && installBtn.addEventListener('click', function () {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function (r) {
                deferredPrompt = null;
                if (r.outcome === 'accepted') {
                    hide();
                    localStorage.setItem(STORAGE_KEY, Date.now().toString());
                }
            });
        } else if (!isIos) {
            hide();
            localStorage.setItem(STORAGE_KEY, Date.now().toString());
        }
    });

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(function () {});
    }
})();
</script>
