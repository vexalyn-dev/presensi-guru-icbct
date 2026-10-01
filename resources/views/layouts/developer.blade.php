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
<link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
/* ─── Design Tokens ─── */
body.dp{
  --ink:#0f172a;--sub:#334155;--mute:#64748b;--faint:#94a3b8;--line:#e2e8f0;--bg:#f8fafc;--card:#fff;
  --v:#4f46e5;--v-d:#4338ca;--v-s:#eef2ff;--v-m:#c7d2fe;
  --ok:#059669;--ok-s:#ecfdf5;--warn:#d97706;--warn-s:#fffbeb;--bad:#dc2626;--bad-s:#fef2f2;--sky:#2563eb;--sky-s:#eff6ff;
  --r:12px;--sh:0 1px 2px rgba(0,0,0,.04),0 1px 3px rgba(0,0,0,.03);--sh-lg:0 8px 24px rgba(0,0,0,.06);
  --ease:cubic-bezier(.22,1,.36,1);
  font-family:'Geist',system-ui,-apple-system,sans-serif;color:var(--ink);background:var(--bg);margin:0;min-height:100vh;display:flex;overflow-x:hidden;
  -webkit-font-smoothing:antialiased;font-size:14px;line-height:1.55}
.dp *,.dp *::before,.dp *::after{box-sizing:border-box}
.dp h1,.dp h2,.dp h3,.dp h4,.dp p{margin:0}
.dp h2{font-size:1.35rem;font-weight:700;letter-spacing:-.02em}
.dp h3{font-size:.95rem;font-weight:600;letter-spacing:-.005em}
.dp [hidden]{display:none!important}
.dp :focus-visible{outline:2px solid var(--v);outline-offset:2px}
.dp ::selection{background:var(--v);color:#fff}
.mono{font-family:'Geist Mono',ui-monospace,monospace}
.mute{color:var(--mute);font-size:.875rem}
.pre{white-space:pre-line}.trunc{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.sr{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)}
.row{display:flex;align-items:center;gap:10px;flex-wrap:wrap}.end{display:flex;justify-content:flex-end;gap:10px;align-items:center}
.stack{display:flex;flex-direction:column;gap:20px}
.page-head{margin-bottom:24px}.page-head p{color:var(--mute);margin-top:4px}

/* ─── Sidebar ─── */
.side{position:fixed;inset:0 auto 0 0;width:252px;z-index:40;display:flex;flex-direction:column;background:var(--card);border-right:1px solid var(--line);transition:transform .35s var(--ease)}
.side-head{height:60px;display:flex;align-items:center;justify-content:space-between;padding:0 16px;border-bottom:1px solid var(--line)}
.brand{display:flex;align-items:center;gap:10px}
.logo{width:32px;height:32px;border-radius:8px;display:grid;place-items:center;color:#fff;background:var(--v);flex-shrink:0}
.brand b{display:block;font-size:.88rem;font-weight:650;letter-spacing:-.01em;color:var(--ink)}.brand small{display:block;font-size:.7rem;color:var(--faint);margin-top:-1px;letter-spacing:.01em}
.nav{flex:1;overflow-y:auto;padding:12px 10px}
.nav-label{padding:14px 10px 6px;font-size:.68rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--faint)}
.nav-item{position:relative;display:flex;align-items:center;gap:10px;width:100%;padding:8px 10px;margin-bottom:1px;font:inherit;font-size:.87rem;font-weight:500;color:var(--mute);background:none;border:0;border-radius:8px;cursor:pointer;text-decoration:none;text-align:left;transition:all .15s var(--ease)}
.nav-item svg{width:17px;height:17px;flex-shrink:0;opacity:.65;transition:opacity .15s}
.nav-item:hover{background:var(--bg);color:var(--sub)}.nav-item:hover svg{opacity:1}
.nav-item.active{background:var(--v-s);color:var(--v);font-weight:600}.nav-item.active svg{opacity:1}
.nav-item.active::before{content:"";position:absolute;left:0;top:6px;bottom:6px;width:3px;border-radius:0 3px 3px 0;background:var(--v)}
.side-foot{padding:10px 12px;border-top:1px solid var(--line)}
.me{display:flex;align-items:center;gap:10px;padding:8px;border-radius:10px}
.me img{width:32px;height:32px;border-radius:50%;flex-shrink:0;border:2px solid var(--line)}.me .n{font-weight:600;font-size:.82rem;color:var(--ink)}.me .r{font-size:.7rem;color:var(--faint)}

/* ─── Main ─── */
.main{flex:1;min-width:0;margin-left:252px;display:flex;flex-direction:column;min-height:100vh}
.top{position:sticky;top:0;z-index:30;height:56px;padding:0 28px;display:flex;align-items:center;justify-content:space-between;gap:14px;background:rgba(248,250,252,.88);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border-bottom:1px solid var(--line)}
.top h1{font-size:.95rem;font-weight:600;letter-spacing:-.01em;color:var(--sub)}
.top-l,.top-r{display:flex;align-items:center;gap:10px}
.clock{font-size:.75rem;font-weight:500;color:var(--mute);padding:5px 10px;background:var(--card);border:1px solid var(--line);border-radius:6px}
.env{display:inline-flex;align-items:center;gap:6px;font-size:.75rem;font-weight:600;padding:4px 10px;border-radius:6px;background:var(--ok-s);color:var(--ok);border:1px solid rgba(5,150,105,.12)}
.env i{width:6px;height:6px;border-radius:50%;background:currentColor}
.ibtn{width:34px;height:34px;display:inline-grid;place-items:center;color:var(--mute);background:var(--card);border:1px solid var(--line);border-radius:8px;cursor:pointer;transition:all .15s}
.ibtn:hover{color:var(--v);border-color:var(--v-m);background:var(--v-s)}
.burger,.side-x{display:none}
.content{flex:1;padding:28px;max-width:1200px;width:100%;margin:0 auto}
.flash{display:flex;flex-direction:column;gap:10px;margin-bottom:20px}
.scrim{position:fixed;inset:0;z-index:35;background:rgba(15,23,42,.4);opacity:0;pointer-events:none;transition:opacity .25s}.scrim.show{opacity:1;pointer-events:auto}
.tab-content{display:none}.tab-content.active{display:block;animation:fadeUp .3s var(--ease)}
@keyframes fadeUp{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:none}}

