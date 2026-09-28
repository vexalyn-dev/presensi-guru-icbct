@extends('layouts.developer')
@section('content')

{{-- ══════════════════════════════════════════════════════
     TAB: DASHBOARD
══════════════════════════════════════════════════════ --}}
<div id="tab-dashboard" class="tab-content">

    {{-- Top Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-6">
        <div class="saas-card tilt-card p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background: rgba(109,94,246,.12); border: 1px solid rgba(109,94,246,.22);">
                    <i data-lucide="users" class="w-4 h-4" style="color: var(--accent-2);"></i>
                </div>
                <p class="text-sm font-medium" style="color: var(--text-2);">Total Pengguna</p>
            </div>
            <div class="text-3xl font-display font-semibold mono" style="color: var(--text-1);"
                 data-count="{{ $stats['total_users'] ?? 0 }}">0</div>
        </div>
        <div class="saas-card tilt-card p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background: rgba(34,211,238,.1); border: 1px solid rgba(34,211,238,.22);">
                    <i data-lucide="graduation-cap" class="w-4 h-4" style="color: var(--accent-cyan);"></i>
                </div>
                <p class="text-sm font-medium" style="color: var(--text-2);">Guru Aktif</p>
            </div>
            <div class="text-3xl font-display font-semibold mono" style="color: var(--text-1);"
                 data-count="{{ $stats['total_teachers'] ?? 0 }}">0</div>
        </div>
        <div class="saas-card tilt-card p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background: rgba(245,165,36,.1); border: 1px solid rgba(245,165,36,.22);">
                    <i data-lucide="server" class="w-4 h-4" style="color: var(--accent-amber);"></i>
                </div>
                <p class="text-sm font-medium" style="color: var(--text-2);">Versi PHP</p>
            </div>
            <div class="text-xl font-display font-semibold mono mt-2" style="color: var(--text-1);">
                {{ $stats['php_version'] ?? '8.x' }}
            </div>
        </div>
        <div class="saas-card tilt-card p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background: rgba(251,113,133,.1); border: 1px solid rgba(251,113,133,.22);">
                    <i data-lucide="code-2" class="w-4 h-4" style="color: var(--accent-rose);"></i>
                </div>
                <p class="text-sm font-medium" style="color: var(--text-2);">Laravel</p>
            </div>
            <div class="text-xl font-display font-semibold mono mt-2" style="color: var(--text-1);">
                v{{ $stats['laravel_version'] ?? '11.x' }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── System Info ── --}}
        <div class="lg:col-span-2 saas-card">
            <div class="px-6 py-4 border-b" style="border-color: var(--glass-border);">
                <h2 class="text-sm font-display font-semibold" style="color: var(--text-1);">Informasi Sistem</h2>
            </div>
            <div class="p-6 grid grid-cols-2 gap-y-6 gap-x-10">
                <div>
                    <p class="text-xs uppercase tracking-wide mb-1" style="color: var(--text-3);">Environment</p>
                    <p class="font-medium mono" style="color: var(--text-1);">{{ strtoupper($stats['env'] ?? 'PRODUCTION') }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide mb-1" style="color: var(--text-3);">Debug Mode</p>
                    <p class="font-medium flex items-center gap-2" style="color: var(--text-1);">
                        <span class="w-2 h-2 rounded-full inline-block"
                              style="background: {{ $stats['debug'] ? 'var(--accent-amber)' : 'var(--accent-emerald)' }};
                                     box-shadow: {{ $stats['debug'] ? '0 0 0 3px rgba(245,165,36,.2)' : '0 0 0 3px rgba(52,211,153,.2)' }};"></span>
                        {{ $stats['debug'] ? 'Aktif ⚠️' : 'Nonaktif ✅' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide mb-1" style="color: var(--text-3);">Host URL</p>
                    <p class="font-medium mono text-sm truncate" style="color: var(--text-1);">
                        {{ parse_url($stats['app_url'] ?? '', PHP_URL_HOST) ?? 'localhost' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide mb-1" style="color: var(--text-3);">Waktu Server</p>
                    <p class="font-medium mono" style="color: var(--text-1);">{{ now()->format('H:i') }} WIB</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide mb-1" style="color: var(--text-3);">Izin Pending</p>
                    <p class="font-medium mono" style="color: var(--text-1);">{{ $stats['pending_leaves'] ?? 0 }} pengajuan</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide mb-1" style="color: var(--text-3);">Operator / Admin</p>
                    <p class="font-medium mono" style="color: var(--text-1);">{{ $stats['total_operators'] ?? 0 }} akun</p>
                </div>
            </div>
        </div>

        {{-- ── Quick Actions ── --}}
        <div class="lg:col-span-1 saas-card flex flex-col">
            <div class="px-6 py-4 border-b" style="border-color: var(--glass-border);">
                <h2 class="text-sm font-display font-semibold" style="color: var(--text-1);">Aksi Cepat</h2>
            </div>
            <div class="p-3 space-y-0.5 flex-1">

                {{-- Bersihkan Cache (Sapu Jagat) --}}
                <a href="{{ route('developer.clear-cache', $secret) }}"
                   onclick="return confirmAction(this, '🧹 Bersihkan semua cache?\n(config, route, view, app cache)')"
                   class="quick-action-btn">
                    <div class="flex items-center gap-3">
                        <span class="qa-icon" style="background: rgba(34,211,238,.1); border-color: rgba(34,211,238,.2);">
                            <i data-lucide="trash-2" class="w-4 h-4" style="color: var(--accent-cyan);"></i>
                        </span>
                        <div>
                            <p class="text-sm font-semibold" style="color: var(--text-1);">Sapu Jagat</p>
                            <p class="text-xs" style="color: var(--text-3);">Clear semua cache</p>
                        </div>
                    </div>
                    <i data-lucide="arrow-right" class="w-4 h-4 flex-shrink-0" style="color: var(--text-3);"></i>
                </a>

                {{-- Run Migration --}}
                <a href="{{ route('developer.migrate', $secret) }}"
                   onclick="return confirmAction(this, '🗄️ Jalankan database migration?\nPastikan backup sudah ada!')"
                   class="quick-action-btn">
                    <div class="flex items-center gap-3">
                        <span class="qa-icon" style="background: rgba(52,211,153,.1); border-color: rgba(52,211,153,.2);">
                            <i data-lucide="database" class="w-4 h-4" style="color: var(--accent-emerald);"></i>
                        </span>
                        <div>
                            <p class="text-sm font-semibold" style="color: var(--text-1);">Run Migration</p>
                            <p class="text-xs" style="color: var(--text-3);">migrate --force</p>
                        </div>
                    </div>
                    <i data-lucide="arrow-right" class="w-4 h-4 flex-shrink-0" style="color: var(--text-3);"></i>
                </a>

                {{-- Optimize --}}
                <a href="{{ route('developer.optimize', $secret) }}"
                   onclick="return confirmAction(this, '⚡ Rebuild semua cache?\n(config, route, view cache)')"
                   class="quick-action-btn">
                    <div class="flex items-center gap-3">
                        <span class="qa-icon" style="background: rgba(245,165,36,.1); border-color: rgba(245,165,36,.2);">
                            <i data-lucide="zap" class="w-4 h-4" style="color: var(--accent-amber);"></i>
                        </span>
                        <div>
                            <p class="text-sm font-semibold" style="color: var(--text-1);">Optimize</p>
                            <p class="text-xs" style="color: var(--text-3);">Rebuild & cache</p>
                        </div>
                    </div>
                    <i data-lucide="arrow-right" class="w-4 h-4 flex-shrink-0" style="color: var(--text-3);"></i>
                </a>

                {{-- Deploy --}}
                <form action="{{ route('developer.deploy', $secret) }}" method="POST" id="deploy-form">
                    @csrf
                    <button type="submit" onclick="return confirmAction(this.closest('form'), '🚀 Jalankan full deploy?\n(git pull → composer → migrate → optimize)')"
                            class="quick-action-btn w-full text-left">
                        <div class="flex items-center gap-3">
                            <span class="qa-icon" style="background: rgba(109,94,246,.1); border-color: rgba(109,94,246,.2);">
                                <i data-lucide="rocket" class="w-4 h-4" style="color: var(--accent-2);"></i>
                            </span>
                            <div>
                                <p class="text-sm font-semibold" style="color: var(--text-1);">Full Deploy</p>
                                <p class="text-xs" style="color: var(--text-3);">git pull → migrate → optimize</p>
                            </div>
                        </div>
                        <i data-lucide="arrow-right" class="w-4 h-4 flex-shrink-0" style="color: var(--text-3);"></i>
                    </button>
                </form>

                {{-- Main App --}}
                <a href="{{ url('/dashboard') }}" target="_blank" class="quick-action-btn">
                    <div class="flex items-center gap-3">
                        <span class="qa-icon" style="background: rgba(255,255,255,.04); border-color: rgba(255,255,255,.08);">
                            <i data-lucide="external-link" class="w-4 h-4" style="color: var(--text-2);"></i>
                        </span>
                        <div>
                            <p class="text-sm font-semibold" style="color: var(--text-1);">Buka Aplikasi</p>
                            <p class="text-xs" style="color: var(--text-3);">dashboard utama</p>
                        </div>
                    </div>
                    <i data-lucide="arrow-right" class="w-4 h-4 flex-shrink-0" style="color: var(--text-3);"></i>
                </a>

            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════
     TAB: APK MANAGER
══════════════════════════════════════════════════════ --}}
<div id="tab-apk" class="tab-content">
    <div class="max-w-4xl">
        <div class="mb-6">
            <h2 class="text-2xl font-display font-semibold" style="color: var(--text-1);">APK Manager</h2>
            <p class="text-sm mt-1" style="color: var(--text-2);">Unggah dan distribusikan build Android terbaru ke pengguna.</p>
        </div>

        @if($appSetting?->apk_file)
            <div class="saas-card tilt-card mb-6">
                <div class="p-6 flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0"
                             style="background: rgba(52,211,153,.1); border: 1px solid rgba(52,211,153,.25);">
                            <i data-lucide="package-check" class="w-6 h-6" style="color: var(--accent-emerald);"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold" style="color: var(--text-1);">
                                {{ $appSetting->apk_name ?? 'ICB CT Presensi' }}
                                <span class="ml-2 inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium mono"
                                      style="background: rgba(255,255,255,.05); border: 1px solid var(--glass-border); color: var(--text-2);">
                                    v{{ $appSetting->apk_version_label ?? $appSetting->apk_version ?? '1.0' }}
                                </span>
                            </h4>
                            <p class="text-sm mt-1 mono" style="color: var(--text-3);">
                                {{ $appSetting->apk_size_human ?? '-' }} ·
                                Diunggah {{ $appSetting->apk_uploaded_at?->diffForHumans() ?? '-' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ $appSetting->apk_url }}" target="_blank" class="saas-btn-secondary saas-btn text-sm">
                            <i data-lucide="download" class="w-4 h-4"></i> Unduh
                        </a>
                        <form action="{{ route('developer.apk.delete', $secret) }}" method="POST"
                              onsubmit="return confirm('Hapus APK ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="saas-btn text-sm"
                                    style="background: linear-gradient(180deg,#fb7185,#ef4444); box-shadow: 0 1px 0 rgba(255,255,255,.2) inset, 0 8px 20px -8px rgba(239,68,68,.5);">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <div class="saas-card">
            <div class="px-6 py-4 border-b" style="border-color: var(--glass-border);">
                <h3 class="text-sm font-display font-semibold" style="color: var(--text-1);">Unggah Build Baru</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('developer.apk', $secret) }}" method="POST"
                      enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-2);">File APK</label>
                        <div class="flex justify-center rounded-xl border border-dashed px-6 py-10 transition-all cursor-pointer"
                             style="border-color: rgba(255,255,255,.14); background: rgba(255,255,255,.015);"
                             onclick="document.getElementById('apk-input').click()"
                             ondragover="event.preventDefault(); this.style.borderColor='rgba(109,94,246,.5)'"
                             ondragleave="this.style.borderColor='rgba(255,255,255,.14)'"
                             ondrop="handleApkDrop(event, this)">
                            <div class="text-center pointer-events-none">
                                <i data-lucide="upload-cloud" class="mx-auto h-10 w-10 mb-3" style="color: var(--text-3);"></i>
                                <p class="text-sm" style="color: var(--text-2);">
                                    <span class="font-semibold" style="color: var(--accent-2);">Klik untuk pilih</span> atau seret file
                                </p>
                                <p id="apk-filename" class="text-xs mt-1 mono" style="color: var(--text-3);">.apk maks 100MB</p>
                            </div>
                        </div>
                        <input type="file" id="apk-input" name="apk_file" accept=".apk" class="hidden"
                               onchange="document.getElementById('apk-filename').textContent = this.files[0]?.name ?? '.apk maks 100MB'">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--text-2);">Nama Aplikasi</label>
                            <input type="text" name="apk_name" class="saas-input"
                                   value="{{ old('apk_name', $appSetting?->apk_name ?? '') }}"
                                   placeholder="ICB CT Mobile">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--text-2);">Label Versi</label>
                            <input type="text" name="apk_version" class="saas-input mono"
                                   value="{{ old('apk_version', $appSetting?->apk_version ?? '') }}"
                                   placeholder="1.0.0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--text-2);">Min Android</label>
                            <input type="text" name="apk_min_android" class="saas-input"
                                   value="{{ old('apk_min_android', $appSetting?->apk_min_android ?? '') }}"
                                   placeholder="Android 8.0+">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--text-2);">Changelog</label>
                            <input type="text" name="apk_changelog" class="saas-input"
                                   value="{{ old('apk_changelog', $appSetting?->apk_changelog ?? '') }}"
                                   placeholder="Bug fixes & improvements">
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="saas-btn">
                            <i data-lucide="save" class="w-4 h-4"></i> Simpan APK
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════
     TAB: SYSTEM STATE
