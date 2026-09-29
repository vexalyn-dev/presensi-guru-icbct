@extends('layouts.developer')
@section('content')

@php
    $hour     = (int) now()->format('H');
    $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
    $debugOn  = (bool) ($stats['debug'] ?? false);
    $envName  = strtoupper($stats['env'] ?? 'PRODUCTION');
    $setting  = \App\Models\AppSetting::getInstance();
    $mOn      = $setting->maintenance_mode ?? false;
@endphp


{{-- ═════════════ MODAL SELAMAT DATANG ═════════════ --}}
<div id="nb-welcome" class="nb-overlay" role="dialog" aria-modal="true" aria-labelledby="nb-welcome-title" hidden>
    <div class="nb-modal">
        <div class="nb-modal-top">
            <span class="nb-sticker">Developer panel</span>
            <button type="button" class="nb-x" data-close-welcome aria-label="Tutup">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="nb-modal-body">
            <div class="nb-wave" aria-hidden="true">👋</div>
            <h2 id="nb-welcome-title">{{ $greeting }}, Developer!</h2>
            <p class="nb-muted">Semua kendali sistem ada di satu tempat. Ini yang bisa kamu lakukan di sini:</p>

            <ul class="nb-tips">
                <li>
                    <span class="nb-ico nb-ico-violet"><i data-lucide="rocket" class="w-4 h-4"></i></span>
                    <div><strong>Deploy dan maintenance</strong><small>Jalankan migration, optimize, atau perbaiki session dengan satu klik.</small></div>
                </li>
                <li>
                    <span class="nb-ico nb-ico-mint"><i data-lucide="package-check" class="w-4 h-4"></i></span>
                    <div><strong>Kelola APK</strong><small>Unggah build Android terbaru dan bagikan ke pengguna.</small></div>
                </li>
                <li>
                    <span class="nb-ico nb-ico-sun"><i data-lucide="history" class="w-4 h-4"></i></span>
                    <div><strong>Umumkan rilis</strong><small>Tulis changelog dan tampilkan sebagai modal ke pengguna.</small></div>
                </li>
            </ul>

            <label class="nb-check">
                <input type="checkbox" id="nb-welcome-skip">
                <span class="nb-box"><i data-lucide="check" class="w-3 h-3"></i></span>
                <span>Jangan tampilkan lagi hari ini</span>
            </label>

            <button type="button" class="nb-btn nb-btn-block" data-close-welcome>
                Mulai bekerja <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
</div>

{{-- ═════════════ MODAL KONFIRMASI ═════════════ --}}
<div id="nb-confirm" class="nb-overlay" role="alertdialog" aria-modal="true" aria-labelledby="nb-confirm-title" hidden>
    <div class="nb-modal nb-modal-sm">
        <div class="nb-modal-body">
            <div class="nb-ico nb-ico-lg nb-ico-violet" id="nb-confirm-icon"><i data-lucide="shield-alert" class="w-6 h-6"></i></div>
            <h3 id="nb-confirm-title">Yakin lanjut?</h3>
            <p class="nb-muted nb-pre" id="nb-confirm-msg"></p>
            <div class="nb-row-end">
                <button type="button" class="nb-btn nb-btn-ghost" id="nb-confirm-no">Batal</button>
                <button type="button" class="nb-btn" id="nb-confirm-yes">Ya, lanjutkan</button>
            </div>
        </div>
    </div>
</div>

