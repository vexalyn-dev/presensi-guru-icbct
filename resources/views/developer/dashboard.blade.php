<!DOCTYPE html>
<html lang="id" id="devHtml" class="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Dev Panel · ICB CT</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0;font-family:'Inter',system-ui,sans-serif}
html{scroll-behavior:smooth}
:root{
  --bg:#f8f7ff;--bg-card:#fff;--bg-card2:#f3f0ff;--bg-input:#f5f3ff;
  --border:rgba(124,58,237,.14);--border2:rgba(124,58,237,.25);
  --txt:#1a0533;--txt2:#4b5563;--muted:#9ca3af;
  --purple:#7c3aed;--purple2:#a855f7;--accent:#ede9fe;
  --shadow:rgba(124,58,237,.12);
}
.dark{
  --bg:#080614;--bg-card:#0f0c1e;--bg-card2:#16122b;--bg-input:#1a1630;
  --border:rgba(168,85,247,.18);--border2:rgba(168,85,247,.35);
  --txt:#f0e8ff;--txt2:#a78bfa;--muted:#5b4f8a;
  --purple:#a855f7;--purple2:#c084fc;--accent:rgba(124,58,237,.2);
  --shadow:rgba(0,0,0,.4);
}
body{background:var(--bg);color:var(--txt);transition:background .3s,color .3s;min-height:100vh}
.card{background:var(--bg-card);border:1.5px solid var(--border);border-radius:1.25rem;transition:all .2s}
.card:hover{box-shadow:0 8px 32px var(--shadow);transform:translateY(-1px)}
.inp{background:var(--bg-input);border:1.5px solid var(--border);border-radius:.75rem;padding:10px 14px;color:var(--txt);font-size:13px;width:100%;outline:none;transition:border .2s}
.inp:focus{border-color:var(--purple)}
.btn{display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:.75rem;font-size:13px;font-weight:700;cursor:pointer;border:none;transition:all .2s}
.btn-p{background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff}
.btn-p:hover{box-shadow:0 8px 24px rgba(124,58,237,.4);transform:translateY(-1px)}
.btn-g{background:var(--bg-card2);border:1.5px solid var(--border);color:var(--txt2)}
.btn-g:hover{border-color:var(--purple);color:var(--purple)}
.badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700}
.section-title{font-size:18px;font-weight:800;color:var(--txt);margin-bottom:4px}
.section-sub{font-size:12px;color:var(--muted)}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
@media(max-width:640px){.grid2{grid-template-columns:1fr}.hide-sm{display:none}}
/* slider */
.dslide{position:absolute;inset:0;opacity:0;transform:translateX(28px);transition:opacity .65s cubic-bezier(.16,1,.3,1),transform .65s cubic-bezier(.16,1,.3,1)}
.dslide.active{opacity:1;transform:translateX(0)}
.dslide.exit{opacity:0;transform:translateX(-20px);transition:opacity .4s ease-in,transform .4s ease-in}
/* orb */
.orb{position:absolute;border-radius:50%;filter:blur(55px);pointer-events:none;animation:orb 7s ease-in-out infinite}
@keyframes orb{0%,100%{opacity:.35}50%{opacity:.65}}
/* loading */
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes lbar{0%{width:0}100%{width:100%}}
/* main anim */
@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.fu{animation:fadeUp .5s ease forwards}
.fu1{animation-delay:.05s;opacity:0}
.fu2{animation-delay:.12s;opacity:0}
.fu3{animation-delay:.2s;opacity:0}
.fu4{animation-delay:.28s;opacity:0}
.fu5{animation-delay:.36s;opacity:0}
/* maintenance toggle */
.tog-track{width:46px;height:26px;border-radius:99px;transition:background .3s;position:relative;cursor:pointer}
.tog-thumb{position:absolute;top:3px;left:3px;width:20px;height:20px;background:#fff;border-radius:50%;transition:all .3s;box-shadow:0 2px 5px rgba(0,0,0,.2)}
.tog-track.on .tog-thumb{left:23px}
.tog-track.on{background:#ef4444}
.tog-track.off{background:var(--border2)}
</style>
</head>
<body>

{{-- ══ LOADING SCREEN ══ --}}
<div id="devLoader" style="position:fixed;inset:0;z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:20px;background:var(--bg)">
  <div style="position:relative;width:64px;height:64px">
    <div style="position:absolute;inset:0;border-radius:50%;border:3px solid var(--border);border-top-color:#7c3aed;animation:spin .9s linear infinite"></div>
    <div style="position:absolute;top:9px;left:9px;right:9px;bottom:9px;border-radius:50%;border:3px solid transparent;border-bottom-color:#a855f7;animation:spin .7s linear infinite reverse"></div>
    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center">
      <i data-lucide="code-2" style="width:22px;height:22px;color:#7c3aed"></i>
    </div>
  </div>
  <div style="text-align:center">
    <p style="font-size:14px;font-weight:700;color:var(--txt)" id="loaderMsg">Memuat Dev Panel…</p>
    <p style="font-size:11px;color:var(--muted);margin-top:3px">ICB CT · Vexalyn Dev</p>
  </div>
  <div style="width:200px;height:3px;background:var(--bg-card2);border-radius:99px;overflow:hidden">
    <div id="loaderBar" style="height:100%;width:0;background:linear-gradient(90deg,#7c3aed,#a855f7);border-radius:99px;transition:width .08s linear"></div>
  </div>
</div>

{{-- ══ WELCOME MODAL ══ --}}
<div id="devModal" style="display:none;position:fixed;inset:0;z-index:8888;background:rgba(0,0,0,.55);backdrop-filter:blur(10px);align-items:center;justify-content:center;padding:16px">
  <div id="devModalBox" style="background:var(--bg-card);border:1.5px solid var(--border2);border-radius:1.5rem;max-width:420px;width:100%;overflow:hidden;transform:translateY(30px) scale(.95);opacity:0;transition:all .45s cubic-bezier(.16,1,.3,1)">
    <div style="height:4px;background:linear-gradient(90deg,#7c3aed,#a855f7,#c084fc)"></div>
    <div style="padding:32px 28px">
      <div style="width:56px;height:56px;background:linear-gradient(135deg,#7c3aed,#a855f7);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;box-shadow:0 8px 24px rgba(124,58,237,.35)">
        <i data-lucide="code-2" style="width:28px;height:28px;color:#fff"></i>
      </div>
      <h2 style="font-size:1.3rem;font-weight:800;color:var(--txt);text-align:center;margin-bottom:6px" id="modalTitle">Selamat Datang, Developer! 👋</h2>
      <p style="font-size:13px;color:var(--txt2);text-align:center;margin-bottom:20px;line-height:1.6" id="modalSub">Kamu mengakses <strong style="color:var(--purple)">Developer Dashboard</strong> ICB CT via URL rahasia.</p>
      @if($latestUpdate)
      <div style="background:var(--accent);border:1.5px solid var(--border2);border-radius:1rem;padding:14px 16px;margin-bottom:18px">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
          <span class="badge" style="background:rgba(124,58,237,.2);color:var(--purple);border:1px solid var(--border2);text-transform:uppercase">{{ $latestUpdate->type }}</span>
          <span style="font-size:12px;font-weight:700;color:var(--txt)">v{{ $latestUpdate->version }}</span>
          <span style="font-size:11px;color:var(--muted);margin-left:auto">{{ $latestUpdate->created_at->diffForHumans() }}</span>
        </div>
        <p style="font-size:13px;font-weight:600;color:var(--txt);margin-bottom:4px">{{ $latestUpdate->title }}</p>
        <p style="font-size:12px;color:var(--txt2);line-height:1.55;white-space:pre-line">{{ \Illuminate\Support\Str::limit($latestUpdate->content,140) }}</p>
      </div>
      @endif
      <button onclick="closeModal()" class="btn btn-p" style="width:100%;justify-content:center">
        <i data-lucide="check" style="width:16px;height:16px"></i>
        <span id="modalBtn">Masuk ke Dashboard</span>
      </button>
    </div>
  </div>
</div>

{{-- ══ TOPBAR ══ --}}
<header id="topbar" style="position:sticky;top:0;z-index:100;background:rgba(248,247,255,.88);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border-bottom:1px solid var(--border);padding:0 24px">
  <div style="max-width:1100px;margin:0 auto;height:58px;display:flex;align-items:center;justify-content:space-between;gap:12px">
    <div style="display:flex;align-items:center;gap:10px">
      <div style="width:34px;height:34px;background:linear-gradient(135deg,#7c3aed,#a855f7);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <i data-lucide="code-2" style="width:17px;height:17px;color:#fff"></i>
      </div>
      <div style="line-height:1.2">
        <p style="font-size:13px;font-weight:800;color:var(--txt)">Dev Panel</p>
        <p style="font-size:10px;color:var(--muted)">ICB CT · Vexalyn Dev</p>
      </div>
    </div>
    <nav style="display:flex;align-items:center;gap:2px" class="hide-sm">
      @foreach(['dashboard'=>'Dashboard','section-apk'=>'APK','section-maintenance'=>'Maintenance','section-updates'=>'Updates'] as $sid=>$lbl)
      <button onclick="document.getElementById('{{$sid}}').scrollIntoView({behavior:'smooth',block:'start'})"
        style="padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;color:var(--txt2);cursor:pointer;border:none;background:transparent;transition:all .15s"
        onmouseover="this.style.background='var(--accent)';this.style.color='var(--purple)'"
        onmouseout="this.style.background='transparent';this.style.color='var(--txt2)'">{{$lbl}}</button>
      @endforeach
    </nav>
    <div style="display:flex;align-items:center;gap:8px">
      {{-- Theme --}}
      <button id="themBtn" onclick="cycleTheme()" title="Toggle theme"
        style="width:34px;height:34px;border-radius:8px;border:1.5px solid var(--border);background:var(--bg-card);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--txt2);transition:all .2s">
        <i id="themIco" data-lucide="sun" style="width:15px;height:15px"></i>
      </button>
      {{-- Lang --}}
      <button id="langBtn" onclick="toggleLang()"
        style="height:34px;padding:0 12px;border-radius:8px;border:1.5px solid var(--border);background:var(--bg-card);cursor:pointer;font-size:11px;font-weight:700;color:var(--txt2);transition:all .2s">
        <span id="langLabel">ID</span>
      </button>
      {{-- Profile --}}
      <div style="position:relative">
        <button onclick="document.getElementById('profMenu').style.display=document.getElementById('profMenu').style.display==='block'?'none':'block'"
          style="display:flex;align-items:center;gap:7px;height:34px;padding:0 10px;border-radius:8px;border:1.5px solid var(--border);background:var(--bg-card);cursor:pointer;transition:all .2s">
          <div style="width:24px;height:24px;border-radius:6px;background:linear-gradient(135deg,#7c3aed,#a855f7);display:flex;align-items:center;justify-content:center">
            <i data-lucide="user" style="width:12px;height:12px;color:#fff"></i>
          </div>
          <span style="font-size:12px;font-weight:700;color:var(--txt)" class="hide-sm">Vio</span>
          <i data-lucide="chevron-down" style="width:11px;height:11px;color:var(--muted)"></i>
        </button>
        <div id="profMenu" style="display:none;position:absolute;right:0;top:calc(100% + 8px);background:var(--bg-card);border:1.5px solid var(--border);border-radius:12px;padding:8px;min-width:190px;box-shadow:0 16px 40px var(--shadow);z-index:200">
          <div style="padding:10px 12px;border-bottom:1px solid var(--border);margin-bottom:6px">
            <p style="font-size:12px;font-weight:700;color:var(--txt)">Vio Atmajaya Saputra</p>
            <p style="font-size:10px;color:var(--muted)">vexalyndev.my.id</p>
          </div>
          @foreach([['external-link','Profil Developer','https://vexalyndev.my.id'],['layout-dashboard','Dashboard App',url('/dashboard')],['settings','Settings',url('/settings')],['github','GitHub','https://github.com/vexalyn-dev']] as [$ico,$lbl,$href])
          <a href="{{$href}}" target="_blank" style="display:flex;align-items:center;gap:9px;padding:8px 12px;border-radius:8px;font-size:12px;font-weight:500;color:var(--txt2);text-decoration:none;transition:all .15s"
             onmouseover="this.style.background='var(--accent)';this.style.color='var(--purple)'"
             onmouseout="this.style.background='transparent';this.style.color='var(--txt2)'">
            <i data-lucide="{{$ico}}" style="width:13px;height:13px"></i>{{$lbl}}
          </a>
          @endforeach
        </div>
      </div>
      <span class="badge hide-sm" style="background:var(--accent);color:var(--purple);border:1px solid var(--border2)">
        <i data-lucide="shield-check" style="width:11px;height:11px"></i> SECRET
      </span>
    </div>
  </div>
</header>

{{-- ══ MAIN ══ --}}
<main style="max-width:1100px;margin:0 auto;padding:32px 20px;display:flex;flex-direction:column;gap:32px" id="mainWrap" class="fu">

@if(session('success'))
<div style="display:flex;align-items:center;gap:10px;padding:14px 18px;border-radius:12px;background:rgba(34,197,94,.1);border:1.5px solid rgba(34,197,94,.3);color:#15803d" class="fu fu1">
  <i data-lucide="check-circle-2" style="width:17px;height:17px;flex-shrink:0"></i>
  <span style="font-size:13px;font-weight:600">{{ session('success') }}</span>
</div>
@endif
@if($errors->any())
<div style="padding:14px 18px;border-radius:12px;background:rgba(239,68,68,.1);border:1.5px solid rgba(239,68,68,.3)">
  @foreach($errors->all() as $e)<p style="font-size:12px;color:#dc2626">• {{$e}}</p>@endforeach
</div>
@endif

{{-- ── DASHBOARD ── --}}
<div id="dashboard" class="fu fu1" style="display:flex;flex-direction:column;gap:20px">

  {{-- Welcome Card --}}
  <div style="position:relative;overflow:hidden;border-radius:1.5rem;padding:32px 36px;background:linear-gradient(135deg,#4c1d95 0%,#7c3aed 55%,#9333ea 100%);min-height:160px">
    <div class="orb" style="width:220px;height:220px;background:rgba(192,132,252,.28);top:-80px;right:-60px"></div>
    <div class="orb" style="width:130px;height:130px;background:rgba(124,58,237,.35);bottom:-40px;left:35%;animation-delay:3s"></div>
    <div style="position:relative;z-index:1;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:20px">
      <div>
        <div style="display:inline-flex;align-items:center;gap:8px;padding:4px 12px;border-radius:99px;background:rgba(255,255,255,.12);color:rgba(255,255,255,.75);font-size:11px;font-weight:700;margin-bottom:14px;border:1px solid rgba(255,255,255,.15)">
          <i data-lucide="shield-check" style="width:12px;height:12px"></i> DEVELOPER ACCESS
        </div>
        <h1 style="font-size:2rem;font-weight:900;color:#fff;line-height:1.15;margin-bottom:8px">Selamat datang,<br><span style="color:#e9d5ff" id="welcomeName">Vio Atmajaya</span> 👋</h1>
        <p style="font-size:13px;color:rgba(255,255,255,.6)" id="welcomeDate">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
      </div>
      <div style="display:flex;gap:12px;flex-wrap:wrap">
        @foreach([['Total User',$stats['total_users'],'users'],['Guru',$stats['total_teachers'],'graduation-cap'],['Pending',$stats['pending_leaves'],'clock']] as [$l,$v,$ic])
        <div style="background:rgba(255,255,255,.12);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.18);border-radius:14px;padding:14px 18px;text-align:center;min-width:90px">
          <i data-lucide="{{$ic}}" style="width:15px;height:15px;color:rgba(255,255,255,.65);margin:0 auto 7px;display:block"></i>
          <p style="font-size:1.7rem;font-weight:900;color:#fff;line-height:1">{{$v}}</p>
          <p style="font-size:10px;color:rgba(255,255,255,.55);margin-top:3px">{{$l}}</p>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Banner Slider --}}
  <div style="position:relative;border-radius:1.25rem;overflow:hidden;height:190px">
    @php $slides=[
      ['bg'=>'linear-gradient(135deg,#0f0c1a 0%,#1e1b4b 60%,#312e81 100%)','badge'=>'System','ic'=>'server','color'=>'#818cf8','title'=>'PHP '.$stats['php_version'].' · Laravel '.$stats['laravel_version'],'sub'=>'Env: '.strtoupper($stats['env']).' · Debug: '.($stats['debug']?'ON':'OFF')],
      ['bg'=>'linear-gradient(135deg,#180830 0%,#4c1d95 60%,#7e22ce 100%)','badge'=>'APK','ic'=>'smartphone','color'=>'#c084fc','title'=>($appSetting->apk_name??'ICB CT Presensi').' '.(($appSetting->apk_version_label)??''),'sub'=>'Ukuran: '.($appSetting->apk_size_human??'-').' · Min: '.($appSetting->apk_min_android??'-')],
      ['bg'=>'linear-gradient(135deg,#0a1628 0%,#0f2847 60%,#1e3a5c 100%)','badge'=>'Stats','ic'=>'bar-chart-3','color'=>'#38bdf8','title'=>$stats['total_users'].' Total Pengguna Aktif','sub'=>$stats['total_teachers'].' Guru · '.$stats['total_operators'].' Operator · '.$stats['pending_leaves'].' Pending Izin'],
      ['bg'=>'linear-gradient(135deg,#0d1117 0%,#161b22 50%,#21262d 100%)','badge'=>'Server','ic'=>'clock','color'=>'#4ade80','title'=>now()->format('H:i:s').' WIB','sub'=>now()->locale('id')->isoFormat('dddd, D MMMM YYYY')],
    ]; @endphp
    @foreach($slides as $i=>$sl)
    <div class="dslide {{$i===0?'active':''}}" style="background:{{$sl['bg']}}">
      <div style="position:absolute;inset:0;display:flex;align-items:center;padding:0 36px;gap:24px">
        <div style="flex:1;min-width:0">
          <div style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:99px;background:rgba(255,255,255,.1);color:{{$sl['color']}};font-size:10px;font-weight:700;margin-bottom:12px;border:1px solid rgba(255,255,255,.1)">
            <i data-lucide="{{$sl['ic']}}" style="width:10px;height:10px"></i>{{$sl['badge']}}
          </div>
          <h3 style="font-size:1.5rem;font-weight:800;color:#fff;margin-bottom:6px;line-height:1.2">{{$sl['title']}}</h3>
          <p style="font-size:12px;color:rgba(255,255,255,.5)">{{$sl['sub']}}</p>
        </div>
        <div style="width:72px;height:72px;background:rgba(255,255,255,.07);border-radius:18px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,.1);flex-shrink:0">
          <i data-lucide="{{$sl['ic']}}" style="width:32px;height:32px;color:{{$sl['color']}}"></i>
        </div>
      </div>
    </div>
    @endforeach
    <div style="position:absolute;bottom:14px;left:50%;transform:translateX(-50%);display:flex;gap:5px;z-index:10;background:rgba(0,0,0,.25);backdrop-filter:blur(8px);padding:5px 10px;border-radius:99px;border:1px solid rgba(255,255,255,.1)">
      @for($i=0;$i<4;$i++)
      <button class="sdot" data-idx="{{$i}}" style="width:{{$i===0?'18px':'6px'}};height:6px;border-radius:99px;background:{{$i===0?'#fff':'rgba(255,255,255,.3)'}};border:none;cursor:pointer;transition:all .3s;padding:0"></button>
      @endfor
    </div>
  </div>

  {{-- System Info + Quick Actions --}}
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px" class="hide-sm" style="grid-template-columns:1fr">
    <div class="card" style="padding:20px">
      <div style="display:flex;align-items:center;gap:7px;margin-bottom:14px;padding-bottom:12px;border-bottom:1px solid var(--border)">
        <i data-lucide="server" style="width:15px;height:15px;color:var(--purple)"></i>
        <p style="font-size:13px;font-weight:700;color:var(--txt)">System Info</p>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
        @foreach([['PHP',$stats['php_version'],'#a78bfa'],['Laravel',$stats['laravel_version'],'#67e8f9'],['Env',strtoupper($stats['env']),'#4ade80'],['Debug',$stats['debug']?'ON':'OFF',$stats['debug']?'#fb923c':'#4ade80'],['URL',parse_url($stats['app_url'],PHP_URL_HOST)??$stats['app_url'],'#f472b6'],['Time',now()->format('H:i').' WIB','#818cf8']] as [$k,$v,$c])
        <div style="padding:10px 12px;border-radius:10px;background:var(--bg-card2)">
          <p style="font-size:9px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">{{$k}}</p>
          <p style="font-size:13px;font-weight:700;color:{{$c}};word-break:break-all">{{$v}}</p>
        </div>
        @endforeach
      </div>
    </div>
    <div class="card" style="padding:20px">
      <div style="display:flex;align-items:center;gap:7px;margin-bottom:14px;padding-bottom:12px;border-bottom:1px solid var(--border)">
        <i data-lucide="zap" style="width:15px;height:15px;color:#facc15"></i>
        <p style="font-size:13px;font-weight:700;color:var(--txt)">Quick Actions</p>
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:9px">
        @foreach([[route('developer.clear-cache',$secret),'refresh-cw','#34d399','Clear Cache','confirm("Clear cache?")'],
                  [url('/run-migrate-secret?key=vexalyn19052009'),'database','#60a5fa','Run Migrate','confirm("Run migrate?")'],
                  [url('/dashboard'),'layout-dashboard','#a78bfa','Dashboard',null],
                  [url('/settings'),'settings','#9ca3af','Settings',null],
                  ['https://github.com/vexalyn-dev','github','#e2e8f0','GitHub',null]] as [$href,$ic,$c,$lbl,$conf])
        <a href="{{$href}}" {{ $conf?"onclick=\"return $conf\"":'' }} target="{{ str_starts_with($href,'http')?'_blank':'' }}"
           class="btn btn-g" style="text-decoration:none;font-size:12px;padding:8px 14px">
          <i data-lucide="{{$ic}}" style="width:13px;height:13px;color:{{$c}}"></i>{{$lbl}}
        </a>
        @endforeach
      </div>
    </div>
  </div>
