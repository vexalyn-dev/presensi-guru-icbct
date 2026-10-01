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
body.dp{
  --ink:#1c1a33; --mute:#6b6987; --faint:#9c9ab4; --line:#e9e7f3; --bg:#f7f6fb; --card:#fff;
  --v:#6d4aff; --v-d:#5a3ae6; --v-s:#f1edff; --v-m:#ddd5ff;
  --ok:#0ea271; --ok-s:#e3f8ef; --warn:#d98a00; --warn-s:#fff4d9; --bad:#e03c4f; --bad-s:#ffe9ec; --sky:#2f7be6; --sky-s:#e6f0ff;
  --r:14px; --sh:0 1px 2px rgba(28,26,51,.04),0 4px 16px rgba(28,26,51,.04); --sh-lg:0 12px 40px rgba(60,40,160,.14);
  --ease:cubic-bezier(.22,1,.36,1);
  font-family:'Geist',system-ui,sans-serif;color:var(--ink);background:var(--bg);margin:0;min-height:100vh;display:flex;overflow-x:hidden;
  -webkit-font-smoothing:antialiased;font-size:14px;line-height:1.5}
.dp *,.dp *::before,.dp *::after{box-sizing:border-box}
.dp h1,.dp h2,.dp h3,.dp h4,.dp p{margin:0}
.dp h2{font-size:1.5rem;font-weight:700;letter-spacing:-.02em}
.dp h3{font-size:1rem;font-weight:600;letter-spacing:-.01em}
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