{{-- ═════════════ MODAL RILIS BARU ═════════════ --}}
<div id="nb-release-modal" class="nb-overlay" role="dialog" aria-modal="true" aria-labelledby="nb-rel-title"
     data-open="{{ ($errors->has('version') || $errors->has('title') || $errors->has('content') || $errors->has('type')) ? '1' : '0' }}" hidden>
    <div class="nb-modal">
        <div class="nb-modal-top">
            <span class="nb-sticker">Rilis baru</span>
            <button type="button" class="nb-x" data-close-release aria-label="Tutup">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form action="{{ route('developer.updates.store', $secret) }}" method="POST" class="nb-modal-body">
            @csrf
            <div>
                <h3 id="nb-rel-title">Umumkan perubahan</h3>
                <p class="nb-muted" style="margin-top:6px">Tulis satu baris per perubahan. Awali dengan tanda “-” supaya tampil sebagai daftar.</p>
            </div>

            <div>
                <label class="nb-field-label">Jenis rilis</label>
                <div class="nb-seg" role="radiogroup">
                    @foreach([['feature','Feature','star'],['update','Update','git-commit'],['fix','Fix','wrench'],['hotfix','Hotfix','flame']] as [$sv, $sl, $si])
                        <label class="nb-seg-i">
                            <input type="radio" name="type" value="{{ $sv }}" {{ old('type', 'feature') === $sv ? 'checked' : '' }}>
                            <span class="nb-seg-b"><i data-lucide="{{ $si }}" class="w-4 h-4"></i>{{ $sl }}</span>
                        </label>
                    @endforeach
                </div>
                @error('type')<p class="nb-err">{{ $message }}</p>@enderror
            </div>

            <div class="nb-form-grid">
                <div>
                    <label class="nb-field-label" for="rel-version">Versi</label>
                    <input id="rel-version" type="text" name="version" class="nb-input nb-mono" required placeholder="1.2.0" value="{{ old('version') }}">
                    @error('version')<p class="nb-err">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="nb-field-label" for="rel-title-in">Judul</label>
                    <input id="rel-title-in" type="text" name="title" class="nb-input" required placeholder="Menambahkan laporan baru" value="{{ old('title') }}">
                    @error('title')<p class="nb-err">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="nb-field-label" for="rel-content">Changelog</label>
                <textarea id="rel-content" name="content" rows="5" class="nb-input" required placeholder="- Memperbaiki bug A&#10;- Menambahkan fitur B">{{ old('content') }}</textarea>
                @error('content')<p class="nb-err">{{ $message }}</p>@enderror
            </div>

            <label class="nb-check">
                <input type="checkbox" name="show_modal" value="1" checked>
                <span class="nb-box"><i data-lucide="check" class="w-3 h-3"></i></span>
                <span>Tampilkan modal ke pengguna</span>
            </label>

            <div class="nb-row-end">
                <button type="button" class="nb-btn nb-btn-ghost" data-close-release>Batal</button>
                <button type="submit" class="nb-btn"><i data-lucide="send" class="w-4 h-4"></i> Publikasikan</button>
            </div>
        </form>
    </div>
</div>

