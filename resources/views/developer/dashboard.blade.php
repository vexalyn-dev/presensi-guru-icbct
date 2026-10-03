@extends('layouts.developer')
@section('content')

@php
    $hour     = (int) now()->format('H');
    $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
    $debugOn  = (bool) ($stats['debug'] ?? false);
    $envName  = strtoupper($stats['env'] ?? 'PRODUCTION');
    $setting  = \App\Models\AppSetting::getInstance();
    $mOn      = $setting->maintenance_mode ?? false;
    $host     = parse_url($stats['app_url'] ?? '', PHP_URL_HOST) ?? 'localhost';
@endphp

{{-- ═════════ MODAL SELAMAT DATANG ═════════ --}}
<div id="nb-welcome" class="overlay" role="dialog" aria-modal="true" aria-labelledby="nb-welcome-title" hidden>
    <div class="modal">
        <div class="modal-top">
            <b>Panduan singkat</b>
            <button type="button" class="ibtn" data-close-welcome aria-label="Tutup"><i data-lucide="x" style="width:16px;height:16px"></i></button>
        </div>
        <div class="modal-body">
            <div>
                <h2 id="nb-welcome-title">{{ $greeting }}, Developer</h2>
                <p class="mute" style="margin-top:6px">Semua kendali sistem ada di satu tempat. Ini yang bisa kamu lakukan:</p>
            </div>
            <ul class="tips">
                <li><span class="ico t-violet"><i data-lucide="rocket" style="width:17px;height:17px"></i></span>
                    <div><strong>Maintenance & cache</strong><small>Jalankan migration, optimize, atau perbaiki session dengan satu klik.</small></div></li>
                <li><span class="ico t-ok"><i data-lucide="package-check" style="width:17px;height:17px"></i></span>
                    <div><strong>Kelola APK</strong><small>Unggah build Android terbaru dan bagikan ke pengguna.</small></div></li>
                <li><span class="ico t-warn"><i data-lucide="history" style="width:17px;height:17px"></i></span>
                    <div><strong>Umumkan rilis</strong><small>Tulis changelog dan tampilkan sebagai modal ke pengguna.</small></div></li>
            </ul>
            <label class="check">
                <input type="checkbox" id="nb-welcome-skip">
                <span class="box"><i data-lucide="check" style="width:12px;height:12px"></i></span>
                <span>Jangan tampilkan lagi hari ini</span>
            </label>
            <button type="button" class="btn btn-block" data-close-welcome>Mulai bekerja</button>
        </div>
    </div>
</div>

{{-- ═════════ MODAL KONFIRMASI ═════════ --}}
<div id="nb-confirm" class="overlay" role="alertdialog" aria-modal="true" aria-labelledby="nb-confirm-title" hidden>
    <div class="modal modal-sm">
        <div class="modal-body">
            <div class="ico ico-lg t-violet" id="nb-confirm-icon"><i data-lucide="shield-alert" style="width:22px;height:22px"></i></div>
            <h3 id="nb-confirm-title">Yakin lanjut?</h3>
            <p class="mute pre" id="nb-confirm-msg"></p>
            <div class="end" style="width:100%">
                <button type="button" class="btn btn-ghost" id="nb-confirm-no">Batal</button>
                <button type="button" class="btn" id="nb-confirm-yes">Ya, lanjutkan</button>
            </div>
        </div>
    </div>
</div>

{{-- ═════════ MODAL RILIS BARU ═════════ --}}
<div id="nb-release-modal" class="overlay" role="dialog" aria-modal="true" aria-labelledby="nb-rel-title"
     data-open="{{ ($errors->has('version') || $errors->has('title') || $errors->has('content') || $errors->has('type')) ? '1' : '0' }}" hidden>
    <div class="modal">
        <div class="modal-top">
            <b>Rilis baru</b>
            <button type="button" class="ibtn" data-close-release aria-label="Tutup"><i data-lucide="x" style="width:16px;height:16px"></i></button>
        </div>
        <form action="{{ route('developer.updates.store', $secret) }}" method="POST" class="modal-body">
            @csrf
            <div>
                <h3 id="nb-rel-title">Umumkan perubahan</h3>
                <p class="mute" style="margin-top:4px">Tulis satu baris per perubahan. Awali dengan “-” supaya tampil sebagai daftar.</p>
            </div>
            <div>
                <label class="label">Jenis rilis</label>
                <div class="seg" role="radiogroup">
                    @foreach([['feature','Feature','star'],['update','Update','git-commit'],['fix','Fix','wrench'],['hotfix','Hotfix','flame']] as [$sv, $sl, $si])
                        <label class="seg-i">
                            <input type="radio" name="type" value="{{ $sv }}" {{ old('type', 'feature') === $sv ? 'checked' : '' }}>
                            <span class="seg-b"><i data-lucide="{{ $si }}" style="width:15px;height:15px"></i>{{ $sl }}</span>
                        </label>
                    @endforeach
                </div>
                @error('type')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div class="form-grid">
                <div>
                    <label class="label" for="rel-version">Versi</label>
                    <input id="rel-version" type="text" name="version" class="input mono" required placeholder="1.2.0" value="{{ old('version') }}">
                    @error('version')<p class="err">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="rel-title-in">Judul</label>
                    <input id="rel-title-in" type="text" name="title" class="input" required placeholder="Menambahkan laporan baru" value="{{ old('title') }}">
                    @error('title')<p class="err">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="label" for="rel-content">Changelog</label>
                <textarea id="rel-content" name="content" rows="5" class="input" required placeholder="- Memperbaiki bug A&#10;- Menambahkan fitur B">{{ old('content') }}</textarea>
                @error('content')<p class="err">{{ $message }}</p>@enderror
            </div>
            <label class="check">
                <input type="checkbox" name="show_modal" value="1" checked>
                <span class="box"><i data-lucide="check" style="width:12px;height:12px"></i></span>
                <span>Tampilkan modal ke pengguna</span>
            </label>
            <div class="end">
                <button type="button" class="btn btn-ghost" data-close-release>Batal</button>
                <button type="submit" class="btn"><i data-lucide="send" style="width:15px;height:15px"></i> Publikasikan</button>
            </div>
        </form>
    </div>
</div>

