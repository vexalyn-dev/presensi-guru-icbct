<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dev Panel · ICB CT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@1.7.0/dist/umd/lucide.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root{
            /* ── Core surface tokens ───────────────────────────── */
            --bg-void:      #05070c;
            --bg-base:      #0a0e17;
            --bg-elevated:  #0d1220;
            --surface:      #10162699;
            --surface-solid:#111827;
            --glass-border: rgba(255,255,255,.07);
            --glass-hi:     rgba(255,255,255,.035);

            /* ── Accents ───────────────────────────────────────── */
            --accent:        #6d5ef6;
            --accent-2:      #a78bfa;
            --accent-cyan:   #22d3ee;
            --accent-emerald:#34d399;
            --accent-amber:  #f5a524;
            --accent-rose:   #fb7185;

            /* ── Text ──────────────────────────────────────────── */
            --text-1: #eef1f8;
            --text-2: #8c94aa;
            --text-3: #4e5568;

            --radius-lg: 18px;
            --radius-md: 12px;
            --radius-sm: 8px;

            --ease-out: cubic-bezier(.16,1,.3,1);
        }

        *{ box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-base);
            color: var(--text-1);
            -webkit-font-smoothing: antialiased;
            position: relative;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, .font-display {
            font-family: 'Space Grotesk', 'Inter', sans-serif;
            letter-spacing: -0.01em;
        }

        .mono { font-family: 'JetBrains Mono', ui-monospace, monospace; letter-spacing: -0.02em; }

        ::selection { background: var(--accent); color: #fff; }

        /* ── Ambient background: layered glow + faint schematic grid ── */
        .bg-scene{
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
            background: var(--bg-void);
        }
        .bg-scene::before{
            content:'';
            position:absolute; inset:0;
            background-image:
                linear-gradient(to right, rgba(255,255,255,.028) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255,255,255,.028) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: radial-gradient(ellipse 80% 60% at 30% 0%, #000 40%, transparent 90%);
        }
        .glow-blob{
            position:absolute;
            border-radius:50%;
            filter: blur(90px);
            opacity:.35;
            will-change: transform;
        }
        .glow-blob.g1{ width:520px; height:520px; top:-160px; left:-120px; background: radial-gradient(circle, var(--accent), transparent 70%); }
        .glow-blob.g2{ width:460px; height:460px; top:10%; right:-160px; background: radial-gradient(circle, var(--accent-cyan), transparent 70%); opacity:.18; }
        .glow-blob.g3{ width:400px; height:400px; bottom:-140px; left:30%; background: radial-gradient(circle, var(--accent-2), transparent 70%); opacity:.16; }

        /* ── Glass surfaces ───────────────────────────────────── */
        .saas-card {
            position: relative;
            background: linear-gradient(180deg, var(--glass-hi), transparent 40%), var(--surface);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            box-shadow: 0 1px 0 rgba(255,255,255,.04) inset, 0 20px 40px -24px rgba(0,0,0,.6);
            transition: transform .45s var(--ease-out), box-shadow .45s var(--ease-out), border-color .3s ease;
            transform-style: preserve-3d;
            will-change: transform;
        }
        .saas-card:hover{
            border-color: rgba(255,255,255,.14);
            box-shadow: 0 1px 0 rgba(255,255,255,.06) inset, 0 30px 60px -20px rgba(0,0,0,.75);
        }
        /* stat cards / quick-action cards get the tilt treatment via JS (.tilt-card) */
        .tilt-glow{
            position:absolute; inset:-1px;
            border-radius: inherit;
            background: radial-gradient(320px circle at var(--mx,50%) var(--my,50%), rgba(109,94,246,.14), transparent 60%);
            opacity:0;
            transition: opacity .35s ease;
            pointer-events:none;
        }
        .tilt-card:hover .tilt-glow{ opacity:1; }

        /* ── Inputs ────────────────────────────────────────────── */
        .saas-input {
            width: 100%;
            background-color: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.09);
            color: var(--text-1);
            font-size: 14px;
            border-radius: var(--radius-sm);
            padding: 10px 13px;
            transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
        }
        .saas-input::placeholder{ color: var(--text-3); }
        .saas-input:focus {
            outline: none;
            background-color: rgba(255,255,255,.045);
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(109,94,246,.15);
        }

        /* ── Buttons ───────────────────────────────────────────── */
        .saas-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(180deg, #7c6df8, var(--accent));
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: var(--radius-sm);
            border: 1px solid rgba(255,255,255,.12);
            box-shadow: 0 1px 0 rgba(255,255,255,.25) inset, 0 10px 24px -10px rgba(109,94,246,.65);
            transition: transform .18s var(--ease-out), box-shadow .25s ease, filter .2s ease;
            cursor: pointer;
        }
        .saas-btn:hover { filter: brightness(1.08); transform: translateY(-1px); }
        .saas-btn:active { transform: translateY(0px) scale(.98); box-shadow: 0 1px 0 rgba(255,255,255,.15) inset, 0 4px 12px -6px rgba(109,94,246,.6); }
        .saas-btn-secondary {
            background: rgba(255,255,255,.04);
            color: var(--text-1);
            border-color: rgba(255,255,255,.1);
            box-shadow: none;
        }
        .saas-btn-secondary:hover { background: rgba(255,255,255,.07); }

        /* ── Sidebar ───────────────────────────────────────────── */
        .sidebar-shell{
            position: fixed;
            top: 16px; bottom: 16px; left: 16px;
            width: 256px;
            z-index: 30;
        }
        .sidebar-item {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 12px;
            margin: 3px 12px;
            border-radius: var(--radius-sm);
            color: var(--text-2);
            font-size: 13.5px;
            font-weight: 500;
            transition: color .18s ease, background-color .18s ease, transform .18s ease;
            cursor: pointer;
        }
        .sidebar-item svg{ width:16px; height:16px; transition: transform .2s ease; }
        .sidebar-item:hover { background-color: rgba(255,255,255,.045); color: var(--text-1); transform: translateX(2px); }
        .sidebar-item.active {
            background: linear-gradient(90deg, rgba(109,94,246,.16), rgba(109,94,246,.02));
            color: #fff;
            font-weight: 600;
        }
        .sidebar-item.active::before{
            content:'';
            position:absolute; left:-12px; top:50%; transform: translateY(-50%);
            width:3px; height:60%;
            background: linear-gradient(180deg, var(--accent-2), var(--accent));
            border-radius: 4px;
        }
        .sidebar-item.active svg{ color: var(--accent-2); }

        /* ── Live pulse (signature element) ───────────────────── */
        .pulse-wrap{ display:flex; align-items:center; gap:8px; }
        .pulse-dot{
            width:7px; height:7px; border-radius:50%;
            background: var(--accent-emerald);
            box-shadow: 0 0 0 0 rgba(52,211,153,.6);
            animation: pulseDot 2.2s ease-out infinite;
        }
        @keyframes pulseDot{
            0%   { box-shadow: 0 0 0 0 rgba(52,211,153,.55); }
            70%  { box-shadow: 0 0 0 8px rgba(52,211,153,0); }
            100% { box-shadow: 0 0 0 0 rgba(52,211,153,0); }
        }
        .pulse-line{ width:64px; height:20px; overflow:visible; opacity:.85; }
        .pulse-line path{
            stroke: var(--accent-emerald);
            stroke-width: 1.6;
            fill: none;
            stroke-dasharray: 120;
            stroke-dashoffset: 120;
            animation: pulseDraw 2.6s linear infinite;
        }
        @keyframes pulseDraw{
            0%   { stroke-dashoffset: 120; opacity:.2; }
            50%  { stroke-dashoffset: 0;   opacity:1; }
            100% { stroke-dashoffset: -120; opacity:.2; }
        }

        /* ── Tab transitions ───────────────────────────────────── */
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* ── Alerts ────────────────────────────────────────────── */
        .alert-box{
            animation: slideDown .4s var(--ease-out);
        }
        @keyframes slideDown{ from{ opacity:0; transform: translateY(-8px);} to{ opacity:1; transform: translateY(0);} }

        /* ── Misc ──────────────────────────────────────────────── */
        ::-webkit-scrollbar{ width:10px; height:10px; }
        ::-webkit-scrollbar-track{ background: transparent; }
        ::-webkit-scrollbar-thumb{ background: rgba(255,255,255,.09); border-radius: 8px; border: 2px solid var(--bg-base); }
        ::-webkit-scrollbar-thumb:hover{ background: rgba(255,255,255,.16); }

        @media (prefers-reduced-motion: reduce){
            *{ animation-duration: .001ms !important; animation-iteration-count: 1 !important; transition-duration: .001ms !important; }
        }
    </style>
</head>
<body class="antialiased flex min-h-screen">

    {{-- AMBIENT BACKGROUND --}}
    <div class="bg-scene">
        <div class="glow-blob g1" data-parallax="18"></div>
        <div class="glow-blob g2" data-parallax="12"></div>
        <div class="glow-blob g3" data-parallax="9"></div>
    </div>

    {{-- SIDEBAR --}}
    <aside class="sidebar-shell saas-card flex flex-col" style="opacity:0;" data-anim="sidebar">
        {{-- Header / Logo --}}
        <div class="h-16 flex items-center px-5 border-b border-white/[.06]">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm" style="background: linear-gradient(135deg, var(--accent-2), var(--accent)); box-shadow: 0 6px 16px -6px rgba(109,94,246,.7);">
                    <i data-lucide="command" class="w-4 h-4 text-white"></i>
                </div>
                <span class="font-display font-semibold text-[15px]" style="color: var(--text-1);">Dev Panel</span>
            </div>
        </div>

        {{-- Nav Links --}}
        <div class="flex-1 overflow-y-auto py-4">
            <div class="px-5 mb-2 text-[10px] font-semibold uppercase tracking-wider" style="color: var(--text-3);">Overview</div>
            <a onclick="switchTab('dashboard')" id="nav-dashboard" class="sidebar-item active">
                <i data-lucide="layout-grid"></i> Dashboard
            </a>

            <div class="px-5 mt-6 mb-2 text-[10px] font-semibold uppercase tracking-wider" style="color: var(--text-3);">Management</div>
            <a onclick="switchTab('apk')" id="nav-apk" class="sidebar-item">
                <i data-lucide="smartphone"></i> APK Manager
            </a>
            <a onclick="switchTab('system')" id="nav-system" class="sidebar-item">
                <i data-lucide="shield-alert"></i> System State
            </a>
            <a onclick="switchTab('releases')" id="nav-releases" class="sidebar-item">
                <i data-lucide="git-merge"></i> Releases
            </a>

            <div class="px-5 mt-6 mb-2 text-[10px] font-semibold uppercase tracking-wider" style="color: var(--text-3);">Links</div>
            <a href="https://github.com/vexalyn-dev/presensi-guru-icbct" target="_blank" class="sidebar-item">
                <i data-lucide="github"></i> Repository
            </a>
            <a href="{{ url('/dashboard') }}" class="sidebar-item">
                <i data-lucide="external-link"></i> Main App
            </a>
        </div>

        {{-- Footer Profile --}}
        <div class="p-4 border-t border-white/[.06]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center overflow-hidden flex-shrink-0" style="border: 1px solid var(--glass-border); box-shadow: 0 6px 14px -6px rgba(0,0,0,.6);">
                    <img src="https://ui-avatars.com/api/?name=Vio+Atmajaya&background=6d5ef6&color=fff" alt="User" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate" style="color: var(--text-1);">Vio Atmajaya</p>
                    <p class="text-xs truncate mono" style="color: var(--text-3);">developer</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 flex flex-col min-h-screen" style="margin-left: 288px; position: relative; z-index: 1;">

        {{-- Top Header --}}
        <header class="saas-card flex items-center justify-between px-6 sticky top-4 z-20"
                style="margin: 16px 16px 0 0; height: 64px; opacity:0;" data-anim="header">
            <div class="flex items-center gap-3">
                <h1 class="text-[17px] font-display font-semibold" id="header-title" style="color: var(--text-1);">Dashboard</h1>
                <svg class="pulse-line" viewBox="0 0 64 20"><path d="M0 10 H18 L23 2 L28 18 L33 6 L38 14 L43 10 H64"/></svg>
            </div>

            <div class="flex items-center gap-5">
                <span class="text-xs mono hidden sm:inline" style="color: var(--text-3);" id="live-clock">--:--:--</span>
                <div class="pulse-wrap px-3 py-1.5 rounded-full text-xs font-medium"
                     style="background: rgba(52,211,153,.08); border: 1px solid rgba(52,211,153,.25); color: var(--accent-emerald);">
                    <span class="pulse-dot"></span> Production
                </div>
                <a href="{{ url()->previous() === url()->current() ? url('/') : url()->previous() }}"
                   class="transition-colors" style="color: var(--text-3);"
                   onmouseover="this.style.color='var(--text-1)'" onmouseout="this.style.color='var(--text-3)'">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </a>
            </div>
        </header>

        {{-- Scrollable Page Content --}}
        <div class="flex-1 p-6" style="padding-top: 24px;">
            {{-- Alerts --}}
            @if(session('success') || session('error'))
                <div class="mb-6 space-y-3">
                    @if(session('success'))
                        <div class="alert-box saas-card p-4 flex items-center gap-3 text-sm" style="border-color: rgba(52,211,153,.25); color: var(--accent-emerald);">
                            <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert-box saas-card p-4 flex items-center gap-3 text-sm" style="border-color: rgba(251,113,133,.25); color: var(--accent-rose);">
                            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        function initIcons() {
            if (window.lucide) lucide.createIcons();
        }

        const tabs = ['dashboard', 'apk', 'system', 'releases'];
        const titles = {
            'dashboard': 'Dashboard',
            'apk': 'APK Manager',
            'system': 'System State',
            'releases': 'Release History'
        };

        function animateTabIn(el){
            if (window.gsap) {
                gsap.fromTo(el, { opacity: 0, y: 10 }, { opacity: 1, y: 0, duration: .45, ease: 'power3.out' });
                gsap.utils.toArray(el.querySelectorAll('.saas-card')).forEach((card, i) => {
                    gsap.fromTo(card, { opacity: 0, y: 14 }, { opacity: 1, y: 0, duration: .5, delay: i * 0.05, ease: 'power3.out' });
                });
            }
        }

        function switchTab(tabId) {
            tabs.forEach(t => {
                const contentEl = document.getElementById('tab-' + t);
                if (contentEl) contentEl.classList.remove('active');
                const navEl = document.getElementById('nav-' + t);
                if (navEl) navEl.classList.remove('active');
            });

            const active = document.getElementById('tab-' + tabId);
            if (active) {
                active.classList.add('active');
                animateTabIn(active);
            }
            const nav = document.getElementById('nav-' + tabId);
            if (nav) nav.classList.add('active');

            const titleEl = document.getElementById('header-title');
            if (titleEl) titleEl.textContent = titles[tabId] || 'Dashboard';

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // ── Live clock (WIB) ─────────────────────────────────────
        function tickClock(){
            const el = document.getElementById('live-clock');
            if (!el) return;
            const now = new Date();
            const fmt = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
            el.textContent = fmt + ' WIB';
        }

        // ── Animated stat counters ───────────────────────────────
        function animateCounters(){
            document.querySelectorAll('[data-count]').forEach(el => {
                const target = parseInt(el.getAttribute('data-count'), 10) || 0;
                if (window.gsap) {
                    const obj = { val: 0 };
                    gsap.to(obj, {
                        val: target, duration: 1.1, ease: 'power2.out',
                        onUpdate: () => { el.textContent = Math.round(obj.val).toLocaleString('id-ID'); }
                    });
                } else {
                    el.textContent = target.toLocaleString('id-ID');
                }
            });
        }

        // ── Subtle 3D tilt on cards ──────────────────────────────
        function initTilt(){
            const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (prefersReduced) return;
            document.querySelectorAll('.tilt-card').forEach(card => {
                if (!card.querySelector('.tilt-glow')) {
                    const glow = document.createElement('div');
                    glow.className = 'tilt-glow';
                    card.appendChild(glow);
                }
                card.addEventListener('mousemove', (e) => {
                    const r = card.getBoundingClientRect();
                    const x = e.clientX - r.left, y = e.clientY - r.top;
                    const px = (x / r.width) - 0.5, py = (y / r.height) - 0.5;
                    card.style.transform = `perspective(900px) rotateX(${(-py * 5).toFixed(2)}deg) rotateY(${(px * 6).toFixed(2)}deg) translateY(-2px)`;
                    card.style.setProperty('--mx', `${(x / r.width) * 100}%`);
                    card.style.setProperty('--my', `${(y / r.height) * 100}%`);
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'perspective(900px) rotateX(0) rotateY(0) translateY(0)';
                });
            });
        }

        // ── Gentle parallax on ambient glows ─────────────────────
        function initParallax(){
            const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (prefersReduced) return;
            const blobs = document.querySelectorAll('[data-parallax]');
            window.addEventListener('mousemove', (e) => {
                const cx = e.clientX / window.innerWidth - 0.5;
                const cy = e.clientY / window.innerHeight - 0.5;
                blobs.forEach(b => {
                    const strength = parseFloat(b.getAttribute('data-parallax')) || 10;
                    b.style.transform = `translate(${cx * strength}px, ${cy * strength}px)`;
                });
            }, { passive: true });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initIcons();
            switchTab('dashboard');
            tickClock();
            setInterval(tickClock, 1000);
            initTilt();
            initParallax();
            animateCounters();

            // Entrance animation for shell chrome
            if (window.gsap) {
                gsap.timeline()
                    .fromTo('[data-anim="sidebar"]', { opacity: 0, x: -16 }, { opacity: 1, x: 0, duration: .55, ease: 'power3.out' })
                    .fromTo('[data-anim="header"]', { opacity: 0, y: -10 }, { opacity: 1, y: 0, duration: .5, ease: 'power3.out' }, '-=0.3')
                    .add(() => animateTabIn(document.getElementById('tab-dashboard')), '-=0.2');
            } else {
                document.querySelectorAll('[data-anim]').forEach(el => el.style.opacity = 1);
            }
        });
    </script>
    @yield('scripts')
</body>
</html>