{{-- ═════════════ TAB: DASHBOARD ═════════════ --}}
<div id="tab-dashboard" class="tab-content">

    {{-- Banner --}}
    <section class="nb-banner nb-in" style="--d:0ms">
        <div class="nb-banner-blob b1" aria-hidden="true"></div>
        <div class="nb-banner-blob b2" aria-hidden="true"></div>
        <div class="nb-banner-text">
            <span class="nb-sticker nb-sticker-white">{{ $envName }}</span>
            <h1>{{ $greeting }}, Developer.</h1>
            <p>Sistem berjalan di <b>{{ parse_url($stats['app_url'] ?? '', PHP_URL_HOST) ?? 'localhost' }}</b> pukul {{ now()->format('H:i') }} WIB.
               @if(($stats['pending_leaves'] ?? 0) > 0)
                    Ada {{ $stats['pending_leaves'] }} pengajuan izin menunggu persetujuan.
               @else
                    Tidak ada pengajuan izin yang menunggu.
               @endif
            </p>
            <div class="nb-banner-actions">
                <a href="{{ url('/dashboard') }}" target="_blank" class="nb-btn nb-btn-white">
                    <i data-lucide="external-link" class="w-4 h-4"></i> Buka aplikasi
                </a>
                <button type="button" class="nb-btn nb-btn-ghost-white" id="nb-open-welcome">
                    <i data-lucide="sparkles" class="w-4 h-4"></i> Panduan singkat
                </button>
            </div>
        </div>
        <div class="nb-banner-art" aria-hidden="true">
            <div class="nb-float f1"><i data-lucide="code-2" class="w-7 h-7"></i></div>
            <div class="nb-float f2"><i data-lucide="database" class="w-6 h-6"></i></div>
            <div class="nb-float f3"><i data-lucide="rocket" class="w-8 h-8"></i></div>
        </div>
    </section>

    @if($debugOn)
        <div class="nb-alert nb-alert-run nb-in" style="--d:80ms" role="alert">
            <i data-lucide="alert-triangle" class="w-5 h-5" style="flex-shrink:0"></i>
            <div class="nb-marquee">
                <div class="nb-marquee-track">
                    @foreach([0, 1] as $half)
                        <div class="nb-marquee-half" @if($half) aria-hidden="true" @endif>
                            @foreach([0, 1, 2] as $rep)
                                <span class="nb-marquee-item {{ $rep ? 'nb-rep' : '' }}"><b>Debug mode aktif.</b> Matikan <code>APP_DEBUG</code> di production supaya detail error tidak terlihat pengguna.</span>
                                <i class="nb-marquee-sep {{ $rep ? 'nb-rep' : '' }}"></i>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Statistik --}}
    <div class="nb-grid-stats">
        <div class="nb-card nb-lift tilt-card nb-in" style="--d:120ms">
            <div class="nb-stat-head"><span class="nb-ico nb-ico-violet"><i data-lucide="users" class="w-4 h-4"></i></span><p>Total pengguna</p></div>
            <div class="nb-num" data-count="{{ $stats['total_users'] ?? 0 }}">0</div>
        </div>
        <div class="nb-card nb-lift tilt-card nb-in" style="--d:180ms">
            <div class="nb-stat-head"><span class="nb-ico nb-ico-sky"><i data-lucide="graduation-cap" class="w-4 h-4"></i></span><p>Guru aktif</p></div>
            <div class="nb-num" data-count="{{ $stats['total_teachers'] ?? 0 }}">0</div>
        </div>
        <div class="nb-card nb-lift tilt-card nb-in" style="--d:240ms">
            <div class="nb-stat-head"><span class="nb-ico nb-ico-sun"><i data-lucide="server" class="w-4 h-4"></i></span><p>Versi PHP</p></div>
            <div class="nb-num nb-num-sm">{{ $stats['php_version'] ?? '8.x' }}</div>
        </div>
        <div class="nb-card nb-lift tilt-card nb-in" style="--d:300ms">
            <div class="nb-stat-head"><span class="nb-ico nb-ico-rose"><i data-lucide="code-2" class="w-4 h-4"></i></span><p>Laravel</p></div>
            <div class="nb-num nb-num-sm">v{{ $stats['laravel_version'] ?? '11.x' }}</div>
        </div>
    </div>

    <div class="nb-grid-main">
        {{-- Informasi sistem --}}
        <div class="nb-card nb-in nb-span-2" style="--d:360ms">
            <div class="nb-card-head"><h2>Informasi sistem</h2>
                <span class="nb-live"><i></i> Live</span>
            </div>
            <div class="nb-info">
                <div><p class="nb-label">Environment</p><p class="nb-val">{{ $envName }}</p></div>
                <div>
                    <p class="nb-label">Debug mode</p>
                    <p class="nb-val"><span class="nb-dot {{ $debugOn ? 'warn' : 'ok' }}"></span>{{ $debugOn ? 'Aktif' : 'Nonaktif' }}</p>
                </div>
                <div><p class="nb-label">Host URL</p><p class="nb-val truncate">{{ parse_url($stats['app_url'] ?? '', PHP_URL_HOST) ?? 'localhost' }}</p></div>
                <div><p class="nb-label">Waktu server</p><p class="nb-val">{{ now()->format('H:i') }} WIB</p></div>
                <div><p class="nb-label">Izin pending</p><p class="nb-val">{{ $stats['pending_leaves'] ?? 0 }} pengajuan</p></div>
                <div><p class="nb-label">Operator dan admin</p><p class="nb-val">{{ $stats['total_operators'] ?? 0 }} akun</p></div>
            </div>
        </div>

        {{-- Aksi cepat --}}
        <div class="nb-card nb-in" style="--d:420ms">
            <div class="nb-card-head"><h2>Aksi cepat</h2></div>
            <div class="nb-actions">
                <a href="{{ route('developer.clear-cache', $secret) }}"
                   onclick="return confirmAction(this, '🧹 Bersihkan semua cache?\n(config, route, view, app cache)')" class="nb-action">
                    <span class="nb-ico nb-ico-sky"><i data-lucide="trash-2" class="w-4 h-4"></i></span>
                    <div><b>Sapu Jagat</b><small>Hapus semua cache</small></div>
                    <i data-lucide="arrow-up-right" class="w-4 h-4 nb-go"></i>
                </a>
                <a href="{{ route('developer.migrate', $secret) }}"
                   onclick="return confirmAction(this, '🗄️ Jalankan database migration?\nPastikan backup sudah ada!')" class="nb-action">
                    <span class="nb-ico nb-ico-mint"><i data-lucide="database" class="w-4 h-4"></i></span>
                    <div><b>Run migration</b><small>migrate --force</small></div>
                    <i data-lucide="arrow-up-right" class="w-4 h-4 nb-go"></i>
                </a>
                <a href="{{ route('developer.optimize', $secret) }}"
                    onclick="return confirmAction(this, '⚡ Rebuild semua cache?\n(config, route, view cache)')" class="nb-action">
                    <span class="nb-ico nb-ico-sun"><i data-lucide="zap" class="w-4 h-4"></i></span>
                    <div><b>Optimize</b><small>Bangun ulang cache</small></div>
                    <i data-lucide="arrow-up-right" class="w-4 h-4 nb-go"></i>
                </a>
                <a href="{{ url('/fix-session?secret=' . $secret) }}"
                    onclick="return confirmAction(this, '🔧 Perbaiki session dir & hapus semua cache?')" class="nb-action">
                    <span class="nb-ico nb-ico-amber"><i data-lucide="wrench" class="w-4 h-4"></i></span>
                    <div><b>Fix Session</b><small>Perbaiki session + clear cache</small></div>
                    <i data-lucide="arrow-up-right" class="w-4 h-4 nb-go"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ═════════════ TAB: APK MANAGER ═════════════ --}}
