@extends('layouts.developer')
@section('content')

    {{-- ══ TAB: DASHBOARD ══ --}}
    <div id="tab-dashboard" class="tab-content">

        {{-- Top Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-6">
            <div class="saas-card tilt-card p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: rgba(109,94,246,.12); border: 1px solid rgba(109,94,246,.25);">
                        <i data-lucide="users" class="w-5 h-5" style="color: var(--accent-2);"></i>
                    </div>
                    <h3 class="text-sm font-medium" style="color: var(--text-2);">Total Pengguna</h3>
                </div>
                <div class="text-3xl font-display font-semibold mono" style="color: var(--text-1);" data-count="{{ $stats['total_users'] ?? 0 }}">0</div>
            </div>
            <div class="saas-card tilt-card p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: rgba(34,211,238,.1); border: 1px solid rgba(34,211,238,.25);">
                        <i data-lucide="graduation-cap" class="w-5 h-5" style="color: var(--accent-cyan);"></i>
                    </div>
                    <h3 class="text-sm font-medium" style="color: var(--text-2);">Guru Aktif</h3>
                </div>
                <div class="text-3xl font-display font-semibold mono" style="color: var(--text-1);" data-count="{{ $stats['total_teachers'] ?? 0 }}">0</div>
            </div>
            <div class="saas-card tilt-card p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: rgba(245,165,36,.1); border: 1px solid rgba(245,165,36,.25);">
                        <i data-lucide="server" class="w-5 h-5" style="color: var(--accent-amber);"></i>
                    </div>
                    <h3 class="text-sm font-medium" style="color: var(--text-2);">Versi PHP</h3>
                </div>
                <div class="text-3xl font-display font-semibold mono" style="color: var(--text-1);">{{ $stats['php_version'] ?? '8.x' }}</div>
            </div>
            <div class="saas-card tilt-card p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: rgba(251,113,133,.1); border: 1px solid rgba(251,113,133,.25);">
                        <i data-lucide="code-2" class="w-5 h-5" style="color: var(--accent-rose);"></i>
                    </div>
                    <h3 class="text-sm font-medium" style="color: var(--text-2);">Framework</h3>
                </div>
                <div class="text-3xl font-display font-semibold mono" style="color: var(--text-1);">v{{ $stats['laravel_version'] ?? '10.x' }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- System Overview --}}
            <div class="lg:col-span-2 saas-card tilt-card">
                <div class="px-6 py-5 border-b" style="border-color: var(--glass-border);">
                    <h2 class="text-base font-display font-semibold" style="color: var(--text-1);">Informasi Sistem</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-y-7 gap-x-10">
                        <div>
                            <p class="text-xs uppercase tracking-wide mb-1.5" style="color: var(--text-3);">Environment</p>
                            <p class="font-medium mono" style="color: var(--text-1);">{{ strtoupper($stats['env'] ?? 'PRODUCTION') }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide mb-1.5" style="color: var(--text-3);">Debug Mode</p>
                            <p class="font-medium flex items-center gap-2" style="color: var(--text-1);">
                                <span class="w-2 h-2 rounded-full" style="background: {{ $stats['debug'] ? 'var(--accent-amber)' : 'var(--text-3)' }}; box-shadow: {{ $stats['debug'] ? '0 0 0 3px rgba(245,165,36,.18)' : 'none' }};"></span>
                                {{ $stats['debug'] ? 'Aktif (Perhatian)' : 'Nonaktif (Aman)' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide mb-1.5" style="color: var(--text-3);">Host URL</p>
                            <p class="font-medium mono" style="color: var(--text-1);">{{ parse_url($stats['app_url'] ?? '', PHP_URL_HOST) ?? 'localhost' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide mb-1.5" style="color: var(--text-3);">Waktu Server</p>
                            <p class="font-medium mono" style="color: var(--text-1);">{{ now()->format('H:i') }} WIB</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="lg:col-span-1 saas-card tilt-card">
                <div class="px-6 py-5 border-b" style="border-color: var(--glass-border);">
                    <h2 class="text-base font-display font-semibold" style="color: var(--text-1);">Aksi Cepat</h2>
                </div>
                <div class="p-3 space-y-1">
                    <a href="{{ route('developer.clear-cache', $secret) }}" onclick="return confirm('Bersihkan cache aplikasi?')"
                       class="w-full flex items-center justify-between p-3 rounded-lg transition-all border border-transparent"
                       onmouseover="this.style.background='rgba(255,255,255,.04)'; this.style.borderColor='var(--glass-border)'"
                       onmouseout="this.style.background='transparent'; this.style.borderColor='transparent'">
                        <div class="flex items-center gap-3">
                            <i data-lucide="refresh-cw" class="w-4 h-4" style="color: var(--text-2);"></i>
                            <span class="text-sm font-medium" style="color: var(--text-1);">Bersihkan Cache</span>
                        </div>
                        <i data-lucide="arrow-right" class="w-4 h-4" style="color: var(--text-3);"></i>
                    </a>
                    <a href="{{ url('/run-migrate-secret?key=vexalyn19052009') }}" target="_blank"
                       class="w-full flex items-center justify-between p-3 rounded-lg transition-all border border-transparent"
                       onmouseover="this.style.background='rgba(255,255,255,.04)'; this.style.borderColor='var(--glass-border)'"
                       onmouseout="this.style.background='transparent'; this.style.borderColor='transparent'">
                        <div class="flex items-center gap-3">
                            <i data-lucide="database" class="w-4 h-4" style="color: var(--text-2);"></i>
                            <span class="text-sm font-medium" style="color: var(--text-1);">Jalankan Migration</span>
                        </div>
                        <i data-lucide="arrow-right" class="w-4 h-4" style="color: var(--text-3);"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>


    {{-- ══ TAB: APK MANAGER ══ --}}
    <div id="tab-apk" class="tab-content">
        <div class="max-w-4xl">
            <div class="mb-6">
                <h2 class="text-2xl font-display font-semibold" style="color: var(--text-1);">APK Manager</h2>
                <p class="text-sm mt-1" style="color: var(--text-2);">Unggah dan distribusikan build Android terbaru ke pengguna.</p>
            </div>

            @if($appSetting?->apk_file)
                <div class="saas-card tilt-card mb-6">
                    <div class="p-6 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: rgba(52,211,153,.1); border: 1px solid rgba(52,211,153,.25);">
                                <i data-lucide="package-check" class="w-6 h-6" style="color: var(--accent-emerald);"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold" style="color: var(--text-1);">
                                    {{ $appSetting->apk_name ?? 'ICB CT Presensi' }}
                                    <span class="ml-2 inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium mono" style="background: rgba(255,255,255,.05); border: 1px solid var(--glass-border); color: var(--text-2);">v{{ $appSetting->apk_version_label ?? '1.0' }}</span>
                                </h4>
                                <p class="text-sm mt-1 mono" style="color: var(--text-3);">{{ $appSetting->apk_size_human ?? '-' }} · Diunggah {{ $appSetting->apk_uploaded_at?->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ $appSetting->apk_url }}" target="_blank" class="saas-btn-secondary saas-btn">
                                <i data-lucide="download" class="w-4 h-4"></i> Unduh
                            </a>
                            <form action="{{ route('developer.apk.delete', $secret) }}" method="POST" onsubmit="return confirm('Hapus APK ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="saas-btn" style="background: linear-gradient(180deg, #fb7185, #ef4444); box-shadow: 0 1px 0 rgba(255,255,255,.25) inset, 0 10px 24px -10px rgba(239,68,68,.55);">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <div class="saas-card">
                <div class="px-6 py-5 border-b" style="border-color: var(--glass-border);">
                    <h3 class="text-base font-display font-semibold" style="color: var(--text-1);">Unggah Build Baru</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('developer.apk', $secret) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- File Input --}}
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--text-2);">File APK</label>
                            <div id="apk-dropzone" class="mt-1 flex justify-center rounded-xl border border-dashed px-6 py-10 transition-all"
                                 style="border-color: rgba(255,255,255,.14); background: rgba(255,255,255,.015);">
                                <div class="text-center">
                                    <i data-lucide="upload-cloud" class="mx-auto h-10 w-10" style="color: var(--text-3);"></i>
                                    <div class="mt-4 flex text-sm leading-6 justify-center" style="color: var(--text-2);">
                                        <label class="relative cursor-pointer font-semibold" style="color: var(--accent-2);">
                                            <span>Pilih file</span>
                                            <input type="file" name="apk_file" accept=".apk" class="sr-only" onchange="document.getElementById('file-name-display').textContent = this.files[0].name">
                                        </label>
                                        <p class="pl-1">atau seret &amp; lepas di sini</p>
                                    </div>
                                    <p id="file-name-display" class="text-xs leading-5 mt-1 mono" style="color: var(--text-3);">.apk maksimal 100MB</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium mb-1.5" style="color: var(--text-2);">Nama Aplikasi</label>
                                <input type="text" name="apk_name" value="{{ old('apk_name', $appSetting?->apk_name ?? '') }}" class="saas-input" placeholder="ICB CT Mobile">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1.5" style="color: var(--text-2);">Label Versi</label>
                                <input type="text" name="apk_version" value="{{ old('apk_version', $appSetting?->apk_version ?? '') }}" class="saas-input mono" placeholder="1.0.0">
                            </div>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit" class="saas-btn">Simpan Konfigurasi APK</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- ══ TAB: SYSTEM STATE ══ --}}
    <div id="tab-system" class="tab-content">
        <div class="max-w-3xl">
            <div class="mb-6">
                <h2 class="text-2xl font-display font-semibold" style="color: var(--text-1);">System State</h2>
                <p class="text-sm mt-1" style="color: var(--text-2);">Atur ketersediaan aplikasi dan mode maintenance.</p>
            </div>

            <div class="saas-card">
                <form action="{{ route('developer.maintenance', $secret) }}" method="POST">
                    @csrf
                    @php $mOn = \App\Models\AppSetting::getInstance()->maintenance_mode ?? false; @endphp

                    <div class="p-6 border-b flex items-center justify-between" style="border-color: var(--glass-border);">
                        <div>
                            <h3 class="text-base font-display font-semibold" style="color: var(--text-1);">Mode Maintenance</h3>
                            <p class="text-sm mt-1 max-w-md" style="color: var(--text-2);">Saat aktif, pengguna biasa akan melihat halaman maintenance. Admin dan Developer tetap dapat mengakses sistem.</p>
                        </div>
                        <div class="flex items-center flex-shrink-0 ml-6">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="maintenance_mode" value="0">
                                <input type="checkbox" name="maintenance_mode" value="1" {{ $mOn ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 rounded-full peer transition-all peer-checked:after:translate-x-full peer-checked:bg-[var(--accent)] after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"
                                     style="background-color: rgba(255,255,255,.1);"
                                ></div>
                            </label>
                        </div>
                    </div>

                    <div class="p-6">
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-2);">Pesan Maintenance (Publik)</label>
                        <textarea name="maintenance_message" rows="3" class="saas-input" placeholder="Sistem sedang dalam pemeliharaan...">{{ \App\Models\AppSetting::getInstance()->maintenance_message }}</textarea>
                    </div>

                    <div class="px-6 py-4 flex justify-end rounded-b-[18px]" style="background: rgba(255,255,255,.02); border-top: 1px solid var(--glass-border);">
                        <button type="submit" class="saas-btn">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        #apk-dropzone:hover{
            border-color: rgba(109,94,246,.5) !important;
            background: rgba(109,94,246,.04) !important;
        }
    </style>


    {{-- ══ TAB: RELEASES ══ --}}
    <div id="tab-releases" class="tab-content">
        <div class="mb-6">
            <h2 class="text-2xl font-display font-semibold" style="color: var(--text-1);">Riwayat Rilis</h2>
            <p class="text-sm mt-1" style="color: var(--text-2);">Kelola changelog dan beri tahu pengguna tentang fitur atau perbaikan baru.</p>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- List --}}
            <div class="xl:col-span-2 space-y-4">
                @forelse($updates as $u)
                    @php
                        $typeColor = $u->type === 'feature' ? 'var(--accent-2)' : ($u->type === 'fix' ? 'var(--accent-amber)' : 'var(--accent-cyan)');
                        $typeBg = $u->type === 'feature' ? 'rgba(167,139,250,.1)' : ($u->type === 'fix' ? 'rgba(245,165,36,.1)' : 'rgba(34,211,238,.1)');
                    @endphp
                    <div class="saas-card tilt-card p-6 flex gap-5">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: {{ $typeBg }}; border: 1px solid {{ $typeColor }}40;">
                            <i data-lucide="{{ $u->type === 'feature' ? 'star' : ($u->type === 'fix' ? 'wrench' : 'git-commit') }}" class="w-5 h-5" style="color: {{ $typeColor }};"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1 gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <h4 class="font-semibold text-lg truncate font-display" style="color: var(--text-1);">{{ $u->title }}</h4>
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium mono flex-shrink-0" style="background: rgba(255,255,255,.05); border: 1px solid var(--glass-border); color: var(--text-2);">v{{ $u->version }}</span>
                                </div>
                                <span class="text-xs flex-shrink-0 mono" style="color: var(--text-3);">{{ $u->created_at->format('d M Y') }}</span>
                            </div>
                            <p class="text-sm whitespace-pre-line mt-3" style="color: var(--text-2);">{{ $u->content }}</p>
                        </div>
                        <div class="pl-4 border-l flex flex-col justify-between flex-shrink-0" style="border-color: var(--glass-border);">
                            <form action="{{ route('developer.updates.delete', [$secret, $u->id]) }}" method="POST" onsubmit="return confirm('Hapus log rilis ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1 transition-colors" style="color: var(--text-3);"
                                        onmouseover="this.style.color='var(--accent-rose)'" onmouseout="this.style.color='var(--text-3)'">
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

            {{-- Form --}}
            <div class="xl:col-span-1">
                <div class="saas-card sticky top-24">
                    <div class="px-5 py-4 border-b" style="border-color: var(--glass-border);">
                        <h3 class="text-sm font-display font-semibold" style="color: var(--text-1);">Buat Rilis Baru</h3>
                    </div>
                    <div class="p-5">
                        <form action="{{ route('developer.updates.store', $secret) }}" method="POST" class="space-y-5">
                            @csrf
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color: var(--text-2);">Versi</label>
                                <input type="text" name="version" class="saas-input py-2 mono" required placeholder="1.2.0">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color: var(--text-2);">Tipe</label>
                                <select name="type" class="saas-input py-2">
                                    <option value="feature">Feature</option>
                                    <option value="update">Update</option>
                                    <option value="fix">Fix</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color: var(--text-2);">Judul</label>
                                <input type="text" name="title" class="saas-input py-2" required placeholder="Menambahkan Laporan Baru">
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color: var(--text-2);">Changelog</label>
                                <textarea name="content" rows="4" class="saas-input py-2" required placeholder="- Memperbaiki bug A&#10;- Menambahkan fitur B"></textarea>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="show_modal" value="1" checked class="rounded" style="accent-color: var(--accent);">
                                <span class="text-xs" style="color: var(--text-2);">Tampilkan modal selamat datang ke pengguna</span>
                            </label>
                            <button type="submit" class="saas-btn w-full">Publikasikan Rilis</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection