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
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
body.nb{
    --ink:#17132e; --paper:#ffffff; --canvas:#faf9ff; --line:#17132e;
    --violet:#7c5cff; --violet-d:#6142f0; --violet-l:#ece7ff; --violet-xl:#f6f3ff;
    --mint:#5eead4; --sun:#ffd166; --rose:#ff8fab; --sky:#8ecbff;
    --r-lg:22px; --r-md:14px; --r-sm:10px;
    --sh:4px 4px 0 var(--ink); --sh-lg:7px 7px 0 var(--ink); --sh-sm:2px 2px 0 var(--ink);
    --ease:cubic-bezier(.22,1,.36,1); --spring:cubic-bezier(.34,1.56,.64,1);
    font-family:'Plus Jakarta Sans',system-ui,sans-serif; color:var(--ink);
    -webkit-font-smoothing:antialiased;
}
.nb *,.nb *::before,.nb *::after{box-sizing:border-box}
.nb h1,.nb h2,.nb h3,.nb h4{font-family:'Bricolage Grotesque','Plus Jakarta Sans',sans-serif;margin:0;letter-spacing:-.02em;color:var(--ink)}
.nb p{margin:0}
.nb .nb-mono{font-family:'JetBrains Mono',ui-monospace,monospace}
.nb .nb-muted{color:rgba(23,19,46,.62);font-size:.875rem;line-height:1.55}
.nb .nb-pre{white-space:pre-line}
.nb .nb-nowrap{white-space:nowrap}
.nb .truncate{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.nb .hidden{display:none}
.nb .sr-only{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)}
.nb :focus-visible{outline:3px solid var(--violet);outline-offset:3px}

/* Layout */
.nb-grid-stats{display:grid;grid-template-columns:repeat(2,1fr);gap:18px;margin-bottom:22px}
.nb-grid-main{display:grid;grid-template-columns:1fr;gap:22px}
.nb-grid-rel{display:grid;grid-template-columns:1fr;gap:22px;align-items:start}
.nb-wrap-md{max-width:56rem}
.nb-stack{display:flex;flex-direction:column;gap:16px}
.nb-stack-lg{display:flex;flex-direction:column;gap:22px}
.nb-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.nb-row-end{display:flex;align-items:center;justify-content:flex-end;gap:10px}
.nb-split{display:flex;align-items:flex-start;justify-content:space-between;gap:16px}
.nb-min0{min-width:0}.nb-mb{margin-bottom:22px}.nb-maxw{max-width:30rem;margin-top:4px}
.nb-pad{padding:22px}.nb-bb{border-bottom:2px solid var(--ink)}
.nb-foot{background:var(--violet-xl);border-radius:0 0 calc(var(--r-lg) - 2px) calc(var(--r-lg) - 2px)}
.nb-form-grid{display:grid;grid-template-columns:1fr;gap:16px}
.nb-sticky{position:sticky;top:96px}
@media(min-width:768px){.nb-grid-stats{grid-template-columns:repeat(4,1fr)}.nb-form-grid{grid-template-columns:1fr 1fr}}
@media(min-width:1024px){.nb-grid-main{grid-template-columns:2fr 1fr}.nb-span-2{grid-column:auto}}
@media(min-width:1280px){.nb-grid-rel{grid-template-columns:2fr 1fr}}

/* Page head */
.nb-page-head{margin-bottom:22px}
.nb-page-head h2{font-size:clamp(1.6rem,3vw,2.1rem);font-weight:800}
.nb-page-head p{margin-top:6px;color:rgba(23,19,46,.62)}
.nb-h3{font-size:1.05rem;font-weight:700}

/* Card */
.nb-card{background:var(--paper);border:2px solid var(--ink);border-radius:var(--r-lg);box-shadow:var(--sh);
    transition:transform .3s var(--ease),box-shadow .3s var(--ease)}
.nb-lift:hover{transform:translate(-3px,-3px);box-shadow:var(--sh-lg)}
.nb-card-head{display:flex;align-items:center;justify-content:space-between;padding:16px 22px;border-bottom:2px solid var(--ink)}
.nb-card-head h2,.nb-card-head h3{font-size:1rem;font-weight:700}
.tilt-card{will-change:transform;transform-style:preserve-3d}

/* Icon box */
.nb-ico{display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;width:38px;height:38px;border:2px solid var(--ink);border-radius:12px;box-shadow:var(--sh-sm);color:var(--ink)}
.nb-ico-lg{width:50px;height:50px;border-radius:15px}
.nb-ico-violet{background:var(--violet-l)}.nb-ico-mint{background:var(--mint)}.nb-ico-sun{background:var(--sun)}
.nb-ico-rose{background:var(--rose)}.nb-ico-sky{background:var(--sky)}.nb-ico-white{background:#fff}

/* Stats */
.nb-stat-head{display:flex;align-items:center;gap:12px;margin-bottom:18px}
.nb-grid-stats .nb-card{padding:20px}
.nb-stat-head p{font-size:.875rem;font-weight:600;color:rgba(23,19,46,.7)}
.nb-num{font-family:'Bricolage Grotesque',sans-serif;font-size:2.4rem;font-weight:800;line-height:1;letter-spacing:-.03em}
.nb-num-sm{font-size:1.5rem;font-family:'JetBrains Mono',monospace;font-weight:600;letter-spacing:-.02em;padding-top:10px}

/* Info */
.nb-info{display:grid;grid-template-columns:1fr 1fr;gap:26px 30px;padding:24px}
.nb-label{font-size:.78rem;font-weight:600;color:rgba(23,19,46,.55);margin-bottom:6px}
.nb-val{font-family:'JetBrains Mono',monospace;font-weight:600;font-size:.95rem;display:flex;align-items:center;gap:8px;min-width:0}
.nb-dot{width:11px;height:11px;border-radius:50%;border:2px solid var(--ink);flex-shrink:0}
.nb-dot.ok{background:var(--mint)}.nb-dot.warn{background:var(--sun)}
.nb-live{display:inline-flex;align-items:center;gap:7px;font-size:.78rem;font-weight:700;background:var(--violet-l);border:2px solid var(--ink);border-radius:999px;padding:3px 11px}
.nb-live i{width:8px;height:8px;border-radius:50%;background:var(--violet);animation:nb-pulse 1.8s ease-out infinite}

/* Buttons */
.nb-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;font:inherit;font-weight:700;font-size:.9rem;
    padding:11px 20px;color:#fff;background:var(--violet);border:2px solid var(--ink);border-radius:14px;box-shadow:var(--sh);
    cursor:pointer;text-decoration:none;position:relative;overflow:hidden;
    transition:transform .2s var(--ease),box-shadow .2s var(--ease),background .2s}
.nb-btn::after{content:"";position:absolute;inset:0;background:linear-gradient(105deg,transparent 35%,rgba(255,255,255,.45) 50%,transparent 65%);transform:translateX(-120%);transition:transform .7s var(--ease)}
.nb-btn:hover{transform:translate(-2px,-2px);box-shadow:6px 6px 0 var(--ink);background:var(--violet-d)}
.nb-btn:hover::after{transform:translateX(120%)}
.nb-btn:active{transform:translate(3px,3px);box-shadow:0 0 0 var(--ink)}
.nb-btn-ghost{background:#fff;color:var(--ink)}.nb-btn-ghost:hover{background:var(--violet-xl)}
.nb-btn-danger{background:var(--rose);color:var(--ink)}.nb-btn-danger:hover{background:#ff7398}
.nb-btn-white{background:#fff;color:var(--ink)}.nb-btn-white:hover{background:var(--sun)}
.nb-btn-ghost-white{background:rgba(255,255,255,.18);color:#fff;border-color:var(--ink);box-shadow:var(--sh)}
.nb-btn-ghost-white:hover{background:rgba(255,255,255,.32);box-shadow:6px 6px 0 var(--ink)}
.nb-btn-block{width:100%}

/* Inputs */
.nb-field-label{display:block;font-size:.82rem;font-weight:700;margin-bottom:8px}
.nb-input{width:100%;font:inherit;font-size:.92rem;color:var(--ink);background:#fff;border:2px solid var(--ink);border-radius:var(--r-md);padding:11px 14px;
    box-shadow:var(--sh-sm);transition:box-shadow .2s var(--ease),transform .2s var(--ease),background .2s}
.nb-input::placeholder{color:rgba(23,19,46,.38)}
.nb-input:focus{outline:none;background:var(--violet-xl);box-shadow:4px 4px 0 var(--violet);transform:translate(-1px,-1px)}
textarea.nb-input{resize:vertical;min-height:70px}
.nb-chip{display:inline-flex;align-items:center;font-family:'JetBrains Mono',monospace;font-size:.72rem;font-weight:600;background:var(--violet-l);border:2px solid var(--ink);border-radius:999px;padding:1px 9px;margin-left:6px;flex-shrink:0}
.nb-release .nb-chip{margin-left:0}

/* Checkbox */
.nb-check{display:flex;align-items:center;gap:10px;cursor:pointer;font-size:.85rem;font-weight:500;user-select:none}
.nb-check input{position:absolute;opacity:0}
.nb-box{width:22px;height:22px;border:2px solid var(--ink);border-radius:7px;background:#fff;display:inline-flex;align-items:center;justify-content:center;color:transparent;box-shadow:var(--sh-sm);transition:all .25s var(--spring)}
.nb-check input:checked + .nb-box{background:var(--violet);color:#fff;transform:rotate(-6deg) scale(1.05)}
.nb-check input:focus-visible + .nb-box{outline:3px solid var(--violet);outline-offset:2px}

/* Switch */
.nb-switch{position:relative;flex-shrink:0;cursor:pointer;margin-top:4px}
.nb-switch input[type=checkbox]{position:absolute;opacity:0;inset:0}
.nb-track{display:block;width:58px;height:32px;background:#fff;border:2px solid var(--ink);border-radius:999px;box-shadow:var(--sh-sm);transition:background .3s var(--ease)}
.nb-thumb{position:absolute;top:5px;left:5px;width:22px;height:22px;background:var(--ink);border-radius:50%;transition:transform .4s var(--spring),background .3s}
.nb-switch input:checked ~ .nb-track{background:var(--violet)}
.nb-switch input:checked ~ .nb-track .nb-thumb{transform:translateX(26px);background:#fff;box-shadow:0 0 0 2px var(--ink)}
.nb-switch input:focus-visible ~ .nb-track{outline:3px solid var(--violet);outline-offset:3px}

/* Quick actions + tools */
.nb-actions{padding:12px;display:flex;flex-direction:column;gap:10px}
.nb-action,.nb-tool{display:flex;align-items:center;gap:14px;width:100%;text-align:left;font:inherit;color:var(--ink);text-decoration:none;cursor:pointer;
    background:#fff;border:2px solid var(--ink);border-radius:var(--r-md);padding:11px 13px;
    transition:transform .25s var(--ease),box-shadow .25s var(--ease),background .2s}
.nb-action b,.nb-tool b{display:block;font-size:.9rem;font-weight:700}
.nb-action small,.nb-tool small{display:block;font-size:.76rem;color:rgba(23,19,46,.58);margin-top:2px}
.nb-action div{flex:1;min-width:0}
.nb-go{opacity:.35;transition:transform .3s var(--spring),opacity .2s}
.nb-action:hover,.nb-tool:hover{transform:translate(-2px,-2px);box-shadow:var(--sh);background:var(--violet-xl)}
.nb-action:hover .nb-go{opacity:1;transform:translate(3px,-3px)}
.nb-action:active,.nb-tool:active{transform:translate(2px,2px);box-shadow:none}
.nb-action-primary,.nb-tool-primary{background:var(--violet);color:#fff}
.nb-action-primary small,.nb-tool-primary small{color:rgba(255,255,255,.8)}
.nb-action-primary:hover,.nb-tool-primary:hover{background:var(--violet-d)}
.nb-tools{display:grid;grid-template-columns:1fr;gap:14px;padding:18px}
.nb-tool{padding:15px}
@media(min-width:640px){.nb-tools{grid-template-columns:1fr 1fr}}

/* APK */
.nb-apk{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding:22px}
.nb-apk-info{display:flex;align-items:center;gap:16px}
.nb-apk h4{font-size:1.05rem;font-weight:700;display:flex;align-items:center;flex-wrap:wrap;gap:4px}
.nb-drop{display:flex;flex-direction:column;align-items:center;gap:8px;text-align:center;padding:34px 20px;cursor:pointer;
    border:2px dashed var(--ink);border-radius:var(--r-lg);background:var(--violet-xl);transition:all .3s var(--ease)}
.nb-drop:hover,.nb-drop.over{background:var(--violet-l);transform:scale(1.012);border-color:var(--violet)}
.nb-drop.over .nb-ico{animation:nb-bob .7s ease-in-out infinite}
.nb-drop.has-file{border-style:solid;background:#e9fff9}
.nb-drop p{font-size:.9rem}

/* Releases */
.nb-release{display:flex;gap:16px;padding:20px;align-items:flex-start}
.nb-release-body{flex:1;min-width:0}
.nb-release h4{font-size:1.02rem;font-weight:700}
.nb-release .nb-muted{margin-top:8px}
.nb-trash{border:2px solid transparent;background:transparent;color:rgba(23,19,46,.45);border-radius:10px;padding:7px;cursor:pointer;transition:all .2s var(--ease)}
.nb-trash:hover{background:var(--rose);border-color:var(--ink);color:var(--ink);transform:rotate(-8deg) scale(1.08)}
.nb-empty{padding:48px 20px;text-align:center;display:flex;flex-direction:column;align-items:center;gap:10px}
.nb-empty h4{font-size:1.1rem}

/* Banner */
.nb-banner{position:relative;overflow:hidden;display:flex;align-items:center;justify-content:space-between;gap:24px;margin-bottom:22px;
    padding:clamp(22px,4vw,40px);color:#fff;border:2px solid var(--ink);border-radius:var(--r-lg);box-shadow:var(--sh-lg);
    background:linear-gradient(125deg,#7c5cff 0%,#9a80ff 55%,#b9a6ff 100%)}
.nb-banner::before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 1px 1px,rgba(255,255,255,.28) 1.4px,transparent 0) 0 0/20px 20px;
    mask-image:linear-gradient(100deg,transparent 30%,#000);-webkit-mask-image:linear-gradient(100deg,transparent 30%,#000)}
.nb-banner-blob{position:absolute;border-radius:50%;border:2px solid var(--ink);animation:nb-drift 9s ease-in-out infinite}
.nb-banner-blob.b1{width:170px;height:170px;background:var(--sun);right:-40px;top:-60px}
.nb-banner-blob.b2{width:90px;height:90px;background:var(--mint);right:150px;bottom:-34px;animation-delay:-4s}
.nb-banner-text{position:relative;z-index:2;max-width:34rem}
.nb-banner h1{color:#fff;font-size:clamp(1.7rem,4vw,2.6rem);font-weight:800;line-height:1.05;margin:14px 0 10px}
.nb-banner p{color:rgba(255,255,255,.92);line-height:1.6}
.nb-banner-actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:22px}
.nb-banner-art{position:relative;z-index:2;width:190px;height:150px;flex-shrink:0;display:none}
.nb-float{position:absolute;display:flex;align-items:center;justify-content:center;background:#fff;color:var(--ink);border:2px solid var(--ink);border-radius:18px;box-shadow:var(--sh);animation:nb-bob 4.5s ease-in-out infinite}
.nb-float.f1{width:62px;height:62px;left:0;top:14px}
.nb-float.f2{width:54px;height:54px;right:6px;top:0;animation-delay:-1.5s;background:var(--mint)}
.nb-float.f3{width:78px;height:78px;left:62px;bottom:0;animation-delay:-3s;background:var(--sun)}
@media(min-width:900px){.nb-banner-art{display:block}}
.nb-sticker{display:inline-block;font-family:'JetBrains Mono',monospace;font-size:.75rem;font-weight:600;background:var(--sun);color:var(--ink);border:2px solid var(--ink);border-radius:999px;padding:4px 13px;box-shadow:var(--sh-sm);transform:rotate(-2deg)}
.nb-sticker-white{background:#fff}

.nb-alert{display:flex;align-items:center;gap:12px;margin-bottom:22px;padding:14px 18px;background:var(--sun);border:2px solid var(--ink);border-radius:var(--r-md);box-shadow:var(--sh-sm);font-size:.9rem}
.nb-alert code{font-family:'JetBrains Mono',monospace;background:rgba(255,255,255,.7);padding:1px 6px;border-radius:6px}

/* Overlay + modal */
.nb-overlay{position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:18px;
    background:rgba(23,19,46,.45);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);opacity:0;transition:opacity .3s var(--ease)}
.nb-overlay[hidden]{display:none}
.nb-overlay.open{opacity:1}
.nb-modal{width:100%;max-width:30rem;max-height:92vh;overflow:auto;background:#fff;border:2px solid var(--ink);border-radius:26px;box-shadow:9px 9px 0 var(--ink);
    transform:translateY(30px) scale(.92) rotate(-1.2deg);transition:transform .55s var(--spring)}
.nb-overlay.open .nb-modal{transform:none}
.nb-modal-sm{max-width:24rem}
.nb-modal-top{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:var(--violet);border-bottom:2px solid var(--ink);border-radius:24px 24px 0 0}
.nb-modal-body{padding:26px;display:flex;flex-direction:column;gap:16px}
.nb-modal-sm .nb-modal-body{align-items:flex-start}
.nb-modal-body h2{font-size:1.7rem;font-weight:800;line-height:1.1}
.nb-modal-body h3{font-size:1.3rem;font-weight:800}
.nb-x{width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center;background:#fff;border:2px solid var(--ink);border-radius:11px;cursor:pointer;color:var(--ink);transition:transform .3s var(--spring)}
.nb-x:hover{transform:rotate(90deg) scale(1.1);background:var(--rose)}
.nb-wave{font-size:2.6rem;line-height:1;transform-origin:70% 80%;animation:nb-wave 2.2s ease-in-out .5s 2}
.nb-tips{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:10px}
.nb-tips li{display:flex;gap:13px;align-items:center;padding:11px 13px;border:2px solid var(--ink);border-radius:var(--r-md);background:var(--violet-xl);
    opacity:0;transform:translateX(-14px)}
.nb-overlay.open .nb-tips li{animation:nb-slide .6s var(--ease) forwards}
.nb-overlay.open .nb-tips li:nth-child(1){animation-delay:.25s}
.nb-overlay.open .nb-tips li:nth-child(2){animation-delay:.35s}
.nb-overlay.open .nb-tips li:nth-child(3){animation-delay:.45s}
.nb-tips strong{display:block;font-size:.9rem}
.nb-tips small{display:block;font-size:.78rem;color:rgba(23,19,46,.6);margin-top:2px;line-height:1.45}

/* Entrance: satu urutan saat tab tampil */
.nb-in{animation:nb-rise .75s var(--ease) backwards;animation-delay:var(--d,0ms)}

@keyframes nb-rise {
    from { opacity:0;transform:translateY(22px) scale(.98) }
    to { opacity:1;transform:none }
}
@keyframes nb-slide {
    to { opacity:1;transform:none }
}
@keyframes nb-pulse {
    0% { box-shadow:0 0 0 0 rgba(124,92,255,.6) }
    100% { box-shadow:0 0 0 9px rgba(124,92,255,0) }
}
@keyframes nb-bob {
    0%,100% { transform:translateY(0) rotate(-3deg) }
    50% { transform:translateY(-10px) rotate(3deg) }
}
@keyframes nb-drift {
    0%,100% { transform:translate(0,0) }
    50% { transform:translate(-14px,12px) }
}
@keyframes nb-wave {
    0%,60%,100% { transform:rotate(0) }
    10%,30% { transform:rotate(16deg) }
    20%,40% { transform:rotate(-10deg) }
    50% { transform:rotate(8deg) }
}

@media(max-width:640px){
    .nb-info{grid-template-columns:1fr}
    .nb-release{flex-wrap:wrap}
}
@media(prefers-reduced-motion:reduce){
    .nb *,.nb *::before,.nb *::after{animation:none!important;transition-duration:.01ms!important}
    .nb-tips li{opacity:1;transform:none}
}

/* ═══════════ Tambahan: utilitas, marquee, APK, System, Releases ═══════════ */
body.nb [hidden] { display: none !important; }
.nb-wrap-full { width: 100%; }
.nb-tone-violet { background: var(--violet-l); }
.nb-tone-sky    { background: var(--sky); }
.nb-tone-sun    { background: var(--sun); }
.nb-tone-rose   { background: var(--rose); }
.nb-tone-mint   { background: var(--mint); }
.nb-err { margin-top: 6px; font-size: .78rem; font-weight: 600; color: #c81e4d; }

/* Running text (peringatan debug) */
.nb-alert-run { overflow: hidden; }
.nb-marquee {
    flex: 1;
    min-width: 0;
    overflow: hidden;
    -webkit-mask-image: linear-gradient(90deg, transparent, #000 28px, #000 calc(100% - 28px), transparent);
    mask-image: linear-gradient(90deg, transparent, #000 28px, #000 calc(100% - 28px), transparent);
}
.nb-marquee-track { display: flex; width: max-content; animation: nb-marquee 34s linear infinite; }
.nb-marquee-track:hover { animation-play-state: paused; }
.nb-marquee-half { display: flex; align-items: center; flex-shrink: 0; }
.nb-marquee-item { white-space: nowrap; }
.nb-marquee-sep {
    width: 9px; height: 9px; margin: 0 30px; flex-shrink: 0;
    background: var(--ink); border-radius: 3px; transform: rotate(45deg);
}
@keyframes nb-marquee {
    from { transform: translateX(0); }
    to   { transform: translateX(-50%); }
}
@media (prefers-reduced-motion: reduce) {
    .nb-marquee-half + .nb-marquee-half, .nb-rep, .nb-marquee-sep { display: none !important; }
    .nb-marquee-item { white-space: normal; }
    .nb-marquee-track { width: auto; }
}

/* APK Manager (full width) */
.nb-apk-meta { display: flex; flex-wrap: wrap; gap: 10px; padding: 0 22px 20px; }
.nb-pill {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: .8rem; font-weight: 600; padding: 6px 13px;
    background: var(--violet-xl); border: 2px solid var(--ink); border-radius: 999px;
}
.nb-apk-form { display: grid; grid-template-columns: minmax(0, 1fr); gap: 24px; }
.nb-apk-drop { display: flex; flex-direction: column; }
.nb-apk-drop .nb-drop { flex: 1; min-height: 220px; justify-content: center; }
.nb-apk-fields { display: flex; flex-direction: column; justify-content: space-between; gap: 22px; }
@media (min-width: 1024px) {
    .nb-apk-form { grid-template-columns: minmax(0, 5fr) minmax(0, 7fr); }
}

/* System State (full width) */
.nb-state {
    display: inline-flex; align-items: center; gap: 7px; margin-left: 10px; vertical-align: middle;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: .72rem; font-weight: 700; letter-spacing: 0;
    padding: 1px 10px; background: #fff; border: 2px solid var(--ink); border-radius: 999px;
}
.nb-state i { width: 6px; height: 6px; box-sizing: content-box; border-radius: 50%; background: var(--mint); border: 2px solid var(--ink); }
.nb-state.on { background: var(--sun); }
.nb-state.on i { background: var(--ink); }
.nb-tools > form { display: flex; }
.nb-tools > form > .nb-tool { flex: 1; }
@media (min-width: 1280px) {
    .nb-tools { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .nb-tool { flex-direction: column; align-items: flex-start; gap: 16px; padding: 18px; }
}

/* Riwayat rilis */
.nb-rel-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 22px; }
.nb-rel-stats { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin-bottom: 28px; }
.nb-mini { display: flex; align-items: center; gap: 14px; padding: 16px 18px; }
.nb-mini p { font-size: .8rem; font-weight: 600; color: rgba(23,19,46,.6); }
.nb-mini b { display: block; margin-top: 2px; font-family: 'Bricolage Grotesque', sans-serif; font-size: 1.6rem; font-weight: 800; line-height: 1.1; letter-spacing: -.02em; }
.nb-mini b.nb-mono { font-family: 'JetBrains Mono', monospace; font-size: 1.2rem; font-weight: 600; letter-spacing: -.02em; }
.nb-rel-grid { display: grid; grid-template-columns: minmax(0, 1fr); gap: 24px; }
.nb-rel-aside { order: -1; }
@media (min-width: 900px) { .nb-rel-stats { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
@media (min-width: 1100px) {
    .nb-rel-grid { grid-template-columns: minmax(0, 1fr) 300px; }
    .nb-rel-aside { order: 0; }
}

.nb-fbtns { display: flex; flex-wrap: wrap; gap: 10px; padding: 16px; }
@media (min-width: 1100px) { .nb-fbtns { flex-direction: column; flex-wrap: nowrap; gap: 8px; } }
.nb-fbtn {
    display: flex; align-items: center; gap: 11px;
    font: inherit; font-size: .88rem; font-weight: 700; color: var(--ink); text-align: left;
    background: #fff; border: 2px solid var(--ink); border-radius: 14px; padding: 8px 12px; cursor: pointer;
    transition: transform .25s var(--ease), box-shadow .25s var(--ease), background .2s;
}
.nb-fbtn small {
    margin-left: auto; min-width: 30px; text-align: center; padding: 0 8px;
    font-family: 'JetBrains Mono', monospace; font-size: .75rem; font-weight: 600;
    background: var(--violet-xl); border: 2px solid var(--ink); border-radius: 999px;
}
.nb-fico { width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; border: 2px solid var(--ink); border-radius: 9px; flex-shrink: 0; }
.nb-fbtn:hover { transform: translate(-2px, -2px); box-shadow: var(--sh-sm); background: var(--violet-xl); }
.nb-fbtn.active { background: var(--violet); color: #fff; box-shadow: var(--sh-sm); }
.nb-fbtn.active small { background: #fff; color: var(--ink); }

/* Timeline */
.nb-tl { --tl: 64px; min-width: 0; }
.nb-tl-group + .nb-tl-group { margin-top: 32px; }
.nb-tl-month { margin: 0 0 18px; }
.nb-tl-month span {
    display: inline-block; padding: 6px 16px; color: #fff; background: var(--ink); border-radius: 999px;
    font-family: 'Bricolage Grotesque', sans-serif; font-size: .95rem; font-weight: 800; letter-spacing: -.01em;
}
.nb-tl-list { position: relative; list-style: none; margin: 0; padding: 0 0 0 var(--tl); }
.nb-tl-list::before { content: ""; position: absolute; left: 21px; top: -6px; bottom: -6px; border-left: 2px dashed var(--ink); }
.nb-tl-item { position: relative; margin-bottom: 18px; }
.nb-tl-item:last-child { margin-bottom: 0; }
.nb-tl-item::before { content: ""; position: absolute; left: calc(44px - var(--tl)); top: 36px; width: calc(var(--tl) - 44px); border-top: 2px solid var(--ink); }
.nb-tl-item .nb-tl-dot { position: absolute; left: calc(var(--tl) * -1); top: 14px; width: 44px; height: 44px; border-radius: 14px; }
.nb-tl-card { padding: 18px 20px; }
.nb-tl-card.is-latest { background: var(--violet-xl); box-shadow: var(--sh-lg); }
.nb-tl-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; }
.nb-tl-title { display: flex; align-items: center; flex-wrap: wrap; gap: 8px 10px; min-width: 0; }
.nb-tl-title h4 { font-size: 1.08rem; font-weight: 800; }
.nb-tl-title .nb-chip { margin-left: 0; }
.nb-tl-side { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.nb-tl-side time { font-size: .75rem; white-space: nowrap; }
.nb-badge { display: inline-flex; align-items: center; font-size: .72rem; font-weight: 700; padding: 1px 10px; border: 2px solid var(--ink); border-radius: 999px; }
.nb-sticker-sm { font-size: .68rem; padding: 2px 10px; box-shadow: none; }
.nb-cl { list-style: none; margin: 14px 0 0; padding: 0; display: flex; flex-direction: column; gap: 8px; }
.nb-cl li { position: relative; padding-left: 24px; font-size: .9rem; line-height: 1.55; color: rgba(23,19,46,.82); }
.nb-cl li::before { content: ""; position: absolute; left: 0; top: .42em; width: 11px; height: 11px; background: var(--violet-l); border: 2px solid var(--ink); border-radius: 4px; }
@media (max-width: 640px) {
    .nb-tl { --tl: 54px; }
    .nb-tl-top { flex-direction: column; gap: 8px; }
    .nb-tl-side { align-self: flex-end; margin-top: -30px; }
}

/* Segmented radio (jenis rilis) */
.nb-seg { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.nb-seg-i { position: relative; cursor: pointer; }
.nb-seg-i input { position: absolute; opacity: 0; inset: 0; cursor: pointer; }
.nb-seg-b {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 12px; font-size: .88rem; font-weight: 700;
    background: #fff; border: 2px solid var(--ink); border-radius: 14px; box-shadow: var(--sh-sm);
    transition: transform .25s var(--spring), background .2s, box-shadow .2s, color .2s;
}
.nb-seg-i:hover .nb-seg-b { background: var(--violet-xl); }
.nb-seg-i input:checked + .nb-seg-b { background: var(--violet); color: #fff; transform: translate(2px, 2px); box-shadow: none; }
.nb-seg-i input:focus-visible + .nb-seg-b { outline: 3px solid var(--violet); outline-offset: 3px; }

/* ═══════════ SHELL: body, sidebar, header ═══════════ */
html { scroll-behavior: smooth; }

body.nb {
    margin: 0;
    min-height: 100vh;
    display: flex;
    overflow-x: hidden;
    background:
        radial-gradient(circle at 1px 1px, rgba(23,19,46,.10) 1px, transparent 0) 0 0 / 22px 22px,
        var(--canvas);
    background-attachment: fixed;
}
body.nb ::selection { background: var(--violet); color: #fff; }

.nb-blob {
    position: fixed;
    z-index: -1;
    border-radius: 50%;
    pointer-events: none;
    will-change: transform;
    transition: transform .6s var(--ease);
}
.nb-blob.g1 { width: 460px; height: 460px; top: -160px; right: -120px; background: var(--violet-l); opacity: .9; }
.nb-blob.g2 { width: 260px; height: 260px; bottom: -110px; left: 22%; background: rgba(94,234,212,.35); }

/* Sidebar */
.nb-side {
    position: fixed;
    top: 0; bottom: 0; left: 0;
    width: 264px;
    z-index: 40;
    display: flex;
    flex-direction: column;
    background: var(--paper);
    border-right: 2px solid var(--ink);
    animation: nb-side-in .7s var(--ease) backwards;
    transition: transform .45s var(--ease), box-shadow .3s;
}
.nb-side-head {
    height: 72px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 18px;
    border-bottom: 2px solid var(--ink);
}
.nb-brand { display: flex; align-items: center; gap: 12px; }
.nb-logo {
    width: 40px; height: 40px;
    display: flex; align-items: center; justify-content: center;
    background: var(--violet);
    color: #fff;
    border: 2px solid var(--ink);
    border-radius: 13px;
    box-shadow: var(--sh-sm);
    transition: transform .4s var(--spring);
}
.nb-brand:hover .nb-logo { transform: rotate(-8deg) scale(1.06); }
.nb-brand b { font-family: 'Bricolage Grotesque', sans-serif; font-size: 1.1rem; font-weight: 800; letter-spacing: -.02em; }
.nb-brand small { display: block; font-size: .72rem; color: rgba(23,19,46,.55); margin-top: -1px; }
.nb-icon-btn.nb-side-close { display: none; }

.nb-nav { flex: 1; overflow-y: auto; padding: 14px 0 10px; }
.nb-nav-label { padding: 12px 22px 6px; font-size: .78rem; font-weight: 700; color: rgba(23,19,46,.5); }
.nb-nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    width: calc(100% - 24px);
    margin: 4px 12px;
    padding: 10px 13px;
    font: inherit;
    font-size: .9rem;
    font-weight: 600;
    color: var(--ink);
    text-align: left;
    text-decoration: none;
    background: transparent;
    border: 2px solid transparent;
    border-radius: 15px;
    cursor: pointer;
    transition: transform .25s var(--ease), background .2s, border-color .2s, box-shadow .25s var(--ease);
}
.nb-nav-item svg { width: 18px; height: 18px; flex-shrink: 0; }
.nb-nav-item:hover { background: var(--violet-xl); border-color: var(--ink); transform: translateX(3px); }
.nb-nav-item.active {
    background: var(--violet);
    color: #fff;
    border-color: var(--ink);
    box-shadow: var(--sh-sm);
}
.nb-nav-item.active:hover { transform: translate(2px, -1px); }

.nb-side-foot { padding: 14px; border-top: 2px solid var(--ink); }
.nb-me {
    display: flex; align-items: center; gap: 12px;
    padding: 10px;
    background: var(--violet-xl);
    border: 2px solid var(--ink);
    border-radius: 18px;
}
.nb-me img { width: 40px; height: 40px; border-radius: 50%; border: 2px solid var(--ink); object-fit: cover; flex-shrink: 0; }
.nb-me p { margin: 0; }
.nb-me .n { font-weight: 700; font-size: .88rem; }
.nb-me .r { font-family: 'JetBrains Mono', monospace; font-size: .72rem; color: rgba(23,19,46,.55); }

/* Main + header */
.nb-main { flex: 1; min-width: 0; display: flex; flex-direction: column; min-height: 100vh; margin-left: 264px; position: relative; }
.nb-top {
    position: sticky;
    top: 0;
    z-index: 30;
    height: 72px;
    margin: 0;
    padding: 0 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    background: var(--paper);
    border-bottom: 2px solid var(--ink);
    animation: nb-top-in .7s var(--ease) .08s backwards;
}
.nb-top-l { display: flex; align-items: center; gap: 14px; min-width: 0; }
.nb-top h1 { font-size: 1.25rem; font-weight: 800; margin: 0; white-space: nowrap; }
.nb-pulse-line { width: 64px; height: 20px; overflow: visible; }
.nb-pulse-line path {
    stroke: var(--violet); stroke-width: 2.4; fill: none; stroke-linecap: round; stroke-linejoin: round;
    stroke-dasharray: 120; stroke-dashoffset: 120;
    animation: nb-draw 2.8s linear infinite;
}
.nb-top-r { display: flex; align-items: center; gap: 12px; }
.nb-clock {
    font-family: 'JetBrains Mono', monospace; font-size: .8rem; font-weight: 600;
    padding: 6px 12px; background: var(--violet-xl); border: 2px solid var(--ink); border-radius: 999px;
}
.nb-env {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: .8rem; font-weight: 700;
    padding: 6px 13px; background: var(--mint); border: 2px solid var(--ink); border-radius: 999px; box-shadow: var(--sh-sm);
}
.nb-env i { width: 8px; height: 8px; border-radius: 50%; background: var(--ink); animation: nb-blink 1.6s ease-in-out infinite; }
.nb-icon-btn {
    width: 40px; height: 40px;
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--ink); background: #fff; border: 2px solid var(--ink); border-radius: 13px; box-shadow: var(--sh-sm);
    cursor: pointer; text-decoration: none;
    transition: transform .25s var(--spring), background .2s, box-shadow .2s;
}
.nb-icon-btn:hover { background: var(--rose); transform: translate(-2px, -2px); box-shadow: var(--sh); }
.nb-icon-btn:active { transform: translate(1px, 1px); box-shadow: none; }
.nb-burger { display: none; }

.nb-content { flex: 1; padding: 28px 28px 40px; }
.nb-alert-ok  { background: var(--mint); }
.nb-alert-err { background: var(--rose); }
.nb-flash { margin-bottom: 22px; display: flex; flex-direction: column; gap: 12px; }
.nb-flash .nb-alert { margin-bottom: 0; }

.nb-scrim {
    position: fixed; inset: 0; z-index: 35;
    background: rgba(23,19,46,.4);
    backdrop-filter: blur(4px);
    opacity: 0; pointer-events: none;
    transition: opacity .3s var(--ease);
}
.nb-scrim.show { opacity: 1; pointer-events: auto; }

/* Tab panels */
.tab-content { display: none; }
.tab-content.active { display: block; }

/* Scrollbar */
::-webkit-scrollbar { width: 12px; height: 12px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--violet); border: 3px solid var(--canvas); border-radius: 10px; }

@keyframes nb-side-in { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: none; } }
@keyframes nb-top-in  { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: none; } }
@keyframes nb-draw {
    0%   { stroke-dashoffset: 120; opacity: .2; }
    50%  { stroke-dashoffset: 0; opacity: 1; }
    100% { stroke-dashoffset: -120; opacity: .2; }
}
@keyframes nb-blink { 0%, 100% { opacity: 1; } 50% { opacity: .25; } }

/* Responsive: sidebar jadi drawer */
@media (max-width: 1023px) {
    .nb-side { transform: translateX(-101%); animation: none; }
    .nb-side.open { transform: none; box-shadow: 10px 0 0 rgba(23,19,46,.16); }
    .nb-icon-btn.nb-side-close { display: inline-flex; }
    .nb-main { margin-left: 0; }
    .nb-top { height: 64px; padding: 0 14px; }
    .nb-content { padding: 20px 14px 32px; }
    .nb-burger { display: inline-flex; }
    .nb-pulse-line, .nb-clock { display: none; }
}
@media (max-width: 480px) {
    .nb-env span { display: none; }
    .nb-env { padding: 6px 10px; }
}
@media (prefers-reduced-motion: reduce) {
    html { scroll-behavior: auto; }
    .nb-side, .nb-top, .nb-blob { animation: none !important; transition: none !important; }
}
    </style>
</head>

<body class="nb antialiased">

    <div class="nb-blob g1" data-parallax="22" aria-hidden="true"></div>
    <div class="nb-blob g2" data-parallax="14" aria-hidden="true"></div>
    <div class="nb-scrim" id="nb-scrim"></div>

    {{-- SIDEBAR --}}
    <aside class="nb-side" id="nb-side">
        <div class="nb-side-head">
            <div class="nb-brand">
                <span class="nb-logo"><i data-lucide="command" class="w-5 h-5"></i></span>
                <div>
                    <b>Dev Panel</b>
                    <small>ICB CT Presensi</small>
                </div>
            </div>
            <button type="button" class="nb-icon-btn nb-side-close" id="nb-side-close" aria-label="Tutup menu">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <nav class="nb-nav" aria-label="Menu utama">
            <div class="nb-nav-label">Ringkasan</div>
            <button type="button" onclick="switchTab('dashboard')" id="nav-dashboard" class="nb-nav-item active">
                <i data-lucide="layout-grid"></i> Dashboard
            </button>

            <div class="nb-nav-label">Kelola</div>
            <button type="button" onclick="switchTab('apk')" id="nav-apk" class="nb-nav-item">
                <i data-lucide="smartphone"></i> APK Manager
            </button>
            <button type="button" onclick="switchTab('system')" id="nav-system" class="nb-nav-item">
                <i data-lucide="shield-alert"></i> System State
            </button>
            <button type="button" onclick="switchTab('releases')" id="nav-releases" class="nb-nav-item">
                <i data-lucide="git-merge"></i> Releases
            </button>

            <div class="nb-nav-label">Tautan</div>
            <a href="https://github.com/vexalyn-dev/presensi-guru-icbct" target="_blank" rel="noopener" class="nb-nav-item">
                <i data-lucide="git-branch"></i> Repository
            </a>
            <a href="{{ url('/dashboard') }}" class="nb-nav-item">
                <i data-lucide="external-link"></i> Main App
            </a>
        </nav>

        <div class="nb-side-foot">
            <div class="nb-me">
                <img src="https://ui-avatars.com/api/?name=Vio+Atmajaya&background=7c5cff&color=fff&bold=true" alt="Vio Atmajaya">
                <div class="min-w-0">
                    <p class="n truncate">Vio Atmajaya</p>
                    <p class="r truncate">developer</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- MAIN --}}
    <main class="nb-main">
        <header class="nb-top">
            <div class="nb-top-l">
                <button type="button" class="nb-icon-btn nb-burger" id="nb-burger" aria-label="Buka menu">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <h1 id="header-title">Dashboard</h1>
                <svg class="nb-pulse-line" viewBox="0 0 64 20" aria-hidden="true"><path d="M2 10 H18 L23 3 L28 17 L33 6 L38 14 L43 10 H62"/></svg>
            </div>
            <div class="nb-top-r">
                <span class="nb-clock" id="live-clock">--:--:--</span>
                <span class="nb-env"><i></i><span>{{ ucfirst(app()->environment()) }}</span></span>
                <a href="{{ url()->previous() === url()->current() ? url('/') : url()->previous() }}"
                   class="nb-icon-btn" aria-label="Keluar dari panel">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </a>
            </div>
        </header>

        <div class="nb-content">
            @if(session('success') || session('error'))
                <div class="nb-flash">
                    @if(session('success'))
                        <div class="nb-alert nb-alert-ok nb-in" role="status">
                            <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="nb-alert nb-alert-err nb-in" role="alert">
                            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        const tabs = ['dashboard', 'apk', 'system', 'releases'];
        const titles = {
            dashboard: 'Dashboard',
            apk: 'APK Manager',
            system: 'System State',
            releases: 'Riwayat Rilis'
        };

        const sideEl  = document.getElementById('nb-side');
        const scrimEl = document.getElementById('nb-scrim');
        function toggleSide(open) {
            sideEl.classList.toggle('open', open);
            scrimEl.classList.toggle('show', open);
        }
        document.getElementById('nb-burger').addEventListener('click', () => toggleSide(true));
        document.getElementById('nb-side-close').addEventListener('click', () => toggleSide(false));
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
            el.textContent = new Date().toLocaleTimeString('id-ID', {
                hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
            }).replace(/\./g, ':') + ' WIB';
        }

        function initParallax() {
            if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            const blobs = document.querySelectorAll('[data-parallax]');
            window.addEventListener('mousemove', e => {
                const cx = e.clientX / innerWidth - .5, cy = e.clientY / innerHeight - .5;
                blobs.forEach(b => {
                    const s = parseFloat(b.dataset.parallax) || 10;
                    b.style.transform = `translate(${cx * s}px, ${cy * s}px)`;
                });
            }, { passive: true });
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
            let saved = null;
            try { saved = sessionStorage.getItem('dev_tab'); } catch (e) {}
            switchTab((location.hash || '').replace('#', '') || saved || 'dashboard');
            window.scrollTo(0, 0);
            tickClock();
            setInterval(tickClock, 1000);
            initParallax();
        });
    </script>
    @yield('scripts')
</body>
</html>