{{-- ═════════ TAB: DASHBOARD ═════════ --}}
<div id="tab-dashboard" class="tab-content">

    {{-- ── Greeting Header ── --}}
    <div class="dash-header">
        <div class="dash-top">
            <div>
                <h2 class="greeting">{{ $greeting }}, <span style="background:linear-gradient(135deg,#a5b4fc 20%,#818cf8 50%,#6366f1);background-clip:text;-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Developer</span></h2>
                <p class="subtext">
                    Sistem aktif di <b>{{ $host }}</b> &nbsp;·&nbsp; {{ now()->locale('id')->isoFormat('dddd, D MMM YYYY') }} &nbsp;·&nbsp; pukul <span id="dash-clock-text">{{ now()->format('H:i') }}</span> WIB.
                    @if(($stats['pending_leaves'] ?? 0) > 0)
                        <span style="color:#fbbf24;font-weight:600"> &nbsp;⚠ {{ $stats['pending_leaves'] }} izin pending.</span>
                    @else
                        <span style="color:#34d399"> &nbsp;✓ Semua operasional normal.</span>
                    @endif
                </p>
                <div class="dash-meta">
                    <span class="dash-pill"><i></i> {{ $envName }}</span>
                    <span class="pill mono"><i data-lucide="server" style="width:13px;height:13px;color:var(--txt-dim)"></i> {{ $host }}</span>
                    @if($debugOn)<span class="pill" style="border-color:rgba(245,158,11,.35);color:#fbbf24"><i data-lucide="alert-triangle" style="width:13px;height:13px"></i> Debug ON</span>@endif
                    @if(($stats['pending_leaves'] ?? 0) > 0)<span class="pill" style="border-color:rgba(245,158,11,.3);color:#fbbf24"><i data-lucide="clock" style="width:13px;height:13px"></i> {{ $stats['pending_leaves'] }} Pending</span>@endif
                </div>
            </div>
            <div class="dash-actions">
                <a href="{{ url('/dashboard') }}" target="_blank" class="btn btn-ghost"><i data-lucide="external-link" style="width:15px;height:15px"></i> Buka Aplikasi</a>
                <button type="button" class="btn btn-ghost" id="nb-open-welcome"><i data-lucide="book-open" style="width:15px;height:15px"></i> Panduan Dev</button>
            </div>
        </div>
    </div>

    {{-- ── Banner Vexalyn Dev (full width, tidak terpotong) ── --}}
    <div style="margin-bottom:24px;border-radius:18px;overflow:hidden;position:relative;border:1px solid rgba(99,102,241,.2);box-shadow:0 8px 32px rgba(99,102,241,.12)">
        <img src="{{ asset('images/banner-vexalyn-dev.png') }}" alt="Vexalyn Dev Banner"
             style="width:100%;display:block;height:auto;max-height:260px;object-fit:contain;object-position:center;background:#050912">
    </div>

    @if($debugOn)
    <div class="alert alert-warn" role="alert" style="margin-bottom:20px">
        <i data-lucide="alert-triangle" style="width:18px;height:18px;flex-shrink:0;margin-top:1px"></i>
        <span><b>Debug mode aktif.</b> Matikan <code>APP_DEBUG</code> di environment production agar detail konfigurasi dan error trace terlindungi.</span>
    </div>
    @endif

    {{-- ── Stat Cards — desain premium ── --}}
    @php
        $statCards = [
            ['label'=>'Total Pengguna',  'value'=>$stats['total_users']??0,     'icon'=>'users',          'color'=>'#818cf8', 'bg'=>'rgba(99,102,241,.08)',  'border'=>'rgba(99,102,241,.2)'],
            ['label'=>'Guru Aktif',      'value'=>$stats['total_teachers']??0,  'icon'=>'graduation-cap', 'color'=>'#38bdf8', 'bg'=>'rgba(56,189,248,.08)',  'border'=>'rgba(56,189,248,.2)'],
            ['label'=>'Operator/Admin',  'value'=>$stats['total_operators']??0, 'icon'=>'shield-check',   'color'=>'#34d399', 'bg'=>'rgba(52,211,153,.08)',  'border'=>'rgba(52,211,153,.2)'],
            ['label'=>'Izin Pending',    'value'=>$stats['pending_leaves']??0,  'icon'=>'clock',          'color'=>($stats['pending_leaves']??0)>0?'#fbbf24':'#34d399', 'bg'=>($stats['pending_leaves']??0)>0?'rgba(251,191,36,.08)':'rgba(52,211,153,.08)', 'border'=>($stats['pending_leaves']??0)>0?'rgba(251,191,36,.2)':'rgba(52,211,153,.2)'],
        ];
    @endphp
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
        @foreach($statCards as $sc)
        <div style="position:relative;overflow:hidden;border-radius:14px;background:{{ $sc['bg'] }};border:1px solid {{ $sc['border'] }};padding:20px 22px;transition:transform .2s,box-shadow .2s"
             onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.25)'"
             onmouseout="this.style.transform='';this.style.boxShadow=''">
            <div style="position:absolute;top:-20px;right:-10px;width:80px;height:80px;border-radius:50%;background:{{ $sc['bg'] }};opacity:.5;filter:blur(12px);pointer-events:none"></div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
                <div style="width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,.06);border:1px solid {{ $sc['border'] }};display:flex;align-items:center;justify-content:center">
                    <i data-lucide="{{ $sc['icon'] }}" style="width:17px;height:17px;color:{{ $sc['color'] }}"></i>
                </div>
                <span style="font-size:.65rem;font-weight:700;color:{{ $sc['color'] }};font-family:'Geist Mono',monospace;text-transform:uppercase;letter-spacing:.08em;opacity:.8">Live</span>
            </div>
            <div class="num" data-count="{{ $sc['value'] }}" style="font-size:2.1rem;font-weight:800;color:#f8fafc;letter-spacing:-.04em;line-height:1;margin-bottom:6px">{{ $sc['value'] }}</div>
            <p style="font-size:.72rem;color:var(--txt-dim);text-transform:uppercase;letter-spacing:.08em;font-family:'Geist Mono',monospace;margin:0">{{ $sc['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── Main Grid: Kiri (2/3) + Kanan (1/3) ── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">

        {{-- ═══ Kolom Kiri ═══ --}}
        <div style="display:flex;flex-direction:column;gap:18px">

            {{-- System Info --}}
            <div class="card">
                <div class="card-head">
                    <h3><i data-lucide="activity" style="width:18px;height:18px;color:#34d399"></i> Informasi Sistem</h3>
                    <span class="live"><i></i> Live</span>
                </div>
                <div class="info">
                    <div><p class="l">Environment</p><p class="v mono"><span class="badge t-ok" style="font-size:0.75rem;padding:3px 9px">{{ $envName }}</span></p></div>
                    <div><p class="l">Debug Mode</p><p class="v"><span class="dot {{ $debugOn ? 'warn' : 'ok' }}"></span>{{ $debugOn ? 'Aktif' : 'Nonaktif' }}</p></div>
                    <div><p class="l">Host URL</p><p class="v mono trunc" style="font-size:0.82rem">{{ $host }}</p></div>
                    <div><p class="l">Waktu Server</p><p class="v mono" id="dash-srv-clock">{{ now()->format('H:i:s') }}</p></div>
                    <div><p class="l">PHP Version</p><p class="v mono">{{ $stats['php_version']??'-' }}</p></div>
                    <div><p class="l">Laravel</p><p class="v mono">v{{ ltrim($stats['laravel_version']??'12.x','vV') }}</p></div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card">
                <div class="card-head">
                    <h3><i data-lucide="zap" style="width:18px;height:18px;color:#818cf8"></i> Aksi Cepat</h3>
                </div>
                <div class="actions">
                    <a href="{{ route('developer.clear-cache', $secret) }}" onclick="return confirmAction(this,'🧹 Bersihkan semua cache?\n(config, route, view, app cache)')" class="action">
                        <span class="ico t-sky"><i data-lucide="trash-2" style="width:17px;height:17px"></i></span>
                        <div><b>Sapu Jagat</b><small>Hapus cache config, route, &amp; view</small></div>
                        <i data-lucide="arrow-up-right" class="go" style="width:16px;height:16px"></i>
                    </a>
                    <a href="{{ url('/fix-session?secret='.$secret) }}" onclick="return confirmAction(this,'🔧 Perbaiki session dir & hapus semua cache?')" class="action">
                        <span class="ico t-warn"><i data-lucide="wrench" style="width:17px;height:17px"></i></span>
                        <div><b>Fix Session</b><small>Perbaiki permissions &amp; clear cache</small></div>
                        <i data-lucide="arrow-up-right" class="go" style="width:16px;height:16px"></i>
                    </a>
                    <a href="{{ route('developer.optimize', $secret) }}" onclick="return confirmAction(this,'⚡ Rebuild semua cache?\n(config, route, view cache)')" class="action">
                        <span class="ico" style="background:rgba(250,204,21,.12);color:#facc15;border:1px solid rgba(250,204,21,.3)"><i data-lucide="zap" style="width:17px;height:17px"></i></span>
                        <div><b>Optimize</b><small>Bangun ulang struktur cache</small></div>
                        <i data-lucide="arrow-up-right" class="go" style="width:16px;height:16px"></i>
                    </a>
                    <a href="{{ route('developer.migrate', $secret) }}" onclick="return confirmAction(this,'🗄️ Jalankan database migration?\nPastikan backup sudah ada!')" class="action">
                        <span class="ico t-ok"><i data-lucide="database" style="width:17px;height:17px"></i></span>
                        <div><b>Run Migration</b><small>artisan migrate --force</small></div>
                        <i data-lucide="arrow-up-right" class="go" style="width:16px;height:16px"></i>
                    </a>
                    <a href="{{ route('developer.run-seeder', $secret) }}" onclick="return confirmAction(this,'🌱 Jalankan database seeder?\nMembuat ulang akun developer dan data demo.')" class="action">
                        <span class="ico t-violet"><i data-lucide="sprout" style="width:17px;height:17px"></i></span>
                        <div><b>Run Seeder</b><small>db:seed --force</small></div>
                        <i data-lucide="arrow-up-right" class="go" style="width:16px;height:16px"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- ═══ Kolom Kanan ═══ --}}
        <div style="display:flex;flex-direction:column;gap:18px">

            {{-- Server Info --}}
            <div class="card">
                <div class="card-head">
                    <h3><i data-lucide="monitor" style="width:18px;height:18px;color:#06b6d4"></i> Server Info</h3>
                    <span class="live"><i></i> Live</span>
                </div>
                <div class="server-info-body">
                    <div class="si-row">
                        <div class="si-item"><span class="si-label">Memory</span><span class="si-val">{{ $stats['memory_limit']??'-' }}</span></div>
                        <div class="si-item"><span class="si-label">Upload Max</span><span class="si-val">{{ $stats['upload_max']??'-' }}</span></div>
                    </div>
                    <div class="si-row">
                        <div class="si-item"><span class="si-label">Post Max</span><span class="si-val">{{ $stats['post_max']??'-' }}</span></div>
                        <div class="si-item"><span class="si-label">Max Exec</span><span class="si-val">{{ $stats['max_exec_time']??'-' }}s</span></div>
                    </div>
                    <div class="si-row">
                        <div class="si-item full"><span class="si-label">Server</span><span class="si-val" style="font-size:0.78rem">{{ $stats['server_software']??'-' }}</span></div>
                    </div>
                    <div class="si-row">
                        <div class="si-item"><span class="si-label">IP</span><span class="si-val" style="font-size:0.78rem">{{ $stats['server_addr']??'-' }}</span></div>
                        <div class="si-item"><span class="si-label">Port</span><span class="si-val">{{ $stats['server_port']??'-' }}</span></div>
                    </div>
                    <div class="si-row">
                        <div class="si-item full"><span class="si-label">Database</span><span class="si-val" style="font-size:0.78rem">{{ $stats['db_driver']??'-' }} / {{ $stats['db_name']??'-' }} @ {{ $stats['db_host']??'-' }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Disk Usage Gauge --}}
            <div class="card" style="padding:20px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                    <span style="font-size:0.78rem;font-weight:600;color:var(--txt-sub);text-transform:uppercase;letter-spacing:.06em;font-family:'Geist Mono',monospace">Disk Usage</span>
                    @php $dp = $stats['disk_percent']??0; $dc = $dp>90?'#f43f5e':($dp>70?'#f59e0b':'#10b981'); @endphp
                    <span style="font-size:0.85rem;font-weight:700;font-family:'Geist Mono',monospace;color:{{ $dc }}">{{ $dp }}%</span>
                </div>
                <div style="display:flex;align-items:center;gap:20px">
                    @php
                        $r = 48; $cx = 56; $cy = 56;
                        $circ = 2 * 3.14159 * $r;
                        $filled = $circ * ($dp / 100);
                        $empty  = $circ - $filled;
                        $dcGlow = $dc . '80';
                    @endphp
                    <svg width="112" height="112" viewBox="0 0 112 112" style="flex-shrink:0">
                        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="9"/>
                        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none" stroke="{{ $dc }}" stroke-width="9"
                                stroke-dasharray="{{ $filled }} {{ $empty }}" stroke-dashoffset="{{ $circ * 0.25 }}"
                                stroke-linecap="round" style="filter:drop-shadow(0 0 6px {{ $dcGlow }});transition:stroke-dasharray 1s ease"/>
                        <text x="{{ $cx }}" y="{{ $cy - 3 }}" text-anchor="middle" dominant-baseline="middle"
                              fill="{{ $dc }}" font-size="18" font-weight="800" font-family="'Geist Mono',monospace">{{ $dp }}%</text>
                        <text x="{{ $cx }}" y="{{ $cy + 14 }}" text-anchor="middle" dominant-baseline="middle"
                              fill="rgba(100,116,139,.8)" font-size="8" font-family="'Plus Jakarta Sans',sans-serif">used</text>
                    </svg>
                    <div style="flex:1">
                        <p style="font-size:.75rem;color:var(--txt-dim);margin:0 0 8px">
                            {{ round(($stats['disk_total']-$stats['disk_free'])/1073741824,1) }} GB dipakai dari {{ round($stats['disk_total']/1073741824,1) }} GB
                        </p>
                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                            @foreach([['label'=>'Config','ok'=>$stats['cache_config']==='CACHED'],['label'=>'Routes','ok'=>$stats['cache_route']==='CACHED'],['label'=>'Views','ok'=>$stats['cache_view']==='CACHED']] as $c)
                            <span style="display:inline-flex;align-items:center;gap:4px;font-size:.68rem;font-weight:600;padding:3px 9px;border-radius:20px;font-family:'Geist Mono',monospace;background:{{ $c['ok']?'rgba(16,185,129,.1)':'rgba(244,63,94,.1)' }};color:{{ $c['ok']?'#34d399':'#f87171' }};border:1px solid {{ $c['ok']?'rgba(16,185,129,.25)':'rgba(244,63,94,.25)' }}">
                                @if($c['ok'])<svg width="9" height="9" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 6l3 3 5-5"/></svg>@else<svg width="9" height="9" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M2.5 2.5l7 7M9.5 2.5l-7 7"/></svg>@endif
                                {{ $c['label'] }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart Aktivitas 7 Hari (crypto-style) --}}
            <div class="card" style="padding:18px 20px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                    <span style="font-size:0.82rem;font-weight:700;color:var(--txt-head);display:flex;align-items:center;gap:6px">
                        <i data-lucide="trending-up" style="width:15px;height:15px;color:#6366f1"></i>
                        Aktivitas Presensi 7 Hari
                    </span>
                    <span class="live" style="font-size:0.72rem"><i></i> Live</span>
                </div>
                @php
                    $activityData = []; $activityLabels = [];
                    for ($d = 6; $d >= 0; $d--) {
                        $date = now()->subDays($d);
                        $activityLabels[] = $date->locale('id')->isoFormat('D MMM');
                        $activityData[]   = \App\Models\Attendance::whereDate('date', $date->toDateString())->count();
                    }
                    $latestVal  = end($activityData);
                    $prevVal    = $activityData[count($activityData)-2] ?? 0;
                    $changeSign = $latestVal >= $prevVal ? '+' : '';
                    $changeDiff = $latestVal - $prevVal;
                @endphp
                <div style="display:flex;align-items:baseline;gap:10px;margin-bottom:14px">
                    <span style="font-size:1.8rem;font-weight:800;color:#f8fafc;letter-spacing:-.04em;line-height:1">{{ $latestVal }}</span>
                    <span style="font-size:.72rem;font-weight:600;padding:2px 8px;border-radius:20px;font-family:'Geist Mono',monospace;
                        {{ $changeDiff >= 0 ? 'background:rgba(16,185,129,.12);color:#34d399;border:1px solid rgba(16,185,129,.25)' : 'background:rgba(244,63,94,.12);color:#f87171;border:1px solid rgba(244,63,94,.25)' }}">
                        {{ $changeSign }}{{ $changeDiff }} hari ini
                    </span>
                    <span style="font-size:.7rem;color:var(--txt-dim);margin-left:auto;font-family:'Geist Mono',monospace">scan presensi</span>
                </div>
                <canvas id="dash-activity-chart" style="width:100%;height:130px"
                        data-labels='@json($activityLabels)'
                        data-values='@json($activityData)'></canvas>
            </div>

            {{-- Runtime Info --}}
            <div class="card" style="padding:16px 18px">
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:12px">
                    <i data-lucide="terminal" style="width:15px;height:15px;color:var(--accent)"></i>
                    <span style="font-size:0.82rem;font-weight:700;color:var(--txt-head)">Runtime Info</span>
                </div>
                <div style="display:flex;flex-direction:column;gap:5px">
                    @foreach([
                        ['k'=>'PHP',        'v'=>PHP_VERSION,                    'c'=>'#818cf8'],
                        ['k'=>'Laravel',    'v'=>'v'.app()->version(),            'c'=>'#f87171'],
                        ['k'=>'Timezone',   'v'=>config('app.timezone','UTC'),    'c'=>'#06b6d4'],
                        ['k'=>'Queue',      'v'=>config('queue.default','sync'),  'c'=>'#34d399'],
                        ['k'=>'Mail',       'v'=>config('mail.default','smtp'),   'c'=>'#fbbf24'],
                    ] as $ei)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 8px;border-radius:7px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.03)">
                        <span style="font-size:.71rem;color:var(--txt-dim);font-family:'Geist Mono',monospace">{{ $ei['k'] }}</span>
                        <span style="font-size:.74rem;font-weight:600;color:{{ $ei['c'] }};font-family:'Geist Mono',monospace">{{ $ei['v'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Disk Usage — full-width crypto bar style --}}
            @php $dp = $stats['disk_percent']??0; $dc = $dp>90?'#f43f5e':($dp>70?'#f59e0b':'#10b981'); $dcA = $dp>90?'rgba(244,63,94,.12)':($dp>70?'rgba(245,158,11,.12)':'rgba(16,185,129,.12)'); @endphp
            <div class="card" style="padding:22px 24px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:34px;height:34px;border-radius:9px;background:{{ $dcA }};border:1px solid {{ $dc }}44;display:flex;align-items:center;justify-content:center">
                            <i data-lucide="hard-drive" style="width:15px;height:15px;color:{{ $dc }}"></i>
                        </div>
                        <div>
                            <p style="font-size:.82rem;font-weight:700;color:var(--txt-head);margin:0">Disk Usage</p>
                            <p style="font-size:.68rem;color:var(--txt-dim);margin:0;font-family:'Geist Mono',monospace">
                                {{ round(($stats['disk_total']-$stats['disk_free'])/1073741824,1) }} GB / {{ round($stats['disk_total']/1073741824,1) }} GB
                            </p>
                        </div>
                    </div>
                    <span style="font-size:2rem;font-weight:800;letter-spacing:-.04em;color:{{ $dc }};font-family:'Geist Mono',monospace;text-shadow:0 0 20px {{ $dc }}66">{{ $dp }}%</span>
                </div>
                {{-- Crypto-style segmented bar --}}
                <div style="position:relative;height:12px;border-radius:12px;background:rgba(255,255,255,.04);overflow:hidden;margin-bottom:8px;border:1px solid rgba(255,255,255,.06)">
                    <div style="height:100%;width:{{ $dp }}%;border-radius:12px;
                        background:linear-gradient(90deg,{{ $dc }}88 0%,{{ $dc }} 100%);
                        box-shadow:0 0 16px {{ $dc }}55;
                        transition:width 1.4s cubic-bezier(.4,0,.2,1);
                        position:relative;overflow:hidden">
                        <div style="position:absolute;inset:0;background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.12) 50%,transparent 100%);animation:shimmer 2.5s infinite"></div>
                    </div>
                    @foreach([25,50,75] as $tick)
                    <div style="position:absolute;top:0;bottom:0;left:{{ $tick }}%;width:1px;background:rgba(255,255,255,.08)"></div>
                    @endforeach
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:16px">
                    @foreach(['0%','25%','50%','75%','100%'] as $lbl)
                    <span style="font-size:.6rem;color:rgba(100,116,139,.5);font-family:'Geist Mono',monospace">{{ $lbl }}</span>
                    @endforeach
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    @foreach([['label'=>'Config','ok'=>$stats['cache_config']==='CACHED'],['label'=>'Routes','ok'=>$stats['cache_route']==='CACHED'],['label'=>'Views','ok'=>$stats['cache_view']==='CACHED']] as $c)
                    <span style="display:inline-flex;align-items:center;gap:5px;font-size:.7rem;font-weight:600;padding:5px 13px;border-radius:20px;font-family:'Geist Mono',monospace;
                        background:{{ $c['ok']?'rgba(16,185,129,.08)':'rgba(244,63,94,.08)' }};
                        color:{{ $c['ok']?'#34d399':'#f87171' }};
                        border:1px solid {{ $c['ok']?'rgba(16,185,129,.2)':'rgba(244,63,94,.2)' }}">
                        @if($c['ok'])<svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 6l3 3 5-5"/></svg>
                        @else<svg width="10" height="10" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M2.5 2.5l7 7M9.5 2.5l-7 7"/></svg>@endif
                        {{ $c['label'] }}
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── System Health Bar ── --}}
    <div class="card" style="padding:20px 24px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
            <h3 style="font-size:0.92rem;font-weight:700;color:var(--txt-head);display:flex;align-items:center;gap:8px">
                <i data-lucide="shield-check" style="width:17px;height:17px;color:#10b981"></i>
                System Health
            </h3>
            <span class="live"><i></i> All Systems Operational</span>
        </div>
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px">
            @php
                $healthItems = [
                    ['label'=>'Database',   'ok'=>true,           'icon'=>'database',    'detail'=>$stats['db_driver']??'mysql'],
                    ['label'=>'Cache',      'ok'=>($stats['cache_config']==='CACHED'), 'icon'=>'cpu',      'detail'=>$stats['cache_config']==='CACHED'?'Cached':'Not Cached'],
                    ['label'=>'Storage',    'ok'=>($dp<90),        'icon'=>'hard-drive',  'detail'=>$dp.'% used'],
                    ['label'=>'Debug',      'ok'=>!$debugOn,       'icon'=>'bug',         'detail'=>$debugOn?'Active':'Off'],
                    ['label'=>'Session',    'ok'=>true,            'icon'=>'lock',        'detail'=>$stats['session_driver']??'file'],
                ];
            @endphp
            @foreach($healthItems as $hi)
            @php $hiOk = $hi['ok']; @endphp
            <div style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:14px 12px;border-radius:10px;background:{{ $hiOk?'rgba(16,185,129,.06)':'rgba(244,63,94,.06)' }};border:1px solid {{ $hiOk?'rgba(16,185,129,.2)':'rgba(244,63,94,.2)' }};transition:all .2s ease" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                <div style="width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;background:{{ $hiOk?'rgba(16,185,129,.12)':'rgba(244,63,94,.12)' }};color:{{ $hiOk?'#10b981':'#f43f5e' }}">
                    <i data-lucide="{{ $hi['icon'] }}" style="width:17px;height:17px"></i>
                </div>
                <span style="font-size:0.78rem;font-weight:700;color:var(--txt-head)">{{ $hi['label'] }}</span>
                <span style="font-size:0.68rem;color:var(--txt-dim);font-family:'Geist Mono',monospace;text-align:center">{{ $hi['detail'] }}</span>
                <div style="width:6px;height:6px;border-radius:50%;background:{{ $hiOk?'#10b981':'#f43f5e' }};box-shadow:0 0 8px {{ $hiOk?'#10b981':'#f43f5e' }};animation:pulseGlow 2s infinite"></div>
            </div>
            @endforeach
        </div>
    </div>