/* Sidebar */
.side{position:fixed;inset:0 auto 0 0;width:256px;z-index:40;display:flex;flex-direction:column;background:#fff;border-right:1px solid var(--line);transition:transform .35s var(--ease)}
.side-head{height:64px;display:flex;align-items:center;justify-content:space-between;padding:0 18px;border-bottom:1px solid var(--line)}
.brand{display:flex;align-items:center;gap:11px}
.logo{width:34px;height:34px;border-radius:10px;display:grid;place-items:center;color:#fff;background:linear-gradient(135deg,#8567ff,#5a3ae6);box-shadow:0 4px 12px rgba(109,74,255,.35)}
.brand b{display:block;font-size:.95rem;font-weight:650;letter-spacing:-.01em}.brand small{display:block;font-size:.72rem;color:var(--faint);margin-top:-2px}
.nav{flex:1;overflow-y:auto;padding:14px 12px}
.nav-label{padding:14px 10px 6px;font-size:.72rem;font-weight:600;color:var(--faint)}
.nav-item{display:flex;align-items:center;gap:11px;width:100%;padding:9px 11px;margin-bottom:2px;font:inherit;font-weight:500;color:var(--mute);background:none;border:0;border-radius:10px;cursor:pointer;text-decoration:none;text-align:left;transition:background .15s,color .15s}
.nav-item svg{width:17px;height:17px;flex-shrink:0}
.nav-item:hover{background:var(--bg);color:var(--ink)}
.nav-item.active{background:var(--v-s);color:var(--v-d);font-weight:600}
.side-foot{padding:12px;border-top:1px solid var(--line)}
.me{display:flex;align-items:center;gap:11px;padding:8px;border-radius:12px;background:var(--bg)}
.me img{width:34px;height:34px;border-radius:50%;flex-shrink:0}.me .n{font-weight:600;font-size:.85rem}.me .r{font-size:.74rem;color:var(--faint)}

/* Main */
.main{flex:1;min-width:0;margin-left:256px;display:flex;flex-direction:column;min-height:100vh}
.top{position:sticky;top:0;z-index:30;height:64px;padding:0 32px;display:flex;align-items:center;justify-content:space-between;gap:14px;background:rgba(255,255,255,.82);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border-bottom:1px solid var(--line)}
.top h1{font-size:1.05rem;font-weight:650;letter-spacing:-.01em}
.top-l,.top-r{display:flex;align-items:center;gap:12px}
.clock{font-size:.78rem;font-weight:500;color:var(--mute);padding:6px 11px;background:var(--bg);border-radius:8px}
.env{display:inline-flex;align-items:center;gap:7px;font-size:.78rem;font-weight:600;padding:5px 11px;border-radius:999px;background:var(--ok-s);color:var(--ok)}
.env i{width:7px;height:7px;border-radius:50%;background:currentColor}
.ibtn{width:36px;height:36px;display:inline-grid;place-items:center;color:var(--mute);background:#fff;border:1px solid var(--line);border-radius:10px;cursor:pointer;transition:all .15s}
.ibtn:hover{color:var(--ink);border-color:var(--v-m);background:var(--v-s)}
.burger,.side-x{display:none}
.content{flex:1;padding:32px;max-width:1280px;width:100%;margin:0 auto}
.flash{display:flex;flex-direction:column;gap:10px;margin-bottom:22px}
.scrim{position:fixed;inset:0;z-index:35;background:rgba(28,26,51,.4);opacity:0;pointer-events:none;transition:opacity .25s}.scrim.show{opacity:1;pointer-events:auto}
.tab-content{display:none}.tab-content.active{display:block;animation:fade .35s var(--ease)}
@keyframes fade{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}

/* Card + parts */
.card{background:var(--card);border:1px solid var(--line);border-radius:var(--r);box-shadow:var(--sh)}
.card-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 22px;border-bottom:1px solid var(--line)}
.pad{padding:22px}.bb{border-bottom:1px solid var(--line)}.foot{background:var(--bg);border-radius:0 0 var(--r) var(--r)}
.ico{width:36px;height:36px;border-radius:10px;display:inline-grid;place-items:center;flex-shrink:0}
.ico-lg{width:46px;height:46px;border-radius:13px}
.t-violet{background:var(--v-s);color:var(--v-d)}.t-ok{background:var(--ok-s);color:var(--ok)}.t-warn{background:var(--warn-s);color:var(--warn)}.t-bad{background:var(--bad-s);color:var(--bad)}.t-sky{background:var(--sky-s);color:var(--sky)}
.chip{display:inline-flex;align-items:center;font-size:.74rem;font-weight:600;padding:2px 9px;border-radius:999px;background:var(--v-s);color:var(--v-d)}
.badge{display:inline-flex;align-items:center;font-size:.72rem;font-weight:600;padding:2px 9px;border-radius:999px}
.live{display:inline-flex;align-items:center;gap:7px;font-size:.76rem;font-weight:600;color:var(--ok)}
.live i{width:7px;height:7px;border-radius:50%;background:var(--ok);animation:pulse 2s infinite}
@keyframes pulse{0%{box-shadow:0 0 0 0 rgba(14,162,113,.5)}100%{box-shadow:0 0 0 8px rgba(14,162,113,0)}}
.state{display:inline-flex;align-items:center;gap:6px;margin-left:8px;font-size:.72rem;font-weight:600;padding:2px 9px;border-radius:999px;background:var(--ok-s);color:var(--ok);vertical-align:middle}
.state i{width:6px;height:6px;border-radius:50%;background:currentColor}.state.on{background:var(--warn-s);color:var(--warn)}

/* Buttons + inputs */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;font:inherit;font-weight:600;font-size:.88rem;padding:9px 16px;color:#fff;background:var(--v);border:1px solid var(--v);border-radius:10px;cursor:pointer;text-decoration:none;box-shadow:0 1px 2px rgba(90,58,230,.3);transition:background .15s,transform .15s,box-shadow .15s}
.btn:hover{background:var(--v-d);box-shadow:0 6px 16px rgba(109,74,255,.32)}.btn:active{transform:translateY(1px)}
.btn-ghost{background:#fff;color:var(--ink);border-color:var(--line);box-shadow:none}.btn-ghost:hover{background:var(--bg);box-shadow:none}
.btn-danger{background:var(--bad);border-color:var(--bad);box-shadow:none}.btn-danger:hover{background:#c92f41;box-shadow:none}
.btn-white{background:#fff;color:var(--v-d);border-color:#fff;box-shadow:none}.btn-white:hover{background:var(--v-s);box-shadow:none}
.btn-glass{background:rgba(255,255,255,.16);border-color:rgba(255,255,255,.35);box-shadow:none}.btn-glass:hover{background:rgba(255,255,255,.26);box-shadow:none}
.btn-block{width:100%}
.label{display:block;font-size:.82rem;font-weight:600;margin-bottom:7px}
.input{width:100%;font:inherit;color:var(--ink);background:#fff;border:1px solid var(--line);border-radius:10px;padding:10px 13px;transition:border-color .15s,box-shadow .15s}
.input::placeholder{color:var(--faint)}.input:focus{outline:none;border-color:var(--v);box-shadow:0 0 0 4px var(--v-s)}
textarea.input{resize:vertical;min-height:84px}
.err{margin-top:6px;font-size:.78rem;font-weight:500;color:var(--bad)}
.form-grid{display:grid;grid-template-columns:1fr;gap:16px}
.check{display:flex;align-items:center;gap:10px;cursor:pointer;font-size:.85rem;user-select:none}
.check input{position:absolute;opacity:0}
.box{width:20px;height:20px;border:1.5px solid var(--line);border-radius:6px;background:#fff;display:inline-grid;place-items:center;color:transparent;transition:all .15s}
.check input:checked+.box{background:var(--v);border-color:var(--v);color:#fff}
.check input:focus-visible+.box{outline:2px solid var(--v);outline-offset:2px}
.switch{position:relative;flex-shrink:0;cursor:pointer}
.switch input[type=checkbox]{position:absolute;opacity:0;inset:0;width:100%;height:100%;cursor:pointer}
.track{display:block;width:46px;height:26px;background:#d6d3e6;border-radius:999px;transition:background .25s}
.thumb{position:absolute;top:3px;left:3px;width:20px;height:20px;background:#fff;border-radius:50%;box-shadow:0 1px 3px rgba(0,0,0,.25);transition:transform .3s var(--ease)}
.switch input:checked~.track{background:var(--v)}.switch input:checked~.track .thumb{transform:translateX(20px)}
.switch input:focus-visible~.track{outline:2px solid var(--v);outline-offset:2px}
.seg{display:grid;grid-template-columns:repeat(2,1fr);gap:8px}
.seg-i{position:relative;cursor:pointer}.seg-i input{position:absolute;opacity:0;inset:0;cursor:pointer}
.seg-b{display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;font-weight:600;font-size:.85rem;color:var(--mute);background:#fff;border:1px solid var(--line);border-radius:10px;transition:all .15s}
.seg-i:hover .seg-b{background:var(--bg)}
.seg-i input:checked+.seg-b{color:var(--v-d);background:var(--v-s);border-color:var(--v)}
.seg-i input:focus-visible+.seg-b{outline:2px solid var(--v);outline-offset:2px}

/* Alerts */
.alert{display:flex;align-items:center;gap:12px;padding:13px 16px;border-radius:12px;font-size:.88rem;border:1px solid transparent}
.alert-ok{background:var(--ok-s);color:#08724f;border-color:#bdecd9}.alert-err{background:var(--bad-s);color:#b02538;border-color:#ffc9d0}
.alert-warn{background:var(--warn-s);color:#8a5a00;border-color:#ffe3a0;margin-bottom:22px}
.alert code{font-family:'Geist Mono',monospace;background:rgba(255,255,255,.7);padding:1px 6px;border-radius:5px}

/* Dashboard */
.hero{position:relative;overflow:hidden;display:flex;align-items:center;justify-content:space-between;gap:24px;margin-bottom:22px;padding:clamp(24px,4vw,40px);color:#fff;border-radius:20px;background:linear-gradient(120deg,#5a3ae6 0%,#7b5cff 55%,#a48dff 100%);box-shadow:var(--sh-lg)}
.hero::before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 1px 1px,rgba(255,255,255,.2) 1px,transparent 0) 0 0/22px 22px;-webkit-mask-image:linear-gradient(100deg,transparent 40%,#000);mask-image:linear-gradient(100deg,transparent 40%,#000)}
.hero::after{content:"";position:absolute;width:320px;height:320px;right:-80px;top:-120px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,.28),transparent 70%)}
.hero-text{position:relative;z-index:1;max-width:36rem}
.hero .pill{display:inline-block;font-size:.74rem;font-weight:600;padding:4px 12px;border-radius:999px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3)}
.hero h1{font-size:clamp(1.6rem,3.4vw,2.3rem);font-weight:700;letter-spacing:-.03em;line-height:1.12;margin:14px 0 8px;color:#fff}
.hero p{color:rgba(255,255,255,.9)}
.hero-actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:22px}
.hero-art{position:relative;z-index:1;display:none;gap:12px}
.hero-art span{width:58px;height:58px;display:grid;place-items:center;border-radius:16px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.3);backdrop-filter:blur(6px)}
.hero-art span:nth-child(2){transform:translateY(16px)}
@media(min-width:900px){.hero-art{display:flex}}
.stats{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:22px}
.stat{padding:18px 20px}.stat-head{display:flex;align-items:center;gap:11px;margin-bottom:14px;color:var(--mute);font-weight:500;font-size:.85rem}
.stat .ico{width:32px;height:32px;border-radius:9px}
.num{font-size:2rem;font-weight:700;line-height:1;letter-spacing:-.03em}.num-sm{font-size:1.4rem;font-family:'Geist Mono',monospace;font-weight:600;padding-top:7px}
.grid-main{display:grid;grid-template-columns:1fr;gap:20px}
.info{display:grid;grid-template-columns:1fr 1fr;gap:0}
.info>div{padding:18px 22px;border-bottom:1px solid var(--line)}.info>div:nth-child(odd){border-right:1px solid var(--line)}
.info>div:nth-last-child(-n+2){border-bottom:0}
.info .l{font-size:.78rem;color:var(--faint);font-weight:500;margin-bottom:4px}.info .v{font-weight:600;display:flex;align-items:center;gap:8px;min-width:0}
.dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}.dot.ok{background:var(--ok)}.dot.warn{background:var(--warn)}
.actions{padding:10px;display:flex;flex-direction:column;gap:4px}
.action,.tool{display:flex;align-items:center;gap:13px;width:100%;padding:10px 12px;font:inherit;color:var(--ink);text-align:left;text-decoration:none;cursor:pointer;background:none;border:1px solid transparent;border-radius:12px;transition:background .15s,border-color .15s}
.action b,.tool b{display:block;font-size:.88rem;font-weight:600}.action small,.tool small{display:block;font-size:.76rem;color:var(--mute);margin-top:1px}
.action>div{flex:1;min-width:0}.go{color:var(--faint);transition:transform .2s,color .2s}
.action:hover,.tool:hover{background:var(--bg);border-color:var(--line)}.action:hover .go{color:var(--v);transform:translate(2px,-2px)}

/* APK */
.apk{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding:20px 22px}
.apk-info{display:flex;align-items:center;gap:15px}.apk h4{font-size:1rem;font-weight:600;display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.apk-meta{display:flex;flex-wrap:wrap;gap:8px;padding:0 22px 20px}
.pill{display:inline-flex;align-items:center;gap:7px;font-size:.8rem;font-weight:500;padding:5px 12px;background:var(--bg);border-radius:999px;color:var(--mute)}
.apk-form{display:grid;grid-template-columns:minmax(0,1fr);gap:24px}
.apk-drop{display:flex;flex-direction:column}.apk-drop .drop{flex:1;min-height:220px}
.apk-fields{display:flex;flex-direction:column;justify-content:space-between;gap:22px}
.drop{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;text-align:center;padding:30px 20px;cursor:pointer;border:1.5px dashed var(--v-m);border-radius:var(--r);background:var(--v-s);transition:all .2s var(--ease)}
.drop:hover,.drop.over{background:#e8e1ff;border-color:var(--v)}
.drop.has-file{border-style:solid;border-color:var(--ok);background:var(--ok-s)}
@media(min-width:1024px){.apk-form{grid-template-columns:minmax(0,5fr) minmax(0,7fr)}}

/* Tools */
.tools{display:grid;grid-template-columns:1fr;gap:12px;padding:16px}
.tools .tool{border-color:var(--line);padding:15px}
@media(min-width:640px){.tools{grid-template-columns:1fr 1fr}}
@media(min-width:1280px){.tools{grid-template-columns:repeat(4,minmax(0,1fr))}.tools .tool{flex-direction:column;align-items:flex-start;gap:14px}}

/* Releases */
.rel-head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:22px}
.rel-stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin-bottom:26px}
.mini{display:flex;align-items:center;gap:13px;padding:15px 18px}.mini p{font-size:.8rem;color:var(--mute);font-weight:500}
.mini b{display:block;font-size:1.45rem;font-weight:700;letter-spacing:-.02em;line-height:1.15}.mini b.mono{font-size:1.15rem}
.rel-grid{display:grid;grid-template-columns:minmax(0,1fr);gap:24px}.rel-aside{order:-1}
@media(min-width:900px){.rel-stats{grid-template-columns:repeat(4,minmax(0,1fr))}}
@media(min-width:1100px){.rel-grid{grid-template-columns:minmax(0,1fr) 280px}.rel-aside{order:0}}
.sticky{position:sticky;top:88px}
.fbtns{display:flex;flex-wrap:wrap;gap:8px;padding:14px}
@media(min-width:1100px){.fbtns{flex-direction:column;flex-wrap:nowrap;gap:4px}}
.fbtn{display:flex;align-items:center;gap:10px;font:inherit;font-weight:500;color:var(--mute);text-align:left;background:none;border:0;border-radius:10px;padding:8px 10px;cursor:pointer;transition:background .15s,color .15s}
.fbtn small{margin-left:auto;min-width:26px;text-align:center;padding:0 7px;font-family:'Geist Mono',monospace;font-size:.74rem;background:var(--bg);border-radius:999px}
.fico{width:26px;height:26px;display:inline-grid;place-items:center;border-radius:8px;flex-shrink:0}
.fbtn:hover{background:var(--bg);color:var(--ink)}.fbtn.active{background:var(--v-s);color:var(--v-d);font-weight:600}.fbtn.active small{background:#fff}
.tl-group+.tl-group{margin-top:28px}
.tl-month{margin-bottom:14px;font-size:.82rem;font-weight:600;color:var(--mute)}
.tl-list{position:relative;list-style:none;margin:0;padding:0 0 0 52px}
.tl-list::before{content:"";position:absolute;left:19px;top:0;bottom:0;border-left:1px solid var(--line)}
.tl-item{position:relative;margin-bottom:14px}.tl-item:last-child{margin-bottom:0}
.tl-dot{position:absolute;left:-52px;top:14px;width:38px;height:38px;border:3px solid var(--bg)}
.tl-card{padding:18px 20px}.tl-card.latest{border-color:var(--v-m);box-shadow:0 0 0 3px var(--v-s),var(--sh)}
.tl-top{display:flex;align-items:flex-start;justify-content:space-between;gap:14px}
.tl-title{display:flex;align-items:center;flex-wrap:wrap;gap:8px;min-width:0}.tl-title h4{font-size:1rem;font-weight:650}
.tl-side{display:flex;align-items:center;gap:6px;flex-shrink:0}.tl-side time{font-size:.76rem;color:var(--faint);white-space:nowrap}
.trash{border:0;background:none;color:var(--faint);border-radius:8px;padding:6px;cursor:pointer;transition:all .15s}.trash:hover{background:var(--bad-s);color:var(--bad)}
.cl{list-style:none;margin:12px 0 0;padding:0;display:flex;flex-direction:column;gap:7px}
.cl li{position:relative;padding-left:20px;color:#3f3d5a;line-height:1.55}
.cl li::before{content:"";position:absolute;left:3px;top:.55em;width:6px;height:6px;border-radius:50%;background:var(--v)}
.empty{padding:48px 20px;text-align:center;display:flex;flex-direction:column;align-items:center;gap:10px}.empty h4{font-size:1.05rem;font-weight:600}

/* Modal */
.overlay{position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:18px;background:rgba(28,26,51,.45);backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);opacity:0;transition:opacity .25s}
.overlay.open{opacity:1}
.modal{width:100%;max-width:30rem;max-height:92vh;overflow:auto;background:#fff;border-radius:20px;box-shadow:0 30px 80px rgba(28,26,51,.3);transform:translateY(16px) scale(.97);transition:transform .35s var(--ease)}
.overlay.open .modal{transform:none}.modal-sm{max-width:24rem}
.modal-top{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--line)}
.modal-top b{font-weight:600}
.modal-body{padding:24px;display:flex;flex-direction:column;gap:16px}
.modal-sm .modal-body{align-items:flex-start}
.modal-body h2{font-size:1.5rem}.modal-body h3{font-size:1.2rem;font-weight:650}
.tips{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:8px}
.tips li{display:flex;gap:13px;align-items:center;padding:11px 13px;border:1px solid var(--line);border-radius:12px}
.tips strong{display:block;font-size:.88rem}.tips small{display:block;font-size:.78rem;color:var(--mute);margin-top:1px}

::-webkit-scrollbar{width:10px;height:10px}::-webkit-scrollbar-thumb{background:#d6d3e6;border:2px solid var(--bg);border-radius:10px}

@media(min-width:768px){.stats{grid-template-columns:repeat(4,1fr)}.form-grid{grid-template-columns:1fr 1fr}}
@media(min-width:1024px){.grid-main{grid-template-columns:2fr 1fr}}
@media(max-width:1023px){
  .side{transform:translateX(-101%)}.side.open{transform:none;box-shadow:12px 0 40px rgba(28,26,51,.18)}
  .side-x,.burger{display:inline-grid}.main{margin-left:0}.top{height:60px;padding:0 14px}.content{padding:20px 14px 32px}.clock{display:none}
}
@media(max-width:640px){
  .info{grid-template-columns:1fr}.info>div:nth-child(odd){border-right:0}.info>div:nth-last-child(2){border-bottom:1px solid var(--line)}
  .tl-list{padding-left:46px}.tl-dot{left:-46px}.tl-top{flex-direction:column;gap:6px}
}
@media(max-width:480px){.env span{display:none}.env{padding:5px 9px}}
@media(prefers-reduced-motion:reduce){.dp *,.dp *::before,.dp *::after{animation:none!important;transition-duration:.01ms!important}}
</style>
</head>

<body class="dp antialiased">
<div class="scrim" id="scrim"></div>

{{-- SIDEBAR --}}
<aside class="side" id="side">
    <div class="side-head">
        <div class="brand">
            <span class="logo"><i data-lucide="command" style="width:18px;height:18px"></i></span>
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
            <img src="https://ui-avatars.com/api/?name=Vio+Atmajaya&background=6d4aff&color=fff&bold=true" alt="Vio Atmajaya">
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