/* ─── Card + Parts ─── */
.card{background:var(--card);border:1px solid var(--line);border-radius:var(--r);box-shadow:var(--sh)}
.card-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 20px;border-bottom:1px solid var(--line)}
.pad{padding:20px}.bb{border-bottom:1px solid var(--line)}.foot{background:var(--bg);border-radius:0 0 var(--r) var(--r)}
.ico{width:34px;height:34px;border-radius:8px;display:inline-grid;place-items:center;flex-shrink:0}
.ico-lg{width:42px;height:42px;border-radius:10px}
.t-violet{background:var(--v-s);color:var(--v)}.t-ok{background:var(--ok-s);color:var(--ok)}.t-warn{background:var(--warn-s);color:var(--warn)}.t-bad{background:var(--bad-s);color:var(--bad)}.t-sky{background:var(--sky-s);color:var(--sky)}
.chip{display:inline-flex;align-items:center;font-size:.72rem;font-weight:600;padding:2px 8px;border-radius:6px;background:var(--v-s);color:var(--v)}
.badge{display:inline-flex;align-items:center;font-size:.7rem;font-weight:600;padding:2px 8px;border-radius:6px}
.live{display:inline-flex;align-items:center;gap:6px;font-size:.75rem;font-weight:600;color:var(--ok)}
.live i{width:6px;height:6px;border-radius:50%;background:var(--ok);animation:pulse 2s infinite}
@keyframes pulse{0%{box-shadow:0 0 0 0 rgba(5,150,105,.4)}100%{box-shadow:0 0 0 6px rgba(5,150,105,0)}}
.state{display:inline-flex;align-items:center;gap:5px;margin-left:8px;font-size:.7rem;font-weight:600;padding:2px 8px;border-radius:6px;background:var(--ok-s);color:var(--ok);vertical-align:middle}
.state i{width:5px;height:5px;border-radius:50%;background:currentColor}.state.on{background:var(--warn-s);color:var(--warn)}

