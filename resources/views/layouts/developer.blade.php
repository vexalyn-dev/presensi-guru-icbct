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
<style>
/* ─── Ultra-Modern SaaS Dev Console Design System ─── */
:root {
  --bg-base: #090d16;
  --bg-surface: #0f172a;
  --bg-surface-elevated: #151f35;
  --bg-surface-glass: rgba(15, 23, 42, 0.78);
  --bg-hover: #1e293b;
  
  --line-subtle: rgba(255, 255, 255, 0.08);
  --line-mid: rgba(255, 255, 255, 0.14);
  --line-bright: rgba(255, 255, 255, 0.24);

  --txt-head: #f8fafc;
  --txt-body: #cbd5e1;
  --txt-sub: #94a3b8;
  --txt-dim: #64748b;

  /* Dev Console Accent Palettes */
  --accent: #6366f1;
  --accent-hover: #4f46e5;
  --accent-glow: rgba(99, 102, 241, 0.28);
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

  --r-sm: 8px;
  --r-md: 12px;
  --r-lg: 16px;

  --sh-card: 0 4px 20px -2px rgba(0, 0, 0, 0.45), 0 1px 3px rgba(0, 0, 0, 0.3);
  --sh-hover: 0 12px 32px -8px rgba(0, 0, 0, 0.6), 0 0 24px -4px var(--accent-glow);
  --sh-modal: 0 25px 60px -15px rgba(0, 0, 0, 0.8), 0 0 30px rgba(99, 102, 241, 0.15);

  --ease: cubic-bezier(0.16, 1, 0.3, 1);
}