</div>

<style>
/* Chart.js canvas sizing */
#dash-activity-chart { max-height: 140px; }

/* Shimmer animation for disk bar */
@keyframes shimmer {
    0%   { transform: translateX(-100%); }
    100% { transform: translateX(300%); }
}

/* Stat counter animation */
@keyframes countUp {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.num[data-count] {
    animation: countUp .5s var(--ease) both;
}
</style>

<script>
/* ── Load Chart.js dynamically ── */
(function() {
    if (typeof Chart !== 'undefined') { initDashChart(); return; }
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js';
    s.onload = initDashChart;
    document.head.appendChild(s);
})();

function initDashChart() {
    var ctx = document.getElementById('dash-activity-chart');
    if (!ctx || typeof Chart === 'undefined') return;

    var labels = JSON.parse(ctx.dataset.labels || '[]');
    var data   = JSON.parse(ctx.dataset.values || '[]');

    /* Crypto-style gradient fill */
    var canvas = ctx;
    var gradFill = canvas.getContext('2d').createLinearGradient(0, 0, 0, 140);
    gradFill.addColorStop(0,   'rgba(99,102,241,0.35)');
    gradFill.addColorStop(0.6, 'rgba(99,102,241,0.08)');
    gradFill.addColorStop(1,   'rgba(99,102,241,0.00)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                fill: true,
                backgroundColor: gradFill,
                borderColor: '#6366f1',
                borderWidth: 2,
                pointBackgroundColor: data.map(function(v, i) {
                    return i === data.length - 1 ? '#818cf8' : 'transparent';
                }),
                pointBorderColor: data.map(function(v, i) {
                    return i === data.length - 1 ? '#6366f1' : 'transparent';
                }),
                pointRadius: data.map(function(v, i) {
                    return i === data.length - 1 ? 5 : 0;
                }),
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#818cf8',
                tension: 0.45,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 800, easing: 'easeInOutQuart' },
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(9,13,28,0.95)',
                    borderColor: 'rgba(99,102,241,0.5)',
                    borderWidth: 1,
                    titleColor: '#f8fafc',
                    bodyColor: '#94a3b8',
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        title: function(items) { return items[0].label; },
                        label: function(item) { return '  ' + item.raw + ' scan presensi'; }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#475569',
                        font: { size: 10, family: "'Geist Mono', monospace" },
                        maxRotation: 0,
                    },
                    border: { display: false },
                },
                y: {
                    position: 'right',
                    grid: {
                        color: 'rgba(255,255,255,0.04)',
                        drawBorder: false,
                    },
                    ticks: {
                        color: '#475569',
                        font: { size: 10, family: "'Geist Mono', monospace" },
                        stepSize: 1,
                        padding: 8,
                        maxTicksLimit: 5,
                    },
                    border: { display: false },
                }
            }
        }
    });
}

/* ── Live clock tick (dashboard) ── */
(function tickDashClock() {
    var el = document.getElementById('dash-srv-clock');
    if (!el) return;
    setInterval(function() {
        var now = new Date();
        var h = String(now.getHours()).padStart(2,'0');
        var m = String(now.getMinutes()).padStart(2,'0');
        var s = String(now.getSeconds()).padStart(2,'0');
        el.textContent = h + ':' + m + ':' + s;
        var dt = document.getElementById('dash-clock-text');
        if (dt) dt.textContent = h + ':' + m;
    }, 1000);
})();

/* ── Count-up animation for stat numbers ── */
document.querySelectorAll('.num[data-count]').forEach(function(el) {
    var target = parseInt(el.dataset.count, 10) || 0;
    if (target === 0) { el.textContent = '0'; return; }
    var start = 0;
    var dur = 800;
    var startTime = null;
    function step(ts) {
        if (!startTime) startTime = ts;
        var prog = Math.min((ts - startTime) / dur, 1);
        var ease = 1 - Math.pow(1 - prog, 3);
        el.textContent = Math.round(start + (target - start) * ease);
        if (prog < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
});
</script>

{{-- ═════════ TAB: ANDROID MANAGER ═════════ --}}
<div id="tab-apk" class="tab-content">
    <header class="page-head">
        <h2 style="display:flex;align-items:center;gap:10px">
            <span style="width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:26px;height:26px;fill:#3DDC84"><path d="M17.523 15.341a.58.58 0 0 1-.58-.58.58.58 0 0 1 .58-.58.58.58 0 0 1 .58.58.58.58 0 0 1-.58.58m-11.046 0a.58.58 0 0 1-.58-.58.58.58 0 0 1 .58-.58.58.58 0 0 1 .58.58.58.58 0 0 1-.58.58M17.78 9.3l1.738-3.01a.361.361 0 0 0-.132-.494.362.362 0 0 0-.494.133l-1.759 3.047a10.879 10.879 0 0 0-5.133-1.27c-1.846 0-3.585.47-5.133 1.27L5.108 5.93a.362.362 0 0 0-.494-.133.361.361 0 0 0-.132.494L6.22 9.3C3.625 10.78 1.9 13.438 1.9 16.5h20.2c0-3.062-1.725-5.72-4.32-7.2"/></svg>
            </span>
            Android Manager
        </h2>
        <p>Unggah dan bagikan build Android terbaru ke pengguna aplikasi.</p>
    </header>

    @if($appSetting?->apk_file)
        <div class="card" style="margin-bottom:24px">
            <div class="apk">
                <div class="apk-info">
                    <span class="ico ico-lg t-ok"><i data-lucide="package-check" style="width:22px;height:22px"></i></span>
                    <div>
                        <h4>{{ $appSetting->apk_name ?? 'ICB CT Presensi' }}
                            <span class="chip mono">v{{ ltrim($appSetting->apk_version_label ?? $appSetting->apk_version ?? '1.0', 'vV') }}</span>
                        </h4>
                        <p class="mono mute">{{ $appSetting->apk_size_human ?? '-' }} • diunggah {{ $appSetting->apk_uploaded_at?->diffForHumans() ?? '-' }}</p>
                    </div>
                </div>
                <div class="row">
                    <a href="{{ $appSetting->apk_url }}" target="_blank" class="btn btn-ghost"><i data-lucide="download" style="width:15px;height:15px"></i> Unduh APK</a>
                    <form action="{{ route('developer.apk.delete', $secret) }}" method="POST"
                          onsubmit="return confirmAction(this, '🗑️ Hapus APK ini?\nPengguna tidak bisa mengunduhnya lagi.', 'danger')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" aria-label="Hapus APK" title="Hapus APK"><i data-lucide="trash-2" style="width:15px;height:15px"></i></button>
                    </form>
                </div>
            </div>
            @if(!empty($appSetting->apk_min_android) || !empty($appSetting->apk_changelog))
                <div class="apk-meta">
                    @if(!empty($appSetting->apk_min_android))
                        <span class="pill"><i data-lucide="smartphone" style="width:14px;height:14px;color:var(--txt-dim)"></i> {{ $appSetting->apk_min_android }}</span>
                    @endif
                    @if(!empty($appSetting->apk_changelog))
                        <span class="pill"><i data-lucide="file-text" style="width:14px;height:14px;color:var(--txt-dim)"></i> {{ $appSetting->apk_changelog }}</span>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <div class="card">
        <div class="card-head">
            <h3><i data-lucide="upload-cloud" style="width:18px;height:18px;color:#818cf8"></i> Unggah Build Baru</h3>
        </div>
        <div class="pad">
            <form action="{{ route('developer.apk', $secret) }}" method="POST" enctype="multipart/form-data" class="apk-form">
                @csrf
                <div class="apk-drop">
                    <label class="label">File APK</label>
                    <div class="drop" id="apk-drop" tabindex="0" role="button"
                          onclick="document.getElementById('apk-input').click()"
                          onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();document.getElementById('apk-input').click()}"
                          ondragover="event.preventDefault(); this.classList.add('over')"
                          ondragleave="this.classList.remove('over')"
                          ondrop="handleApkDrop(event, this)">
                        <span class="ico ico-lg t-violet"><i data-lucide="upload-cloud" style="width:22px;height:22px"></i></span>
                        <p><b>Klik untuk memilih</b> atau seret file ke area ini</p>
                        <p id="apk-filename" class="mono mute">Format .apk, ukuran maksimal 100MB</p>
                    </div>
                    <input type="file" id="apk-input" name="apk_file" accept=".apk" hidden onchange="setApkName(this.files[0])">
                    @error('apk_file')<p class="err">{{ $message }}</p>@enderror
                </div>

                <div class="apk-fields">
                    <div class="form-grid">
                        <div>
                            <label class="label">Nama Aplikasi</label>
                            <input type="text" name="apk_name" class="input" value="{{ old('apk_name', $appSetting?->apk_name ?? '') }}" placeholder="ICB CT Mobile">
                        </div>
                        <div>
                            <label class="label">Label Versi</label>
                            <input type="text" name="apk_version" class="input mono" value="{{ old('apk_version', $appSetting?->apk_version ?? '') }}" placeholder="1.0.0">
                        </div>
                        <div>
                            <label class="label">Minimal Versi Android</label>
                            <input type="text" name="apk_min_android" class="input" value="{{ old('apk_min_android', $appSetting?->apk_min_android ?? '') }}" placeholder="Android 8.0+">
                        </div>
                        <div>
                            <label class="label">Catatan Perubahan (Changelog)</label>
                            <input type="text" name="apk_changelog" class="input" value="{{ old('apk_changelog', $appSetting?->apk_changelog ?? '') }}" placeholder="Perbaikan performa dan antarmuka">
                        </div>
                    </div>
                    <div class="end">
                        <button type="submit" class="btn"><i data-lucide="save" style="width:15px;height:15px"></i> Simpan APK</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═════════ TAB: iOS MANAGER ═════════ --}}
