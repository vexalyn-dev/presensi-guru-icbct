{{-- ══════════════════════════════════════════════════
     PWA INSTALL PROMPT — Premium slide-in dari kanan
     Muncul 3 detik setelah load, pojok kanan bawah.
     Klik X: slide keluar ke kanan lalu masuk lagi (hide).
     Disimpan di localStorage selama 3 hari jika ditutup.
══════════════════════════════════════════════════ --}}

<div id="pwa-prompt" aria-label="Install aplikasi" role="dialog" style="
    position: fixed;
    bottom: 88px;
    right: 16px;
    z-index: 9000;
    width: 320px;
    max-width: calc(100vw - 32px);
    transform: translateX(calc(100% + 32px));
    transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.35s ease;
    opacity: 0;
    pointer-events: none;
    will-change: transform, opacity;
">
    <div style="
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border: 1px solid rgba(250,204,21,0.25);
        border-radius: 20px;
        padding: 18px 18px 16px;
        box-shadow: 0 24px 56px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.04), inset 0 1px 0 rgba(255,255,255,0.06);
        position: relative;
        overflow: hidden;
    ">
        {{-- Glow accent --}}
        <div style="
            position: absolute; top: -40px; right: -40px;
            width: 160px; height: 160px;
            background: radial-gradient(circle, rgba(250,204,21,0.12) 0%, transparent 70%);
            pointer-events: none;
        "></div>

        {{-- Close button --}}
        <button id="pwa-close" aria-label="Tutup" style="
            position: absolute; top: 12px; right: 12px;
            width: 28px; height: 28px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: background 0.2s;
            color: rgba(255,255,255,0.6);
        " onmouseover="this.style.background='rgba(255,255,255,0.15)'"
           onmouseout="this.style.background='rgba(255,255,255,0.08)'">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                <path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>

        {{-- Header --}}
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px; padding-right: 24px;">
            <div style="
                width: 48px; height: 48px; border-radius: 14px; flex-shrink: 0;
                background: linear-gradient(135deg, #facc15, #f59e0b);
                display: flex; align-items: center; justify-content: center;
                box-shadow: 0 8px 20px rgba(250,204,21,0.3);
            ">
                @php $appSettings = \App\Models\AppSetting::getInstance(); @endphp
                @if($appSettings && $appSettings->app_logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($appSettings->app_logo))
                    <img src="{{ asset('storage/' . $appSettings->app_logo) }}" style="width:32px;height:32px;object-fit:contain;border-radius:8px;">
                @else
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                @endif
            </div>
            <div>
                <p style="font-size: 0.82rem; font-weight: 700; color: #fff; line-height: 1.2; margin: 0;">
                    {{ $appSettings->app_name ?? 'Presensi ICB CT' }}
                </p>
                <p style="font-size: 0.72rem; color: rgba(148,163,184,1); margin: 2px 0 0; line-height: 1.3;">
                    Install sebagai aplikasi
                </p>
            </div>
        </div>

        {{-- Description --}}
        <p style="
            font-size: 0.75rem;
            color: rgba(148,163,184,0.9);
            line-height: 1.5;
            margin: 0 0 14px;
        ">
            Akses lebih cepat, tampilan fullscreen, dan bekerja offline tanpa perlu browser.
        </p>

        {{-- Features --}}
        <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;">
            @foreach(['Fullscreen', 'Offline', 'Cepat'] as $feat)
            <span style="
                font-size: 0.65rem; font-weight: 600;
                padding: 3px 10px; border-radius: 999px;
                background: rgba(250,204,21,0.1);
                border: 1px solid rgba(250,204,21,0.2);
                color: #facc15;
                letter-spacing: 0.03em;
            ">{{ $feat }}</span>
            @endforeach
        </div>

        {{-- Install button --}}
        <button id="pwa-install-btn" style="
            width: 100%;
            padding: 11px;
            background: linear-gradient(135deg, #facc15 0%, #f59e0b 100%);
            border: none; border-radius: 12px;
            font-size: 0.82rem; font-weight: 700;
            color: #0f172a;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: opacity 0.2s, transform 0.15s;
            box-shadow: 0 4px 16px rgba(250,204,21,0.3);
        "
        onmouseover="this.style.opacity='0.9'"
        onmouseout="this.style.opacity='1'"
        onmousedown="this.style.transform='scale(0.97)'"
        onmouseup="this.style.transform=''">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Install Sekarang
        </button>

        {{-- iOS hint (hanya tampil di iOS) --}}
        <p id="pwa-ios-hint" style="
            display: none;
            font-size: 0.68rem;
            color: rgba(148,163,184,0.7);
            text-align: center;
            margin: 10px 0 0;
            line-height: 1.4;
        ">
            Di iOS: ketuk <strong style="color:#facc15">Share</strong> lalu <strong style="color:#facc15">Add to Home Screen</strong>
        </p>
    </div>
</div>

<script>
(function () {
    var STORAGE_KEY = 'pwa_prompt_dismissed';
    var DISMISS_DAYS = 3;

    var prompt  = document.getElementById('pwa-prompt');
    var closeBtn= document.getElementById('pwa-close');
    var installBtn = document.getElementById('pwa-install-btn');
    var iosHint = document.getElementById('pwa-ios-hint');

    if (!prompt) return;

    // Jangan tampil kalau sudah dismiss baru-baru ini
    var dismissed = localStorage.getItem(STORAGE_KEY);
    if (dismissed && (Date.now() - parseInt(dismissed)) < DISMISS_DAYS * 86400 * 1000) return;

    // Jangan tampil kalau sudah installed (standalone mode)
    if (window.matchMedia('(display-mode: standalone)').matches) return;
    if (window.navigator.standalone === true) return; // iOS standalone

    var deferredPrompt = null;

    // Tangkap event beforeinstallprompt (Chrome/Android)
    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;
    });

    // Deteksi iOS
    var isIos = /iphone|ipad|ipod/i.test(navigator.userAgent) && !window.MSStream;
    if (isIos && iosHint) {
        iosHint.style.display = 'block';
        installBtn.style.display = 'none';
    }

    function showPrompt() {
        prompt.style.pointerEvents = 'all';
        // Slide in dari kanan
        requestAnimationFrame(function () {
            prompt.style.transform = 'translateX(0)';
            prompt.style.opacity   = '1';
        });
    }

    function hidePrompt(onDone) {
        // Slide keluar ke kanan
        prompt.style.transform = 'translateX(calc(100% + 32px))';
        prompt.style.opacity   = '0';
        prompt.style.pointerEvents = 'none';
        setTimeout(function () { if (onDone) onDone(); }, 450);
    }

    // Tampil setelah 3 detik
    setTimeout(showPrompt, 3000);

    // Tutup dengan animasi: keluar kanan → masuk lagi → masuk kiri (dismiss)
    closeBtn.addEventListener('click', function () {
        // Slide keluar ke kanan
        hidePrompt(function () {
            // Simpan dismiss
            localStorage.setItem(STORAGE_KEY, Date.now().toString());
            // Jangan tampilkan lagi dalam session ini
        });
    });

    // Tombol install
    installBtn.addEventListener('click', function () {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function (result) {
                deferredPrompt = null;
                if (result.outcome === 'accepted') {
                    hidePrompt();
                    localStorage.setItem(STORAGE_KEY, Date.now().toString());
                }
            });
        } else if (!isIos) {
            // Browser tidak support — tutup saja
            hidePrompt();
            localStorage.setItem(STORAGE_KEY, Date.now().toString());
        }
    });

    // Register service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(function () {});
    }
})();
</script>