</div>

{{-- ── APK ── --}}
<div id="section-apk" class="fu fu2" style="display:flex;flex-direction:column;gap:16px">
  <div style="display:flex;align-items:center;gap:12px">
    <div style="width:38px;height:38px;background:linear-gradient(135deg,#7c3aed,#a855f7);border-radius:11px;display:flex;align-items:center;justify-content:center">
      <i data-lucide="smartphone" style="width:19px;height:19px;color:#fff"></i>
    </div>
    <div><p class="section-title">APK Management</p><p class="section-sub">Upload & kelola APK mobile ICB CT</p></div>
  </div>

  @if($appSetting?->apk_file)
  <div style="display:flex;align-items:center;gap:14px;padding:16px 20px;border-radius:1rem;background:rgba(34,197,94,.08);border:1.5px solid rgba(34,197,94,.25)">
    <div style="width:42px;height:42px;background:rgba(34,197,94,.15);border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
      <i data-lucide="package-check" style="width:21px;height:21px;color:#16a34a"></i>
    </div>
    <div style="flex:1;min-width:0">
      <p style="font-size:13px;font-weight:700;color:#15803d">APK Terpasang</p>
      <p style="font-size:12px;color:#16a34a;margin-top:2px">{{ $appSetting->apk_name ?? 'ICB CT Presensi' }} · {{ $appSetting->apk_version_label ?? 'v1.0.0' }} · {{ $appSetting->apk_size_human ?? '-' }}</p>
      @if($appSetting->apk_uploaded_at)
      <p style="font-size:10px;color:rgba(21,128,61,.6);margin-top:2px">Diupload {{ $appSetting->apk_uploaded_at->diffForHumans() }}</p>
      @endif
    </div>
    <form action="{{ route('developer.apk.delete',$secret) }}" method="POST" onsubmit="return confirm('Hapus APK?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-g" style="color:#dc2626;border-color:rgba(220,38,38,.3);font-size:12px;padding:8px 14px">
        <i data-lucide="trash-2" style="width:13px;height:13px"></i> Hapus
      </button>
    </form>
  </div>
  @else
  <div style="display:flex;align-items:center;gap:10px;padding:14px 18px;border-radius:1rem;background:var(--bg-card2);border:1.5px dashed var(--border)">
    <i data-lucide="package-x" style="width:17px;height:17px;color:var(--muted)"></i>
    <p style="font-size:13px;color:var(--muted)">Belum ada APK yang diupload.</p>
  </div>
  @endif

  <form action="{{ route('developer.apk',$secret) }}" method="POST" enctype="multipart/form-data" class="card" style="padding:24px;display:flex;flex-direction:column;gap:16px">
    @csrf
    <div id="apkZone" onclick="document.getElementById('apkFile').click()"
         style="border:2px dashed var(--border);border-radius:1rem;padding:28px;text-align:center;cursor:pointer;transition:all .2s;position:relative"
         ondragover="event.preventDefault();this.style.borderColor='var(--purple)'"
         ondragleave="this.style.borderColor='var(--border)'"
         ondrop="event.preventDefault();handleApk(event.dataTransfer.files[0])">
      <input type="file" name="apk_file" accept=".apk" id="apkFile" style="display:none" onchange="handleApk(this.files[0])">
      <i data-lucide="upload-cloud" style="width:32px;height:32px;color:var(--muted);margin:0 auto 10px;display:block"></i>
      <p style="font-size:13px;font-weight:600;color:var(--txt2)" id="apkZoneTxt">Drag & drop atau klik untuk pilih .apk</p>
      <p style="font-size:11px;color:var(--muted);margin-top:4px">Format: .apk · Maks 100MB</p>
    </div>
    <div class="grid2">
      @foreach([['apk_name','apkName','type','Nama Aplikasi','ICB CT Presensi'],['apk_version','apkVer','tag','Versi','1.0.0'],['apk_min_android','apkAndroid','smartphone','Min. Android','Android 8.0+'],['apk_changelog','apkLog','file-text','Changelog','Perubahan...']] as [$n,$id,$ic,$lbl,$ph])
      <div>
        <p style="font-size:11px;font-weight:600;color:var(--txt2);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">{{$lbl}}</p>
        <div style="position:relative">
          <i data-lucide="{{$ic}}" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--muted)"></i>
          <input type="text" name="{{$n}}" id="{{$id}}" placeholder="{{$ph}}" value="{{ old($n, $appSetting?->$n ?? '') }}" class="inp" style="padding-left:36px">
        </div>
      </div>
      @endforeach
    </div>
    <div style="display:flex;align-items:center;gap:10px;padding-top:12px;border-top:1px solid var(--border)">
      <button type="submit" class="btn btn-p"><i data-lucide="save" style="width:15px;height:15px"></i> Simpan APK</button>
      @if($appSetting?->apk_url)
      <a href="{{ $appSetting->apk_url }}" target="_blank" class="btn btn-g" style="text-decoration:none;font-size:13px">
        <i data-lucide="download" style="width:14px;height:14px"></i> Download
      </a>
      @endif
    </div>
  </form>
