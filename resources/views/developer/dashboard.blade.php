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

<div class="nb">

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
                    <div><strong>Deploy dan maintenance</strong><small>Jalankan migration, optimize, atau full deploy dengan satu klik.</small></div>
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
            <div class="nb-ico nb-ico-lg nb-ico-violet" id="nb-confirm-icon"><i data-lucide="shield-question" class="w-6 h-6"></i></div>
            <h3 id="nb-confirm-title">Yakin lanjut?</h3>
            <p class="nb-muted nb-pre" id="nb-confirm-msg"></p>
            <div class="nb-row-end">
                <button type="button" class="nb-btn nb-btn-ghost" id="nb-confirm-no">Batal</button>
                <button type="button" class="nb-btn" id="nb-confirm-yes">Ya, lanjutkan</button>
            </div>
        </div>
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
        <div class="nb-alert nb-in" style="--d:80ms">
            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            <span><b>Debug mode aktif.</b> Matikan <code>APP_DEBUG</code> di production supaya detail error tidak terlihat pengguna.</span>
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
                <form action="{{ route('developer.deploy', $secret) }}" method="POST">
                    @csrf
                    <button type="submit" class="nb-action nb-action-primary"
                            onclick="return confirmAction(this.closest('form'), '🚀 Jalankan full deploy?\n(git pull → composer → migrate → optimize)')">
                        <span class="nb-ico nb-ico-white"><i data-lucide="rocket" class="w-4 h-4"></i></span>
                        <div><b>Full deploy</b><small>git pull, migrate, optimize</small></div>
                        <i data-lucide="arrow-up-right" class="w-4 h-4 nb-go"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═════════════ TAB: APK MANAGER ═════════════ --}}
<div id="tab-apk" class="tab-content">
    <div class="nb-wrap-md">
        <header class="nb-page-head nb-in" style="--d:0ms">
            <h2>APK Manager</h2>
            <p>Unggah dan bagikan build Android terbaru ke pengguna.</p>
        </header>

        @if($appSetting?->apk_file)
            <div class="nb-card nb-lift tilt-card nb-in nb-mb" style="--d:80ms">
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
            </div>
        @endif

        <div class="nb-card nb-in" style="--d:140ms">
            <div class="nb-card-head"><h3>Unggah build baru</h3></div>
            <div class="nb-pad">
                <form action="{{ route('developer.apk', $secret) }}" method="POST" enctype="multipart/form-data" class="nb-stack">
                    @csrf
                    <div>
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
                    </div>

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
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═════════════ TAB: SYSTEM STATE ═════════════ --}}
<div id="tab-system" class="tab-content">
    <div class="nb-wrap-md nb-stack-lg">
        <header class="nb-page-head nb-in" style="--d:0ms">
            <h2>System State</h2>
            <p>Atur ketersediaan aplikasi dan jalankan tools sistem.</p>
        </header>

        <div class="nb-card nb-in" style="--d:80ms">
            <form action="{{ route('developer.maintenance', $secret) }}" method="POST">
                @csrf
                <div class="nb-pad nb-split nb-bb">
                    <div>
                        <h3 class="nb-h3">Mode maintenance</h3>
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
                    <textarea name="maintenance_message" rows="2" class="nb-input" placeholder="Sistem sedang dalam pemeliharaan...">{{ $setting->maintenance_message }}</textarea>
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
                <form action="{{ route('developer.deploy', $secret) }}" method="POST"
                      onsubmit="return confirmAction(this, '🚀 Jalankan full deploy?\ngit pull → composer → migrate → optimize')">
                    @csrf
                    <button type="submit" class="nb-tool nb-tool-primary">
                        <span class="nb-ico nb-ico-lg nb-ico-white"><i data-lucide="rocket" class="w-5 h-5"></i></span>
                        <div><b>Full deploy</b><small>git pull, composer, migrate, cache</small></div>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═════════════ TAB: RELEASES ═════════════ --}}