<div id="tab-ios" class="tab-content">
    <header class="page-head">
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 814 1000" style="width:22px;height:22px;vertical-align:middle;margin-right:6px;fill:var(--accent)"><path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76 0-103.7 40.8-165.9 40.8s-105-42.3-150.3-110.7c-46-70.4-73.9-161.4-73.9-247.9 0-157.1 100.1-247.4 198.5-247.4 51.6 0 95.1 33.9 127.5 33.9 31.3 0 80.4-36.1 139.2-36.1 22.4 0 108.2 2 167 74.2zM726.4 82.4c24.2-28.8 41.7-68.7 41.7-108.6 0-5.5-.5-11.1-1.5-15.5-39.1 1.5-85.5 26.1-113.8 56.3-22.4 24.7-43.2 64.6-43.2 105.1 0 6 1 12 1.5 14.1 2.5.5 6.5 1 10.5 1 35.4 0 79.4-23.2 104.8-52.4z"/></svg>
            iOS Manager
        </h2>
        <p>Unggah dan bagikan build iOS (IPA) terbaru ke pengguna aplikasi.</p>
    </header>

    @if($appSetting?->ios_file)
        <div class="card" style="margin-bottom:24px">
            <div class="apk">
                <div class="apk-info">
                    <span class="ico ico-lg t-sky"><i data-lucide="package-check" style="width:22px;height:22px"></i></span>
                    <div>
                        <h4>{{ $appSetting->ios_name ?? 'ICB CT Presensi' }}
                            <span class="chip mono">v{{ ltrim($appSetting->ios_version_label ?? $appSetting->ios_version ?? '1.0', 'vV') }}</span>
                        </h4>
                        <p class="mono mute">{{ $appSetting->ios_size_human ?? '-' }} • diunggah {{ $appSetting->ios_uploaded_at?->diffForHumans() ?? '-' }}</p>
                    </div>
                </div>
                <div class="row">
                    <a href="{{ $appSetting->ios_url }}" target="_blank" class="btn btn-ghost"><i data-lucide="download" style="width:15px;height:15px"></i> Unduh IPA</a>
                    <form action="{{ route('developer.ios.delete', $secret) }}" method="POST"
                          onsubmit="return confirmAction(this, '🗑️ Hapus file iOS ini?\nPengguna tidak bisa mengunduhnya lagi.', 'danger')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" aria-label="Hapus iOS" title="Hapus iOS"><i data-lucide="trash-2" style="width:15px;height:15px"></i></button>
                    </form>
                </div>
            </div>
            @if(!empty($appSetting->ios_min_version) || !empty($appSetting->ios_changelog))
                <div class="apk-meta">
                    @if(!empty($appSetting->ios_min_version))
                        <span class="pill"><i data-lucide="smartphone" style="width:14px;height:14px;color:var(--txt-dim)"></i> {{ $appSetting->ios_min_version }}</span>
                    @endif
                    @if(!empty($appSetting->ios_changelog))
                        <span class="pill"><i data-lucide="file-text" style="width:14px;height:14px;color:var(--txt-dim)"></i> {{ $appSetting->ios_changelog }}</span>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <div class="card">
        <div class="card-head">
            <h3><i data-lucide="upload-cloud" style="width:18px;height:18px;color:#38bdf8"></i> Unggah Build iOS Baru</h3>
        </div>
        <div class="pad">
            <form action="{{ route('developer.ios', $secret) }}" method="POST" enctype="multipart/form-data" class="apk-form">
                @csrf
                <div class="apk-drop">
                    <label class="label">File IPA</label>
                    <div class="drop" id="ios-drop" tabindex="0" role="button"
                         onclick="document.getElementById('ios-input').click()"
                         onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();document.getElementById('ios-input').click()}"
                         ondragover="event.preventDefault(); this.classList.add('over')"
                         ondragleave="this.classList.remove('over')"
                         ondrop="handleIosDrop(event, this)">
                        <span class="ico ico-lg t-sky"><i data-lucide="upload-cloud" style="width:22px;height:22px"></i></span>
                        <p><b>Klik untuk memilih</b> atau seret file ke area ini</p>
                        <p id="ios-filename" class="mono mute">Format .ipa / .zip, ukuran maksimal 200MB</p>
                    </div>
                    <input type="file" id="ios-input" name="ios_file" accept=".ipa,.zip" hidden
                           onchange="document.getElementById('ios-filename').textContent = this.files[0]?.name || 'Format .ipa / .zip, ukuran maksimal 200MB'">
                    @error('ios_file')<p class="err">{{ $message }}</p>@enderror
                </div>

                <div class="apk-fields">
                    <div class="form-grid">
                        <div>
                            <label class="label">Nama Aplikasi</label>
                            <input type="text" name="ios_name" class="input" value="{{ old('ios_name', $appSetting?->ios_name ?? '') }}" placeholder="ICB CT Mobile">
                        </div>
                        <div>
                            <label class="label">Label Versi</label>
                            <input type="text" name="ios_version" class="input mono" value="{{ old('ios_version', $appSetting?->ios_version ?? '') }}" placeholder="1.0.0">
                        </div>
                        <div>
                            <label class="label">Minimal Versi iOS</label>
                            <input type="text" name="ios_min_version" class="input" value="{{ old('ios_min_version', $appSetting?->ios_min_version ?? '') }}" placeholder="iOS 14.0+">
                        </div>
                        <div>
                            <label class="label">Catatan Perubahan (Changelog)</label>
                            <input type="text" name="ios_changelog" class="input" value="{{ old('ios_changelog', $appSetting?->ios_changelog ?? '') }}" placeholder="Perbaikan performa dan antarmuka">
                        </div>
                    </div>
                    <div class="end">
                        <button type="submit" class="btn"><i data-lucide="save" style="width:15px;height:15px"></i> Simpan iOS</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═════════ TAB: SYSTEM STATE ═════════ --}}
<div id="tab-system" class="tab-content">
    <header class="page-head">
        <h2><i data-lucide="cpu" style="width:22px;height:22px;color:var(--accent);vertical-align:middle;margin-right:6px"></i> System State</h2>
        <p>Atur ketersediaan aplikasi dan jalankan tools eksekusi sistem.</p>
    </header>

    <div class="stack">
        <div class="card">
            <form action="{{ route('developer.maintenance', $secret) }}" method="POST">
                @csrf
                <div class="pad bb" style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px">
                    <div>
                        <h3><i data-lucide="shield-alert" style="width:18px;height:18px;color:#fbbf24;vertical-align:middle;margin-right:6px"></i> Mode Maintenance <span class="state {{ $mOn ? 'on' : '' }}"><i></i>{{ $mOn ? 'Aktif' : 'Nonaktif' }}</span></h3>
                        <p class="mute" style="max-width:34rem;margin-top:5px">Saat aktif, hanya developer yang bisa login. Admin, operator, guru, dan piket akan melihat halaman maintenance sistem.</p>
                    </div>
                    <label class="switch">
                        <input type="hidden" name="maintenance_mode" value="0">
                        <input type="checkbox" name="maintenance_mode" value="1" {{ $mOn ? 'checked' : '' }}>
                        <span class="track"><span class="thumb"></span></span>
                        <span class="sr">Aktifkan mode maintenance</span>
                    </label>
                </div>
                <div class="pad bb">
                    <label class="label">Pesan Maintenance</label>
                    <textarea name="maintenance_message" rows="3" class="input" placeholder="Sistem sedang dalam proses pemeliharaan berkala...">{{ $setting->maintenance_message }}</textarea>
                </div>
                <div class="pad foot end">
                    <button type="submit" class="btn"><i data-lucide="save" style="width:15px;height:15px"></i> Simpan Pengaturan</button>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-head">
                <h3><i data-lucide="terminal" style="width:18px;height:18px;color:#38bdf8"></i> Developer Tools & Artisan Commands</h3>
            </div>
            <div class="tools">
                <a href="{{ route('developer.clear-cache', $secret) }}" onclick="return confirmAction(this, '🧹 Bersihkan semua cache?')" class="tool">
                    <span class="ico ico-lg t-sky"><i data-lucide="trash-2" style="width:20px;height:20px"></i></span>
                    <div><b>Sapu Jagat</b><small>Hapus cache config, route, view, & app</small></div>
                </a>
                <a href="{{ route('developer.migrate', $secret) }}" onclick="return confirmAction(this, '🗄️ Jalankan migration?\nPastikan backup ada!')" class="tool">
                    <span class="ico ico-lg t-ok"><i data-lucide="database" style="width:20px;height:20px"></i></span>
                    <div><b>Run Migration</b><small>artisan migrate --force</small></div>
                </a>
                <a href="{{ route('developer.optimize', $secret) }}" onclick="return confirmAction(this, '⚡ Rebuild semua cache?')" class="tool">
                    <span class="ico ico-lg t-warn"><i data-lucide="zap" style="width:20px;height:20px"></i></span>
                    <div><b>Optimize</b><small>Bangun ulang cache config, route, & view</small></div>
                </a>
                <a href="{{ url('/fix-session?secret=' . $secret) }}" onclick="return confirmAction(this, '🔧 Perbaiki session dir & hapus semua cache?')" class="tool">
                    <span class="ico ico-lg t-warn"><i data-lucide="wrench" style="width:20px;height:20px"></i></span>
                    <div><b>Fix Session</b><small>Perbaiki session dir + clear cache</small></div>
                </a>
                <button type="button" class="tool" id="debug-toggle-btn" data-debug="{{ $stats['debug'] ? '1' : '0' }}">
                    <span class="ico ico-lg t-bad" id="debug-toggle-ico"><i data-lucide="bug" style="width:20px;height:20px"></i></span>
                    <div><b id="debug-toggle-label">Debug ON</b><small id="debug-toggle-desc">Klik untuk mematikan debug</small></div>
                </button>
                <a href="{{ route('developer.run-seeder', $secret) }}" onclick="return confirmAction(this, '🌱 Jalankan database seeder?\nMembuat ulang akun developer dan data demo.')" class="tool">
                    <span class="ico ico-lg t-violet"><i data-lucide="sprout" style="width:20px;height:20px"></i></span>
                    <div><b>Run Seeder</b><small>db:seed --force</small></div>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ═════════ TAB: RELEASES ═════════ --}}