</div>

{{-- ── MAINTENANCE ── --}}
<div id="section-maintenance" class="fu fu3" style="display:flex;flex-direction:column;gap:16px">
  <div style="display:flex;align-items:center;gap:12px">
    <div style="width:38px;height:38px;background:linear-gradient(135deg,#d97706,#f59e0b);border-radius:11px;display:flex;align-items:center;justify-content:center">
      <i data-lucide="construction" style="width:19px;height:19px;color:#fff"></i>
    </div>
    <div style="flex:1">
      <p class="section-title">Mode Maintenance</p>
      <p class="section-sub">Tampilkan halaman maintenance ke guru — admin tetap bisa akses</p>
    </div>
    @php $mOn = \App\Models\AppSetting::getInstance()->maintenance_mode ?? false; @endphp
    <span class="badge" style="{{ $mOn ? 'background:rgba(220,38,38,.15);color:#dc2626;border:1px solid rgba(220,38,38,.3)':'background:rgba(34,197,94,.12);color:#16a34a;border:1px solid rgba(34,197,94,.3)' }}">
      {{ $mOn ? '● AKTIF' : '○ NONAKTIF' }}
    </span>
  </div>
  <form action="{{ route('developer.maintenance',$secret) }}" method="POST" class="card" style="padding:24px;display:flex;flex-direction:column;gap:16px">
    @csrf
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-radius:.875rem;background:var(--bg-card2)">
      <div>
        <p style="font-size:13px;font-weight:600;color:var(--txt)">Aktifkan Maintenance Mode</p>
        <p style="font-size:11px;color:var(--muted);margin-top:2px">Admin & Operator tetap bisa login dan akses semua fitur</p>
      </div>
      <label style="cursor:pointer" onclick="toggleMaintUI()">
        <input type="hidden" name="maintenance_mode" id="maintVal" value="{{ $mOn?'1':'0' }}">
        <div class="tog-track {{ $mOn?'on':'off' }}" id="maintTrack">
          <div class="tog-thumb"></div>
        </div>
      </label>
    </div>
    <div>
      <p style="font-size:11px;font-weight:600;color:var(--txt2);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Pesan Maintenance</p>
      <textarea name="maintenance_message" rows="2" placeholder="Sistem sedang dalam pemeliharaan, silakan coba beberapa saat lagi…" class="inp" style="resize:none">{{ \App\Models\AppSetting::getInstance()->maintenance_message }}</textarea>
    </div>
    <div>
      <button type="submit" class="btn btn-p"><i data-lucide="save" style="width:15px;height:15px"></i> Simpan Status</button>
    </div>
  </form>
