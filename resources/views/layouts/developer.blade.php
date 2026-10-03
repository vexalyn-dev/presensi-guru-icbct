<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Dev Panel · ICB CT</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script src="https://unpkg.com/lucide@1.7.0/dist/umd/lucide.min.js" defer></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Geist+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script>
    /* Theme initial loader to prevent flash of unstyled theme */
    (function() {
        try {
            const saved = localStorage.getItem('dev_panel_theme') || 'obsidian';
            document.documentElement.setAttribute('data-theme', saved);
        } catch (e) {}
    })();
</script>
<style>
/* ═════════════════════════════════════════════════════════════
   THEME DEFINITIONS (5 MODERN DEVELOPER THEMES)
   ═════════════════════════════════════════════════════════════ */

/* ─── 1. Obsidian Console (Default) ─── */
:root,
body.dp[data-theme="obsidian"],
html[data-theme="obsidian"] body.dp {
  --bg-base: #090d16;
  --bg-surface: #0f172a;
  --bg-surface-elevated: #152037;
  --bg-surface-glass: rgba(15, 23, 42, 0.82);
  --bg-hover: #1e293b;
  
  --line-subtle: rgba(255, 255, 255, 0.08);
  --line-mid: rgba(255, 255, 255, 0.15);
  --line-bright: rgba(255, 255, 255, 0.25);

  --txt-head: #f8fafc;
  --txt-body: #cbd5e1;
  --txt-sub: #94a3b8;
  --txt-dim: #64748b;

  --accent: #6366f1;
  --accent-hover: #4f46e5;
  --accent-glow: rgba(99, 102, 241, 0.35);
  --accent-soft: rgba(99, 102, 241, 0.12);
  --accent-line: rgba(99, 102, 241, 0.35);

  --ok: #10b981;
  --ok-soft: rgba(16, 185, 129, 0.12);
  --ok-glow: rgba(16, 185, 129, 0.3);
  --ok-line: rgba(16, 185, 129, 0.35);

  --warn: #f59e0b;
  --warn-soft: rgba(245, 158, 11, 0.12);
  --warn-glow: rgba(245, 158, 11, 0.3);
  --warn-line: rgba(245, 158, 11, 0.35);

  --bad: #f43f5e;
  --bad-soft: rgba(244, 63, 94, 0.12);
  --bad-glow: rgba(244, 63, 94, 0.3);
  --bad-line: rgba(244, 63, 94, 0.35);

  --sky: #06b6d4;
  --sky-soft: rgba(6, 182, 212, 0.12);
  --sky-glow: rgba(6, 182, 212, 0.3);
  --sky-line: rgba(6, 182, 212, 0.35);
}

/* ─── 2. Tokyo Night ─── */
body.dp[data-theme="tokyo-night"],
html[data-theme="tokyo-night"] body.dp {
  --bg-base: #1a1b26;
  --bg-surface: #16161e;
  --bg-surface-elevated: #1f2335;
  --bg-surface-glass: rgba(22, 22, 30, 0.85);
  --bg-hover: #292e42;
  
  --line-subtle: rgba(255, 255, 255, 0.08);
  --line-mid: rgba(122, 162, 247, 0.25);
  --line-bright: rgba(122, 162, 247, 0.4);

  --txt-head: #c0caf5;
  --txt-body: #a9b1d6;
  --txt-sub: #7aa2f7;
  --txt-dim: #565f89;

  --accent: #7aa2f7;
  --accent-hover: #5b87e5;
  --accent-glow: rgba(122, 162, 247, 0.4);
  --accent-soft: rgba(122, 162, 247, 0.15);
  --accent-line: rgba(122, 162, 247, 0.4);

  --ok: #73daca;
  --ok-soft: rgba(115, 218, 202, 0.15);
  --ok-glow: rgba(115, 218, 202, 0.35);
  --ok-line: rgba(115, 218, 202, 0.35);

  --warn: #e0af68;
  --warn-soft: rgba(224, 175, 104, 0.15);
  --warn-glow: rgba(224, 175, 104, 0.35);
  --warn-line: rgba(224, 175, 104, 0.35);

  --bad: #f7768e;
  --bad-soft: rgba(247, 118, 142, 0.15);
  --bad-glow: rgba(247, 118, 142, 0.35);
  --bad-line: rgba(247, 118, 142, 0.35);

  --sky: #7dcfff;
  --sky-soft: rgba(125, 207, 255, 0.15);
  --sky-glow: rgba(125, 207, 255, 0.35);
  --sky-line: rgba(125, 207, 255, 0.35);
}

/* ─── 3. Cappuccino Mocha (Warm Roasted Coffee & Caramel) ─── */
body.dp[data-theme="cappuccino"],
body.dp[data-theme="catppuccin"],
html[data-theme="cappuccino"] body.dp,
html[data-theme="catppuccin"] body.dp {
  --bg-base: #140d0a;
  --bg-surface: #1e140f;
  --bg-surface-elevated: #2a1c15;
  --bg-surface-glass: rgba(30, 20, 15, 0.88);
  --bg-hover: #36241b;
  
  --line-subtle: rgba(226, 198, 170, 0.12);
  --line-mid: rgba(212, 163, 115, 0.28);
  --line-bright: rgba(212, 163, 115, 0.45);

  --txt-head: #faf3eb;
  --txt-body: #e6ccb2;
  --txt-sub: #b08968;
  --txt-dim: #7f5539;

  --accent: #d4a373;
  --accent-hover: #c58f5d;
  --accent-glow: rgba(212, 163, 115, 0.4);
  --accent-soft: rgba(212, 163, 115, 0.16);
  --accent-line: rgba(212, 163, 115, 0.42);

  --ok: #52b788;
  --ok-soft: rgba(82, 183, 136, 0.15);
  --ok-glow: rgba(82, 183, 136, 0.35);
  --ok-line: rgba(82, 183, 136, 0.35);

  --warn: #e9c46a;
  --warn-soft: rgba(233, 196, 106, 0.15);
  --warn-glow: rgba(233, 196, 106, 0.35);
  --warn-line: rgba(233, 196, 106, 0.35);

  --bad: #e76f51;
  --bad-soft: rgba(231, 111, 81, 0.15);
  --bad-glow: rgba(231, 111, 81, 0.35);
  --bad-line: rgba(231, 111, 81, 0.35);

  --sky: #dd9754;
  --sky-soft: rgba(221, 151, 84, 0.15);
  --sky-glow: rgba(221, 151, 84, 0.35);
  --sky-line: rgba(221, 151, 84, 0.35);
}

/* ─── 4. Nordic Aurora ─── */
body.dp[data-theme="nordic"],
html[data-theme="nordic"] body.dp {
  --bg-base: #0e141d;
  --bg-surface: #17202d;
  --bg-surface-elevated: #1f2b3c;
  --bg-surface-glass: rgba(23, 32, 45, 0.85);
  --bg-hover: #2e3b4e;
  
  --line-subtle: rgba(255, 255, 255, 0.08);
  --line-mid: rgba(136, 192, 208, 0.22);
  --line-bright: rgba(136, 192, 208, 0.35);

  --txt-head: #eceff4;
  --txt-body: #d8dee9;
  --txt-sub: #9aa8bd;
  --txt-dim: #63758e;

  --accent: #88c0d0;
  --accent-hover: #81a1c1;
  --accent-glow: rgba(136, 192, 208, 0.4);
  --accent-soft: rgba(136, 192, 208, 0.15);
  --accent-line: rgba(136, 192, 208, 0.4);

  --ok: #a3be8c;
  --ok-soft: rgba(163, 190, 140, 0.15);
  --ok-glow: rgba(163, 190, 140, 0.35);
  --ok-line: rgba(163, 190, 140, 0.35);

  --warn: #ebcb8b;
  --warn-soft: rgba(235, 203, 139, 0.15);
  --warn-glow: rgba(235, 203, 139, 0.35);
  --warn-line: rgba(235, 203, 139, 0.35);

  --bad: #bf616a;
  --bad-soft: rgba(191, 97, 106, 0.15);
  --bad-glow: rgba(191, 97, 106, 0.35);
  --bad-line: rgba(191, 97, 106, 0.35);

  --sky: #8fbcbb;
  --sky-soft: rgba(143, 188, 187, 0.15);
  --sky-glow: rgba(143, 188, 187, 0.35);
  --sky-line: rgba(143, 188, 187, 0.35);
}