/* ─── Buttons + Inputs ─── */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;font:inherit;font-weight:600;font-size:.84rem;padding:8px 16px;color:#fff;background:var(--v);border:1px solid var(--v);border-radius:8px;cursor:pointer;text-decoration:none;transition:all .15s var(--ease)}
.btn:hover{background:var(--v-d);border-color:var(--v-d)}.btn:active{transform:translateY(1px)}
.btn-ghost{background:var(--card);color:var(--sub);border-color:var(--line)}.btn-ghost:hover{background:var(--bg);border-color:var(--v-m);color:var(--v)}
.btn-danger{background:var(--bad);border-color:var(--bad)}.btn-danger:hover{background:#b91c1c}
.btn-white{background:#fff;color:var(--v);border-color:var(--line)}.btn-white:hover{background:var(--v-s);border-color:var(--v-m)}
.btn-glass{background:rgba(255,255,255,.12);color:#fff;border-color:rgba(255,255,255,.25)}.btn-glass:hover{background:rgba(255,255,255,.22)}
.btn-block{width:100%}
.label{display:block;font-size:.8rem;font-weight:600;margin-bottom:6px;color:var(--sub)}
.input{width:100%;font:inherit;color:var(--ink);background:var(--card);border:1px solid var(--line);border-radius:8px;padding:9px 12px;transition:border-color .15s,box-shadow .15s}
.input::placeholder{color:var(--faint)}.input:focus{outline:none;border-color:var(--v);box-shadow:0 0 0 3px var(--v-s)}
textarea.input{resize:vertical;min-height:80px}
.err{margin-top:5px;font-size:.78rem;font-weight:500;color:var(--bad)}
.form-grid{display:grid;grid-template-columns:1fr;gap:14px}
.check{display:flex;align-items:center;gap:10px;cursor:pointer;font-size:.84rem;user-select:none}
.check input{position:absolute;opacity:0}
.box{width:18px;height:18px;border:1.5px solid var(--line);border-radius:5px;background:#fff;display:inline-grid;place-items:center;color:transparent;transition:all .15s}
.check input:checked+.box{background:var(--v);border-color:var(--v);color:#fff}
.check input:focus-visible+.box{outline:2px solid var(--v);outline-offset:2px}
.switch{position:relative;flex-shrink:0;cursor:pointer}
.switch input[type=checkbox]{position:absolute;opacity:0;inset:0;width:100%;height:100%;cursor:pointer}
.track{display:block;width:44px;height:24px;background:#cbd5e1;border-radius:999px;transition:background .25s}
.thumb{position:absolute;top:3px;left:3px;width:18px;height:18px;background:#fff;border-radius:50%;box-shadow:0 1px 2px rgba(0,0,0,.18);transition:transform .3s var(--ease)}
.switch input:checked~.track{background:var(--v)}.switch input:checked~.track .thumb{transform:translateX(20px)}
.switch input:focus-visible~.track{outline:2px solid var(--v);outline-offset:2px}
.seg{display:grid;grid-template-columns:repeat(2,1fr);gap:8px}
.seg-i{position:relative;cursor:pointer}.seg-i input{position:absolute;opacity:0;inset:0;cursor:pointer}
.seg-b{display:flex;align-items:center;justify-content:center;gap:6px;padding:8px;font-weight:600;font-size:.84rem;color:var(--mute);background:var(--card);border:1px solid var(--line);border-radius:8px;transition:all .15s}
.seg-i:hover .seg-b{background:var(--bg)}
.seg-i input:checked+.seg-b{color:var(--v);background:var(--v-s);border-color:var(--v)}
.seg-i input:focus-visible+.seg-b{outline:2px solid var(--v);outline-offset:2px}

/* ─── Alerts ─── */
.alert{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border-radius:var(--r);font-size:.84rem;border:1px solid transparent;line-height:1.5}
.alert-ok{background:var(--ok-s);color:#065f46;border-color:rgba(5,150,105,.18)}.alert-err{background:var(--bad-s);color:#991b1b;border-color:rgba(220,38,38,.18)}
.alert-warn{background:var(--warn-s);color:#92400e;border-color:rgba(217,119,6,.18);margin-bottom:20px}
.alert code{font-family:'Geist Mono',monospace;font-size:.82em;background:rgba(255,255,255,.6);padding:1px 5px;border-radius:4px}

/* ─── Dashboard Header ─── */
.dash-header{margin-bottom:24px}
.dash-header .greeting{font-size:1.5rem;font-weight:700;letter-spacing:-.03em;line-height:1.2;color:var(--ink)}
.dash-header .subtext{color:var(--mute);font-size:.88rem;margin-top:6px;line-height:1.55}
.dash-header .subtext b{color:var(--sub);font-weight:600}
.dash-top{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap}
.dash-meta{display:flex;align-items:center;gap:8px;margin-top:12px;flex-wrap:wrap}
.dash-pill{display:inline-flex;align-items:center;gap:5px;font-size:.75rem;font-weight:600;padding:4px 10px;border-radius:6px;background:var(--ok-s);color:var(--ok);border:1px solid rgba(5,150,105,.1)}
.dash-pill i{width:5px;height:5px;border-radius:50%;background:currentColor;animation:pulse 2s infinite}
.dash-actions{display:flex;gap:8px;align-items:flex-start;flex-wrap:wrap;padding-top:4px}

/* ─── Dashboard Stats ─── */
.hero{display:none}
.stats{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:20px}
.stat{padding:16px 18px}
.stat-head{display:flex;align-items:center;gap:8px;margin-bottom:12px;color:var(--mute);font-weight:500;font-size:.82rem}
.stat-label{display:flex;align-items:center;gap:8px;margin-bottom:12px;color:var(--mute);font-weight:500;font-size:.82rem}
.stat-label .ico,.stat .ico,.stat-head .ico{width:28px;height:28px;border-radius:7px}
.num{font-size:1.85rem;font-weight:700;line-height:1;letter-spacing:-.03em;color:var(--ink)}.num-sm{font-size:1.2rem;font-family:'Geist Mono',monospace;font-weight:600}
.grid-main{display:grid;grid-template-columns:1fr;gap:16px}
.info{display:grid;grid-template-columns:1fr 1fr;gap:0}
.info>div{padding:14px 20px;border-bottom:1px solid var(--line)}.info>div:nth-child(odd){border-right:1px solid var(--line)}
.info>div:nth-last-child(-n+2){border-bottom:0}
.info .l{font-size:.72rem;color:var(--faint);font-weight:500;margin-bottom:3px;text-transform:uppercase;letter-spacing:.04em}.info .v{font-weight:600;font-size:.88rem;display:flex;align-items:center;gap:7px;min-width:0}
.dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}.dot.ok{background:var(--ok)}.dot.warn{background:var(--warn)}
.actions{padding:8px;display:flex;flex-direction:column;gap:2px}
.action,.tool{display:flex;align-items:center;gap:12px;width:100%;padding:10px 12px;font:inherit;color:var(--ink);text-align:left;text-decoration:none;cursor:pointer;background:none;border:1px solid transparent;border-radius:8px;transition:all .15s var(--ease)}
.action b,.tool b{display:block;font-size:.85rem;font-weight:600}.action small,.tool small{display:block;font-size:.74rem;color:var(--mute);margin-top:1px}
.action>div{flex:1;min-width:0}.go{color:var(--faint);transition:transform .2s,color .2s}
.action:hover,.tool:hover{background:var(--bg);border-color:var(--line)}.action:hover .go{color:var(--v);transform:translate(2px,-2px)}

/* ─── APK ─── */
.apk{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding:18px 20px}
.apk-info{display:flex;align-items:center;gap:14px}.apk h4{font-size:.95rem;font-weight:600;display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.apk-meta{display:flex;flex-wrap:wrap;gap:8px;padding:0 20px 18px}
.pill{display:inline-flex;align-items:center;gap:6px;font-size:.78rem;font-weight:500;padding:4px 10px;background:var(--bg);border-radius:6px;color:var(--mute);border:1px solid var(--line)}
.apk-form{display:grid;grid-template-columns:minmax(0,1fr);gap:22px}
.apk-drop{display:flex;flex-direction:column}.apk-drop .drop{flex:1;min-height:200px}
.apk-fields{display:flex;flex-direction:column;justify-content:space-between;gap:20px}
.drop{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;text-align:center;padding:28px 20px;cursor:pointer;border:1.5px dashed var(--v-m);border-radius:var(--r);background:var(--v-s);transition:all .2s var(--ease)}
.drop:hover,.drop.over{background:#e0e7ff;border-color:var(--v)}
.drop.has-file{border-style:solid;border-color:var(--ok);background:var(--ok-s)}
@media(min-width:1024px){.apk-form{grid-template-columns:minmax(0,5fr) minmax(0,7fr)}}

/* ─── Tools ─── */
.tools{display:grid;grid-template-columns:1fr;gap:10px;padding:14px}
.tools .tool{border-color:var(--line);padding:14px}
@media(min-width:640px){.tools{grid-template-columns:1fr 1fr}}
@media(min-width:1280px){.tools{grid-template-columns:repeat(4,minmax(0,1fr))}.tools .tool{flex-direction:column;align-items:flex-start;gap:12px}}

/* ─── Releases ─── */
.rel-head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:20px}
.rel-stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-bottom:24px}
.mini{display:flex;align-items:center;gap:12px;padding:14px 16px}.mini p{font-size:.78rem;color:var(--mute);font-weight:500}
.mini b{display:block;font-size:1.35rem;font-weight:700;letter-spacing:-.02em;line-height:1.15}.mini b.mono{font-size:1.1rem}
.rel-grid{display:grid;grid-template-columns:minmax(0,1fr);gap:22px}.rel-aside{order:-1}
@media(min-width:900px){.rel-stats{grid-template-columns:repeat(4,minmax(0,1fr))}}
@media(min-width:1100px){.rel-grid{grid-template-columns:minmax(0,1fr) 260px}.rel-aside{order:0}}
.sticky{position:sticky;top:80px}
.fbtns{display:flex;flex-wrap:wrap;gap:6px;padding:12px}
@media(min-width:1100px){.fbtns{flex-direction:column;flex-wrap:nowrap;gap:2px}}
.fbtn{display:flex;align-items:center;gap:9px;font:inherit;font-weight:500;font-size:.84rem;color:var(--mute);text-align:left;background:none;border:0;border-radius:8px;padding:7px 10px;cursor:pointer;transition:all .15s var(--ease)}
.fbtn small{margin-left:auto;min-width:24px;text-align:center;padding:0 6px;font-family:'Geist Mono',monospace;font-size:.72rem;background:var(--bg);border-radius:6px}
.fico{width:24px;height:24px;display:inline-grid;place-items:center;border-radius:6px;flex-shrink:0}
.fbtn:hover{background:var(--bg);color:var(--sub)}.fbtn.active{background:var(--v-s);color:var(--v);font-weight:600}.fbtn.active small{background:#fff}
.tl-group+.tl-group{margin-top:24px}
.tl-month{margin-bottom:12px;font-size:.78rem;font-weight:600;color:var(--mute);text-transform:uppercase;letter-spacing:.04em}
.tl-list{position:relative;list-style:none;margin:0;padding:0 0 0 48px}
.tl-list::before{content:"";position:absolute;left:17px;top:0;bottom:0;border-left:1px solid var(--line)}
.tl-item{position:relative;margin-bottom:12px}.tl-item:last-child{margin-bottom:0}
.tl-dot{position:absolute;left:-48px;top:14px;width:34px;height:34px;border:3px solid var(--bg)}
.tl-card{padding:16px 18px}.tl-card.latest{border-color:var(--v-m);box-shadow:0 0 0 2px var(--v-s),var(--sh)}
.tl-top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
.tl-title{display:flex;align-items:center;flex-wrap:wrap;gap:6px;min-width:0}.tl-title h4{font-size:.95rem;font-weight:650}
.tl-side{display:flex;align-items:center;gap:5px;flex-shrink:0}.tl-side time{font-size:.74rem;color:var(--faint);white-space:nowrap}
.trash{border:0;background:none;color:var(--faint);border-radius:6px;padding:5px;cursor:pointer;transition:all .15s}.trash:hover{background:var(--bad-s);color:var(--bad)}
.cl{list-style:none;margin:10px 0 0;padding:0;display:flex;flex-direction:column;gap:5px}
.cl li{position:relative;padding-left:16px;color:var(--sub);font-size:.88rem;line-height:1.55}
.cl li::before{content:"";position:absolute;left:2px;top:.6em;width:5px;height:5px;border-radius:50%;background:var(--v-m)}
.empty{padding:44px 20px;text-align:center;display:flex;flex-direction:column;align-items:center;gap:10px}.empty h4{font-size:1rem;font-weight:600}

/* ─── Modal ─── */
.overlay{position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(15,23,42,.45);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);opacity:0;transition:opacity .25s}
.overlay.open{opacity:1}
.modal{width:100%;max-width:28rem;max-height:92vh;overflow:auto;background:var(--card);border-radius:16px;box-shadow:0 24px 64px rgba(0,0,0,.18);transform:translateY(12px) scale(.97);transition:transform .3s var(--ease)}
.overlay.open .modal{transform:none}.modal-sm{max-width:22rem}
.modal-top{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;border-bottom:1px solid var(--line)}
.modal-top b{font-weight:600;font-size:.9rem}
.modal-body{padding:20px;display:flex;flex-direction:column;gap:14px}
.modal-sm .modal-body{align-items:flex-start}
.modal-body h2{font-size:1.35rem;font-weight:700;letter-spacing:-.02em}.modal-body h3{font-size:1.1rem;font-weight:650}
.tips{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:6px}
.tips li{display:flex;gap:12px;align-items:center;padding:10px 12px;border:1px solid var(--line);border-radius:10px}
.tips strong{display:block;font-size:.85rem}.tips small{display:block;font-size:.76rem;color:var(--mute);margin-top:1px}

/* ─── Scrollbar ─── */
::-webkit-scrollbar{width:8px;height:8px}::-webkit-scrollbar-thumb{background:#cbd5e1;border:2px solid var(--bg);border-radius:8px}::-webkit-scrollbar-thumb:hover{background:#94a3b8}

/* ─── Responsive ─── */
@media(min-width:768px){.stats{grid-template-columns:repeat(4,1fr)}.form-grid{grid-template-columns:1fr 1fr}}
@media(min-width:1024px){.grid-main{grid-template-columns:3fr 2fr}}
@media(max-width:1023px){
  .side{transform:translateX(-101%)}.side.open{transform:none;box-shadow:8px 0 30px rgba(0,0,0,.1)}
  .side-x,.burger{display:inline-grid}.main{margin-left:0}.top{height:56px;padding:0 14px}.content{padding:18px 14px 28px}.clock{display:none}
}
@media(max-width:640px){
  .info{grid-template-columns:1fr}.info>div:nth-child(odd){border-right:0}.info>div:nth-last-child(2){border-bottom:1px solid var(--line)}
  .tl-list{padding-left:42px}.tl-dot{left:-42px}.tl-top{flex-direction:column;gap:6px}
  .dash-top{flex-direction:column}.dash-actions{width:100%}
}
@media(max-width:480px){.env span{display:none}.env{padding:4px 8px}}
@media(prefers-reduced-motion:reduce){.dp *,.dp *::before,.dp *::after{animation:none!important;transition-duration:.01ms!important}}
</style>
</head>

<body class="dp antialiased">
<div class="scrim" id="scrim"></div>

{{-- SIDEBAR --}}
<aside class="side" id="side">
    <div class="side-head">
        <div class="brand">
            <span class="logo"><i data-lucide="command" style="width:16px;height:16px"></i></span>
            <div><b>Dev Panel</b><small>ICB CT Presensi</small></div>
        </div>
        <button type="button" class="ibtn side-x" id="side-x" aria-label="Tutup menu"><i data-lucide="x" style="width:16px;height:16px"></i></button>
    </div>

    <nav class="nav" aria-label="Menu utama">
        <div class="nav-label">Ringkasan</div>
        <button type="button" onclick="switchTab('dashboard')" id="nav-dashboard" class="nav-item active"><i data-lucide="layout-grid"></i> Dashboard</button>

        <div class="nav-label">Kelola</div>
        <button type="button" onclick="switchTab('apk')" id="nav-apk" class="nav-item"><i data-lucide="smartphone"></i> APK Manager</button>
        <button type="button" onclick="switchTab('system')" id="nav-system" class="nav-item"><i data-lucide="shield-alert"></i> System State</button>
        <button type="button" onclick="switchTab('releases')" id="nav-releases" class="nav-item"><i data-lucide="git-merge"></i> Releases</button>

        <div class="nav-label">Tautan</div>
        <a href="https://github.com/vexalyn-dev/presensi-guru-icbct" target="_blank" rel="noopener" class="nav-item"><i data-lucide="git-branch"></i> Repository</a>
        <a href="{{ url('/dashboard') }}" class="nav-item"><i data-lucide="external-link"></i> Main App</a>
    </nav>

    <div class="side-foot">
        <div class="me">
            <img src="https://ui-avatars.com/api/?name=Vio+Atmajaya&background=4f46e5&color=fff&bold=true" alt="Vio Atmajaya">
            <div style="min-width:0"><p class="n trunc">Vio Atmajaya</p><p class="r trunc">developer</p></div>
        </div>
    </div>
</aside>

{{-- MAIN --}}
<main class="main">
    <header class="top">
        <div class="top-l">
            <button type="button" class="ibtn burger" id="burger" aria-label="Buka menu"><i data-lucide="menu" style="width:18px;height:18px"></i></button>
            <h1 id="header-title">Dashboard</h1>
        </div>
        <div class="top-r">
            <span class="clock mono" id="live-clock">--:--:--</span>
            <span class="env"><i></i><span>{{ ucfirst(app()->environment()) }}</span></span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline" onsubmit="return confirmAction(this, 'Yakin ingin keluar dari developer panel?')">
                @csrf
                <button type="submit" class="ibtn" aria-label="Logout"><i data-lucide="log-out" style="width:16px;height:16px"></i></button>
            </form>
        </div>
    </header>

    <div class="content">
        @if(session('success') || session('error'))
            <div class="flash">
                @if(session('success'))
                    <div class="alert alert-ok" role="status"><i data-lucide="check-circle" style="width:18px;height:18px;flex-shrink:0"></i><span>{{ session('success') }}</span></div>
                @endif
                @if(session('error'))
                    <div class="alert alert-err" role="alert"><i data-lucide="alert-circle" style="width:18px;height:18px;flex-shrink:0"></i><span>{{ session('error') }}</span></div>
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