</div>

{{-- ── UPDATES ── --}}
<div id="section-updates" class="fu fu4" style="display:flex;flex-direction:column;gap:16px">
  <div style="display:flex;align-items:center;gap:12px">
    <div style="width:38px;height:38px;background:linear-gradient(135deg,#0ea5e9,#38bdf8);border-radius:11px;display:flex;align-items:center;justify-content:center">
      <i data-lucide="rocket" style="width:19px;height:19px;color:#fff"></i>
    </div>
    <div><p class="section-title">Rilis Update</p><p class="section-sub">Tulis changelog — muncul di modal user saat pertama masuk</p></div>
  </div>
  <form action="{{ route('developer.updates.store',$secret) }}" method="POST" class="card" style="padding:24px;display:flex;flex-direction:column;gap:14px">
    @csrf
    <div class="grid2">
      <div>
        <p style="font-size:11px;font-weight:600;color:var(--txt2);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Versi</p>
        <input type="text" name="version" placeholder="2.3.1" class="inp" required>
      </div>
      <div>
        <p style="font-size:11px;font-weight:600;color:var(--txt2);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Tipe</p>
        <select name="type" class="inp" style="cursor:pointer">
          <option value="feature">✨ Feature</option>
          <option value="fix">🔧 Fix</option>
          <option value="update">⬆️ Update</option>
          <option value="hotfix">🚨 Hotfix</option>
        </select>
      </div>
    </div>
    <div>
      <p style="font-size:11px;font-weight:600;color:var(--txt2);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Judul Update</p>
      <input type="text" name="title" placeholder="Perbaikan bug + fitur baru" class="inp" required>
    </div>
    <div>
      <p style="font-size:11px;font-weight:600;color:var(--txt2);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Detail Perubahan</p>
      <textarea name="content" rows="4" placeholder="• Perbaiki bug saat login&#10;• Tambah fitur export Excel&#10;• Update UI halaman presensi" class="inp" style="resize:vertical" required></textarea>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;padding-top:10px;border-top:1px solid var(--border)">
      <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
        <input type="checkbox" name="show_modal" value="1" checked style="accent-color:#7c3aed;width:15px;height:15px">
        <span style="font-size:12px;color:var(--txt2)">Tampilkan ke user lewat modal</span>
      </label>
      <button type="submit" class="btn btn-p"><i data-lucide="plus" style="width:15px;height:15px"></i> Tambah</button>
    </div>
  </form>

  @if(count($updates)>0)
  <div style="display:flex;flex-direction:column;gap:8px">
    @foreach($updates as $u)
    @php $tc=['feature'=>['#a78bfa','rgba(139,92,246,.12)','rgba(139,92,246,.3)'],'fix'=>['#4ade80','rgba(34,197,94,.1)','rgba(34,197,94,.25)'],'hotfix'=>['#f87171','rgba(239,68,68,.1)','rgba(239,68,68,.25)'],'update'=>['#60a5fa','rgba(96,165,250,.1)','rgba(96,165,250,.25)']][$u->type]??['#9ca3af','rgba(156,163,175,.1)','rgba(156,163,175,.25)']; @endphp
    <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 18px;border-radius:1rem;background:var(--bg-card);border:1.5px solid var(--border)">
      <span style="padding:3px 9px;border-radius:6px;font-size:10px;font-weight:700;flex-shrink:0;background:{{$tc[1]}};color:{{$tc[0]}};border:1px solid {{$tc[2]}};text-transform:uppercase;margin-top:2px">{{$u->type}}</span>
      <div style="flex:1;min-width:0">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;flex-wrap:wrap">
          <span style="font-size:12px;font-weight:700;color:var(--txt)">v{{$u->version}}</span>
          <span style="font-size:12px;color:var(--txt2)">{{$u->title}}</span>
          @if($u->show_modal)<span style="font-size:9px;color:#a78bfa;background:rgba(139,92,246,.1);padding:2px 7px;border-radius:99px;border:1px solid rgba(139,92,246,.2)">modal</span>@endif
        </div>
        <p style="font-size:11px;color:var(--muted)">{{ \Illuminate\Support\Str::limit($u->content,80) }} · {{$u->created_at->diffForHumans()}}</p>
      </div>
      <form action="{{ route('developer.updates.delete',[$secret,$u->id]) }}" method="POST" onsubmit="return confirm('Hapus?')">
        @csrf @method('DELETE')
        <button type="submit" style="padding:6px 8px;border-radius:8px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#f87171;cursor:pointer;transition:all .2s;display:flex;align-items:center"
                onmouseover="this.style.background='rgba(239,68,68,.2)'" onmouseout="this.style.background='rgba(239,68,68,.1)'">
          <i data-lucide="trash-2" style="width:13px;height:13px"></i>
        </button>
      </form>
    </div>
    @endforeach
  </div>
  @else
  <div class="card" style="padding:28px;text-align:center">
    <i data-lucide="inbox" style="width:32px;height:32px;color:var(--muted);margin:0 auto 10px;display:block"></i>
    <p style="font-size:13px;color:var(--muted)">Belum ada update.</p>
  </div>
  @endif