/* ─── 5. Cyberpunk Matrix ─── */
body.dp[data-theme="cyberpunk"],
html[data-theme="cyberpunk"] body.dp {
  --bg-base: #05080e;
  --bg-surface: #0a111a;
  --bg-surface-elevated: #101c2b;
  --bg-surface-glass: rgba(10, 17, 26, 0.85);
  --bg-hover: #16263a;
  
  --line-subtle: rgba(255, 255, 255, 0.08);
  --line-mid: rgba(16, 185, 129, 0.25);
  --line-bright: rgba(16, 185, 129, 0.45);

  --txt-head: #f0fdf4;
  --txt-body: #bbf7d0;
  --txt-sub: #6ee7b7;
  --txt-dim: #3b5771;

  --accent: #10b981;
  --accent-hover: #059669;
  --accent-glow: rgba(16, 185, 129, 0.45);
  --accent-soft: rgba(16, 185, 129, 0.15);
  --accent-line: rgba(16, 185, 129, 0.45);

  --ok: #10b981;
  --ok-soft: rgba(16, 185, 129, 0.15);
  --ok-glow: rgba(16, 185, 129, 0.45);
  --ok-line: rgba(16, 185, 129, 0.45);

  --warn: #fbbf24;
  --warn-soft: rgba(251, 191, 36, 0.15);
  --warn-glow: rgba(251, 191, 36, 0.4);
  --warn-line: rgba(251, 191, 36, 0.4);

  --bad: #f43f5e;
  --bad-soft: rgba(244, 63, 94, 0.15);
  --bad-glow: rgba(244, 63, 94, 0.4);
  --bad-line: rgba(244, 63, 94, 0.4);

  --sky: #06b6d4;
  --sky-soft: rgba(6, 182, 212, 0.15);
  --sky-glow: rgba(6, 182, 212, 0.4);
  --sky-line: rgba(6, 182, 212, 0.4);
}

/* ─── 6. Alabaster Light (Clean, Crisp & Modern Light Mode) ─── */
body.dp[data-theme="light"],
html[data-theme="light"] body.dp {
  --bg-base: #f8fafc;
  --bg-surface: #ffffff;
  --bg-surface-elevated: #f1f5f9;
  --bg-surface-glass: rgba(255, 255, 255, 0.88);
  --bg-hover: #e2e8f0;
  
  --line-subtle: rgba(15, 23, 42, 0.08);
  --line-mid: rgba(15, 23, 42, 0.16);
  --line-bright: rgba(99, 102, 241, 0.35);

  --txt-head: #0f172a;
  --txt-body: #334155;
  --txt-sub: #64748b;
  --txt-dim: #94a3b8;

  --accent: #4f46e5;
  --accent-hover: #4338ca;
  --accent-glow: rgba(79, 70, 229, 0.22);
  --accent-soft: rgba(79, 70, 229, 0.08);
  --accent-line: rgba(79, 70, 229, 0.25);

  --ok: #059669;
  --ok-soft: rgba(5, 150, 105, 0.08);
  --ok-glow: rgba(5, 150, 105, 0.2);
  --ok-line: rgba(5, 150, 105, 0.2);

  --warn: #d97706;
  --warn-soft: rgba(217, 119, 6, 0.08);
  --warn-glow: rgba(217, 119, 6, 0.2);
  --warn-line: rgba(217, 119, 6, 0.2);

  --bad: #e11d48;
  --bad-soft: rgba(225, 29, 72, 0.08);
  --bad-glow: rgba(225, 29, 72, 0.2);
  --bad-line: rgba(225, 29, 72, 0.2);

  --sky: #0284c7;
  --sky-soft: rgba(2, 132, 199, 0.08);
  --sky-glow: rgba(2, 132, 199, 0.2);
  --sky-line: rgba(2, 132, 199, 0.2);

  --sh-card: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 14px -2px rgba(15, 23, 42, 0.06);
  --sh-hover: 0 10px 25px -4px rgba(15, 23, 42, 0.09), 0 0 18px rgba(79, 70, 229, 0.12);
  --sh-modal: 0 20px 45px -10px rgba(15, 23, 42, 0.18), 0 0 25px rgba(79, 70, 229, 0.1);
}

/* ─── Ultra-Smooth Luxury Theme Transitions ─── */
::view-transition-old(root),
::view-transition-new(root) {
  animation: none;
  mix-blend-mode: normal;
}
::view-transition-old(root) {
  z-index: 1;
}
::view-transition-new(root) {
  z-index: 999999;
}

/* Fallback smooth transition */
html.theme-morphing,
html.theme-morphing body.dp,
html.theme-morphing body.dp *,
html.theme-morphing body.dp *::before,
html.theme-morphing body.dp *::after {
  transition: 
    background-color 0.48s cubic-bezier(0.22, 1, 0.36, 1),
    border-color 0.48s cubic-bezier(0.22, 1, 0.36, 1),
    color 0.48s cubic-bezier(0.22, 1, 0.36, 1),
    box-shadow 0.48s cubic-bezier(0.22, 1, 0.36, 1) !important;
}

/* ═════════════════════════════════════════════════════════════
   BASE STYLES & LAYOUT SYSTEM (FULL-WIDTH MODERN SAAS CONSOLE)
   ═════════════════════════════════════════════════════════════ */