body.dp {
  /* Backward-compatible token mappings */
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
    radial-gradient(ellipse 90% 600px at 50% -120px, rgba(99, 102, 241, 0.14), transparent),
    radial-gradient(ellipse 60% 500px at 100% 0, rgba(6, 182, 212, 0.07), transparent),
    radial-gradient(ellipse 40% 400px at 0% 100%, rgba(99, 102, 241, 0.05), transparent);
  background-attachment: fixed;
  margin: 0;
  min-height: 100vh;
  display: flex;
  overflow-x: hidden;
  -webkit-font-smoothing: antialiased;
  font-size: 14px;
  line-height: 1.55;
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
.page-head h2 { font-size: 1.4rem; font-weight: 700; color: #fff; letter-spacing: -0.025em; }
.page-head p { color: var(--txt-sub); margin-top: 4px; font-size: 0.9rem; }

/* ─── Sidebar (Dark SaaS Console) ─── */
.side {
  position: fixed;
  inset: 0 auto 0 0;
  width: 256px;
  z-index: 40;
  display: flex;
  flex-direction: column;
  background: rgba(11, 17, 32, 0.94);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-right: 1px solid var(--line-subtle);
  transition: transform 0.35s var(--ease);
}
.side-head {
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 18px;
  border-bottom: 1px solid var(--line-subtle);
}
.brand { display: flex; align-items: center; gap: 12px; }
.logo {
  width: 34px;
  height: 34px;
  border-radius: 9px;
  display: grid;
  place-items: center;
  color: #fff;
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  box-shadow: 0 0 16px rgba(99, 102, 241, 0.45);
  border: 1px solid rgba(255, 255, 255, 0.2);
  flex-shrink: 0;
}
.brand b {
  display: block;
  font-size: 0.92rem;
  font-weight: 700;
  letter-spacing: -0.015em;
  color: #fff;
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
  gap: 2px;
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
  font-weight: 500;
  color: var(--txt-sub);
  background: none;
  border: 0;
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
  opacity: 0.7;
  transition: all 0.2s var(--ease);
}
.nav-item:hover {
  background: rgba(255, 255, 255, 0.05);
  color: #fff;
  transform: translateX(2px);
}
.nav-item:hover svg {
  opacity: 1;
  color: var(--accent);
}
.nav-item.active {
  background: rgba(99, 102, 241, 0.12);
  color: #fff;
  font-weight: 600;
  border: 1px solid rgba(99, 102, 241, 0.22);
}
.nav-item.active svg {
  opacity: 1;
  color: #818cf8;
}
.nav-item.active::before {
  content: "";
  position: absolute;
  left: 0;
  top: 7px;
  bottom: 7px;
  width: 3px;
  border-radius: 0 4px 4px 0;
  background: var(--accent);
  box-shadow: 0 0 10px var(--accent);
}
.side-foot {
  padding: 12px 14px;
  border-top: 1px solid var(--line-subtle);
  background: rgba(8, 12, 22, 0.6);
}
.me {
  display: flex;
  align-items: center;
  gap: 11px;
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
  color: #fff;
}
.me .r {
  font-size: 0.72rem;
  color: var(--txt-dim);
  font-family: 'Geist Mono', monospace;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

/* ─── Main Content Shell ─── */
.main {
  flex: 1;
  min-width: 0;
  margin-left: 256px;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}
.top {
  position: sticky;
  top: 0;
  z-index: 30;
  height: 60px;
  padding: 0 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  background: rgba(9, 13, 22, 0.82);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid var(--line-subtle);
}
.top h1 {
  font-size: 1rem;
  font-weight: 650;
  letter-spacing: -0.015em;
  color: #fff;
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
  padding: 5px 12px;
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
  padding: 4px 10px;
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
  background: #34d399;
  box-shadow: 0 0 8px #34d399;
}
.ibtn {
  width: 34px;
  height: 34px;
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
  box-shadow: 0 0 12px var(--accent-glow);
  transform: translateY(-1px);
}
.burger, .side-x { display: none; }
.content {
  flex: 1;
  padding: 32px;
  max-width: 1240px;
  width: 100%;
  margin: 0 auto;
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
  padding: 16px 22px;
  border-bottom: 1px solid var(--line-subtle);
  background: rgba(255, 255, 255, 0.015);
}
.card-head h3 {
  font-size: 0.96rem;
  font-weight: 700;
  letter-spacing: -0.01em;
  color: #fff;
  display: flex;
  align-items: center;
  gap: 8px;
}
.pad { padding: 22px; }
.bb { border-bottom: 1px solid var(--line-subtle); }
.foot {
  background: rgba(0, 0, 0, 0.2);
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
.t-violet { background: var(--accent-soft); color: #818cf8; border: 1px solid var(--accent-line); }
.t-ok { background: var(--ok-soft); color: #34d399; border: 1px solid var(--ok-line); }
.t-warn { background: var(--warn-soft); color: #fbbf24; border: 1px solid var(--warn-line); }
.t-bad { background: var(--bad-soft); color: #fb7185; border: 1px solid var(--bad-line); }
.t-sky { background: var(--sky-soft); color: #38bdf8; border: 1px solid var(--sky-line); }

.chip {
  display: inline-flex;
  align-items: center;
  font-size: 0.72rem;
  font-weight: 600;
  padding: 3px 9px;
  border-radius: 6px;
  background: var(--accent-soft);
  color: #a5b4fc;
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
  color: #34d399;
}
.live i {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #10b981;
  box-shadow: 0 0 10px #10b981;
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
  color: #34d399;
  border: 1px solid var(--ok-line);
  vertical-align: middle;
}
.state i { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
.state.on {
  background: var(--warn-soft);
  color: #fbbf24;
  border-color: var(--warn-line);
}

/* ─── Modern Buttons & Inputs ─── */
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
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 8px;
  cursor: pointer;
  text-decoration: none;
  box-shadow: 0 2px 10px rgba(99, 102, 241, 0.35);
  transition: all 0.2s var(--ease);
}
.btn:hover {
  background: linear-gradient(135deg, #4f46e5, #4338ca);
  box-shadow: 0 4px 18px rgba(99, 102, 241, 0.5);
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
  background: linear-gradient(135deg, #f43f5e, #e11d48);
  border-color: rgba(255, 255, 255, 0.18);
  box-shadow: 0 2px 12px rgba(244, 63, 94, 0.35);
}
.btn-danger:hover {
  background: linear-gradient(135deg, #e11d48, #be123c);
  box-shadow: 0 4px 18px rgba(244, 63, 94, 0.5);
}
.btn-white {
  background: #fff;
  color: #0f172a;
  border-color: #fff;
}
.btn-white:hover {
  background: #f1f5f9;
}
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
  color: #f8fafc;
  background: #0a0e18;
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
  background: #0c1220;
}
textarea.input { resize: vertical; min-height: 85px; }
.err { margin-top: 6px; font-size: 0.78rem; font-weight: 500; color: #fb7185; }
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
  background: #0a0e18;
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
  background: #0a0e18;
  border: 1px solid var(--line-subtle);
  border-radius: 8px;
  transition: all 0.2s var(--ease);
}
.seg-i:hover .seg-b { background: #131b2e; color: #fff; }
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
.alert-ok { background: var(--ok-soft); color: #34d399; border: 1px solid var(--ok-line); }
.alert-err { background: var(--bad-soft); color: #fb7185; border: 1px solid var(--bad-line); }
.alert-warn { background: var(--warn-soft); color: #fbbf24; border: 1px solid var(--warn-line); margin-bottom: 22px; }
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
  font-size: 1.65rem;
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.2;
  color: #fff;
}
.dash-header .subtext {
  color: var(--txt-sub);
  font-size: 0.9rem;
  margin-top: 7px;
  line-height: 1.6;
}
.dash-header .subtext b {
  color: #fff;
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
  color: #34d399;
  border: 1px solid var(--ok-line);
  box-shadow: 0 0 12px rgba(16, 185, 129, 0.15);
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

/* ─── Dashboard Stats ─── */
.hero { display: none; }
.stats {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
  margin-bottom: 22px;
}
.stat {
  padding: 20px 22px;
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
  border-color: rgba(99, 102, 241, 0.3);
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
  font-size: 2.1rem;
  font-weight: 800;
  line-height: 1;
  letter-spacing: -0.035em;
  color: #fff;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.num-sm {
  font-size: 1.35rem;
  font-family: 'Geist Mono', monospace;
  font-weight: 650;
  color: #fff;
}
.grid-main {
  display: grid;
  grid-template-columns: 1fr;
  gap: 18px;
}

/* System Info Matrix */
.info { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
.info > div {
  padding: 16px 22px;
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
  color: #fff;
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 0;
}
.dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.dot.ok { background: #10b981; box-shadow: 0 0 8px #10b981; }
.dot.warn { background: #f59e0b; box-shadow: 0 0 8px #f59e0b; }

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
  padding: 12px 14px;
  font: inherit;
  color: #fff;
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
  color: #fff;
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
  padding: 22px 24px;
}
.apk-info { display: flex; align-items: center; gap: 16px; }
.apk h4 {
  font-size: 1.05rem;
  font-weight: 700;
  color: #fff;
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
  background: rgba(99, 102, 241, 0.03);
  transition: all 0.25s var(--ease);
}
.drop:hover, .drop.over {
  background: rgba(99, 102, 241, 0.1);
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
  gap: 12px;
  padding: 16px;
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
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
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
  color: #fff;
  letter-spacing: -0.02em;
  line-height: 1.2;
}
.mini b.mono { font-size: 1.15rem; }
.rel-grid {
  display: grid;
  grid-template-columns: minmax(0, 1fr);
  gap: 24px;
}
.rel-aside { order: -1; }
@media(min-width: 900px) { .rel-stats { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
@media(min-width: 1100px) {
  .rel-grid { grid-template-columns: minmax(0, 1fr) 280px; }
  .rel-aside { order: 0; }
}
.sticky { position: sticky; top: 84px; }
.fbtns {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  padding: 14px;
}
@media(min-width: 1100px) {
  .fbtns { flex-direction: column; flex-wrap: nowrap; gap: 4px; }
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
.fbtn:hover {
  background: rgba(255, 255, 255, 0.04);
  color: #fff;
}
.fbtn.active {
  background: var(--accent-soft);
  color: #fff;
  border-color: var(--accent-line);
  font-weight: 600;
}
.fbtn.active small {
  background: var(--accent);
  color: #fff;
}
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
.tl-card {
  padding: 18px 22px;
}
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
.tl-title h4 { font-size: 0.98rem; font-weight: 700; color: #fff; }
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
.trash:hover {
  background: var(--bad-soft);
  color: #fb7185;
}
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

/* ─── Modal Dialogs (Glassmorphic Dark) ─── */
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
  background: #0f172a;
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
.modal-top b { font-weight: 700; font-size: 0.95rem; color: #fff; }
.modal-body {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.modal-sm .modal-body { align-items: flex-start; }
.modal-body h2 { font-size: 1.4rem; font-weight: 800; letter-spacing: -0.025em; color: #fff; }
.modal-body h3 { font-size: 1.15rem; font-weight: 700; color: #fff; }
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
.tips strong { display: block; font-size: 0.88rem; color: #fff; }
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

/* ─── Responsive ─── */
@media(min-width: 768px) {
  .stats { grid-template-columns: repeat(4, 1fr); }
  .form-grid { grid-template-columns: 1fr 1fr; }
}
@media(min-width: 1024px) {
  .grid-main { grid-template-columns: 3fr 2fr; }
}
@media(max-width: 1023px) {
  .side { transform: translateX(-101%); }
  .side.open { transform: none; box-shadow: 12px 0 40px rgba(0, 0, 0, 0.8); }
  .side-x, .burger { display: inline-grid; }
  .main { margin-left: 0; }
  .top { height: 58px; padding: 0 16px; }
  .content { padding: 20px 16px 32px; }
  .clock { display: none; }
}
@media(max-width: 640px) {
  .info { grid-template-columns: 1fr; }
  .info > div:nth-child(odd) { border-right: 0; }
  .info > div:nth-last-child(2) { border-bottom: 1px solid var(--line-subtle); }
  .tl-list { padding-left: 42px; }
  .tl-dot { left: -42px; }
  .tl-top { flex-direction: column; gap: 8px; }
  .dash-top { flex-direction: column; }
  .dash-actions { width: 100%; }
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
<div class="scrim" id="scrim"></div>

{{-- SIDEBAR --}}
<aside class="side" id="side">
    <div class="side-head">
        <div class="brand">
            <span class="logo"><i data-lucide="terminal" style="width:17px;height:17px"></i></span>
            <div><b>Dev Panel</b><small>ICB CT v2.0</small></div>
        </div>
        <button type="button" class="ibtn side-x" id="side-x" aria-label="Tutup menu"><i data-lucide="x" style="width:16px;height:16px"></i></button>
    </div>

    <nav class="nav" aria-label="Menu utama">
        <div class="nav-label">Ringkasan</div>
        <button type="button" onclick="switchTab('dashboard')" id="nav-dashboard" class="nav-item active"><i data-lucide="layout-dashboard"></i> Dashboard</button>

        <div class="nav-label">Kelola Sistem</div>
        <button type="button" onclick="switchTab('apk')" id="nav-apk" class="nav-item"><i data-lucide="smartphone"></i> APK Manager</button>
        <button type="button" onclick="switchTab('system')" id="nav-system" class="nav-item"><i data-lucide="cpu"></i> System State</button>
        <button type="button" onclick="switchTab('releases')" id="nav-releases" class="nav-item"><i data-lucide="git-pull-request"></i> Releases</button>

        <div class="nav-label">Tautan Eksternal</div>
        <a href="https://github.com/vexalyn-dev/presensi-guru-icbct" target="_blank" rel="noopener" class="nav-item"><i data-lucide="git-branch"></i> Repository</a>
        <a href="{{ url('/dashboard') }}" class="nav-item"><i data-lucide="external-link"></i> Main App</a>
    </nav>

    <div class="side-foot">
        <div class="me">
            <img src="https://ui-avatars.com/api/?name=Vio+Atmajaya&background=6366f1&color=fff&bold=true" alt="Vio Atmajaya">
            <div style="min-width:0"><p class="n trunc">Vio Atmajaya</p><p class="r trunc">developer console</p></div>
        </div>
    </div>
</aside>

{{-- MAIN --}}
<main class="main">
    <header class="top">
        <div class="top-l">
            <button type="button" class="ibtn burger" id="burger" aria-label="Buka menu"><i data-lucide="menu" style="width:18px;height:18px"></i></button>
            <h1 id="header-title"><i data-lucide="layout-dashboard" style="width:18px;height:18px;color:var(--accent)"></i> Dashboard</h1>
        </div>
        <div class="top-r">
            <span class="clock mono" id="live-clock">--:--:--</span>
            <span class="env"><i></i><span>{{ strtoupper(app()->environment()) }}</span></span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline" onsubmit="return confirmAction(this, 'Yakin ingin keluar dari developer panel?')">
                @csrf
                <button type="submit" class="ibtn" aria-label="Logout" title="Keluar"><i data-lucide="log-out" style="width:16px;height:16px"></i></button>
            </form>
        </div>
    </header>

    <div class="content">
        @if(session('success') || session('error'))
            <div class="flash">
                @if(session('success'))
                    <div class="alert alert-ok" role="status"><i data-lucide="check-circle-2" style="width:18px;height:18px;flex-shrink:0"></i><span>{{ session('success') }}</span></div>
                @endif
                @if(session('error'))
                    <div class="alert alert-err" role="alert"><i data-lucide="alert-triangle" style="width:18px;height:18px;flex-shrink:0"></i><span>{{ session('error') }}</span></div>
                @endif
            </div>
        @endif

        @yield('content')
    </div>
</main>

<script>
    const tabs = ['dashboard', 'apk', 'system', 'releases'];
    const titles = { dashboard: 'Dashboard', apk: 'APK Manager', system: 'System State', releases: 'Riwayat Rilis' };

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
        document.getElementById('header-title').textContent = titles[tabId];
        try { history.replaceState(null, '', '#' + tabId); sessionStorage.setItem('dev_tab', tabId); } catch (e) {}
        toggleSide(false);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function tickClock() {
        const el = document.getElementById('live-clock');
        if (!el) return;
        el.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }).replace(/\./g, ':') + ' WIB';
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
        let saved = null;
        try { saved = sessionStorage.getItem('dev_tab'); } catch (e) {}
        switchTab((location.hash || '').replace('#', '') || saved || 'dashboard');
        window.scrollTo(0, 0);
        tickClock();
        setInterval(tickClock, 1000);
    });
</script>
@yield('scripts')
</body>
</html>