<div id="tab-apk" class="tab-content">
    <div class="nb-wrap-full">
        <header class="nb-page-head nb-in" style="--d:0ms">
            <h2>APK Manager</h2>
            <p>Unggah dan bagikan build Android terbaru ke pengguna.</p>
        </header>

        @if($appSetting?->apk_file)
            <div class="nb-card nb-lift nb-in nb-mb" style="--d:80ms">
                <div class="nb-apk">
                    <div class="nb-apk-info">
                        <span class="nb-ico nb-ico-lg nb-ico-mint"><i data-lucide="package-check" class="w-6 h-6"></i></span>
                        <div>
                            <h4>{{ $appSetting->apk_name ?? 'ICB CT Presensi' }}
                                <span class="nb-chip">v{{ $appSetting->apk_version_label ?? $appSetting->apk_version ?? '1.0' }}</span>
                            </h4>
                            <p class="nb-mono nb-muted">{{ $appSetting->apk_size_human ?? '-' }} – diunggah {{ $appSetting->apk_uploaded_at?->diffForHumans() ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="nb-row">
                        <a href="{{ $appSetting->apk_url }}" target="_blank" class="nb-btn nb-btn-ghost">
                            <i data-lucide="download" class="w-4 h-4"></i> Unduh
                        </a>
                        <form action="{{ route('developer.apk.delete', $secret) }}" method="POST"
                              onsubmit="return confirmAction(this, '🗑️ Hapus APK ini?\nPengguna tidak bisa mengunduhnya lagi.', 'danger')">
                            @csrf @method('DELETE')
                            <button type="submit" class="nb-btn nb-btn-danger" aria-label="Hapus APK">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @if(!empty($appSetting->apk_min_android) || !empty($appSetting->apk_changelog))
                    <div class="nb-apk-meta">
                        @if(!empty($appSetting->apk_min_android))
                            <span class="nb-pill"><i data-lucide="smartphone" class="w-4 h-4"></i> {{ $appSetting->apk_min_android }}</span>
                        @endif
                        @if(!empty($appSetting->apk_changelog))
                            <span class="nb-pill"><i data-lucide="file-text" class="w-4 h-4"></i> {{ $appSetting->apk_changelog }}</span>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        <div class="nb-card nb-in" style="--d:140ms">
            <div class="nb-card-head"><h3>Unggah build baru</h3></div>
            <div class="nb-pad">
                <form action="{{ route('developer.apk', $secret) }}" method="POST" enctype="multipart/form-data" class="nb-apk-form">
                    @csrf
                    <div class="nb-apk-drop">
                        <label class="nb-field-label">File APK</label>
                        <div class="nb-drop" id="apk-drop" tabindex="0" role="button"
                             onclick="document.getElementById('apk-input').click()"
                             onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();document.getElementById('apk-input').click()}"
                             ondragover="event.preventDefault(); this.classList.add('over')"
                             ondragleave="this.classList.remove('over')"
                             ondrop="handleApkDrop(event, this)">
                            <span class="nb-ico nb-ico-lg nb-ico-violet"><i data-lucide="upload-cloud" class="w-6 h-6"></i></span>
                            <p><b>Klik untuk memilih</b> atau seret file ke sini</p>
                            <p id="apk-filename" class="nb-mono nb-muted">.apk, maksimal 100MB</p>
                        </div>
                        <input type="file" id="apk-input" name="apk_file" accept=".apk" class="hidden"
                               onchange="setApkName(this.files[0])">
                        @error('apk_file')<p class="nb-err" style="margin-top:4px">{{ $message }}</p>@enderror
                    </div>

                    <div class="nb-apk-fields">
                        <div class="nb-form-grid">
                            <div><label class="nb-field-label">Nama aplikasi</label>
                                <input type="text" name="apk_name" class="nb-input" value="{{ old('apk_name', $appSetting?->apk_name ?? '') }}" placeholder="ICB CT Mobile"></div>
                            <div><label class="nb-field-label">Label versi</label>
                                <input type="text" name="apk_version" class="nb-input nb-mono" value="{{ old('apk_version', $appSetting?->apk_version ?? '') }}" placeholder="1.0.0"></div>
                            <div><label class="nb-field-label">Minimal Android</label>
                                <input type="text" name="apk_min_android" class="nb-input" value="{{ old('apk_min_android', $appSetting?->apk_min_android ?? '') }}" placeholder="Android 8.0+"></div>
                            <div><label class="nb-field-label">Changelog</label>
                                <input type="text" name="apk_changelog" class="nb-input" value="{{ old('apk_changelog', $appSetting?->apk_changelog ?? '') }}" placeholder="Perbaikan bug dan peningkatan"></div>
                        </div>
                        <div class="nb-row-end">
                            <button type="submit" class="nb-btn"><i data-lucide="save" class="w-4 h-4"></i> Simpan APK</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═════════════ TAB: SYSTEM STATE ═════════════ --}}