══════════════════════════════════════════════════════ --}}
<div id="tab-system" class="tab-content">
    <div class="max-w-3xl space-y-6">
        <div>
            <h2 class="text-2xl font-display font-semibold" style="color: var(--text-1);">System State</h2>
            <p class="text-sm mt-1" style="color: var(--text-2);">Atur ketersediaan aplikasi dan tools sistem.</p>
        </div>

        {{-- Maintenance Mode --}}
        <div class="saas-card">
            @php $mOn = \App\Models\AppSetting::getInstance()->maintenance_mode ?? false; @endphp
            <form action="{{ route('developer.maintenance', $secret) }}" method="POST">
                @csrf
                <div class="p-6 border-b flex items-start justify-between gap-6" style="border-color: var(--glass-border);">
                    <div>
                        <h3 class="text-base font-display font-semibold" style="color: var(--text-1);">Mode Maintenance</h3>
                        <p class="text-sm mt-1 max-w-md" style="color: var(--text-2);">
                            Saat aktif, pengguna biasa akan melihat halaman maintenance.
                            Admin & operator tetap bisa akses.
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                        <input type="hidden" name="maintenance_mode" value="0">
                        <input type="checkbox" name="maintenance_mode" value="1" {{ $mOn ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 rounded-full peer transition-all
                                    peer-checked:after:translate-x-full after:content-['']
                                    after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"
                             style="background-color: rgba(255,255,255,.1);"
                             :class="{'bg-[var(--accent)]': checked}">
                        </div>
                    </label>
                </div>
                <div class="p-6 border-b" style="border-color: var(--glass-border);">
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-2);">Pesan Maintenance</label>
                    <textarea name="maintenance_message" rows="2" class="saas-input"
                              placeholder="Sistem sedang dalam pemeliharaan...">{{ \App\Models\AppSetting::getInstance()->maintenance_message }}</textarea>
                </div>
                <div class="px-6 py-4 flex justify-end rounded-b-[18px]"
                     style="background: rgba(255,255,255,.02);">
                    <button type="submit" class="saas-btn text-sm">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- System Tools Grid --}}
        <div class="saas-card">
            <div class="px-6 py-4 border-b" style="border-color: var(--glass-border);">
                <h3 class="text-sm font-display font-semibold" style="color: var(--text-1);">System Tools</h3>
            </div>
            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3">

                {{-- Clear Cache --}}
                <a href="{{ route('developer.clear-cache', $secret) }}"
                   onclick="return confirmAction(this, '🧹 Bersihkan semua cache?')"
                   class="system-tool-card">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: rgba(34,211,238,.1); border: 1px solid rgba(34,211,238,.2);">
                        <i data-lucide="trash-2" class="w-5 h-5" style="color: var(--accent-cyan);"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-sm" style="color: var(--text-1);">Sapu Jagat</p>
                        <p class="text-xs mt-0.5" style="color: var(--text-3);">Clear config, route, view & app cache</p>
                    </div>
                </a>

                {{-- Migrate --}}
                <a href="{{ route('developer.migrate', $secret) }}"
                   onclick="return confirmAction(this, '🗄️ Jalankan migration? Pastikan backup ada!')"
                   class="system-tool-card">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: rgba(52,211,153,.1); border: 1px solid rgba(52,211,153,.2);">
                        <i data-lucide="database" class="w-5 h-5" style="color: var(--accent-emerald);"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-sm" style="color: var(--text-1);">Run Migration</p>
                        <p class="text-xs mt-0.5" style="color: var(--text-3);">artisan migrate --force</p>
                    </div>
                </a>

                {{-- Optimize --}}
                <a href="{{ route('developer.optimize', $secret) }}"
                   onclick="return confirmAction(this, '⚡ Rebuild semua cache?')"
                   class="system-tool-card">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: rgba(245,165,36,.1); border: 1px solid rgba(245,165,36,.2);">
                        <i data-lucide="zap" class="w-5 h-5" style="color: var(--accent-amber);"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-sm" style="color: var(--text-1);">Optimize</p>
                        <p class="text-xs mt-0.5" style="color: var(--text-3);">Rebuild & cache semua config/route/view</p>
                    </div>
                </a>

                {{-- Full Deploy --}}
                <form action="{{ route('developer.deploy', $secret) }}" method="POST"
                      onsubmit="return confirmAction(this, '🚀 Full deploy?\ngit pull → composer → migrate → optimize')">
                    @csrf
                    <button type="submit" class="system-tool-card w-full text-left">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                             style="background: rgba(109,94,246,.1); border: 1px solid rgba(109,94,246,.2);">
                            <i data-lucide="rocket" class="w-5 h-5" style="color: var(--accent-2);"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-sm" style="color: var(--text-1);">Full Deploy</p>
                            <p class="text-xs mt-0.5" style="color: var(--text-3);">git pull → composer → migrate → cache</p>
                        </div>
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════
     TAB: RELEASES