</div>

<div style="text-align:center;padding:16px 0;border-top:1px solid var(--border)" class="fu fu5">
  <p style="font-size:11px;color:var(--muted)">ICB CT Developer Panel · <a href="https://vexalyndev.my.id" target="_blank" style="color:var(--purple);text-decoration:none;font-weight:600">Vexalyn Dev</a> · {{ now()->year }}</p>
</div>
</main>

<script>
// ── i18n ──
const T = {
  id:{ welcome:'Selamat datang,', date:'{{ now()->locale("id")->isoFormat("dddd, D MMMM YYYY") }}', modalTitle:'Selamat Datang, Developer! 👋', modalSub:'Kamu mengakses <strong style="color:var(--purple)">Developer Dashboard</strong> ICB CT via URL rahasia.', modalBtn:'Masuk ke Dashboard', loaderMsg:'Memuat Dev Panel…' },
  en:{ welcome:'Welcome,', date:'{{ now()->isoFormat("dddd, D MMMM YYYY") }}', modalTitle:'Welcome, Developer! 👋', modalSub:'You\'re accessing <strong style="color:var(--purple)">Developer Dashboard</strong> ICB CT via secret URL.', modalBtn:'Enter Dashboard', loaderMsg:'Loading Dev Panel…' }
};
let lang = localStorage.getItem('devLang') || 'id';