<div id="tab-system" class="tab-content">
    <div class="nb-wrap-full nb-stack-lg">
        <header class="nb-page-head nb-in" style="--d:0ms">
            <h2>System State</h2>
            <p>Atur ketersediaan aplikasi dan jalankan tools sistem.</p>
        </header>

        <div class="nb-card nb-in" style="--d:80ms">
            <form action="{{ route('developer.maintenance', $secret) }}" method="POST">
                @csrf
                <div class="nb-pad nb-split nb-bb">
                    <div>
                        <h3 class="nb-h3">Mode maintenance
                            <span class="nb-state {{ $mOn ? 'on' : '' }}"><i></i>{{ $mOn ? 'Aktif' : 'Nonaktif' }}</span>
                        </h3>
                        <p class="nb-muted nb-maxw">Saat aktif, pengguna biasa melihat halaman maintenance. Admin dan operator tetap bisa masuk.</p>
                    </div>
                    <label class="nb-switch">
                        <input type="hidden" name="maintenance_mode" value="0">
                        <input type="checkbox" name="maintenance_mode" value="1" {{ $mOn ? 'checked' : '' }}>
                        <span class="nb-track"><span class="nb-thumb"></span></span>
                        <span class="sr-only">Aktifkan mode maintenance</span>
                    </label>
                </div>
                <div class="nb-pad nb-bb">
                    <label class="nb-field-label">Pesan maintenance</label>
                    <textarea name="maintenance_message" rows="3" class="nb-input" placeholder="Sistem sedang dalam pemeliharaan...">{{ $setting->maintenance_message }}</textarea>
                </div>
                <div class="nb-pad nb-foot nb-row-end">
                    <button type="submit" class="nb-btn"><i data-lucide="save" class="w-4 h-4"></i> Simpan perubahan</button>
                </div>
            </form>
        </div>

        <div class="nb-card nb-in" style="--d:140ms">
            <div class="nb-card-head"><h3>System tools</h3></div>
            <div class="nb-tools">
                <a href="{{ route('developer.clear-cache', $secret) }}" onclick="return confirmAction(this, '🧹 Bersihkan semua cache?')" class="nb-tool">
                    <span class="nb-ico nb-ico-lg nb-ico-sky"><i data-lucide="trash-2" class="w-5 h-5"></i></span>
                    <div><b>Sapu Jagat</b><small>Hapus cache config, route, view, dan app</small></div>
                </a>
                <a href="{{ route('developer.migrate', $secret) }}" onclick="return confirmAction(this, '🗄️ Jalankan migration?\nPastikan backup ada!')" class="nb-tool">
                    <span class="nb-ico nb-ico-lg nb-ico-mint"><i data-lucide="database" class="w-5 h-5"></i></span>
                    <div><b>Run migration</b><small>artisan migrate --force</small></div>
                </a>
                <a href="{{ route('developer.optimize', $secret) }}" onclick="return confirmAction(this, '⚡ Rebuild semua cache?')" class="nb-tool">
                    <span class="nb-ico nb-ico-lg nb-ico-sun"><i data-lucide="zap" class="w-5 h-5"></i></span>
                    <div><b>Optimize</b><small>Bangun ulang cache config, route, dan view</small></div>
                </a>
                <a href="{{ url('/fix-session?secret=' . $secret) }}"
                   onclick="return confirmAction(this, '🔧 Perbaiki session dir & hapus semua cache?')" class="nb-tool">
                    <span class="nb-ico nb-ico-lg nb-ico-amber"><i data-lucide="wrench" class="w-5 h-5"></i></span>
                    <div><b>Fix Session</b><small>Perbaiki session dir + clear semua cache</small></div>
                </a>
                <button type="button" class="nb-tool" id="debug-toggle-btn"
                        data-debug="{{ $stats['debug'] ? '1' : '0' }}">
                    <span class="nb-ico nb-ico-lg nb-ico-rose" id="debug-toggle-ico">
                        <i data-lucide="bug" class="w-5 h-5"></i>
                    </span>
                    <div>
                        <b id="debug-toggle-label">Debug ON</b>
                        <small id="debug-toggle-desc">Klik untuk mematikan debug</small>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═════════════ TAB: RELEASES ═════════════ --}}