══════════════════════════════════════════════════════ --}}
<div id="tab-releases" class="tab-content">
    <div class="mb-6">
        <h2 class="text-2xl font-display font-semibold" style="color: var(--text-1);">Riwayat Rilis</h2>
        <p class="text-sm mt-1" style="color: var(--text-2);">Kelola changelog dan notifikasi fitur baru ke pengguna.</p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- List --}}
        <div class="xl:col-span-2 space-y-4">
            @forelse($updates as $u)
                @php
                    $color = match($u->type) {
                        'feature' => 'var(--accent-2)',
                        'fix'     => 'var(--accent-amber)',
                        'hotfix'  => 'var(--accent-rose)',
                        default   => 'var(--accent-cyan)',
                    };
                    $bg = match($u->type) {
                        'feature' => 'rgba(167,139,250,.1)',
                        'fix'     => 'rgba(245,165,36,.1)',
                        'hotfix'  => 'rgba(251,113,133,.1)',
                        default   => 'rgba(34,211,238,.1)',
                    };
                    $icon = match($u->type) {
                        'feature' => 'star',
                        'fix'     => 'wrench',
                        'hotfix'  => 'flame',
                        default   => 'git-commit',
                    };
                @endphp
                <div class="saas-card tilt-card p-5 flex gap-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                         style="background: {{ $bg }}; border: 1px solid {{ $color }}40;">
                        <i data-lucide="{{ $icon }}" class="w-5 h-5" style="color: {{ $color }};"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-3 mb-1">
                            <div class="flex items-center gap-2 min-w-0">
                                <h4 class="font-semibold truncate font-display" style="color: var(--text-1);">{{ $u->title }}</h4>
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium mono flex-shrink-0"
                                      style="background: rgba(255,255,255,.05); border: 1px solid var(--glass-border); color: var(--text-2);">
                                    v{{ $u->version }}
                                </span>
                            </div>
                            <span class="text-xs flex-shrink-0 mono" style="color: var(--text-3);">
                                {{ $u->created_at->format('d M Y') }}
                            </span>
                        </div>
                        <p class="text-sm whitespace-pre-line" style="color: var(--text-2);">{{ $u->content }}</p>
                    </div>
                    <div class="pl-4 border-l flex flex-col justify-center flex-shrink-0"
                         style="border-color: var(--glass-border);">
                        <form action="{{ route('developer.updates.delete', [$secret, $u->id]) }}" method="POST"
                              onsubmit="return confirm('Hapus log rilis ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1 transition-colors" style="color: var(--text-3);"
                                    onmouseover="this.style.color='var(--accent-rose)'"
                                    onmouseout="this.style.color='var(--text-3)'">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="saas-card p-12 text-center" style="color: var(--text-2);">
                    <i data-lucide="history" class="w-10 h-10 mx-auto mb-3 opacity-20"></i>
                    <p>Belum ada riwayat rilis.</p>
                </div>
            @endforelse
        </div>

        {{-- Form buat rilis baru --}}
        <div class="xl:col-span-1">
            <div class="saas-card sticky top-24">
                <div class="px-5 py-4 border-b" style="border-color: var(--glass-border);">
                    <h3 class="text-sm font-display font-semibold" style="color: var(--text-1);">Buat Rilis Baru</h3>
                </div>
                <div class="p-5">
                    <form action="{{ route('developer.updates.store', $secret) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color: var(--text-2);">Versi</label>
                            <input type="text" name="version" class="saas-input py-2 mono" required placeholder="1.2.0">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color: var(--text-2);">Tipe</label>
                            <select name="type" class="saas-input py-2">
                                <option value="feature">✨ Feature</option>
                                <option value="update">🔄 Update</option>
                                <option value="fix">🔧 Fix</option>
                                <option value="hotfix">🔥 Hotfix</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color: var(--text-2);">Judul</label>
                            <input type="text" name="title" class="saas-input py-2" required placeholder="Menambahkan Laporan Baru">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color: var(--text-2);">Changelog</label>
                            <textarea name="content" rows="4" class="saas-input py-2" required
                                      placeholder="- Memperbaiki bug A&#10;- Menambahkan fitur B"></textarea>
                        </div>
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="show_modal" value="1" checked
                                   class="rounded" style="accent-color: var(--accent);">
                            <span class="text-xs" style="color: var(--text-2);">Tampilkan modal ke pengguna</span>
                        </label>
                        <button type="submit" class="saas-btn w-full text-sm">
                            <i data-lucide="send" class="w-4 h-4"></i> Publikasikan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ── Inline Styles ── --}}