<div id="tab-releases" class="tab-content">
    <header class="nb-page-head nb-in" style="--d:0ms">
        <h2>Riwayat rilis</h2>
        <p>Kelola changelog dan beri tahu pengguna soal fitur baru.</p>
    </header>

    <div class="nb-grid-rel">
        <div class="nb-stack">
            @forelse($updates as $u)
                @php
                    $tone = match($u->type) { 'feature' => 'violet', 'fix' => 'sun', 'hotfix' => 'rose', default => 'sky' };
                    $icon = match($u->type) { 'feature' => 'star', 'fix' => 'wrench', 'hotfix' => 'flame', default => 'git-commit' };
                @endphp
                <article class="nb-card nb-lift tilt-card nb-in nb-release" style="--d:{{ min($loop->index, 6) * 60 + 80 }}ms">
                    <span class="nb-ico nb-ico-lg nb-ico-{{ $tone }}"><i data-lucide="{{ $icon }}" class="w-5 h-5"></i></span>
                    <div class="nb-release-body">
                        <div class="nb-split">
                            <div class="nb-row nb-min0">
                                <h4 class="truncate">{{ $u->title }}</h4>
                                <span class="nb-chip">v{{ $u->version }}</span>
                            </div>
                            <span class="nb-mono nb-muted nb-nowrap">{{ $u->created_at->format('d M Y') }}</span>
                        </div>
                        <p class="nb-muted nb-pre">{{ $u->content }}</p>
                    </div>
                    <form action="{{ route('developer.updates.delete', [$secret, $u->id]) }}" method="POST"
                          onsubmit="return confirmAction(this, '🗑️ Hapus log rilis ini?', 'danger')">
                        @csrf @method('DELETE')
                        <button type="submit" class="nb-trash" aria-label="Hapus rilis"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                    </form>
                </article>
            @empty
                <div class="nb-card nb-empty nb-in" style="--d:80ms">
                    <span class="nb-ico nb-ico-lg nb-ico-violet"><i data-lucide="history" class="w-6 h-6"></i></span>
                    <h4>Belum ada riwayat rilis</h4>
                    <p class="nb-muted">Buat rilis pertamamu lewat form di samping.</p>
                </div>
            @endforelse
        </div>

        <div>
            <div class="nb-card nb-in nb-sticky" style="--d:140ms">
                <div class="nb-card-head"><h3>Buat rilis baru</h3></div>
                <div class="nb-pad">
                    <form action="{{ route('developer.updates.store', $secret) }}" method="POST" class="nb-stack">
                        @csrf
                        <div><label class="nb-field-label">Versi</label>
                            <input type="text" name="version" class="nb-input nb-mono" required placeholder="1.2.0"></div>
                        <div><label class="nb-field-label">Tipe</label>
                            <select name="type" class="nb-input">
                                <option value="feature">✨ Feature</option>
                                <option value="update">🔄 Update</option>
                                <option value="fix">🔧 Fix</option>
                                <option value="hotfix">🔥 Hotfix</option>
                            </select></div>
                        <div><label class="nb-field-label">Judul</label>
                            <input type="text" name="title" class="nb-input" required placeholder="Menambahkan laporan baru"></div>
                        <div><label class="nb-field-label">Changelog</label>
                            <textarea name="content" rows="4" class="nb-input" required placeholder="- Memperbaiki bug A&#10;- Menambahkan fitur B"></textarea></div>
                        <label class="nb-check">
                            <input type="checkbox" name="show_modal" value="1" checked>
                            <span class="nb-box"><i data-lucide="check" class="w-3 h-3"></i></span>
                            <span>Tampilkan modal ke pengguna</span>
                        </label>
                        <button type="submit" class="nb-btn nb-btn-block"><i data-lucide="send" class="w-4 h-4"></i> Publikasikan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</div>{{-- /.nb --}}

<style>
@import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap');

.nb{
    --ink:#17132e; --paper:#ffffff; --canvas:#faf9ff; --line:#17132e;
    --violet:#7c5cff; --violet-d:#6142f0; --violet-l:#ece7ff; --violet-xl:#f6f3ff;
    --mint:#5eead4; --sun:#ffd166; --rose:#ff8fab; --sky:#8ecbff;
    --r-lg:22px; --r-md:14px; --r-sm:10px;
    --sh:4px 4px 0 var(--ink); --sh-lg:7px 7px 0 var(--ink); --sh-sm:2px 2px 0 var(--ink);
    --ease:cubic-bezier(.22,1,.36,1); --spring:cubic-bezier(.34,1.56,.64,1);
    font-family:'Plus Jakarta Sans',system-ui,sans-serif; color:var(--ink);
    background:
        radial-gradient(circle at 1px 1px, rgba(23,19,46,.09) 1px, transparent 0) 0 0/22px 22px,
        var(--canvas);
    border:2px solid var(--ink); border-radius:28px; padding:clamp(16px,3vw,32px);
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
.nb-btn-ghost-white{background:transparent;color:#fff;border-color:#fff;box-shadow:4px 4px 0 rgba(255,255,255,.9)}
.nb-btn-ghost-white:hover{background:rgba(255,255,255,.14);box-shadow:6px 6px 0 #fff}
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
.nb-in{animation:nb-rise .75s var(--ease) both;animation-delay:var(--d,0ms)}

@keyframes nb-rise{from{opacity:0;transform:translateY(22px) scale(.98)}to{opacity:1;transform:none}}
@keyframes nb-slide{to{opacity:1;transform:none}}
@keyframes nb-pulse{0%{box-shadow:0 0 0 0 rgba(124,92,255,.6)}100%{box-shadow:0 0 0 9px rgba(124,92,255,0)}}
@keyframes nb-bob{0%,100%{transform:translateY(0) rotate(-3deg)}50%{transform:translateY(-10px) rotate(3deg)}}
@keyframes nb-drift{0%,100%{transform:translate(0,0)}50%{transform:translate(-14px,12px)}}
@keyframes nb-wave{0%,60%,100%{transform:rotate(0)}10%,30%{transform:rotate(16deg)}20%,40%{transform:rotate(-10deg)}50%{transform:rotate(8deg)}}

@media(max-width:640px){
    .nb{border-radius:20px}
    .nb-info{grid-template-columns:1fr}
    .nb-release{flex-wrap:wrap}
}
@media(prefers-reduced-motion:reduce){
    .nb *,.nb *::before,.nb *::after{animation:none!important;transition-duration:.01ms!important}
    .nb-tips li{opacity:1;transform:none}
}
</style>

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
    if (safe(() => localStorage.getItem(KEY)) !== today) setTimeout(() => openOverlay(welcome), 450);

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

    document.addEventListener('keydown', e => {
        if (e.key !== 'Escape') return;
        if (!box.hidden) closeConfirm();
        else if (!welcome.hidden) closeWelcome();
    });

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