function applyLang() {
  document.getElementById('langLabel').textContent = lang.toUpperCase();
  document.getElementById('welcomeDate').textContent = T[lang].date;
  document.getElementById('modalTitle').textContent  = T[lang].modalTitle;
  document.getElementById('modalSub').innerHTML      = T[lang].modalSub;
  document.getElementById('modalBtn').textContent    = T[lang].modalBtn;
  document.getElementById('loaderMsg').textContent   = T[lang].loaderMsg;
}
function toggleLang() { lang = lang === 'id' ? 'en' : 'id'; localStorage.setItem('devLang',lang); applyLang(); }

// ── Theme ──
const themes = ['light','dark','system'];
let themeIdx = themes.indexOf(localStorage.getItem('devTheme') || 'system');
const themeIcons = { light:'sun', dark:'moon', system:'monitor' };

function applyTheme() {
  const t = themes[themeIdx];
  localStorage.setItem('devTheme', t);
  const html = document.getElementById('devHtml');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  html.className = (t === 'system') ? (prefersDark ? 'dark' : 'light') : t;
  // Topbar bg hack for dark
  document.getElementById('topbar').style.background = html.classList.contains('dark')
    ? 'rgba(8,6,20,.88)' : 'rgba(248,247,255,.88)';
  const ico = themeIcons[t];
  document.getElementById('themIco').setAttribute('data-lucide', ico);
  lucide.createIcons();
}
function cycleTheme() { themeIdx = (themeIdx + 1) % 3; applyTheme(); }