@php
    $items = $updates instanceof \Illuminate\Pagination\AbstractPaginator ? $updates->getCollection() : collect($updates);
    $typeMeta = [
        'feature' => ['Feature', 'violet', 'star'],
        'update'  => ['Update',  'sky',    'git-commit'],
        'fix'     => ['Fix',     'warn',   'wrench'],
        'hotfix'  => ['Hotfix',  'bad',    'flame'],
    ];
    $typeKey = fn ($u) => isset($typeMeta[$u->type]) ? $u->type : 'update';
    $counts  = $items->countBy($typeKey);
    $latest  = $items->first();
    $groups  = $items->groupBy(fn ($u) => $u->created_at->format('Y-m'));
@endphp
<div id="tab-releases" class="tab-content">

    <div class="rel-head">
        <header class="page-head" style="margin-bottom:0">
            <h2><i data-lucide="git-pull-request" style="width:22px;height:22px;color:var(--accent);vertical-align:middle;margin-right:6px"></i> Riwayat Rilis</h2>
            <p>Catatan rilis dan pembaruan sistem yang didistribusikan ke pengguna.</p>
        </header>
        <button type="button" class="btn" data-open-release><i data-lucide="plus" style="width:15px;height:15px"></i> Rilis Baru</button>
    </div>

    <div class="rel-stats">
        <div class="card mini"><span class="ico t-ok"><i data-lucide="layers" style="width:17px;height:17px"></i></span><div><p>Total Rilis</p><b>{{ $items->count() }}</b></div></div>
        <div class="card mini"><span class="ico t-violet"><i data-lucide="tag" style="width:17px;height:17px"></i></span><div><p>Versi Terbaru</p><b class="mono">{{ $latest ? ('v' . ltrim($latest->version, 'vV')) : '-' }}</b></div></div>
        <div class="card mini"><span class="ico t-violet"><i data-lucide="star" style="width:17px;height:17px"></i></span><div><p>Fitur Baru</p><b>{{ $counts->get('feature', 0) }}</b></div></div>
        <div class="card mini"><span class="ico t-warn"><i data-lucide="wrench" style="width:17px;height:17px"></i></span><div><p>Perbaikan</p><b>{{ $counts->get('fix', 0) + $counts->get('hotfix', 0) }}</b></div></div>
    </div>

    <div class="rel-grid">
        <div class="tl">
            @forelse($groups as $ym => $rows)
                <section class="tl-group">
                    <h3 class="tl-month">{{ $rows->first()->created_at->locale('id')->translatedFormat('F Y') }}</h3>
                    <ol class="tl-list">
                        @foreach($rows as $u)
                            @php
                                $tk = $typeKey($u);
                                [$tLabel, $tone, $tIcon] = $typeMeta[$tk];
                                $isLatest = $latest && $u->id === $latest->id;
                                $lines = array_values(array_filter(
                                    array_map('trim', preg_split('/\r\n|\r|\n/', (string) $u->content)),
                                    fn ($l) => $l !== ''
                                ));
                            @endphp
                            <li class="tl-item" data-type="{{ $tk }}">
                                <span class="ico tl-dot t-{{ $tone }}"><i data-lucide="{{ $tIcon }}" style="width:16px;height:16px"></i></span>
                                <article class="card tl-card {{ $isLatest ? 'latest' : '' }}">
                                    <div class="tl-top">
                                        <div class="tl-title">
                                            <h4>{{ $u->title }}</h4>
                                            <span class="chip mono">v{{ ltrim($u->version, 'vV') }}</span>
                                            <span class="badge t-{{ $tone }}">{{ $tLabel }}</span>
                                            @if($isLatest)<span class="badge" style="background:var(--accent);color:#fff;box-shadow:0 0 10px var(--accent-glow)">Terbaru</span>@endif
                                        </div>
                                        <div class="tl-side">
                                            <time class="mono" datetime="{{ $u->created_at->toDateString() }}">{{ $u->created_at->locale('id')->translatedFormat('d M Y') }}</time>
                                            <form action="{{ route('developer.updates.delete', [$secret, $u->id]) }}" method="POST"
                                                  onsubmit="return confirmAction(this, '🗑️ Hapus log rilis ini?', 'danger')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="trash" aria-label="Hapus rilis {{ $u->version }}"><i data-lucide="trash-2" style="width:15px;height:15px"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                    @if(count($lines))
                                        <ul class="cl">
                                            @foreach($lines as $ln)
                                                <li>{{ preg_replace('/^[-*•·]\s*/u', '', $ln) }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </article>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @empty
                <div class="card empty">
                    <span class="ico ico-lg t-violet"><i data-lucide="history" style="width:22px;height:22px"></i></span>
                    <h4>Belum ada riwayat rilis</h4>
                    <p class="mute">Rilis pertamamu akan muncul di sini sebagai timeline.</p>
                    <button type="button" class="btn" data-open-release><i data-lucide="plus" style="width:15px;height:15px"></i> Buat rilis pertama</button>
                </div>
            @endforelse

            <div class="card empty" id="nb-tl-empty" hidden>
                <span class="ico ico-lg t-warn"><i data-lucide="search-x" style="width:22px;height:22px"></i></span>
                <h4>Tidak ada rilis dengan jenis ini</h4>
                <p class="mute">Pilih jenis lain di filter untuk melihat rilis lainnya.</p>
            </div>
        </div>

        <aside class="rel-aside">
            <div class="card sticky">
                <div class="card-head"><h3>Filter jenis rilis</h3></div>
                <div class="fbtns">
                    <button type="button" class="fbtn active" data-filter="all">
                        <span class="fico t-ok"><i data-lucide="layers" style="width:14px;height:14px"></i></span>Semua <small>{{ $items->count() }}</small>
                    </button>
                    @foreach($typeMeta as $key => [$fLabel, $fTone, $fIcon])
                        <button type="button" class="fbtn" data-filter="{{ $key }}">
                            <span class="fico t-{{ $fTone }}"><i data-lucide="{{ $fIcon }}" style="width:14px;height:14px"></i></span>{{ $fLabel }} <small>{{ $counts->get($key, 0) }}</small>
                        </button>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
</div>

{{-- ═════════ TAB: SETTINGS & TEMA ═════════ --}}
<div id="tab-settings" class="tab-content">
    <header class="page-head">
        <h2><i data-lucide="settings" style="width:22px;height:22px;color:var(--accent);vertical-align:middle;margin-right:8px"></i> Settings & Tema Konsol</h2>
        <p>Sesuaikan tampilan antarmuka (theme) dan preferensi kerja Developer Console sesuai kenyamanan kamu.</p>
    </header>

    <div class="card" style="margin-bottom:24px">
        <div class="card-head">
            <h3><i data-lucide="palette" style="width:18px;height:18px;color:var(--accent)"></i> Pilihan Tema Konsol (Themes)</h3>
            <span class="chip mono" id="active-theme-label">Obsidian Console</span>
        </div>
        <div class="pad">
            <p class="mute" style="margin-bottom:22px">
                Setiap tema dirancang dengan kontras warna presisi tinggi, nuansa developer modern, dan aksen glowing yang elegan. Klik kartu di bawah untuk langsung mengganti tema secara realtime:
            </p>

            <div class="theme-grid">
                {{-- Theme 1: Obsidian Console --}}
                <div class="theme-card" data-theme="obsidian" onclick="window.setConsoleTheme('obsidian', event)">
                    <div class="theme-preview-box" style="background:#090d16;">
                        <div class="theme-mini-side" style="background:rgba(15,23,42,0.9); border-color:rgba(255,255,255,0.08);">
                            <div class="mini-logo" style="background:#6366f1;"></div>
                            <div class="mini-nav-line active" style="background:#6366f1;"></div>
                            <div class="mini-nav-line" style="background:#94a3b8;"></div>
                            <div class="mini-nav-line" style="background:#94a3b8;"></div>
                        </div>
                        <div class="theme-mini-main">
                            <div class="theme-mini-top" style="background:rgba(15,23,42,0.8); border-color:rgba(255,255,255,0.08);">
                                <div class="mini-title-line" style="background:#f8fafc;"></div>
                                <div class="mini-pill" style="background:rgba(16,185,129,0.2); border:1px solid #10b981;"></div>
                            </div>
                            <div class="theme-mini-content">
                                <div class="theme-mini-stats">
                                    <div class="theme-mini-stat-card" style="background:#0f172a;">
                                        <div class="theme-mini-stat-line" style="background:#94a3b8;"></div>
                                        <div class="theme-mini-stat-val" style="background:#6366f1;"></div>
                                    </div>
                                    <div class="theme-mini-stat-card" style="background:#0f172a;">
                                        <div class="theme-mini-stat-line" style="background:#94a3b8;"></div>
                                        <div class="theme-mini-stat-val" style="background:#06b6d4;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="theme-card-foot">
                        <div class="theme-card-info">
                            <h4>Obsidian Console <span class="badge t-violet">Default</span></h4>
                            <p>Deep dark obsidian dengan aksen electric indigo & cyan glow.</p>
                            <div class="theme-card-swatches">
                                <span class="theme-swatch" style="background:#090d16;" title="Base #090d16"></span>
                                <span class="theme-swatch" style="background:#0f172a;" title="Surface #0f172a"></span>
                                <span class="theme-swatch" style="background:#6366f1;" title="Accent #6366f1"></span>
                                <span class="theme-swatch" style="background:#10b981;" title="Success #10b981"></span>
                            </div>
                        </div>
                        <span class="theme-check-badge"><i data-lucide="check" style="width:13px;height:13px"></i></span>
                    </div>
                </div>

                {{-- Theme 2: Tokyo Night --}}
                <div class="theme-card" data-theme="tokyo-night" onclick="window.setConsoleTheme('tokyo-night', event)">
                    <div class="theme-preview-box" style="background:#1a1b26;">
                        <div class="theme-mini-side" style="background:#16161e; border-color:rgba(122,162,247,0.2);">
                            <div class="mini-logo" style="background:#7aa2f7;"></div>
                            <div class="mini-nav-line active" style="background:#7aa2f7;"></div>
                            <div class="mini-nav-line" style="background:#a9b1d6;"></div>
                            <div class="mini-nav-line" style="background:#a9b1d6;"></div>
                        </div>
                        <div class="theme-mini-main">
                            <div class="theme-mini-top" style="background:#16161e; border-color:rgba(122,162,247,0.2);">
                                <div class="mini-title-line" style="background:#c0caf5;"></div>
                                <div class="mini-pill" style="background:rgba(115,218,202,0.2); border:1px solid #73daca;"></div>
                            </div>
                            <div class="theme-mini-content">
                                <div class="theme-mini-stats">
                                    <div class="theme-mini-stat-card" style="background:#1f2335;">
                                        <div class="theme-mini-stat-line" style="background:#787c99;"></div>
                                        <div class="theme-mini-stat-val" style="background:#7aa2f7;"></div>
                                    </div>
                                    <div class="theme-mini-stat-card" style="background:#1f2335;">
                                        <div class="theme-mini-stat-line" style="background:#787c99;"></div>
                                        <div class="theme-mini-stat-val" style="background:#bb9af7;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="theme-card-foot">
                        <div class="theme-card-info">
                            <h4>Tokyo Night <span class="badge t-sky">Populer</span></h4>
                            <p>Palet legendaris Tokyo Night indigo gelap dengan aksen neon blue & purple.</p>
                            <div class="theme-card-swatches">
                                <span class="theme-swatch" style="background:#1a1b26;" title="Base #1a1b26"></span>
                                <span class="theme-swatch" style="background:#16161e;" title="Surface #16161e"></span>
                                <span class="theme-swatch" style="background:#7aa2f7;" title="Accent #7aa2f7"></span>
                                <span class="theme-swatch" style="background:#73daca;" title="Success #73daca"></span>
                            </div>
                        </div>
                        <span class="theme-check-badge"><i data-lucide="check" style="width:13px;height:13px"></i></span>
                    </div>
                </div>

                {{-- Theme 3: Cappuccino Mocha --}}
                <div class="theme-card" data-theme="cappuccino" onclick="window.setConsoleTheme('cappuccino', event)">
                    <div class="theme-preview-box" style="background:#140d0a;">
                        <div class="theme-mini-side" style="background:#1e140f; border-color:rgba(212,163,115,0.25);">
                            <div class="mini-logo" style="background:#d4a373;"></div>
                            <div class="mini-nav-line active" style="background:#d4a373;"></div>
                            <div class="mini-nav-line" style="background:#b08968;"></div>
                            <div class="mini-nav-line" style="background:#b08968;"></div>
                        </div>
                        <div class="theme-mini-main">
                            <div class="theme-mini-top" style="background:#1e140f; border-color:rgba(212,163,115,0.25);">
                                <div class="mini-title-line" style="background:#faf3eb;"></div>
                                <div class="mini-pill" style="background:rgba(82,183,136,0.25); border:1px solid #52b788;"></div>
                            </div>
                            <div class="theme-mini-content">
                                <div class="theme-mini-stats">
                                    <div class="theme-mini-stat-card" style="background:#2a1c15;">
                                        <div class="theme-mini-stat-line" style="background:#7f5539;"></div>
                                        <div class="theme-mini-stat-val" style="background:#d4a373;"></div>
                                    </div>
                                    <div class="theme-mini-stat-card" style="background:#2a1c15;">
                                        <div class="theme-mini-stat-line" style="background:#7f5539;"></div>
                                        <div class="theme-mini-stat-val" style="background:#e9c46a;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="theme-card-foot">
                        <div class="theme-card-info">
                            <h4>Cappuccino Mocha <span class="badge t-warn">Warm Coffee</span></h4>
                            <p>Nuansa roasted espresso, creamy caramel hangat, dan kayu walnut yang nyaman di mata.</p>
                            <div class="theme-card-swatches">
                                <span class="theme-swatch" style="background:#140d0a;" title="Espresso #140d0a"></span>
                                <span class="theme-swatch" style="background:#1e140f;" title="Mocha #1e140f"></span>
                                <span class="theme-swatch" style="background:#d4a373;" title="Caramel #d4a373"></span>
                                <span class="theme-swatch" style="background:#e9c46a;" title="Honey Latte #e9c46a"></span>
                            </div>
                        </div>
                        <span class="theme-check-badge"><i data-lucide="check" style="width:13px;height:13px"></i></span>
                    </div>
                </div>

                {{-- Theme 4: Nordic Aurora --}}
                <div class="theme-card" data-theme="nordic" onclick="window.setConsoleTheme('nordic', event)">
                    <div class="theme-preview-box" style="background:#0e141d;">
                        <div class="theme-mini-side" style="background:#17202d; border-color:rgba(136,192,208,0.2);">
                            <div class="mini-logo" style="background:#88c0d0;"></div>
                            <div class="mini-nav-line active" style="background:#88c0d0;"></div>
                            <div class="mini-nav-line" style="background:#9aa8bd;"></div>
                            <div class="mini-nav-line" style="background:#9aa8bd;"></div>
                        </div>
                        <div class="theme-mini-main">
                            <div class="theme-mini-top" style="background:#17202d; border-color:rgba(136,192,208,0.2);">
                                <div class="mini-title-line" style="background:#eceff4;"></div>
                                <div class="mini-pill" style="background:rgba(163,190,140,0.2); border:1px solid #a3be8c;"></div>
                            </div>
                            <div class="theme-mini-content">
                                <div class="theme-mini-stats">
                                    <div class="theme-mini-stat-card" style="background:#1f2b3c;">
                                        <div class="theme-mini-stat-line" style="background:#63758e;"></div>
                                        <div class="theme-mini-stat-val" style="background:#88c0d0;"></div>
                                    </div>
                                    <div class="theme-mini-stat-card" style="background:#1f2b3c;">
                                        <div class="theme-mini-stat-line" style="background:#63758e;"></div>
                                        <div class="theme-mini-stat-val" style="background:#8fbcbb;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="theme-card-foot">
                        <div class="theme-card-info">
                            <h4>Nordic Aurora <span class="badge t-sky">Clean</span></h4>
                            <p>Arsitektur kutub utara yang dingin, tenang, dan bersih dengan aksen frost ice blue.</p>
                            <div class="theme-card-swatches">
                                <span class="theme-swatch" style="background:#0e141d;" title="Base #0e141d"></span>
                                <span class="theme-swatch" style="background:#17202d;" title="Surface #17202d"></span>
                                <span class="theme-swatch" style="background:#88c0d0;" title="Accent #88c0d0"></span>
                                <span class="theme-swatch" style="background:#a3be8c;" title="Success #a3be8c"></span>
                            </div>
                        </div>
                        <span class="theme-check-badge"><i data-lucide="check" style="width:13px;height:13px"></i></span>
                    </div>
                </div>

                {{-- Theme 5: Cyberpunk Matrix --}}
                <div class="theme-card" data-theme="cyberpunk" onclick="window.setConsoleTheme('cyberpunk', event)">
                    <div class="theme-preview-box" style="background:#05080e;">
                        <div class="theme-mini-side" style="background:#0a111a; border-color:rgba(16,185,129,0.25);">
                            <div class="mini-logo" style="background:#10b981;"></div>
                            <div class="mini-nav-line active" style="background:#10b981;"></div>
                            <div class="mini-nav-line" style="background:#6ee7b7;"></div>
                            <div class="mini-nav-line" style="background:#6ee7b7;"></div>
                        </div>
                        <div class="theme-mini-main">
                            <div class="theme-mini-top" style="background:#0a111a; border-color:rgba(16,185,129,0.25);">
                                <div class="mini-title-line" style="background:#f0fdf4;"></div>
                                <div class="mini-pill" style="background:rgba(16,185,129,0.3); border:1px solid #10b981;"></div>
                            </div>
                            <div class="theme-mini-content">
                                <div class="theme-mini-stats">
                                    <div class="theme-mini-stat-card" style="background:#101c2b;">
                                        <div class="theme-mini-stat-line" style="background:#3b5771;"></div>
                                        <div class="theme-mini-stat-val" style="background:#10b981;"></div>
                                    </div>
                                    <div class="theme-mini-stat-card" style="background:#101c2b;">
                                        <div class="theme-mini-stat-line" style="background:#3b5771;"></div>
                                        <div class="theme-mini-stat-val" style="background:#06b6d4;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="theme-card-foot">
                        <div class="theme-card-info">
                            <h4>Cyberpunk Matrix <span class="badge t-ok">High Tech</span></h4>
                            <p>Gaya stealth terminal hacker berenergi tinggi dengan pendaran neon emerald & cyan.</p>
                            <div class="theme-card-swatches">
                                <span class="theme-swatch" style="background:#05080e;" title="Base #05080e"></span>
                                <span class="theme-swatch" style="background:#0a111a;" title="Surface #0a111a"></span>
                                <span class="theme-swatch" style="background:#10b981;" title="Accent #10b981"></span>
                                <span class="theme-swatch" style="background:#06b6d4;" title="Cyan #06b6d4"></span>
                            </div>
                        </div>
                        <span class="theme-check-badge"><i data-lucide="check" style="width:13px;height:13px"></i></span>
                    </div>
                </div>

                {{-- Theme 6: Alabaster Light --}}
                <div class="theme-card" data-theme="light" onclick="window.setConsoleTheme('light', event)">
                    <div class="theme-preview-box" style="background:#f8fafc; border:1px solid rgba(15,23,42,0.08);">
                        <div class="theme-mini-side" style="background:#ffffff; border-color:rgba(15,23,42,0.08);">
                            <div class="mini-logo" style="background:#4f46e5;"></div>
                            <div class="mini-nav-line active" style="background:#4f46e5;"></div>
                            <div class="mini-nav-line" style="background:#cbd5e1;"></div>
                            <div class="mini-nav-line" style="background:#cbd5e1;"></div>
                        </div>
                        <div class="theme-mini-main">
                            <div class="theme-mini-top" style="background:#ffffff; border-color:rgba(15,23,42,0.08);">
                                <div class="mini-title-line" style="background:#0f172a;"></div>
                                <div class="mini-pill" style="background:rgba(5,150,105,0.12); border:1px solid #059669;"></div>
                            </div>
                            <div class="theme-mini-content">
                                <div class="theme-mini-stats">
                                    <div class="theme-mini-stat-card" style="background:#ffffff; border-color:rgba(15,23,42,0.08);">
                                        <div class="theme-mini-stat-line" style="background:#94a3b8;"></div>
                                        <div class="theme-mini-stat-val" style="background:#4f46e5;"></div>
                                    </div>
                                    <div class="theme-mini-stat-card" style="background:#ffffff; border-color:rgba(15,23,42,0.08);">
                                        <div class="theme-mini-stat-line" style="background:#94a3b8;"></div>
                                        <div class="theme-mini-stat-val" style="background:#0284c7;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="theme-card-foot">
                        <div class="theme-card-info">
                            <h4>Alabaster Light <span class="badge t-sky">Clean Mode</span></h4>
                            <p>Desain terang modern, clean, dan super jernih dengan kontras tajam & nuansa visual elegan.</p>
                            <div class="theme-card-swatches">
                                <span class="theme-swatch" style="background:#f8fafc;" title="Base #f8fafc"></span>
                                <span class="theme-swatch" style="background:#ffffff; border-color:#cbd5e1;" title="Surface #ffffff"></span>
                                <span class="theme-swatch" style="background:#4f46e5;" title="Accent #4f46e5"></span>
                                <span class="theme-swatch" style="background:#059669;" title="Success #059669"></span>
                            </div>
                        </div>
                        <span class="theme-check-badge"><i data-lucide="check" style="width:13px;height:13px"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-head">
            <h3><i data-lucide="sliders" style="width:18px;height:18px;color:var(--sky)"></i> Informasi Konsol & Sesi</h3>
        </div>
        <div class="info">
            <div><p class="l">Developer Account</p><p class="v">Vio Atmajaya <span class="badge t-violet">SUPERUSER</span></p></div>
            <div><p class="l">Penyimpanan Preferensi</p><p class="v mono"><span class="dot ok"></span> LocalStorage Browser (Persisten)</p></div>
            <div><p class="l">Target Host</p><p class="v mono trunc">{{ $host }}</p></div>
            <div><p class="l">Konsol Versi</p><p class="v mono">v2.0 (Modern SaaS Edition)</p></div>
        </div>
    </div>
</div>

{{-- ═════════ TAB: PROFIL DEVELOPER ═════════ --}}
<div id="tab-profile" class="tab-content">

<style>
/* ── Profile Tab Styles ── */
.cp-hero {
    position: relative; overflow: hidden; border-radius: 20px;
    background: linear-gradient(135deg, #060b16 0%, #0d1425 50%, #0a1628 100%);
    border: 1px solid rgba(99,102,241,.3);
    padding: 40px 36px 32px; margin-bottom: 24px;
    box-shadow: 0 0 60px rgba(99,102,241,.08), inset 0 1px 0 rgba(255,255,255,.05);
}
.cp-hero::before {
    content: ''; position: absolute; inset: 0; pointer-events: none;
    background:
        radial-gradient(ellipse 55% 100% at 85% 50%, rgba(99,102,241,.14) 0%, transparent 65%),
        radial-gradient(ellipse 35% 70% at 5% 90%, rgba(6,182,212,.09) 0%, transparent 60%),
        radial-gradient(ellipse 25% 50% at 50% 0%, rgba(139,92,246,.07) 0%, transparent 60%);
}
.cp-hero canvas { position: absolute; inset: 0; width: 100%; height: 100%; opacity: .12; pointer-events: none; }
.cp-avatar-wrap { position: relative; width: 96px; height: 96px; flex-shrink: 0; }
.cp-avatar-ring {
    position: absolute; inset: -4px; border-radius: 50%;
    background: conic-gradient(from 0deg, #6366f1, #06b6d4, #10b981, #818cf8, #6366f1);
    animation: cpRingSpin 5s linear infinite;
}
.cp-avatar-ring::before {
    content: ''; position: absolute; inset: 3px; border-radius: 50%;
    background: #060b16;
}
.cp-avatar {
    position: relative; z-index: 1; width: 96px; height: 96px;
    border-radius: 50%; object-fit: cover; border: 3px solid #0d1425;
    transition: transform .3s ease;
}
.cp-avatar-wrap:hover .cp-avatar { transform: scale(1.04); }
@keyframes cpRingSpin { to { transform: rotate(360deg); } }
.cp-online-dot {
    position: absolute; bottom: 3px; right: 3px; z-index: 2;
    width: 20px; height: 20px; border-radius: 50%;
    background: #10b981; border: 3px solid #060b16;
    box-shadow: 0 0 10px rgba(16,185,129,.8);
    animation: cpOnlinePulse 2.5s ease-in-out infinite;
}
@keyframes cpOnlinePulse {
    0%,100% { box-shadow: 0 0 8px rgba(16,185,129,.7); }
    50%      { box-shadow: 0 0 18px rgba(16,185,129,1); }
}
.cp-badge-pill {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: .68rem; font-weight: 700; padding: 4px 12px;
    border-radius: 20px; letter-spacing: .1em; font-family: 'Geist Mono', monospace;
    background: rgba(99,102,241,.15); color: #a5b4fc;
    border: 1px solid rgba(99,102,241,.35);
}
.cp-stat-mini {
    display: flex; flex-direction: column; align-items: center; gap: 5px;
    padding: 16px 12px; background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.07); border-radius: 12px;
    transition: all .2s ease;
}
.cp-stat-mini:hover { background: rgba(99,102,241,.06); border-color: rgba(99,102,241,.25); transform: translateY(-2px); }
.cp-stat-mini b { font-size: 1.5rem; font-weight: 800; color: var(--txt-head); letter-spacing: -.03em; line-height: 1; }
.cp-stat-mini span { font-size: .65rem; color: var(--txt-dim); text-transform: uppercase; letter-spacing: .08em; font-family: 'Geist Mono', monospace; }

/* Cards */
.cp-card {
    background: rgba(8,12,24,.7); border: 1px solid rgba(99,102,241,.12);
    border-radius: 16px; padding: 24px; position: relative; overflow: hidden;
    backdrop-filter: blur(10px);
}
.cp-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
    background: linear-gradient(90deg, transparent, rgba(99,102,241,.5), rgba(6,182,212,.3), transparent);
}
.cp-section-title {
    font-size: .7rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
    color: var(--txt-dim); font-family: 'Geist Mono', monospace;
    margin-bottom: 18px; display: flex; align-items: center; gap: 8px;
}
.cp-section-title::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,.05); }
.cp-field {
    background: rgba(6,9,18,.8); border: 1px solid rgba(99,102,241,.18);
    border-radius: 10px; padding: 12px 15px; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: .88rem; color: var(--txt-head); width: 100%;
    transition: border-color .2s, box-shadow .2s; outline: none;
}
.cp-field:focus {
    border-color: rgba(99,102,241,.55);
    box-shadow: 0 0 0 3px rgba(99,102,241,.12), 0 0 20px rgba(99,102,241,.08);
}
.cp-field::placeholder { color: rgba(100,116,139,.6); }
.cp-label {
    font-size: .7rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
    color: var(--txt-dim); margin-bottom: 7px; display: block; font-family: 'Geist Mono', monospace;
}

