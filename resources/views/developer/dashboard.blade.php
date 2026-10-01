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

    <div class="dash-header">
        <div class="dash-top">
            <div>
                <h2 class="greeting">{{ $greeting }}, <span style="background: linear-gradient(135deg, #cbd5e1 30%, #818cf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Developer</span></h2>
                <p class="subtext">
                    Sistem aktif di <b>{{ $host }}</b> pukul {{ now()->format('H:i') }} WIB.
                    @if(($stats['pending_leaves'] ?? 0) > 0)
                        <span style="color: #fbbf24;">• Ada {{ $stats['pending_leaves'] }} pengajuan izin menunggu persetujuan.</span>
                    @else
                        <span style="color: #34d399;">• Semua status operasional normal.</span>
                    @endif
                </p>
                <div class="dash-meta">
                    <span class="dash-pill"><i></i> {{ $envName }}</span>
                    <span class="pill mono"><i data-lucide="server" style="width:13px;height:13px;color:var(--txt-dim)"></i> {{ $host }}</span>
                    @if(($stats['pending_leaves'] ?? 0) > 0)
                        <span class="pill" style="border-color: rgba(245,158,11,0.3); color: #fbbf24;"><i data-lucide="clock" style="width:13px;height:13px"></i> {{ $stats['pending_leaves'] }} Pending</span>
                    @endif
                </div>
            </div>
            <div class="dash-actions">
                <a href="{{ url('/dashboard') }}" target="_blank" class="btn btn-ghost"><i data-lucide="external-link" style="width:15px;height:15px"></i> Buka Aplikasi</a>
                <button type="button" class="btn btn-ghost" id="nb-open-welcome"><i data-lucide="book-open" style="width:15px;height:15px"></i> Panduan Dev</button>
            </div>
        </div>
    </div>

    @if($debugOn)
        <div class="alert alert-warn" role="alert">
            <i data-lucide="alert-triangle" style="width:18px;height:18px;flex-shrink:0;margin-top:1px"></i>
            <span><b>Debug mode aktif.</b> Matikan <code>APP_DEBUG</code> di environment production agar detail konfigurasi dan error trace terlindungi.</span>
        </div>
    @endif

    <div class="stats">
        <div class="card stat">
            <div class="stat-head"><span class="ico t-violet"><i data-lucide="users" style="width:16px;height:16px"></i></span>Total Pengguna</div>
            <div class="num" data-count="{{ $stats['total_users'] ?? 0 }}">0</div>
        </div>
        <div class="card stat">
            <div class="stat-head"><span class="ico t-sky"><i data-lucide="graduation-cap" style="width:16px;height:16px"></i></span>Guru Aktif</div>
            <div class="num" data-count="{{ $stats['total_teachers'] ?? 0 }}">0</div>
        </div>
        <div class="card stat">
            <div class="stat-head"><span class="ico t-warn"><i data-lucide="cpu" style="width:16px;height:16px"></i></span>Versi PHP</div>
            <div class="num num-sm">{{ $stats['php_version'] ?? '8.x' }}</div>
        </div>
        <div class="card stat">
            <div class="stat-head"><span class="ico t-bad"><i data-lucide="code-2" style="width:16px;height:16px"></i></span>Laravel Framework</div>
            <div class="num num-sm">v{{ ltrim($stats['laravel_version'] ?? '11.x', 'vV') }}</div>
        </div>
    </div>

    <div class="grid-main">
        <div class="card left-col">
            <div class="card-head">
                <h3><i data-lucide="activity" style="width:18px;height:18px;color:#34d399"></i> Informasi Sistem</h3>
                <span class="live"><i></i> Live</span>
            </div>
            <div class="info">
                <div><p class="l">Environment</p><p class="v mono"><span class="badge t-ok">{{ $envName }}</span></p></div>
                <div><p class="l">Debug mode</p><p class="v"><span class="dot {{ $debugOn ? 'warn' : 'ok' }}"></span>{{ $debugOn ? 'Aktif' : 'Nonaktif' }}</p></div>
                <div><p class="l">Host URL</p><p class="v mono trunc">{{ $host }}</p></div>
                <div><p class="l">Waktu server</p><p class="v mono">{{ now()->format('H:i') }} WIB</p></div>
                <div><p class="l">Operator & admin</p><p class="v">{{ $stats['total_operators'] ?? 0 }} akun</p></div>
            </div>
        </div>

        <div class="card right-col">
            <div class="card-head">
                <h3><i data-lucide="monitor" style="width:18px;height:18px;color:#06b6d4"></i> Server Info</h3>
                <span class="live"><i></i> Live</span>
            </div>
            <div class="server-info-body">
                <div class="si-row">
                    <div class="si-item"><span class="si-label">PHP</span><span class="si-val mono">{{ $stats['php_version'] ?? '8.x' }}</span></div>
                    <div class="si-item"><span class="si-label">Laravel</span><span class="si-val mono">v{{ ltrim($stats['laravel_version'] ?? '11.x', 'vV') }}</span></div>
                </div>
                <div class="si-row">
                    <div class="si-item"><span class="si-label">Memory Limit</span><span class="si-val mono">{{ $stats['memory_limit'] ?? '-' }}</span></div>
                    <div class="si-item"><span class="si-label">Upload Max</span><span class="si-val mono">{{ $stats['upload_max'] ?? '-' }}</span></div>
                </div>
                <div class="si-row">
                    <div class="si-item"><span class="si-label">Post Max</span><span class="si-val mono">{{ $stats['post_max'] ?? '-' }}</span></div>
                    <div class="si-item"><span class="si-label">Max Exec</span><span class="si-val mono">{{ $stats['max_exec_time'] ?? '-' }}s</span></div>
                </div>
                <div class="si-row">
                    <div class="si-item full"><span class="si-label">Server</span><span class="si-val mono" style="font-size:11px">{{ $stats['server_software'] ?? '-' }}</span></div>
                </div>
                <div class="si-row">
                    <div class="si-item"><span class="si-label">Server IP</span><span class="si-val mono">{{ $stats['server_addr'] ?? '-' }}</span></div>
                    <div class="si-item"><span class="si-label">Port</span><span class="si-val mono">{{ $stats['server_port'] ?? '-' }}</span></div>
                </div>
                <div class="si-row">
                    <div class="si-item full"><span class="si-label">Database</span><span class="si-val mono" style="font-size:11px">{{ $stats['db_driver'] ?? '-' }} | {{ $stats['db_name'] ?? '-' }}@{{ $stats['db_host'] ?? '-' }}</span></div>
                </div>
                <div class="si-row">
                    <div class="si-item full"><span class="si-label">Cache</span>
                        <span class="si-val mono" style="font-size:11px">
                            @if($stats['cache_config'] === 'CACHED')<span style="color:#10b981">✓</span>@else<span style="color:#f43f5e">✗</span>@endifCfg
                            @if($stats['cache_route'] === 'CACHED')<span style="color:#10b981">✓</span>@else<span style="color:#f43f5e">✗</span>@endifRt
                            @if($stats['cache_view'] === 'CACHED')<span style="color:#10b981">✓</span>@else<span style="color:#f43f5e">✗</span>@endifVw
                        </span>
                    </div>
                </div>

                {{-- Disk Usage Crypto Chart --}}
                <div class="disk-chart-wrap">
                    <div class="si-label" style="margin-bottom:10px">Disk Usage</div>
                    <div class="disk-chart">
                        @php
                            $used = ($stats['disk_total'] - $stats['disk_free']) / 1073741824;
                            $total = $stats['disk_total'] / 1073741824;
                            $percent = $stats['disk_percent'];
                            $cw = 300; $ch = 70;
                            $pts = [];
                            for ($i = 0; $i <= 20; $i++) {
                                $baseY = $ch - ($percent / 100) * $ch;
                                $v = sin($i * 0.8) * 4 + cos($i * 1.3) * 2;
                                $y = max(4, min($ch - 4, $baseY + $v));
                                $x = ($i / 20) * $cw;
                                $pts[] = "$x,$y";
                            }
                            $areaPts = [$cw.','.$ch, '0,'.$ch] + $pts;
                            $areaStr = implode(' ', array_map(function($p){$a=explode(',',$p);return $a[0].','.$a[1];}, $areaPts));
                            $lineStr = implode(' ', $pts);
                            $lastPt = end($pts); $lastY = (float)explode(',', $lastPt)[1];
                        @endphp
                        <svg viewBox="0 0 {{ $cw }} {{ $ch + 18 }}" preserveAspectRatio="none" style="width:100%;height:90px">
                            <defs>
                                <linearGradient id="dgG" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#10b981" stop-opacity="0.4"/>
                                    <stop offset="100%" stop-color="#10b981" stop-opacity="0.02"/>
                                </linearGradient>
                                <linearGradient id="dgL" x1="0" y1="0" x2="1" y2="0">
                                    <stop offset="0%" stop-color="#059669"/>
                                    <stop offset="60%" stop-color="#10b981"/>
                                    <stop offset="100%" stop-color="#34d399"/>
                                </linearGradient>
                            </defs>
                            <polygon points="{{ $areaStr }}" fill="url(#dgG)"/>
                            <polyline points="{{ $lineStr }}" fill="none" stroke="url(#dgL)" stroke-width="2" stroke-linejoin="round"/>
                            <circle cx="{{ $cw }}" cy="{{ $lastY }}" r="4" fill="#34d399" stroke="var(--bg-surface)" stroke-width="2"/>
                        </svg>
                        <div class="disk-stats">
                            <span class="mono" style="color:#10b981;font-weight:700">{{ round($used,1) }}GB</span>
                            <span class="mono" style="color:var(--txt-dim)">/{{ round($total,1) }}GB</span>
                            <span class="mono disk-pct" style="color:{{ $percent > 90 ? '#f43f5e' : ($percent > 70 ? '#f59e0b' : '#10b981') }};margin-left:auto">{{ $percent }}%</span>
                        </div>
                    </div>
                </div>

                <div class="si-row" style="margin-top:8px">
                    <div class="si-item"><span class="si-label">Session</span><span class="si-val mono">{{ $stats['session_driver'] ?? '-' }} · {{ $stats['session_lifetime'] ?? '-' }}min</span></div>
                    <div class="si-item"><span class="si-label">Remote IP</span><span class="si-val mono" style="font-size:11px">{{ $stats['remote_addr'] ?? '-' }}</span></div>
                </div>
            </div>
        </div>

        <div class="card left-col">
            <div class="card-head">
                <h3><i data-lucide="zap" style="width:18px;height:18px;color:#818cf8"></i> Aksi Cepat</h3>
            </div>
            <div class="actions">
                <a href="{{ route('developer.clear-cache', $secret) }}" onclick="return confirmAction(this, '🧹 Bersihkan semua cache?\n(config, route, view, app cache)')" class="action">
                    <span class="ico t-sky"><i data-lucide="trash-2" style="width:17px;height:17px"></i></span>
                    <div><b>Sapu Jagat</b><small>Hapus cache config, route, & view</small></div>
                    <i data-lucide="arrow-up-right" class="go" style="width:16px;height:16px"></i>
                </a>
                <a href="{{ url('/fix-session?secret=' . $secret) }}" onclick="return confirmAction(this, '🔧 Perbaiki session dir & hapus semua cache?')" class="action">
                    <span class="ico t-warn"><i data-lucide="wrench" style="width:17px;height:17px"></i></span>
                    <div><b>Fix Session</b><small>Perbaiki permissions & clear cache</small></div>
                    <i data-lucide="arrow-up-right" class="go" style="width:16px;height:16px"></i>
                </a>
                <a href="{{ route('developer.optimize', $secret) }}" onclick="return confirmAction(this, '⚡ Rebuild semua cache?\n(config, route, view cache)')" class="action">
                    <span class="ico t-sun"><i data-lucide="zap" style="width:17px;height:17px"></i></span>
                    <div><b>Optimize</b><small>Bangun ulang struktur cache</small></div>
                    <i data-lucide="arrow-up-right" class="go" style="width:16px;height:16px"></i>
                </a>
                <a href="{{ route('developer.migrate', $secret) }}" onclick="return confirmAction(this, '🗄️ Jalankan database migration?\nPastikan backup sudah ada!')" class="action">
                    <span class="ico t-ok"><i data-lucide="database" style="width:17px;height:17px"></i></span>
                    <div><b>Run Migration</b><small>artisan migrate --force</small></div>
                    <i data-lucide="arrow-up-right" class="go" style="width:16px;height:16px"></i>
                </a>
                <a href="{{ route('developer.run-seeder', $secret) }}" onclick="return confirmAction(this, '🌱 Jalankan database seeder?\nMembuat ulang akun developer dan data demo.')" class="action">
                    <span class="ico t-violet"><i data-lucide="sprout" style="width:17px;height:17px"></i></span>
                    <div><b>Run Seeder</b><small>db:seed --force</small></div>
                    <i data-lucide="arrow-up-right" class="go" style="width:16px;height:16px"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ═════════ TAB: APK MANAGER ═════════ --}}
<div id="tab-apk" class="tab-content">
    <header class="page-head">
        <h2><i data-lucide="smartphone" style="width:22px;height:22px;color:var(--accent);vertical-align:middle;margin-right:6px"></i> APK Manager</h2>
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
    if (!relErr && safe(() => localStorage.getItem(SKIP_KEY)) !== today) setTimeout(() => openOverlay(welcome), 450);

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