body.dp {
  --r-sm: 8px;
  --r-md: 12px;
  --r-lg: 16px;
  --sh-card: 0 4px 20px -2px rgba(0, 0, 0, 0.45), 0 1px 3px rgba(0, 0, 0, 0.3);
  --sh-hover: 0 12px 32px -8px rgba(0, 0, 0, 0.6), 0 0 24px -4px var(--accent-glow);
  --sh-modal: 0 25px 60px -15px rgba(0, 0, 0, 0.8), 0 0 30px var(--accent-glow);
  --ease: cubic-bezier(0.16, 1, 0.3, 1);

  /* Legacy mappings */
  --ink: var(--txt-head);
  --sub: var(--txt-body);
  --mute: var(--txt-sub);
  --faint: var(--txt-dim);
  --line: var(--line-subtle);
  --bg: var(--bg-base);
  --card: var(--bg-surface);
  --v: var(--accent);
  --v-d: var(--accent-hover);
  --v-s: var(--accent-soft);
  --v-m: var(--accent-line);
  --ok-s: var(--ok-soft);
  --warn-s: var(--warn-soft);
  --bad-s: var(--bad-soft);
  --sky-s: var(--sky-soft);
  --r: var(--r-md);
  --sh: var(--sh-card);
  --sh-lg: var(--sh-modal);

  font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
  color: var(--txt-body);
  background-color: var(--bg-base);
  background-image: 
    radial-gradient(ellipse 90% 600px at 50% -120px, var(--accent-soft), transparent),
    radial-gradient(ellipse 60% 500px at 100% 0, var(--sky-soft), transparent);
  background-attachment: fixed;
  margin: 0;
  min-height: 100vh;
  display: flex;
  overflow-x: hidden;
  -webkit-font-smoothing: antialiased;
  font-size: 14px;
  line-height: 1.55;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.dp *, .dp *::before, .dp *::after { box-sizing: border-box; }
.dp h1, .dp h2, .dp h3, .dp h4, .dp p { margin: 0; }
.dp h1 { font-size: 1.5rem; font-weight: 700; letter-spacing: -0.025em; color: var(--txt-head); }
.dp h2 { font-size: 1.35rem; font-weight: 700; letter-spacing: -0.025em; color: var(--txt-head); }
.dp h3 { font-size: 0.95rem; font-weight: 650; letter-spacing: -0.01em; color: var(--txt-head); }
.dp [hidden] { display: none !important; }
.dp :focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
.dp ::selection { background: var(--accent); color: #fff; }

.mono { font-family: 'Geist Mono', ui-monospace, SFMono-Regular, monospace; }
.mute { color: var(--txt-sub); font-size: 0.875rem; }
.pre { white-space: pre-line; }
.trunc { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.sr { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); }
.row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.end { display: flex; justify-content: flex-end; gap: 10px; align-items: center; }
.stack { display: flex; flex-direction: column; gap: 20px; }
.page-head { margin-bottom: 24px; }
.page-head h2 { font-size: 1.45rem; font-weight: 800; color: #fff; letter-spacing: -0.025em; display: flex; align-items: center; }
.page-head p { color: var(--txt-sub); margin-top: 5px; font-size: 0.9rem; }

/* ─── Sidebar (Refined Console Navigation) ─── */
.side {
  position: fixed;
  inset: 0 auto 0 0;
  width: 260px;
  z-index: 40;
  display: flex;
  flex-direction: column;
  background: var(--bg-surface-glass);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-right: 1px solid var(--line-subtle);
  transition: transform 0.35s var(--ease), background 0.3s;
}
.side-head {
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  border-bottom: 1px solid var(--line-subtle);
}
.brand { display: flex; align-items: center; gap: 12px; }
.logo {
  width: 36px;
  height: 36px;
  border-radius: 9px;
  display: grid;
  place-items: center;
  color: #fff;
  background: linear-gradient(135deg, var(--accent), var(--accent-hover));
  box-shadow: 0 0 16px var(--accent-glow);
  border: 1px solid rgba(255, 255, 255, 0.2);
  flex-shrink: 0;
}
.brand b {
  display: block;
  font-size: 0.95rem;
  font-weight: 750;
  letter-spacing: -0.015em;
  color: var(--txt-head);
}
.brand small {
  display: block;
  font-size: 0.72rem;
  color: var(--txt-dim);
  font-family: 'Geist Mono', monospace;
  margin-top: -1px;
}
.nav {
  flex: 1;
  overflow-y: auto;
  padding: 16px 12px;
  display: flex;
  flex-direction: column;
  gap: 3px;
}
.nav-label {
  padding: 16px 10px 6px;
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--txt-dim);
}
.nav-item {
  position: relative;
  display: flex;
  align-items: center;
  gap: 11px;
  width: 100%;
  padding: 9px 12px;
  font: inherit;
  font-size: 0.88rem;
  font-weight: 550;
  color: var(--txt-sub);
  background: none;
  border: 1px solid transparent;
  border-radius: 8px;
  cursor: pointer;
  text-decoration: none;
  text-align: left;
  transition: all 0.2s var(--ease);
}
.nav-item svg {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
  opacity: 0.75;
  transition: all 0.2s var(--ease);
}
.nav-item:hover {
  background: rgba(255, 255, 255, 0.05);
  color: var(--txt-head);
  transform: translateX(2px);
}
.nav-item:hover svg {
  opacity: 1;
  color: var(--accent);
}
.nav-item.active {
  background: var(--accent-soft);
  color: #fff;
  font-weight: 600;
  border-color: var(--accent-line);
}
.nav-item.active svg {
  opacity: 1;
  color: var(--accent);
}
.nav-item.active::before {
  content: "";
  position: absolute;
  left: 0;
  top: 8px;
  bottom: 8px;
  width: 3px;
  border-radius: 0 4px 4px 0;
  background: var(--accent);
  box-shadow: 0 0 10px var(--accent);
}
.side-foot {
  padding: 14px;
  border-top: 1px solid var(--line-subtle);
  background: rgba(0, 0, 0, 0.15);
}
.me {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 10px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid var(--line-subtle);
  transition: border-color 0.2s;
}
.me:hover {
  border-color: var(--line-mid);
}
.me img {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  flex-shrink: 0;
  border: 1px solid rgba(255, 255, 255, 0.15);
}
.me .n {
  font-weight: 650;
  font-size: 0.84rem;
  color: var(--txt-head);
}
.me .r {
  font-size: 0.72rem;
  color: var(--txt-dim);
  font-family: 'Geist Mono', monospace;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

/* ─── Main Content Shell (Full Width) ─── */
.main {
  flex: 1;
  min-width: 0;
  margin-left: 260px;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}
.top {
  position: sticky;
  top: 0;
  z-index: 30;
  height: 64px;
  padding: 0 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  background: var(--bg-surface-glass);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-bottom: 1px solid var(--line-subtle);
  transition: background 0.3s;
}
.top h1 {
  font-size: 1.05rem;
  font-weight: 700;
  letter-spacing: -0.015em;
  color: var(--txt-head);
  display: flex;
  align-items: center;
  gap: 8px;
}
.top-l, .top-r {
  display: flex;
  align-items: center;
  gap: 12px;
}
.clock {
  font-size: 0.76rem;
  font-weight: 600;
  color: var(--txt-sub);
  padding: 6px 14px;
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid var(--line-subtle);
  border-radius: 6px;
  letter-spacing: 0.02em;
}
.env {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 0.75rem;
  font-weight: 600;
  font-family: 'Geist Mono', monospace;
  padding: 5px 12px;
  border-radius: 6px;
  background: var(--ok-soft);
  color: #34d399;
  border: 1px solid var(--ok-line);
  box-shadow: 0 0 12px rgba(16, 185, 129, 0.15);
}
.env i {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: currentColor;
  box-shadow: 0 0 8px currentColor;
  animation: pulseGlow 2s infinite;
}
.ibtn {
  width: 36px;
  height: 36px;
  display: inline-grid;
  place-items: center;
  color: var(--txt-sub);
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid var(--line-subtle);
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s var(--ease);
}
.ibtn:hover {
  color: #fff;
  border-color: var(--accent-line);
  background: var(--accent-soft);
  box-shadow: 0 0 14px var(--accent-glow);
  transform: translateY(-1px);
}
.burger, .side-x { display: none; }
.content {
  flex: 1;
  padding: 28px 36px 48px;
  max-width: 100% !important;
  width: 100% !important;
  margin: 0;
}
.flash {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 24px;
}
.scrim {
  position: fixed;
  inset: 0;
  z-index: 35;
  background: rgba(4, 7, 13, 0.7);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.25s;
}
.scrim.show {
  opacity: 1;
  pointer-events: auto;
}

/* ─── Tab Animation ─── */
.tab-content { display: none; }
.tab-content.active {
  display: block;
  animation: tabSlideUp 0.35s var(--ease) forwards;
}
@keyframes tabSlideUp {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ─── Cards & Surfaces ─── */
.card {
  background: var(--bg-surface);
  border: 1px solid var(--line-subtle);
  border-radius: var(--r-md);
  box-shadow: inset 0 1px 0 0 rgba(255, 255, 255, 0.05), var(--sh-card);
  transition: border-color 0.25s var(--ease), box-shadow 0.25s var(--ease), transform 0.25s var(--ease);
  position: relative;
}
.card:hover {
  border-color: var(--line-mid);
}
.card-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 16px 24px;
  border-bottom: 1px solid var(--line-subtle);
  background: rgba(255, 255, 255, 0.015);
}
.card-head h3 {
  font-size: 0.98rem;
  font-weight: 700;
  letter-spacing: -0.01em;
  color: var(--txt-head);
  display: flex;
  align-items: center;
  gap: 8px;
}
.pad { padding: 24px; }
.bb { border-bottom: 1px solid var(--line-subtle); }
.foot {
  background: rgba(0, 0, 0, 0.15);
  border-top: 1px solid var(--line-subtle);
  border-radius: 0 0 var(--r-md) var(--r-md);
}

/* ─── Glowing Icon Containers ─── */
.ico {
  width: 36px;
  height: 36px;
  border-radius: 9px;
  display: inline-grid;
  place-items: center;
  flex-shrink: 0;
  transition: all 0.2s var(--ease);
}
.ico-lg {
  width: 44px;
  height: 44px;
  border-radius: 12px;
}
.t-violet { background: var(--accent-soft); color: var(--accent); border: 1px solid var(--accent-line); }
.t-ok { background: var(--ok-soft); color: var(--ok); border: 1px solid var(--ok-line); }
.t-warn { background: var(--warn-soft); color: var(--warn); border: 1px solid var(--warn-line); }
.t-bad { background: var(--bad-soft); color: var(--bad); border: 1px solid var(--bad-line); }
.t-sky { background: var(--sky-soft); color: var(--sky); border: 1px solid var(--sky-line); }

.chip {
  display: inline-flex;
  align-items: center;
  font-size: 0.72rem;
  font-weight: 600;
  padding: 3px 9px;
  border-radius: 6px;
  background: var(--accent-soft);
  color: var(--accent);
  border: 1px solid var(--accent-line);
}
.badge {
  display: inline-flex;
  align-items: center;
  font-size: 0.7rem;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 6px;
}
.live {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--ok);
}
.live i {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: currentColor;
  box-shadow: 0 0 10px currentColor;
  animation: pulseGlow 2s infinite;
}
@keyframes pulseGlow {
  0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
  70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
  100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
.state {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin-left: 8px;
  font-size: 0.72rem;
  font-weight: 600;
  padding: 3px 9px;
  border-radius: 6px;
  background: var(--ok-soft);
  color: var(--ok);
  border: 1px solid var(--ok-line);
  vertical-align: middle;
}
.state i { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
.state.on {
  background: var(--warn-soft);
  color: var(--warn);
  border-color: var(--warn-line);
}

/* ─── Buttons & Inputs ─── */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font: inherit;
  font-weight: 600;
  font-size: 0.85rem;
  padding: 9px 18px;
  color: #fff;
  background: linear-gradient(135deg, var(--accent), var(--accent-hover));
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 8px;
  cursor: pointer;
  text-decoration: none;
  box-shadow: 0 2px 10px var(--accent-glow);
  transition: all 0.2s var(--ease);
}
.btn:hover {
  filter: brightness(1.1);
  box-shadow: 0 4px 18px var(--accent-glow);
  transform: translateY(-1px);
}
.btn:active { transform: translateY(1px); }

.btn-ghost {
  background: rgba(255, 255, 255, 0.05);
  color: var(--txt-body);
  border: 1px solid var(--line-subtle);
  box-shadow: none;
}
.btn-ghost:hover {
  background: rgba(255, 255, 255, 0.09);
  border-color: var(--line-mid);
  color: #fff;
  transform: translateY(-1px);
}
.btn-danger {
  background: linear-gradient(135deg, var(--bad), #be123c);
  border-color: rgba(255, 255, 255, 0.18);
  box-shadow: 0 2px 12px var(--bad-glow);
}
.btn-danger:hover {
  filter: brightness(1.1);
  box-shadow: 0 4px 18px var(--bad-glow);
}
.btn-white {
  background: #fff;
  color: #0f172a;
  border-color: #fff;
}
.btn-white:hover { background: #f1f5f9; }
.btn-block { width: 100%; }

.label {
  display: block;
  font-size: 0.8rem;
  font-weight: 600;
  margin-bottom: 7px;
  color: var(--txt-sub);
}
.input {
  width: 100%;
  font: inherit;
  color: var(--txt-head);
  background: var(--bg-surface-elevated);
  border: 1px solid var(--line-subtle);
  border-radius: 8px;
  padding: 10px 14px;
  transition: all 0.2s var(--ease);
}
.input::placeholder { color: var(--txt-dim); }
.input:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--accent-glow);
}
textarea.input { resize: vertical; min-height: 85px; }
.err { margin-top: 6px; font-size: 0.78rem; font-weight: 500; color: var(--bad); }
.form-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }

.check {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  font-size: 0.85rem;
  user-select: none;
  color: var(--txt-sub);
}
.check input { position: absolute; opacity: 0; }
.box {
  width: 18px;
  height: 18px;
  border: 1.5px solid var(--line-mid);
  border-radius: 5px;
  background: var(--bg-base);
  display: inline-grid;
  place-items: center;
  color: transparent;
  transition: all 0.15s;
}
.check input:checked + .box {
  background: var(--accent);
  border-color: var(--accent);
  color: #fff;
}
.check input:focus-visible + .box { outline: 2px solid var(--accent); outline-offset: 2px; }

/* Switch */
.switch { position: relative; flex-shrink: 0; cursor: pointer; }
.switch input[type=checkbox] { position: absolute; opacity: 0; inset: 0; width: 100%; height: 100%; cursor: pointer; }
.track {
  display: block;
  width: 46px;
  height: 26px;
  background: #1e293b;
  border: 1px solid var(--line-subtle);
  border-radius: 999px;
  transition: background 0.25s, border-color 0.25s;
}
.thumb {
  position: absolute;
  top: 4px;
  left: 4px;
  width: 18px;
  height: 18px;
  background: #fff;
  border-radius: 50%;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
  transition: transform 0.25s var(--ease);
}
.switch input:checked ~ .track {
  background: var(--accent);
  border-color: var(--accent-line);
  box-shadow: 0 0 14px var(--accent-glow);
}
.switch input:checked ~ .track .thumb { transform: translateX(20px); }

/* Segments */
.seg { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }
.seg-i { position: relative; cursor: pointer; }
.seg-i input { position: absolute; opacity: 0; inset: 0; cursor: pointer; }
.seg-b {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
  padding: 9px;
  font-weight: 600;
  font-size: 0.85rem;
  color: var(--txt-sub);
  background: var(--bg-surface-elevated);
  border: 1px solid var(--line-subtle);
  border-radius: 8px;
  transition: all 0.2s var(--ease);
}
.seg-i:hover .seg-b { background: var(--bg-hover); color: #fff; }
.seg-i input:checked + .seg-b {
  color: #fff;
  background: var(--accent-soft);
  border-color: var(--accent);
  box-shadow: 0 0 14px var(--accent-glow);
}

/* ─── Alerts ─── */
.alert {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 14px 18px;
  border-radius: var(--r-md);
  font-size: 0.86rem;
  line-height: 1.5;
  backdrop-filter: blur(10px);
}
.alert-ok { background: var(--ok-soft); color: var(--ok); border: 1px solid var(--ok-line); }
.alert-err { background: var(--bad-soft); color: var(--bad); border: 1px solid var(--bad-line); }
.alert-warn { background: var(--warn-soft); color: var(--warn); border: 1px solid var(--warn-line); margin-bottom: 22px; }
.alert code {
  font-family: 'Geist Mono', monospace;
  font-size: 0.84em;
  background: rgba(0, 0, 0, 0.35);
  padding: 2px 6px;
  border-radius: 4px;
  border: 1px solid var(--line-subtle);
}

/* ─── Dashboard Header ─── */
.dash-header { margin-bottom: 26px; }
.dash-header .greeting {
  font-size: 1.7rem;
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.2;
  color: var(--txt-head);
}
.dash-header .subtext {
  color: var(--txt-sub);
  font-size: 0.9rem;
  margin-top: 7px;
  line-height: 1.6;
}
.dash-header .subtext b {
  color: var(--txt-head);
  font-weight: 600;
  font-family: 'Geist Mono', monospace;
}
.dash-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
}
.dash-meta {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 14px;
  flex-wrap: wrap;
}
.dash-pill {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 0.75rem;
  font-weight: 600;
  font-family: 'Geist Mono', monospace;
  padding: 4px 12px;
  border-radius: 999px;
  background: var(--ok-soft);
  color: var(--ok);
  border: 1px solid var(--ok-line);
  box-shadow: 0 0 12px var(--ok-glow);
}
.dash-pill i {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: currentColor;
  box-shadow: 0 0 8px currentColor;
  animation: pulseGlow 2s infinite;
}
.dash-actions {
  display: flex;
  gap: 10px;
  align-items: center;
  flex-wrap: wrap;
  padding-top: 4px;
}

/* ─── Dashboard Stats (Full Width Grid) ─── */
.hero { display: none; }
.stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 18px;
  margin-bottom: 24px;
}
.stat {
  padding: 20px 24px;
  position: relative;
  overflow: hidden;
}
.stat::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, var(--accent), transparent);
  opacity: 0;
  transition: opacity 0.3s;
}
.stat:hover::after { opacity: 1; }
.stat:hover {
  transform: translateY(-2px);
  box-shadow: var(--sh-hover);
  border-color: var(--accent-line);
}
.stat-head {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 14px;
  color: var(--txt-sub);
  font-weight: 600;
  font-size: 0.82rem;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.stat-head .ico { width: 30px; height: 30px; border-radius: 8px; }
.num {
  font-size: 2.2rem;
  font-weight: 800;
  line-height: 1;
  letter-spacing: -0.035em;
  color: var(--txt-head);
}
.num-sm {
  font-size: 1.4rem;
  font-family: 'Geist Mono', monospace;
  font-weight: 650;
  color: var(--txt-head);
}
.grid-main {
  display: grid;
  grid-template-columns: 1.6fr 1fr;
  gap: 20px;
}

/* System Info Matrix */
.info { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
.info > div {
  padding: 16px 24px;
  border-bottom: 1px solid var(--line-subtle);
  transition: background 0.2s;
}
.info > div:hover { background: rgba(255, 255, 255, 0.02); }
.info > div:nth-child(odd) { border-right: 1px solid var(--line-subtle); }
.info > div:nth-last-child(-n+2) { border-bottom: 0; }
.info .l {
  font-size: 0.72rem;
  color: var(--txt-dim);
  font-weight: 600;
  margin-bottom: 4px;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-family: 'Geist Mono', monospace;
}
.info .v {
  font-weight: 600;
  font-size: 0.92rem;
  color: var(--txt-head);
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 0;
}
.dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.dot.ok { background: var(--ok); box-shadow: 0 0 8px var(--ok); }
.dot.warn { background: var(--warn); box-shadow: 0 0 8px var(--warn); }

/* Dashboard Grid Layout */
.grid-main {
  display: grid;
  grid-template-columns: 1.5fr 1fr;
  gap: 20px;
}
.left-col { display: flex; flex-direction: column; gap: 20px; }
.right-col { display: flex; flex-direction: column; }

/* Server Info Body */
.server-info-body { padding: 0 4px; }
.si-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 16px; padding: 10px 4px; }
.si-row.full { grid-template-columns: 1fr; }
.si-item { display: flex; flex-direction: column; gap: 3px; }
.si-item.full { grid-column: 1 / -1; }
.si-label {
  font-size: 0.65rem;
  color: var(--txt-dim);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-family: 'Geist Mono', monospace;
}
.si-val {
  font-weight: 600;
  font-size: 0.88rem;
  color: var(--txt-head);
  font-family: 'Geist Mono', monospace;
  word-break: break-all;
}

/* Disk Chart */
.disk-chart-wrap { margin: 8px 4px; }
.disk-chart {
  background: var(--bg-base);
  border: 1px solid var(--line-subtle);
  border-radius: 10px;
  padding: 10px;
  position: relative;
}
.disk-stats {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 6px;
  font-size: 0.82rem;
}
.disk-pct { font-weight: 700; }

/* Quick Actions Console */
.actions {
  padding: 10px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.action, .tool {
  display: flex;
  align-items: center;
  gap: 14px;
  width: 100%;
  padding: 12px 16px;
  font: inherit;
  color: var(--txt-head);
  text-align: left;
  text-decoration: none;
  cursor: pointer;
  background: rgba(255, 255, 255, 0.015);
  border: 1px solid transparent;
  border-radius: 10px;
  transition: all 0.2s var(--ease);
}
.action b, .tool b {
  display: block;
  font-size: 0.88rem;
  font-weight: 650;
  color: var(--txt-head);
}
.action small, .tool small {
  display: block;
  font-size: 0.75rem;
  color: var(--txt-sub);
  margin-top: 2px;
}
.action > div { flex: 1; min-width: 0; }
.go {
  color: var(--txt-dim);
  transition: transform 0.2s var(--ease), color 0.2s;
}
.action:hover, .tool:hover {
  background: var(--bg-surface-elevated);
  border-color: var(--accent-line);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35);
  transform: translateX(3px);
}
.action:hover .go {
  color: var(--accent);
  transform: translate(3px, -3px);
}

/* ─── APK Manager ─── */
.apk {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  flex-wrap: wrap;
  padding: 24px;
}
.apk-info { display: flex; align-items: center; gap: 16px; }
.apk h4 {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--txt-head);
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}
.apk-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  padding: 0 24px 20px;
}
.pill {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 0.78rem;
  font-weight: 500;
  padding: 5px 12px;
  background: rgba(255, 255, 255, 0.04);
  border-radius: 6px;
  color: var(--txt-sub);
  border: 1px solid var(--line-subtle);
}
.apk-form {
  display: grid;
  grid-template-columns: minmax(0, 1fr);
  gap: 24px;
}
.apk-drop { display: flex; flex-direction: column; }
.apk-drop .drop { flex: 1; min-height: 220px; }
.apk-fields { display: flex; flex-direction: column; justify-content: space-between; gap: 22px; }
.drop {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 10px;
  text-align: center;
  padding: 32px 22px;
  cursor: pointer;
  border: 1.5px dashed var(--accent-line);
  border-radius: var(--r-md);
  background: var(--accent-soft);
  transition: all 0.25s var(--ease);
}
.drop:hover, .drop.over {
  background: var(--accent-glow);
  border-color: var(--accent);
  box-shadow: 0 0 20px var(--accent-glow);
  transform: scale(1.005);
}
.drop.has-file {
  border-style: solid;
  border-color: var(--ok);
  background: var(--ok-soft);
  box-shadow: 0 0 20px var(--ok-glow);
}
@media(min-width: 1024px) {
  .apk-form { grid-template-columns: minmax(0, 5fr) minmax(0, 7fr); }
}