<style>
    .quick-action-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid transparent;
        transition: background .18s ease, border-color .18s ease, transform .18s ease;
        cursor: pointer;
        background: transparent;
    }
    .quick-action-btn:hover {
        background: rgba(255,255,255,.04);
        border-color: var(--glass-border);
        transform: translateX(2px);
    }
    .qa-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1px solid;
        flex-shrink: 0;
    }
    .system-tool-card {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px;
        border-radius: 12px;
        border: 1px solid var(--glass-border);
        background: rgba(255,255,255,.02);
        transition: background .18s ease, border-color .18s ease, transform .18s ease;
        cursor: pointer;
    }
    .system-tool-card:hover {
        background: rgba(255,255,255,.05);
        border-color: rgba(255,255,255,.12);
        transform: translateY(-1px);
    }
</style>

@endsection

@section('scripts')
<script>
    function confirmAction(el, msg) {
        if (!confirm(msg)) return false;
        // Visual feedback: flash the element
        if (el && el.style) {
            el.style.opacity = '0.6';
            el.style.pointerEvents = 'none';
        }
        return true;
    }

    function handleApkDrop(event, zone) {
        event.preventDefault();
        zone.style.borderColor = 'rgba(255,255,255,.14)';
        const files = event.dataTransfer.files;
        if (files.length) {
            const input = document.getElementById('apk-input');
            const dt = new DataTransfer();
            dt.items.add(files[0]);
            input.files = dt.files;
            document.getElementById('apk-filename').textContent = files[0].name;
        }
    }
</script>
@endsection