/* Password strength */
.cp-progress { height: 4px; background: rgba(255,255,255,.05); border-radius: 4px; overflow: hidden; margin-top: 10px; }
.cp-progress-bar { height: 100%; background: linear-gradient(90deg, #10b981, #06b6d4); border-radius: 4px; transition: width .4s ease, background .4s ease; }

/* Session info rows */
.cp-session-row {
    display: flex; align-items: center; gap: 12px; padding: 10px 13px;
    background: rgba(255,255,255,.02); border-radius: 9px;
    border: 1px solid rgba(255,255,255,.04); transition: background .15s;
}
.cp-session-row:hover { background: rgba(99,102,241,.04); }
.cp-session-icon {
    width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center;
    justify-content: center; flex-shrink: 0;
    background: rgba(6,182,212,.08); border: 1px solid rgba(6,182,212,.18);
}
</style>

@php
    $devUser  = auth()->user();
    $devPhoto = ($devUser->photo_path ?: $devUser->photo)
        ? asset('storage/' . ($devUser->photo_path ?: $devUser->photo))
        : asset('images/profile-dev.png');
    $totalUsers     = \App\Models\User::count();
    $totalGuruAktif = \App\Models\User::where('role','guru')->where('is_active',true)->count();
    $totalPresensi  = \App\Models\Attendance::whereDate('date', today())->count();
@endphp

{{-- ── Hero Banner ── --}}
<div class="cp-hero">
    <canvas id="cp-hero-canvas"></canvas>
    <div style="position:relative;z-index:1;display:flex;align-items:center;gap:28px;flex-wrap:wrap">

        {{-- Avatar --}}
        <div style="position:relative;cursor:pointer;flex-shrink:0"
             onclick="document.getElementById('cp-photo-input').click()" title="Klik untuk ganti foto profil">
            <div class="cp-avatar-wrap">
                <div class="cp-avatar-ring"></div>
                <img id="cp-avatar-img" src="{{ $devPhoto }}" class="cp-avatar" alt="{{ $devUser->name }}">
            </div>
            <div class="cp-online-dot"></div>
            {{-- Camera overlay on hover --}}
            <div style="position:absolute;inset:0;border-radius:50%;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .2s;z-index:3"
                 onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
                <i data-lucide="camera" style="width:20px;height:20px;color:#fff"></i>
            </div>
        </div>
        <input type="file" id="cp-photo-input" accept="image/*" hidden onchange="cpPreviewPhoto(this)">

        {{-- Info --}}
        <div style="flex:1;min-width:200px">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:8px">
                <h2 id="cp-name-display"
                    style="font-size:1.65rem;font-weight:800;letter-spacing:-.04em;color:#f8fafc;margin:0;line-height:1">
                    {{ $devUser->name }}
                </h2>
                <span class="cp-badge-pill">
                    <i data-lucide="terminal" style="width:11px;height:11px"></i>
                    DEVELOPER
                </span>
            </div>
            <p style="font-size:.83rem;color:#475569;margin:0 0 12px;font-family:'Geist Mono',monospace">
                {{ $devUser->email }}
            </p>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                <span style="display:inline-flex;align-items:center;gap:6px;font-size:.72rem;color:#34d399;font-family:'Geist Mono',monospace">
                    <span style="width:7px;height:7px;border-radius:50%;background:#10b981;box-shadow:0 0 8px #10b981;animation:pulseGlow 2s infinite"></span>
                    Online
                </span>
                <span style="color:rgba(255,255,255,.1)">|</span>
                <span style="font-size:.72rem;color:#475569;font-family:'Geist Mono',monospace">
                    Bergabung {{ $devUser->created_at->locale('id')->isoFormat('MMM YYYY') }}
                </span>
                <span style="color:rgba(255,255,255,.1)">|</span>
                <span style="font-size:.72rem;color:#475569;font-family:'Geist Mono',monospace">
                    UID #{{ $devUser->id }}
                </span>
            </div>
        </div>

        {{-- Mini stats — dihapus per permintaan --}}
    </div>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div style="display:flex;align-items:center;gap:10px;padding:13px 18px;border-radius:12px;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);color:#34d399;font-size:.85rem;font-weight:600;margin-bottom:20px">
    <i data-lucide="check-circle-2" style="width:17px;height:17px;flex-shrink:0"></i>
    {{ session('success') }}
</div>
@endif
@if($errors->any())
<div style="padding:13px 18px;border-radius:12px;background:rgba(244,63,94,.08);border:1px solid rgba(244,63,94,.25);color:#f87171;font-size:.85rem;margin-bottom:20px">
    <div style="display:flex;align-items:center;gap:8px;font-weight:700;margin-bottom:6px">
        <i data-lucide="alert-circle" style="width:16px;height:16px;flex-shrink:0"></i>
        Ada kesalahan:
    </div>
    <ul style="margin:0 0 0 22px;padding:0;font-weight:400;line-height:1.8">
        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
    </ul>
</div>
@endif

{{-- ── 2-col layout: Kiri (form) | Kanan (sesi) ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

    {{-- ════ Kolom Kiri: Identitas + Password ════ --}}
    <div style="display:flex;flex-direction:column;gap:18px">

        {{-- Card Identitas --}}
        <div class="cp-card">
            <p class="cp-section-title">
                <i data-lucide="user" style="width:13px;height:13px;color:var(--accent)"></i>
                Identitas Developer
            </p>
            <form action="{{ url('dev-panel/'.$secret.'/profile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="photo" id="cp-photo-hidden" accept="image/*" hidden>
                <div style="display:flex;flex-direction:column;gap:15px">
                    <div>
                        <label class="cp-label">Nama Lengkap</label>
                        <input type="text" name="name" class="cp-field"
                               value="{{ old('name', $devUser->name) }}" required
                               oninput="document.getElementById('cp-name-display').textContent=this.value||'Developer'">
                    </div>
                    <div>
                        <label class="cp-label">Alamat Email</label>
                        <input type="email" name="email" class="cp-field"
                               value="{{ old('email', $devUser->email) }}" required>
                    </div>
                    <div>
                        <label class="cp-label">Nomor Telepon</label>
                        <input type="text" name="phone" class="cp-field"
                               value="{{ old('phone', $devUser->phone ?? '') }}"
                               placeholder="08xx-xxxx-xxxx">
                    </div>
                </div>
                <div style="display:flex;justify-content:flex-end;margin-top:20px">
                    <button type="submit" class="btn">
                        <i data-lucide="save" style="width:15px;height:15px"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- Card Ganti Password --}}
        <div class="cp-card">
            <p class="cp-section-title">
                <i data-lucide="key-round" style="width:13px;height:13px;color:#f43f5e"></i>
                Keamanan Akun
            </p>
            <form action="{{ url('dev-panel/'.$secret.'/profile/password') }}" method="POST">
                @csrf
                <div style="display:flex;flex-direction:column;gap:14px">
                    <div>
                        <label class="cp-label">Password Saat Ini</label>
                        <input type="password" name="current_password" class="cp-field"
                               required placeholder="••••••••">
                    </div>
                    <div>
                        <label class="cp-label">Password Baru</label>
                        <input type="password" name="password" id="cp-pw-new" class="cp-field"
                               required minlength="8" placeholder="Minimal 8 karakter">
                        <div class="cp-progress"><div id="cp-pw-bar" class="cp-progress-bar" style="width:0%"></div></div>
                        <p id="cp-pw-hint" style="font-size:.69rem;color:var(--txt-dim);margin-top:6px;font-family:'Geist Mono',monospace">
                            Masukkan password baru
                        </p>
                    </div>
                    <div>
                        <label class="cp-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="cp-field"
                               required minlength="8" placeholder="Ulangi password baru">
                    </div>
                </div>
                <div style="display:flex;justify-content:flex-end;margin-top:20px">
                    <button type="submit" class="btn btn-danger">
                        <i data-lucide="shield-check" style="width:15px;height:15px"></i>
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════ Kolom Kanan: Sesi & Info Akun ════ --}}
    <div style="display:flex;flex-direction:column;gap:18px">

        {{-- Card Sesi Aktif --}}
        <div class="cp-card">
            <p class="cp-section-title">
                <i data-lucide="activity" style="width:13px;height:13px;color:#06b6d4"></i>
                Sesi Aktif
            </p>
            <div style="display:flex;flex-direction:column;gap:8px">
                @php
                    $sessionItems = [
                        ['label'=>'IP Address',     'val'=>request()->ip(),                                     'icon'=>'map-pin',    'color'=>'rgba(6,182,212,.1)',   'border'=>'rgba(6,182,212,.2)'],
                        ['label'=>'Browser',        'val'=>substr(request()->userAgent()??'-',0,38).'…',         'icon'=>'monitor',    'color'=>'rgba(99,102,241,.1)',  'border'=>'rgba(99,102,241,.2)'],
                        ['label'=>'Login Terakhir', 'val'=>$devUser->updated_at->locale('id')->diffForHumans(), 'icon'=>'clock',      'color'=>'rgba(16,185,129,.1)',  'border'=>'rgba(16,185,129,.2)'],
                        ['label'=>'Session Driver', 'val'=>config('session.driver','file'),                      'icon'=>'database',   'color'=>'rgba(245,158,11,.1)',  'border'=>'rgba(245,158,11,.2)'],
                        ['label'=>'Role',           'val'=>strtoupper($devUser->role??'developer'),              'icon'=>'shield',     'color'=>'rgba(139,92,246,.1)',  'border'=>'rgba(139,92,246,.2)'],
                        ['label'=>'Environment',    'val'=>strtoupper(config('app.env','production')),           'icon'=>'server',     'color'=>'rgba(244,63,94,.1)',   'border'=>'rgba(244,63,94,.2)'],
                    ];
                @endphp
                @foreach($sessionItems as $si)
                <div class="cp-session-row">
                    <div class="cp-session-icon"
                         style="background:{{ $si['color'] }};border-color:{{ $si['border'] }}">
                        <i data-lucide="{{ $si['icon'] }}" style="width:13px;height:13px;color:#06b6d4"></i>
                    </div>
                    <div style="min-width:0;flex:1">
                        <p style="font-size:.66rem;color:var(--txt-dim);margin:0;font-family:'Geist Mono',monospace;text-transform:uppercase;letter-spacing:.07em">{{ $si['label'] }}</p>
                        <p style="font-size:.82rem;font-weight:600;color:var(--txt-head);margin:2px 0 0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $si['val'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Card Info Akun --}}
        <div class="cp-card">
            <p class="cp-section-title">
                <i data-lucide="info" style="width:13px;height:13px;color:#818cf8"></i>
                Info Akun
            </p>
            <div style="display:flex;flex-direction:column;gap:8px">
                @foreach([
                    ['l'=>'ID Pengguna',    'v'=>'#'.$devUser->id,                                              'mono'=>true],
                    ['l'=>'Tanggal Daftar', 'v'=>$devUser->created_at->locale('id')->isoFormat('D MMMM YYYY'),  'mono'=>false],
                    ['l'=>'Status',         'v'=>$devUser->is_active ? 'Aktif' : 'Nonaktif',                    'mono'=>true],
                ] as $ai)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 12px;background:rgba(255,255,255,.02);border-radius:8px;border:1px solid rgba(255,255,255,.04)">
                    <span style="font-size:.72rem;color:var(--txt-dim){{ $ai['mono'] ? ';font-family:\'Geist Mono\',monospace' : '' }}">{{ $ai['l'] }}</span>
                    <span style="font-size:.8rem;font-weight:600;color:var(--txt-head){{ $ai['mono'] ? ';font-family:\'Geist Mono\',monospace' : '' }}">{{ $ai['v'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Logout button --}}
        <form action="{{ route('logout') }}" method="POST"
              onsubmit="return confirmAction(this,'⚠️ Yakin ingin keluar dari Developer Panel?','danger')">
            @csrf
            <button type="submit" class="btn btn-ghost btn-block"
                    style="border-color:rgba(244,63,94,.3);color:#f87171;gap:8px">
                <i data-lucide="log-out" style="width:15px;height:15px"></i>
                Keluar dari Sesi
            </button>
        </form>
    </div>
</div>

<script>
/* Hero canvas particles */
(function(){
    var c=document.getElementById('cp-hero-canvas');if(!c)return;
    var ctx=c.getContext('2d'),W,H,pts=[];
    function resize(){W=c.width=c.parentElement.offsetWidth;H=c.height=c.parentElement.offsetHeight;}
    resize();window.addEventListener('resize',resize);
    for(var i=0;i<50;i++) pts.push({
        x:Math.random()*1400,y:Math.random()*400,
        vx:(Math.random()-.5)*.25,vy:(Math.random()-.5)*.18,
        r:Math.random()*1.8+.4,
        c:Math.random()>.5?'rgba(99,102,241,.65)':'rgba(6,182,212,.45)'
    });
    function draw(){
        ctx.clearRect(0,0,W,H);
        pts.forEach(function(p){
            p.x+=p.vx;p.y+=p.vy;
            if(p.x<0)p.x=W;if(p.x>W)p.x=0;
            if(p.y<0)p.y=H;if(p.y>H)p.y=0;
            ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
            ctx.fillStyle=p.c;ctx.fill();
        });
        requestAnimationFrame(draw);
    }
    draw();
})();

function cpPreviewPhoto(input){
    if(!input.files||!input.files[0])return;
    var r=new FileReader();
    r.onload=function(e){
        document.getElementById('cp-avatar-img').src=e.target.result;
        var dt=new DataTransfer();dt.items.add(input.files[0]);
        document.getElementById('cp-photo-hidden').files=dt.files;
    };
    r.readAsDataURL(input.files[0]);
}

document.getElementById('cp-pw-new')?.addEventListener('input',function(){
    var v=this.value,score=0;
    if(v.length>=8)score++;
    if(/[A-Z]/.test(v))score++;
    if(/[0-9]/.test(v))score++;
    if(/[^A-Za-z0-9]/.test(v))score++;
    var pct=score*25;
    var colors=['#f43f5e','#f43f5e','#f59e0b','#10b981','#10b981'];
    var labels=['Terlalu lemah','Lemah','Sedang','Kuat','Sangat kuat'];
    var bar=document.getElementById('cp-pw-bar'),hint=document.getElementById('cp-pw-hint');
    if(bar){bar.style.width=pct+'%';bar.style.background='linear-gradient(90deg,'+colors[score]+','+(score>2?'#06b6d4':colors[score])+')';}
    if(hint){hint.textContent=v.length?labels[score]:'Masukkan password baru';hint.style.color=v.length?colors[score]:'';}
});
</script>
</div>

@endsection

@section('scripts')
<script>
(function () {
    const $  = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => [...r.querySelectorAll(s)];
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const icons = () => window.lucide && lucide.createIcons();
    const safe = fn => { try { return fn(); } catch (e) { return null; } };

    /* Overlay helper */
    function openOverlay(el) {
        el.hidden = false;
        requestAnimationFrame(() => requestAnimationFrame(() => el.classList.add('open')));
        document.body.style.overflow = 'hidden';
    }
    function closeOverlay(el) {
        el.classList.remove('open');
        setTimeout(() => { el.hidden = true; document.body.style.overflow = ''; }, 250);
    }

    /* Selamat datang: tampil sekali per hari */
    const welcome = $('#nb-welcome');
    const today = new Date().toISOString().slice(0, 10);
    const SKIP_KEY = 'dev_panel_welcome_skip';
    function closeWelcome() {
        if ($('#nb-welcome-skip').checked) safe(() => localStorage.setItem(SKIP_KEY, today));
        closeOverlay(welcome);
    }
    $$('[data-close-welcome]').forEach(b => b.addEventListener('click', closeWelcome));
    welcome.addEventListener('click', e => { if (e.target === welcome) closeWelcome(); });
    $('#nb-open-welcome')?.addEventListener('click', () => openOverlay(welcome));
    const relErr = !!$('#nb-release-modal[data-open="1"]');
    if (relErr) setTimeout(() => openOverlay($('#nb-release-modal')), 450);

    /* Konfirmasi (dipakai link, form, dan toggle debug) */
    const box = $('#nb-confirm');
    let pending = null;
    function setConfirm(msg, danger) {
        $('#nb-confirm-msg').textContent = msg;
        $('#nb-confirm-icon').className = 'ico ico-lg ' + (danger ? 't-bad' : 't-violet');
        $('#nb-confirm-yes').className = 'btn' + (danger ? ' btn-danger' : '');
        openOverlay(box);
        setTimeout(() => $('#nb-confirm-yes').focus(), 60);
    }
    function closeConfirm() { closeOverlay(box); pending = null; }

    window.confirmAction = function (el, msg, tone) {
        pending = el;
        setConfirm(msg, tone === 'danger');
        return false;
    };
    $('#nb-confirm-no').addEventListener('click', closeConfirm);
    box.addEventListener('click', e => { if (e.target === box) closeConfirm(); });
    $('#nb-confirm-yes').addEventListener('click', () => {
        const el = pending;
        closeConfirm();
        if (!el) return;
        el.style.opacity = '.6';
        el.style.pointerEvents = 'none';
        if (el.tagName === 'FORM') el.submit();
        else if (el.href) window.location.href = el.href;
    });

    /* Modal rilis baru */
    const rel = $('#nb-release-modal');
    const openRel = () => openOverlay(rel);
    const closeRel = () => closeOverlay(rel);
    $$('[data-open-release]').forEach(b => b.addEventListener('click', openRel));
    $$('[data-close-release]').forEach(b => b.addEventListener('click', closeRel));
    rel.addEventListener('click', e => { if (e.target === rel) closeRel(); });
    if (rel.dataset.open === '1') setTimeout(openRel, 300);

    document.addEventListener('keydown', e => {
        if (e.key !== 'Escape') return;
        if (!box.hidden) closeConfirm();
        else if (!rel.hidden) closeRel();
        else if (!welcome.hidden) closeWelcome();
    });

    /* Filter timeline */
    const fbtns = $$('[data-filter]');
    const tlItems = $$('.tl-item');
    const tlGroups = $$('.tl-group');
    const tlEmpty = $('#nb-tl-empty');
    fbtns.forEach(btn => btn.addEventListener('click', () => {
        const f = btn.dataset.filter;
        fbtns.forEach(b => b.classList.toggle('active', b === btn));
        let shown = 0;
        tlItems.forEach(it => {
            const ok = f === 'all' || it.dataset.type === f;
            it.hidden = !ok;
            if (ok) shown++;
        });
        tlGroups.forEach(g => { g.hidden = !g.querySelector('.tl-item:not([hidden])'); });
        if (tlEmpty && tlItems.length) tlEmpty.hidden = shown > 0;
    }));

    /* Upload APK */
    window.setApkName = function (file) {
        $('#apk-filename').textContent = file ? file.name : '.apk, maksimal 100MB';
        $('#apk-drop').classList.toggle('has-file', !!file);
    };
    window.handleApkDrop = function (event, zone) {
        event.preventDefault();
        zone.classList.remove('over');
        const files = event.dataTransfer.files;
        if (!files.length) return;
        const dt = new DataTransfer();
        dt.items.add(files[0]);
        $('#apk-input').files = dt.files;
        setApkName(files[0]);
    };

    window.handleIosDrop = function (event, zone) {
        event.preventDefault();
        zone.classList.remove('over');
        const files = event.dataTransfer.files;
        if (!files.length) return;
        const dt = new DataTransfer();
        dt.items.add(files[0]);
        const input = document.getElementById('ios-input');
        if (input) input.files = dt.files;
        const label = document.getElementById('ios-filename');
        if (label) label.textContent = files[0].name;
        const drop = document.getElementById('ios-drop');
        if (drop) drop.classList.add('has-file');
    };

    /* Konfirmasi berbasis Promise (untuk debug toggle) */
    function confirmModal(msg, danger) {
        return new Promise(resolve => {
            setConfirm(msg, danger);
            const done = v => { closeOverlay(box); pending = null; resolve(v); };
            $('#nb-confirm-yes').onclick = () => done(true);
            $('#nb-confirm-no').onclick = () => done(false);
        });
    }

    /* Debug toggle */
    const dbgBtn = $('#debug-toggle-btn');
    if (dbgBtn) {
        const label = $('#debug-toggle-label'), desc = $('#debug-toggle-desc'), ico = $('#debug-toggle-ico');
        const updateUI = on => {
            label.textContent = on ? 'Debug ON' : 'Debug OFF';
            desc.textContent  = on ? 'Klik untuk mematikan debug' : 'Klik untuk menyalakan debug';
            ico.className = 'ico ico-lg ' + (on ? 't-bad' : 't-ok');
            dbgBtn.dataset.debug = on ? '1' : '0';
        };
        updateUI(dbgBtn.dataset.debug === '1');

        dbgBtn.addEventListener('click', async () => {
            const next = dbgBtn.dataset.debug !== '1';
            const ok = await confirmModal(
                next ? '⚠️ Nyalakan APP_DEBUG?\nSemua error detail akan terlihat ke pengguna!' : 'Matikan APP_DEBUG?\nDetail error akan disembunyikan.',
                next
            );
            if (!ok) return;
            label.textContent = 'Memproses...';
            desc.textContent = 'Mohon tunggu';
            dbgBtn.style.pointerEvents = 'none';
            dbgBtn.style.opacity = '.6';
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
            fetch('/dev-panel/{{ $secret }}/toggle-debug', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({ debug: next })
            })
            .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.text(); })
            .then(() => { updateUI(next); location.reload(); })
            .catch(e => {
                updateUI(!next);
                dbgBtn.style.pointerEvents = '';
                dbgBtn.style.opacity = '';
                confirmModal('Gagal toggle debug: ' + e.message, true);
            });
        });
    }

    /* Hitung naik saat angka terlihat */
    function countUp(el) {
        const target = parseInt(el.dataset.count, 10) || 0;
        if (reduce || target === 0) { el.textContent = target.toLocaleString('id-ID'); return; }
        const dur = 1000, t0 = performance.now();
        (function tick(t) {
            const p = Math.min((t - t0) / dur, 1);
            el.textContent = Math.round(target * (1 - Math.pow(1 - p, 4))).toLocaleString('id-ID');
            if (p < 1) requestAnimationFrame(tick);
        })(t0);
    }
    const io = 'IntersectionObserver' in window ? new IntersectionObserver(entries => {
        entries.forEach(en => {
            if (en.isIntersecting && !en.target.dataset.done) {
                en.target.dataset.done = '1';
                countUp(en.target);
                io.unobserve(en.target);
            }
        });
    }, { threshold: .4 }) : null;
    $$('[data-count]').forEach(el => io ? io.observe(el) : countUp(el));

    icons();
    document.addEventListener('DOMContentLoaded', icons);
})();
</script>
@endsection