/* ─── Tools Matrix ─── */
.tools {
  display: grid;
  grid-template-columns: 1fr;
  gap: 14px;
  padding: 18px;
}
.tools .tool {
  border-color: var(--line-subtle);
  padding: 16px;
}
@media(min-width: 640px) { .tools { grid-template-columns: 1fr 1fr; } }
@media(min-width: 1280px) {
  .tools { grid-template-columns: repeat(4, minmax(0, 1fr)); }
  .tools .tool { flex-direction: column; align-items: flex-start; gap: 14px; }
}

/* ─── Releases Timeline ─── */
.rel-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 18px;
  flex-wrap: wrap;
  margin-bottom: 24px;
}
.rel-stats {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
  margin-bottom: 26px;
}
.mini {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px 18px;
}
.mini p {
  font-size: 0.78rem;
  color: var(--txt-sub);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.mini b {
  display: block;
  font-size: 1.4rem;
  font-weight: 750;
  color: var(--txt-head);
  letter-spacing: -0.02em;
  line-height: 1.2;
}
.mini b.mono { font-size: 1.15rem; }
.rel-grid {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 280px;
  gap: 24px;
}
.sticky { position: sticky; top: 88px; }
.fbtns {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 14px;
}
.fbtn {
  display: flex;
  align-items: center;
  gap: 10px;
  font: inherit;
  font-weight: 500;
  font-size: 0.85rem;
  color: var(--txt-sub);
  text-align: left;
  background: none;
  border: 1px solid transparent;
  border-radius: 8px;
  padding: 8px 12px;
  cursor: pointer;
  transition: all 0.2s var(--ease);
}
.fbtn small {
  margin-left: auto;
  min-width: 24px;
  text-align: center;
  padding: 2px 7px;
  font-family: 'Geist Mono', monospace;
  font-size: 0.74rem;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 6px;
  color: var(--txt-sub);
}
.fico {
  width: 26px;
  height: 26px;
  display: inline-grid;
  place-items: center;
  border-radius: 6px;
  flex-shrink: 0;
}
.fbtn:hover { background: rgba(255, 255, 255, 0.04); color: #fff; }
.fbtn.active {
  background: var(--accent-soft);
  color: #fff;
  border-color: var(--accent-line);
  font-weight: 600;
}
.fbtn.active small { background: var(--accent); color: #fff; }
.tl-group + .tl-group { margin-top: 28px; }
.tl-month {
  margin-bottom: 14px;
  font-size: 0.8rem;
  font-weight: 700;
  color: var(--txt-sub);
  text-transform: uppercase;
  letter-spacing: 0.08em;
  font-family: 'Geist Mono', monospace;
}
.tl-list {
  position: relative;
  list-style: none;
  margin: 0;
  padding: 0 0 0 48px;
}
.tl-list::before {
  content: "";
  position: absolute;
  left: 17px;
  top: 0;
  bottom: 0;
  border-left: 1px solid var(--line-subtle);
}
.tl-item { position: relative; margin-bottom: 16px; }
.tl-item:last-child { margin-bottom: 0; }
.tl-dot {
  position: absolute;
  left: -48px;
  top: 14px;
  width: 36px;
  height: 36px;
  border: 3px solid var(--bg-base);
  box-shadow: 0 0 14px rgba(0, 0, 0, 0.6);
}
.tl-card { padding: 18px 24px; }
.tl-card.latest {
  border-color: var(--accent-line);
  box-shadow: 0 0 24px -4px var(--accent-glow), var(--sh-card);
}
.tl-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 14px;
}
.tl-title {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  min-width: 0;
}
.tl-title h4 { font-size: 0.98rem; font-weight: 700; color: var(--txt-head); }
.tl-side {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}
.tl-side time {
  font-size: 0.76rem;
  color: var(--txt-dim);
  font-family: 'Geist Mono', monospace;
  white-space: nowrap;
}
.trash {
  border: 0;
  background: none;
  color: var(--txt-dim);
  border-radius: 6px;
  padding: 6px;
  cursor: pointer;
  transition: all 0.2s var(--ease);
}
.trash:hover { background: var(--bad-soft); color: var(--bad); }
.cl {
  list-style: none;
  margin: 12px 0 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.cl li {
  position: relative;
  padding-left: 18px;
  color: var(--txt-body);
  font-size: 0.88rem;
  line-height: 1.6;
}
.cl li::before {
  content: "";
  position: absolute;
  left: 2px;
  top: 0.65em;
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: var(--accent);
}
.empty {
  padding: 48px 24px;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}
.empty h4 { font-size: 1.05rem; font-weight: 700; color: #fff; }

/* ─── GitHub-Style Theme Settings Card & Mini Mockups ─── */
.theme-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
  gap: 20px;
}
.theme-card {
  background: var(--bg-surface-elevated);
  border: 1.5px solid var(--line-subtle);
  border-radius: var(--r-md);
  padding: 16px;
  cursor: pointer;
  transition: all 0.25s var(--ease);
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  gap: 14px;
  user-select: none;
}
.theme-card:hover {
  transform: translateY(-3px);
  border-color: var(--accent-line);
  box-shadow: 0 10px 28px -6px rgba(0, 0, 0, 0.5), 0 0 20px -4px var(--accent-glow);
}
.theme-card.selected {
  border-color: var(--accent);
  box-shadow: 0 0 0 2px var(--accent-glow), 0 12px 32px -6px rgba(0, 0, 0, 0.6);
  background: rgba(255, 255, 255, 0.03);
}

/* Mini UI Mockup Preview */
.theme-preview-box {
  width: 100%;
  height: 124px;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  position: relative;
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.4);
}
.theme-mini-side {
  width: 28%;
  height: 100%;
  border-right: 1px solid rgba(255, 255, 255, 0.08);
  padding: 10px 8px;
  display: flex;
  flex-direction: column;
  gap: 5px;
}
.theme-mini-side .mini-logo {
  width: 14px;
  height: 14px;
  border-radius: 3px;
  margin-bottom: 6px;
}
.theme-mini-side .mini-nav-line {
  height: 4px;
  border-radius: 2px;
  width: 70%;
  opacity: 0.4;
}
.theme-mini-side .mini-nav-line.active {
  width: 90%;
  opacity: 1;
}

.theme-mini-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  height: 100%;
}
.theme-mini-top {
  height: 22px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  padding: 0 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.theme-mini-top .mini-title-line {
  width: 38px;
  height: 4px;
  border-radius: 2px;
}
.theme-mini-top .mini-pill {
  width: 24px;
  height: 7px;
  border-radius: 4px;
}

.theme-mini-content {
  flex: 1;
  padding: 10px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.theme-mini-stats {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 5px;
}
.theme-mini-stat-card {
  height: 28px;
  border-radius: 5px;
  border: 1px solid rgba(255, 255, 255, 0.06);
  padding: 4px 6px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 3px;
}
.theme-mini-stat-line {
  height: 3px;
  width: 50%;
  border-radius: 1.5px;
  opacity: 0.5;
}
.theme-mini-stat-val {
  height: 6px;
  width: 75%;
  border-radius: 2px;
}

/* Theme Card Footer */
.theme-card-foot {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
}
.theme-card-info h4 {
  font-size: 0.95rem;
  font-weight: 750;
  color: var(--txt-head);
  display: flex;
  align-items: center;
  gap: 6px;
}
.theme-card-info p {
  font-size: 0.78rem;
  color: var(--txt-sub);
  margin-top: 3px;
  line-height: 1.45;
}
.theme-card-swatches {
  display: flex;
  gap: 5px;
  margin-top: 10px;
}
.theme-swatch {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.theme-check-badge {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  border: 1.5px solid var(--line-mid);
  display: grid;
  place-items: center;
  color: transparent;
  flex-shrink: 0;
  transition: all 0.2s var(--ease);
}
.theme-card.selected .theme-check-badge {
  background: var(--accent);
  border-color: var(--accent);
  color: #fff;
  box-shadow: 0 0 10px var(--accent-glow);
}

/* ─── Modal Dialogs ─── */
.overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  background: rgba(4, 7, 13, 0.78);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  opacity: 0;
  transition: opacity 0.25s var(--ease);
}
.overlay.open { opacity: 1; }
.modal {
  width: 100%;
  max-width: 30rem;
  max-height: 90vh;
  overflow: auto;
  background: var(--bg-surface);
  border: 1px solid var(--line-mid);
  border-radius: var(--r-lg);
  box-shadow: var(--sh-modal);
  transform: translateY(16px) scale(0.96);
  transition: transform 0.3s var(--ease);
}
.overlay.open .modal { transform: none; }
.modal-sm { max-width: 24rem; }
.modal-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 22px;
  border-bottom: 1px solid var(--line-subtle);
  background: rgba(255, 255, 255, 0.02);
}
.modal-top b { font-weight: 700; font-size: 0.95rem; color: var(--txt-head); }
.modal-body {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.modal-sm .modal-body { align-items: flex-start; }
.modal-body h2 { font-size: 1.4rem; font-weight: 800; letter-spacing: -0.025em; color: var(--txt-head); }
.modal-body h3 { font-size: 1.15rem; font-weight: 700; color: var(--txt-head); }
.tips {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.tips li {
  display: flex;
  gap: 14px;
  align-items: center;
  padding: 12px 14px;
  border: 1px solid var(--line-subtle);
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.02);
  transition: border-color 0.2s;
}
.tips li:hover { border-color: var(--line-mid); }
.tips strong { display: block; font-size: 0.88rem; color: var(--txt-head); }
.tips small { display: block; font-size: 0.78rem; color: var(--txt-sub); margin-top: 2px; }

/* ─── Scrollbar ─── */
::-webkit-scrollbar { width: 8px; height: 8px; }
::-webkit-scrollbar-track { background: var(--bg-base); }
::-webkit-scrollbar-thumb {
  background: #1e293b;
  border: 2px solid var(--bg-base);
  border-radius: 8px;
}
::-webkit-scrollbar-thumb:hover { background: #334155; }

/* ─── Responsive Media Queries ─── */
@media(max-width: 1200px) {
  .stats { grid-template-columns: repeat(2, 1fr); }
  .grid-main { grid-template-columns: 1fr; }
  .left-col { gap: 20px; }
  .rel-stats { grid-template-columns: repeat(2, 1fr); }
  .rel-grid { grid-template-columns: 1fr; }
}
@media(max-width: 1023px) {
  .side { transform: translateX(-101%); }
  .side.open { transform: none; box-shadow: 12px 0 40px rgba(0, 0, 0, 0.8); }
  .side-x, .burger { display: inline-grid; }
  .main { margin-left: 0; }
  .top { height: 60px; padding: 0 18px; }
  .content { padding: 20px 18px 36px; }
  .clock { display: none; }
}
@media(max-width: 640px) {
  .stats { grid-template-columns: 1fr; }
  .info { grid-template-columns: 1fr; }
  .info > div:nth-child(odd) { border-right: 0; }
  .info > div:nth-last-child(2) { border-bottom: 1px solid var(--line-subtle); }
  .tl-list { padding-left: 42px; }
  .tl-dot { left: -42px; }
  .tl-top { flex-direction: column; gap: 8px; }
  .dash-top { flex-direction: column; }
  .dash-actions { width: 100%; }
  .rel-stats { grid-template-columns: 1fr; }
}
@media(max-width: 480px) {
  .env span { display: none; }
  .env { padding: 4px 8px; }
}
@media(prefers-reduced-motion: reduce) {
  .dp *, .dp *::before, .dp *::after {
    animation: none !important;
    transition-duration: 0.01ms !important;
  }
}
</style>
</head>

<body class="dp antialiased">

{{-- ══ CYBER SPLASH SCREEN ══ --}}
<div id="dev-splash" style="
    position:fixed;inset:0;z-index:99999;
    background:#050912;
    display:flex;flex-direction:column;
    align-items:center;justify-content:center;
    font-family:'Geist Mono',ui-monospace,monospace;
    overflow:hidden;
    transition:opacity .6s cubic-bezier(.4,0,.2,1),transform .6s cubic-bezier(.4,0,.2,1);
">
    {{-- Animated grid background --}}
    <canvas id="splash-canvas" style="position:absolute;inset:0;width:100%;height:100%;opacity:.35"></canvas>

    {{-- Glow orbs --}}
    <div style="position:absolute;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(99,102,241,.18) 0%,transparent 70%);top:-100px;left:-100px;animation:splashOrb1 6s ease-in-out infinite alternate;pointer-events:none"></div>
    <div style="position:absolute;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(6,182,212,.12) 0%,transparent 70%);bottom:-100px;right:-100px;animation:splashOrb2 7s ease-in-out infinite alternate;pointer-events:none"></div>

    {{-- Logo --}}
    <div style="position:relative;z-index:1;text-align:center;margin-bottom:40px">
        <div style="width:72px;height:72px;margin:0 auto 16px;border-radius:18px;
            background:linear-gradient(135deg,#6366f1,#4f46e5);
            display:flex;align-items:center;justify-content:center;
            box-shadow:0 0 40px rgba(99,102,241,.5),0 0 80px rgba(99,102,241,.2);
            animation:splashLogoPulse 2s ease-in-out infinite">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/>
            </svg>
        </div>
        <p style="font-size:.7rem;font-weight:700;letter-spacing:.25em;color:#6366f1;text-transform:uppercase;margin:0">DEV PANEL</p>
        <p style="font-size:.62rem;letter-spacing:.15em;color:#334155;margin:4px 0 0;font-family:'Geist Mono',monospace">ICB CT · v2.0</p>
    </div>

    {{-- Terminal boot sequence --}}
    <div id="splash-terminal" style="
        position:relative;z-index:1;
        width:min(460px,92vw);
        background:rgba(9,13,22,.9);
        border:1px solid rgba(99,102,241,.3);
        border-radius:12px;
        padding:18px 20px;
        box-shadow:0 0 40px rgba(99,102,241,.15),inset 0 1px 0 rgba(255,255,255,.04);
    ">
        <div style="display:flex;align-items:center;gap:7px;margin-bottom:14px;padding-bottom:12px;border-bottom:1px solid rgba(255,255,255,.06)">
            <span style="width:10px;height:10px;border-radius:50%;background:#f43f5e"></span>
            <span style="width:10px;height:10px;border-radius:50%;background:#f59e0b"></span>
            <span style="width:10px;height:10px;border-radius:50%;background:#10b981"></span>
            <span style="margin-left:8px;font-size:.72rem;color:#475569;letter-spacing:.05em">dev-console ~ icb-ct</span>
        </div>
        <div id="splash-lines" style="font-size:.75rem;line-height:1.8;color:#94a3b8;min-height:120px"></div>
        <div style="display:flex;align-items:center;gap:6px;margin-top:8px">
            <span style="color:#6366f1;font-size:.75rem">❯</span>
            <span id="splash-cursor" style="width:8px;height:14px;background:#6366f1;display:inline-block;animation:splashBlink .9s step-end infinite;border-radius:1px"></span>
        </div>
    </div>

    {{-- Progress bar --}}
    <div style="position:relative;z-index:1;width:min(460px,92vw);margin-top:20px">
        <div style="height:2px;background:rgba(255,255,255,.05);border-radius:2px;overflow:hidden">
            <div id="splash-bar" style="height:100%;width:0%;background:linear-gradient(90deg,#6366f1,#06b6d4);border-radius:2px;transition:width .3s ease;box-shadow:0 0 8px #6366f1"></div>
        </div>
        <p id="splash-status" style="font-size:.65rem;color:#475569;margin:8px 0 0;text-align:right;letter-spacing:.05em">Memuat sistem...</p>
    </div>
</div>

<style>
@keyframes splashOrb1   { from{transform:translate(0,0) scale(1)} to{transform:translate(80px,60px) scale(1.2)} }
@keyframes splashOrb2   { from{transform:translate(0,0) scale(1)} to{transform:translate(-60px,-80px) scale(1.15)} }
@keyframes splashLogoPulse { 0%,100%{box-shadow:0 0 40px rgba(99,102,241,.5),0 0 80px rgba(99,102,241,.2)} 50%{box-shadow:0 0 60px rgba(99,102,241,.7),0 0 120px rgba(99,102,241,.3)} }
@keyframes splashBlink  { 0%,100%{opacity:1} 50%{opacity:0} }
</style>

<script>
(function() {
    var splash = document.getElementById('dev-splash');

    /* ── Cek apakah splash sudah pernah ditampilkan di sesi ini ── */
    var splashKey = 'dev_splash_shown';
    var alreadyShown = false;
    try { alreadyShown = !!sessionStorage.getItem(splashKey); } catch(e) {}

    if (alreadyShown) {
        /* Sudah login & splash sudah tampil — sembunyikan langsung tanpa animasi */
        if (splash) { splash.style.display = 'none'; }
        return;
    }

    /* ── Canvas grid animation ── */
    var c = document.getElementById('splash-canvas');
    if (c) {
        var ctx = c.getContext('2d');
        var W, H;
        function resize() {
            W = c.width  = window.innerWidth;
            H = c.height = window.innerHeight;
        }
        resize(); window.addEventListener('resize', resize);
        var t = 0;
        function drawGrid() {
            ctx.clearRect(0,0,W,H);
            ctx.strokeStyle = 'rgba(99,102,241,.15)';
            ctx.lineWidth = .5;
            for(var x=0;x<=W;x+=50){ctx.beginPath();ctx.moveTo(x,0);ctx.lineTo(x,H);ctx.stroke();}
            for(var y=0;y<=H;y+=50){ctx.beginPath();ctx.moveTo(0,y);ctx.lineTo(W,y);ctx.stroke();}
            /* Traveling pulse */
            var px = (t % W); var py = (t * .6) % H;
            ctx.fillStyle='rgba(99,102,241,.4)'; ctx.beginPath(); ctx.arc(px,py,2,0,Math.PI*2); ctx.fill();
            ctx.fillStyle='rgba(6,182,212,.3)'; ctx.beginPath(); ctx.arc(W-px, py, 1.5,0,Math.PI*2); ctx.fill();
            t+=1.2;
            requestAnimationFrame(drawGrid);
        }
        drawGrid();
    }

    /* ── Boot sequence ── */
    var lines = document.getElementById('splash-lines');
    var bar   = document.getElementById('splash-bar');
    var stat  = document.getElementById('splash-status');

    var bootLines = [
        { txt: '<span style="color:#6366f1">▶</span> Menginisialisasi Dev Panel...', pct: 8, delay: 0 },
        { txt: '<span style="color:#10b981">✓</span> Memverifikasi kredensial developer', pct: 18, delay: 520 },
        { txt: '<span style="color:#10b981">✓</span> Memeriksa izin akses & role', pct: 30, delay: 1050 },
        { txt: '<span style="color:#10b981">✓</span> Memuat konfigurasi sistem', pct: 42, delay: 1600 },
        { txt: '<span style="color:#10b981">✓</span> Koneksi database: <span style="color:#34d399">OK</span>', pct: 55, delay: 2150 },
        { txt: '<span style="color:#10b981">✓</span> Cache engine: <span style="color:#34d399">Aktif</span>', pct: 67, delay: 2680 },
        { txt: '<span style="color:#f59e0b">⟳</span> Memuat modul APK & iOS manager...', pct: 78, delay: 3100 },
        { txt: '<span style="color:#10b981">✓</span> Mengambil data sistem real-time', pct: 88, delay: 3550 },
        { txt: '<span style="color:#06b6d4">⟳</span> Kompilasi antarmuka konsol...', pct: 95, delay: 4000 },
        { txt: '<span style="color:#10b981">✓</span> Sistem siap — <span style="color:#818cf8">selamat datang, Developer</span>', pct: 100, delay: 4500 },
    ];

    bootLines.forEach(function(item) {
        setTimeout(function() {
            var p = document.createElement('div');
            p.innerHTML = item.txt;
            p.style.cssText = 'opacity:0;transform:translateY(5px);transition:opacity .35s ease,transform .35s ease';
            lines.appendChild(p);
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    p.style.opacity = '1'; p.style.transform = 'translateY(0)';
                });
            });
            if (bar) { bar.style.transition = 'width .5s cubic-bezier(.4,0,.2,1)'; bar.style.width = item.pct + '%'; }
            if (stat) stat.textContent = item.pct < 100 ? 'Memuat... ' + item.pct + '%' : 'Siap masuk ✓';
        }, item.delay);
    });

    /* ── Dismiss, reveal UI, lalu tandai splash sudah ditampilkan ── */
    setTimeout(function() {
        if (!splash) return;
        splash.style.transition = 'opacity .7s ease, transform .7s ease';
        splash.style.opacity = '0';
        splash.style.transform = 'scale(1.04)';
        setTimeout(function() {
            splash.style.display = 'none';
            /* Tandai sudah tampil — tidak akan muncul lagi sampai logout */
            try { sessionStorage.setItem(splashKey, '1'); } catch(e) {}
        }, 750);
    }, 5100);
})();
</script>
<div class="scrim" id="scrim"></div>

{{-- SIDEBAR --}}
<aside class="side" id="side">
    <div class="side-head">
        <div class="brand">
            <span class="logo"><i data-lucide="terminal" style="width:18px;height:18px"></i></span>
            <div><b>Dev Panel</b><small>ICB CT v2.0</small></div>
        </div>
        <button type="button" class="ibtn side-x" id="side-x" aria-label="Tutup menu"><i data-lucide="x" style="width:16px;height:16px"></i></button>
    </div>

    <nav class="nav" aria-label="Menu utama">
        <div class="nav-label">Ringkasan</div>
        <button type="button" onclick="switchTab('dashboard')" id="nav-dashboard" class="nav-item active"><i data-lucide="layout-dashboard"></i> Dashboard</button>

        <div class="nav-label">Kelola Sistem</div>
        <button type="button" onclick="switchTab('apk')" id="nav-apk" class="nav-item">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:18px;height:18px;flex-shrink:0;fill:#3DDC84"><path d="M17.523 15.341a.58.58 0 0 1-.58-.58.58.58 0 0 1 .58-.58.58.58 0 0 1 .58.58.58.58 0 0 1-.58.58m-11.046 0a.58.58 0 0 1-.58-.58.58.58 0 0 1 .58-.58.58.58 0 0 1 .58.58.58.58 0 0 1-.58.58M17.78 9.3l1.738-3.01a.361.361 0 0 0-.132-.494.362.362 0 0 0-.494.133l-1.759 3.047a10.879 10.879 0 0 0-5.133-1.27c-1.846 0-3.585.47-5.133 1.27L5.108 5.93a.362.362 0 0 0-.494-.133.361.361 0 0 0-.132.494L6.22 9.3C3.625 10.78 1.9 13.438 1.9 16.5h20.2c0-3.062-1.725-5.72-4.32-7.2"/></svg>
            Android Manager
        </button>
        <button type="button" onclick="switchTab('ios')" id="nav-ios" class="nav-item">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 814 1000" style="width:18px;height:18px;fill:currentColor;flex-shrink:0"><path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76 0-103.7 40.8-165.9 40.8s-105-42.3-150.3-110.7c-46-70.4-73.9-161.4-73.9-247.9 0-157.1 100.1-247.4 198.5-247.4 51.6 0 95.1 33.9 127.5 33.9 31.3 0 80.4-36.1 139.2-36.1 22.4 0 108.2 2 167 74.2zM726.4 82.4c24.2-28.8 41.7-68.7 41.7-108.6 0-5.5-.5-11.1-1.5-15.5-39.1 1.5-85.5 26.1-113.8 56.3-22.4 24.7-43.2 64.6-43.2 105.1 0 6 1 12 1.5 14.1 2.5.5 6.5 1 10.5 1 35.4 0 79.4-23.2 104.8-52.4z"/></svg>
            iOS Manager
        </button>
        <button type="button" onclick="switchTab('system')" id="nav-system" class="nav-item"><i data-lucide="cpu"></i> System State</button>
        <button type="button" onclick="switchTab('releases')" id="nav-releases" class="nav-item"><i data-lucide="git-pull-request"></i> Releases</button>

        <div class="nav-label">Tautan &amp; Preferensi</div>
        <a href="https://github.com/vexalyn-dev/presensi-guru-icbct" target="_blank" rel="noopener" class="nav-item"><i data-lucide="git-branch"></i> Repository</a>
        <a href="{{ url('/dashboard') }}" class="nav-item"><i data-lucide="external-link"></i> Main App</a>
        <button type="button" onclick="switchTab('settings')" id="nav-settings" class="nav-item"><i data-lucide="settings"></i> Settings &amp; Tema</button>
    </nav>

    <div class="side-foot">
        {{-- Logout di sidebar bawah --}}
        <form action="{{ route('logout') }}" method="POST"
              onsubmit="if(!confirmAction(this,'Yakin ingin keluar dari Developer Panel?','danger'))return false;try{sessionStorage.removeItem('dev_splash_shown');}catch(e){}return true;">
            @csrf
            <button type="submit" class="nav-item"
                    style="width:100%;color:#f87171;border-color:rgba(244,63,94,.15);background:rgba(244,63,94,.05)">
                <i data-lucide="log-out" style="width:17px;height:17px"></i>
                Keluar dari Panel
            </button>
        </form>
    </div>
</aside>

{{-- MAIN --}}
<main class="main">
    <header class="top">
        <div class="top-l">
            <button type="button" class="ibtn burger" id="burger" aria-label="Buka menu"><i data-lucide="menu" style="width:18px;height:18px"></i></button>
            <h1 id="header-title"><i data-lucide="layout-dashboard" style="width:18px;height:18px;color:var(--accent);vertical-align:middle;margin-right:6px"></i> <span>Dashboard</span></h1>
        </div>
        <div class="top-r">
            <span class="clock mono" id="live-clock">--:--:--</span>
            <span class="env"><i></i><span>{{ strtoupper(app()->environment()) }}</span></span>
            {{-- Profile card di pojok kanan atas --}}
            @php
                $topUser  = auth()->user();
                $topPhoto = ($topUser->photo_path ?: $topUser->photo)
                    ? asset('storage/'.($topUser->photo_path ?: $topUser->photo))
                    : asset('images/profile-dev.png');
            @endphp
            <button type="button" onclick="switchTab('profile')" title="Buka Profil"
                    style="display:flex;align-items:center;gap:10px;padding:5px 12px 5px 5px;
                           background:rgba(255,255,255,.03);border:1px solid var(--line-subtle);
                           border-radius:10px;cursor:pointer;transition:all .2s var(--ease);font:inherit"
                    onmouseover="this.style.background='rgba(99,102,241,.08)';this.style.borderColor='rgba(99,102,241,.3)'"
                    onmouseout="this.style.background='rgba(255,255,255,.03)';this.style.borderColor='var(--line-subtle)'">
                <img src="{{ $topPhoto }}" alt="{{ $topUser->name }}"
                     style="width:32px;height:32px;border-radius:50%;object-fit:cover;
                            border:2px solid rgba(99,102,241,.5);flex-shrink:0">
                <div style="text-align:left;min-width:0">
                    <p style="margin:0;font-size:.82rem;font-weight:650;color:var(--txt-head);
                               white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:130px">
                        {{ $topUser->name }}
                    </p>
                    <p style="margin:0;font-size:.68rem;color:var(--txt-dim);
                               font-family:'Geist Mono',monospace;display:flex;align-items:center;gap:4px">
                        <span style="width:5px;height:5px;border-radius:50%;background:#10b981;
                                     box-shadow:0 0 5px #10b981;flex-shrink:0"></span>
                        DEVELOPER CONSOLE
                    </p>
                </div>
            </button>
        </div>
    </header>

    <div class="content">
        @if(session('success') || session('error') || session('warning'))
        <div class="flash"
             x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 2000)"
             x-show="show"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            @if(session('success'))
                <div class="alert alert-ok" role="status"><i data-lucide="check-circle-2" style="width:18px;height:18px;flex-shrink:0"></i><span>{{ session('success') }}</span></div>
            @endif
            @if(session('error'))
                <div class="alert alert-err" role="alert"><i data-lucide="alert-triangle" style="width:18px;height:18px;flex-shrink:0"></i><span>{{ session('error') }}</span></div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warn" role="alert"><i data-lucide="alert-triangle" style="width:18px;height:18px;flex-shrink:0"></i><span>{{ session('warning') }}</span></div>
            @endif
        </div>
        @endif

        @yield('content')
    </div>