// ── Profile menu close on outside click ──
document.addEventListener('click', e => {
  const pm = document.getElementById('profMenu');
  if (pm && !pm.parentElement.contains(e.target)) pm.style.display = 'none';
});

// ── Loading screen ──
const loaderMsgs = { id:['Memuat Dev Panel…','Menyiapkan data…','Hampir selesai…','Siap!'], en:['Loading Dev Panel…','Preparing data…','Almost ready…','Done!'] };
let lp = 0;
const loaderBar = document.getElementById('loaderBar');
const loaderMsg = document.getElementById('loaderMsg');
const lTimer = setInterval(() => {
  lp += Math.random() * 22 + 8;
  if (lp > 100) lp = 100;
  loaderBar.style.width = lp + '%';
  const msgs = loaderMsgs[lang];
  const mi = Math.floor((lp/100) * (msgs.length - 1));
  loaderMsg.textContent = msgs[mi];
  if (lp >= 100) { clearInterval(lTimer); showApp(); }
}, 120);

function showApp() {
  const loader = document.getElementById('devLoader');
  loader.style.transition = 'opacity .5s ease';
  loader.style.opacity = '0';
  setTimeout(() => {
    loader.style.display = 'none';
    document.getElementById('mainWrap').style.opacity = '1';
    showModal();
  }, 500);
}