@php
    $items = $updates instanceof \Illuminate\Pagination\AbstractPaginator ? $updates->getCollection() : collect($updates);
    $typeMeta = [
        'feature' => ['Feature', 'violet', 'star'],
        'update'  => ['Update',  'sky',    'git-commit'],
        'fix'     => ['Fix',     'sun',    'wrench'],
        'hotfix'  => ['Hotfix',  'rose',   'flame'],
    ];
    $typeKey  = fn ($u) => isset($typeMeta[$u->type]) ? $u->type : 'update';
    $counts   = $items->countBy($typeKey);
    $latest   = $items->first();
    $groups   = $items->groupBy(fn ($u) => $u->created_at->format('Y-m'));
    $relError = $errors->has('version') || $errors->has('title') || $errors->has('content') || $errors->has('type');
@endphp
<div id="tab-releases" class="tab-content">

    <div class="nb-rel-head nb-in" style="--d:0ms">
        <header class="nb-page-head" style="margin-bottom:0">
            <h2>Riwayat rilis</h2>
            <p>Catatan perubahan yang dikirim ke pengguna, urut dari yang terbaru.</p>
        </header>
        <button type="button" class="nb-btn" data-open-release>
            <i data-lucide="plus" class="w-4 h-4"></i> Rilis baru
        </button>
    </div>

    <div class="nb-rel-stats">
        <div class="nb-card nb-mini nb-in" style="--d:60ms">
            <span class="nb-ico nb-tone-mint"><i data-lucide="layers" class="w-4 h-4"></i></span>
            <div><p>Total rilis</p><b>{{ $items->count() }}</b></div>
        </div>
        <div class="nb-card nb-mini nb-in" style="--d:120ms">
            <span class="nb-ico nb-tone-violet"><i data-lucide="tag" class="w-4 h-4"></i></span>
            <div><p>Versi terbaru</p><b class="nb-mono">{{ $latest ? 'v'.$latest->version : '-' }}</b></div>
        </div>
        <div class="nb-card nb-mini nb-in" style="--d:180ms">
            <span class="nb-ico nb-tone-violet"><i data-lucide="star" class="w-4 h-4"></i></span>
            <div><p>Fitur baru</p><b>{{ $counts->get('feature', 0) }}</b></div>
        </div>
        <div class="nb-card nb-mini nb-in" style="--d:240ms">
            <span class="nb-ico nb-tone-sun"><i data-lucide="wrench" class="w-4 h-4"></i></span>
            <div><p>Perbaikan</p><b>{{ $counts->get('fix', 0) + $counts->get('hotfix', 0) }}</b></div>
        </div>
    </div>

    <div class="nb-rel-grid">

        {{-- Timeline --}}
        <div class="nb-tl">
            @forelse($groups as $ym => $rows)
                <section class="nb-tl-group">
                    <h3 class="nb-tl-month"><span>{{ $rows->first()->created_at->locale('id')->translatedFormat('F Y') }}</span></h3>
                    <ol class="nb-tl-list">
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
                            <li class="nb-tl-item nb-in" data-type="{{ $tk }}" @style(['--d:' . (min($loop->index, 8) * 50) . 'ms'])>
                                <span class="nb-ico nb-tl-dot nb-tone-{{ $tone }}"><i data-lucide="{{ $tIcon }}" class="w-5 h-5"></i></span>
                                <article class="nb-card nb-lift nb-tl-card {{ $isLatest ? 'is-latest' : '' }}">
                                    <div class="nb-tl-top">
                                        <div class="nb-tl-title">
                                            <h4>{{ $u->title }}</h4>
                                            <span class="nb-chip">v{{ $u->version }}</span>
                                            <span class="nb-badge nb-tone-{{ $tone }}">{{ $tLabel }}</span>
                                            @if($isLatest)<span class="nb-sticker nb-sticker-sm">Terbaru</span>@endif
                                        </div>
                                        <div class="nb-tl-side">
                                            <time class="nb-mono nb-muted" datetime="{{ $u->created_at->toDateString() }}">{{ $u->created_at->locale('id')->translatedFormat('d M Y') }}</time>
                                            <form action="{{ route('developer.updates.delete', [$secret, $u->id]) }}" method="POST"
                                                  onsubmit="return confirmAction(this, '🗑️ Hapus log rilis ini?', 'danger')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="nb-trash" aria-label="Hapus rilis {{ $u->version }}">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @if(count($lines))
                                        <ul class="nb-cl">
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
                <div class="nb-card nb-empty nb-in" style="--d:80ms">
                    <span class="nb-ico nb-ico-lg nb-ico-violet"><i data-lucide="history" class="w-6 h-6"></i></span>
                    <h4>Belum ada riwayat rilis</h4>
                    <p class="nb-muted">Rilis pertamamu akan muncul di sini sebagai timeline.</p>
                    <button type="button" class="nb-btn" data-open-release><i data-lucide="plus" class="w-4 h-4"></i> Buat rilis pertama</button>
                </div>
            @endforelse

            <div class="nb-card nb-empty" id="nb-tl-empty" hidden>
                <span class="nb-ico nb-ico-lg nb-ico-sun"><i data-lucide="search-x" class="w-6 h-6"></i></span>
                <h4>Tidak ada rilis dengan jenis ini</h4>
                <p class="nb-muted">Pilih jenis lain di filter untuk melihat rilis lainnya.</p>
            </div>
        </div>

        {{-- Filter --}}
        <aside class="nb-rel-aside">
            <div class="nb-card nb-in nb-sticky" style="--d:140ms">
                <div class="nb-card-head"><h3>Filter jenis rilis</h3></div>
                <div class="nb-fbtns">
                    <button type="button" class="nb-fbtn active" data-filter="all">
                        <span class="nb-fico nb-tone-mint"><i data-lucide="layers" class="w-4 h-4"></i></span>
                        Semua <small>{{ $items->count() }}</small>
                    </button>
                    @foreach($typeMeta as $key => [$fLabel, $fTone, $fIcon])
                        <button type="button" class="nb-fbtn" data-filter="{{ $key }}">
                            <span class="nb-fico nb-tone-{{ $fTone }}"><i data-lucide="{{ $fIcon }}" class="w-4 h-4"></i></span>
                            {{ $fLabel }} <small>{{ $counts->get($key, 0) }}</small>
                        </button>
                    @endforeach
                </div>
            </div>
        </aside>
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

    /* ───── Modal helper ───── */
    function openOverlay(el) {
        el.hidden = false;
        requestAnimationFrame(() => requestAnimationFrame(() => el.classList.add('open')));
        document.body.style.overflow = 'hidden';
    }
    function closeOverlay(el) {
        el.classList.remove('open');
        setTimeout(() => { el.hidden = true; document.body.style.overflow = ''; }, 300);
    }

    /* ───── Modal selamat datang (sekali per hari) ───── */
    const welcome = $('#nb-welcome');
    const today = new Date().toISOString().slice(0, 10);
    const KEY = 'dev_panel_welcome_skip';
    const safe = (fn) => { try { return fn(); } catch (e) { return null; } };

    function closeWelcome() {
        if ($('#nb-welcome-skip').checked) safe(() => localStorage.setItem(KEY, today));
        closeOverlay(welcome);
    }
    $$('[data-close-welcome]').forEach(b => b.addEventListener('click', closeWelcome));
    welcome.addEventListener('click', e => { if (e.target === welcome) closeWelcome(); });
    $('#nb-open-welcome')?.addEventListener('click', () => openOverlay(welcome));
    const relErr = !!document.querySelector('#nb-release-modal[data-open="1"]');
    const WELCOME_SEEN_KEY = 'dev_panel_welcome_seen';
    const seen = safe(() => localStorage.getItem(WELCOME_SEEN_KEY));
    if (!relErr && !seen && safe(() => localStorage.getItem(KEY + '_skip')) !== today) {
        setTimeout(() => {
            openOverlay(welcome);
            safe(() => localStorage.setItem(WELCOME_SEEN_KEY, '1'));
        }, 450);
    }

    /* ───── Modal konfirmasi ───── */
    const box = $('#nb-confirm');
    let pending = null;
    function closeConfirm() { closeOverlay(box); pending = null; }

    window.confirmAction = function (el, msg, tone) {
        pending = el;
        $('#nb-confirm-msg').textContent = msg;
        const danger = tone === 'danger';
        const ico = $('#nb-confirm-icon');
        ico.className = 'nb-ico nb-ico-lg ' + (danger ? 'nb-ico-rose' : 'nb-ico-violet');
        $('#nb-confirm-yes').className = 'nb-btn' + (danger ? ' nb-btn-danger' : '');
        openOverlay(box);
        setTimeout(() => $('#nb-confirm-yes').focus(), 60);
        return false; // selalu tahan aksi, lanjut setelah pengguna menyetujui
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

    /* ───── Modal rilis baru ───── */
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

    /* ───── Filter timeline rilis ───── */
    const fbtns = $$('[data-filter]');
    const tlItems = $$('.nb-tl-item');
    const tlGroups = $$('.nb-tl-group');
    const tlEmpty = $('#nb-tl-empty');
    fbtns.forEach(btn => btn.addEventListener('click', () => {
        const f = btn.dataset.filter;
        fbtns.forEach(b => b.classList.toggle('active', b === btn));
        let shown = 0;
        tlItems.forEach((it, i) => {
            const ok = f === 'all' || it.dataset.type === f;
            it.hidden = !ok;
            if (!ok) return;
            shown++;
            it.style.setProperty('--d', Math.min(i, 8) * 40 + 'ms');
            it.classList.remove('nb-in');
            void it.offsetWidth;
            it.classList.add('nb-in');
        });
        tlGroups.forEach(g => { g.hidden = !g.querySelector('.nb-tl-item:not([hidden])'); });
        if (tlEmpty && tlItems.length) tlEmpty.hidden = shown > 0;
    }));

    /* ───── Upload APK ───── */
    window.setApkName = function (file) {
        const drop = $('#apk-drop');
        $('#apk-filename').textContent = file ? file.name : '.apk, maksimal 100MB';
        drop.classList.toggle('has-file', !!file);
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

    /* ───── Debug toggle ───── */
    const dbgBtn = $('#debug-toggle-btn');
    if (dbgBtn) {
        const updateDebugUI = (on) => {
            document.getElementById('debug-toggle-label').textContent = on ? 'Debug ON' : 'Debug OFF';
            document.getElementById('debug-toggle-desc').textContent  = on ? 'Klik untuk mematikan debug' : 'Klik untuk menyalakan debug';
            const ico = document.getElementById('debug-toggle-ico');
            ico.className = 'nb-ico nb-ico-lg ' + (on ? 'nb-ico-rose' : 'nb-ico-mint');
            dbgBtn.dataset.debug = on ? '1' : '0';
        };
        updateDebugUI(dbgBtn.dataset.debug === '1');

        dbgBtn.addEventListener('click', () => {
            const next = dbgBtn.dataset.debug !== '1';
            if (!confirm(next ? '⚠️ Nyalakan APP_DEBUG?\nSemua error detail akan terlihat ke pengguna!' : 'Matikan APP_DEBUG?\nDetail error akan disembunyikan.')) return;
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
            fetch('/dev-panel/{{ $secret }}/toggle-debug', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': token},
                body: JSON.stringify({ debug: next })
            }).then(r => r.json())
              .then(() => { updateDebugUI(next); location.reload(); })
              .catch(e => alert('Gagal toggle debug: ' + e.message));
        });
    }

    /* ───── Hitung naik saat angka terlihat ───── */
    function countUp(el) {
        const target = parseInt(el.dataset.count, 10) || 0;
        if (reduce || target === 0) { el.textContent = target.toLocaleString('id-ID'); return; }
        const dur = 1100, t0 = performance.now();
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
    $$('.nb [data-count]').forEach(el => io ? io.observe(el) : countUp(el));

    /* ───── Tilt halus pada kartu ───── */
    if (!reduce && matchMedia('(hover: hover)').matches) {
        $$('.nb .tilt-card').forEach(card => {
            card.addEventListener('pointermove', e => {
                const r = card.getBoundingClientRect();
                const x = (e.clientX - r.left) / r.width - .5;
                const y = (e.clientY - r.top) / r.height - .5;
                card.style.transform = `perspective(900px) rotateX(${(-y * 4).toFixed(2)}deg) rotateY(${(x * 5).toFixed(2)}deg) translate(-3px,-3px)`;
            });
            card.addEventListener('pointerleave', () => { card.style.transform = ''; });
        });
    }

    icons();
    document.addEventListener('DOMContentLoaded', icons);
})();
</script>
@endsection