</main>

<script>
    const tabs = ['dashboard', 'apk', 'ios', 'system', 'releases', 'settings', 'profile'];
    const titles = {
        dashboard: 'Dashboard',
        apk: 'Android Manager',
        ios: 'iOS Manager',
        system: 'System State',
        releases: 'Riwayat Rilis',
        settings: 'Settings & Tema',
        profile: 'Profil Developer',
    };
    const tabIcons = {
        dashboard: 'layout-dashboard',
        apk: 'smartphone',
        ios: 'smartphone',
        system: 'cpu',
        releases: 'git-pull-request',
        settings: 'settings',
        profile: 'user-circle',
    };

    const sideEl = document.getElementById('side');
    const scrimEl = document.getElementById('scrim');
    function toggleSide(open) { sideEl.classList.toggle('open', open); scrimEl.classList.toggle('show', open); }
    document.getElementById('burger').addEventListener('click', () => toggleSide(true));
    document.getElementById('side-x').addEventListener('click', () => toggleSide(false));
    scrimEl.addEventListener('click', () => toggleSide(false));

    function switchTab(tabId) {
        if (!tabs.includes(tabId)) tabId = 'dashboard';
        tabs.forEach(t => {
            document.getElementById('tab-' + t)?.classList.remove('active');
            document.getElementById('nav-' + t)?.classList.remove('active');
        });
        document.getElementById('tab-' + tabId)?.classList.add('active');
        document.getElementById('nav-' + tabId)?.classList.add('active');
        
        const titleEl = document.getElementById('header-title');
        const iconName = tabIcons[tabId] || 'layout-dashboard';
        if (titleEl) {
            titleEl.innerHTML = `<i data-lucide="${iconName}" style="width:18px;height:18px;color:var(--accent);vertical-align:middle;margin-right:6px"></i> <span>${titles[tabId]}</span>`;
        }

        try { history.replaceState(null, '', '#' + tabId); sessionStorage.setItem('dev_tab', tabId); } catch (e) {}
        toggleSide(false);
        window.scrollTo({ top: 0, behavior: 'smooth' });
        if (window.lucide) lucide.createIcons();
    }

    function tickClock() {
        const el = document.getElementById('live-clock');
        if (!el) return;
        el.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }).replace(/\./g, ':') + ' WIB';
    }

    /* ─── Ultra-Smooth Luxury Theme Manager ─── */
    window.setConsoleTheme = function(themeName, event, skipAnimation) {
        const validThemes = ['obsidian', 'tokyo-night', 'cappuccino', 'catppuccin', 'nordic', 'cyberpunk', 'light'];
        if (!validThemes.includes(themeName)) themeName = 'obsidian';

        const applyThemeDOM = () => {
            document.body.setAttribute('data-theme', themeName);
            document.documentElement.setAttribute('data-theme', themeName);
            try { localStorage.setItem('dev_panel_theme', themeName); } catch (e) {}

            // Update Theme Cards selection state
            document.querySelectorAll('.theme-card').forEach(card => {
                const isMatch = card.dataset.theme === themeName || 
                    ((themeName === 'cappuccino' || themeName === 'catppuccin') && 
                     (card.dataset.theme === 'cappuccino' || card.dataset.theme === 'catppuccin'));
                card.classList.toggle('selected', isMatch);
            });

            // Update theme label if present
            const lbl = document.getElementById('active-theme-label');
            if (lbl) {
                const names = {
                    'obsidian': 'Obsidian Console (Default)',
                    'tokyo-night': 'Tokyo Night',
                    'cappuccino': 'Cappuccino Mocha (Warm Coffee)',
                    'catppuccin': 'Cappuccino Mocha (Warm Coffee)',
                    'nordic': 'Nordic Aurora',
                    'cyberpunk': 'Cyberpunk Matrix',
                    'light': 'Alabaster Light (Clean Mode)'
                };
                lbl.textContent = names[themeName] || themeName;
            }

            if (window.lucide) lucide.createIcons();
        };

        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        
        // Immediate apply if on initial load or user prefers reduced motion
        if (skipAnimation || prefersReduced) {
            applyThemeDOM();
            return;
        }

        // View Transition API circular reveal (luxurious radial sweep)
        if (document.startViewTransition) {
            let x = window.innerWidth / 2;
            let y = window.innerHeight / 2;
            if (event && (event.clientX || event.clientY)) {
                x = event.clientX;
                y = event.clientY;
            } else if (event && event.currentTarget) {
                const rect = event.currentTarget.getBoundingClientRect();
                x = rect.left + rect.width / 2;
                y = rect.top + rect.height / 2;
            }

            const endRadius = Math.hypot(
                Math.max(x, window.innerWidth - x),
                Math.max(y, window.innerHeight - y)
            );

            const transition = document.startViewTransition(() => {
                applyThemeDOM();
            });

            transition.ready.then(() => {
                document.documentElement.animate(
                    {
                        clipPath: [
                            `circle(0px at ${x}px ${y}px)`,
                            `circle(${endRadius}px at ${x}px ${y}px)`
                        ]
                    },
                    {
                        duration: 520,
                        easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                        pseudoElement: '::view-transition-new(root)'
                    }
                );
            }).catch(() => {
                applyThemeDOM();
            });
        } else {
            // Buttery-smooth CSS morphing fallback
            document.documentElement.classList.add('theme-morphing');
            applyThemeDOM();
            setTimeout(() => {
                document.documentElement.classList.remove('theme-morphing');
            }, 550);
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        // Load active theme smoothly without initial flash
        const savedTheme = localStorage.getItem('dev_panel_theme') || 'obsidian';
        window.setConsoleTheme(savedTheme, null, true);

        if (window.lucide) lucide.createIcons();
        let saved = null;
        try { saved = sessionStorage.getItem('dev_tab'); } catch (e) {}
        // Buka tab sesuai flash session jika ada (misal setelah save profile)
        const flashTab = '{{ session("active_tab_dev") }}';
        switchTab(flashTab || (location.hash || '').replace('#', '') || saved || 'dashboard');
        window.scrollTo(0, 0);
        tickClock();
        setInterval(tickClock, 1000);
    });
</script>
@yield('scripts')
</body>
</html>