function showModal() {
  const m = document.getElementById('devModal');
  const b = document.getElementById('devModalBox');
  m.style.display = 'flex';
  requestAnimationFrame(() => requestAnimationFrame(() => {
    b.style.transform = 'translateY(0) scale(1)';
    b.style.opacity = '1';
    lucide.createIcons();
  }));
}
function closeModal() {
  const b = document.getElementById('devModalBox');
  b.style.transform = 'translateY(20px) scale(.95)';
  b.style.opacity = '0';
  setTimeout(() => { document.getElementById('devModal').style.display = 'none'; }, 350);
}
document.getElementById('devModal').addEventListener('click', e => { if (e.target === document.getElementById('devModal')) closeModal(); });

// ── Slider ──
let sIdx = 0;
const slides = document.querySelectorAll('.dslide');
const dots   = document.querySelectorAll('.sdot');
let sTimer;
function gotoSlide(i) {
  if (i === sIdx) return;
  slides[sIdx].classList.add('exit');
  slides[sIdx].classList.remove('active');
  setTimeout(() => slides[sIdx < slides.length ? sIdx : 0].classList.remove('exit'), 500);
  dots[sIdx].style.width = '6px';
  dots[sIdx].style.background = 'rgba(255,255,255,.3)';
  sIdx = (i + slides.length) % slides.length;
  slides[sIdx].classList.add('active');
  dots[sIdx].style.width = '18px';
  dots[sIdx].style.background = '#fff';
}
function startSlider() { clearInterval(sTimer); sTimer = setInterval(() => gotoSlide(sIdx + 1), 3500); }
dots.forEach(d => d.addEventListener('click', () => { gotoSlide(+d.dataset.idx); startSlider(); }));
startSlider();

// ── APK auto-fill ──
function handleApk(file) {
  if (!file) return;
  document.getElementById('apkZoneTxt').textContent = file.name;
  document.getElementById('apkZone').style.borderColor = 'var(--purple)';
  const base = file.name.replace(/\.apk$/i, '');
  const vm = base.match(/[vV]?(\d+\.\d+(?:\.\d+)?(?:\.\d+)?)/);
  if (vm) {
    document.getElementById('apkVer').value = vm[1];
    const nm = base.replace(/[-_\s]*[vV]?\d+\.\d+(?:\.\d+)?(?:\.\d+)?[-_\s]*/g,'').replace(/[-_]/g,' ').trim();
    if (nm) document.getElementById('apkName').value = nm;
  } else {
    const nm = base.replace(/[-_]/g,' ').trim();
    if (nm) document.getElementById('apkName').value = nm;
  }
}

// ── Maintenance toggle UI ──
function toggleMaintUI() {
  const t = document.getElementById('maintTrack');
  const v = document.getElementById('maintVal');
  const isOn = t.classList.contains('on');
  t.classList.toggle('on', !isOn);
  t.classList.toggle('off', isOn);
  v.value = isOn ? '0' : '1';
}

// ── Init ──
window.addEventListener('load', () => {
  applyTheme();
  applyLang();
  lucide.createIcons();
  document.getElementById('mainWrap').style.opacity = '0';
  document.getElementById('mainWrap').style.transition = 'opacity .4s ease';
});
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
  if (localStorage.getItem('devTheme') === 'system') applyTheme();
});
</script>
</body>
</html>
