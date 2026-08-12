<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Dashboard — ICB CT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        navy: { 800: '#1E293B', 900: '#0F172A' },
                        gold: { 400: '#FACC15', 500: '#EAB308' },
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .card { background: rgba(30,41,59,0.8); border: 1px solid rgba(255,255,255,0.08); border-radius: 1rem; backdrop-filter: blur(12px); }
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; }
        input[type=file]::-webkit-file-upload-button { display: none; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
        .fade-in { animation: fadeIn 0.4s ease forwards; }
    </style>
</head>
<body class="bg-slate-950 text-white min-h-screen">

    {{-- Top Bar --}}
    <div class="sticky top-0 z-50 bg-slate-900/90 backdrop-blur-md border-b border-white/5 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-violet-600 rounded-lg flex items-center justify-center">
                <i data-lucide="code-2" class="w-4 h-4 text-white"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-white leading-none">Developer Dashboard</p>
                <p class="text-[10px] text-slate-400">ICB CT · Vexalyn Dev</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="badge" style="background:rgba(139,92,246,0.2);color:#C4B5FD;border:1px solid rgba(139,92,246,0.3);">
                <i data-lucide="shield-check" class="w-3 h-3"></i> SECRET ACCESS
            </span>
            <span class="badge" style="background:rgba(34,197,94,0.15);color:#4ade80;border:1px solid rgba(34,197,94,0.3);">
                {{ config('app.env') }}
            </span>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-8 space-y-8 fade-in">

        {{-- Alerts --}}
        @if(session('success'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-green-500/15 border border-green-500/30 text-green-300 text-sm font-medium">
            <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-red-500/15 border border-red-500/30 text-red-300 text-sm font-medium">
            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
            {{ session('error') }}
        </div>
        @endif
        @if($errors->any())
        <div class="p-4 rounded-xl bg-red-500/15 border border-red-500/30 text-red-300 text-sm">
            @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
        </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach([
                ['icon'=>'users','label'=>'Total User','value'=>$stats['total_users'],'color'=>'#818CF8'],
                ['icon'=>'graduation-cap','label'=>'Guru','value'=>$stats['total_teachers'],'color'=>'#34D399'],
                ['icon'=>'shield','label'=>'Operator','value'=>$stats['total_operators'],'color'=>'#FACC15'],
                ['icon'=>'clock','label'=>'Pending Izin','value'=>$stats['pending_leaves'],'color'=>'#F87171'],
            ] as $s)
            <div class="card p-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:{{ $s['color'] }}20;">
                        <i data-lucide="{{ $s['icon'] }}" class="w-4 h-4" style="color:{{ $s['color'] }}"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400">{{ $s['label'] }}</p>
                        <p class="text-xl font-bold text-white">{{ $s['value'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- System Info --}}
        <div class="card p-5">
            <div class="flex items-center gap-2 mb-4 pb-4 border-b border-white/5">
                <i data-lucide="server" class="w-4 h-4 text-violet-400"></i>
                <h2 class="text-sm font-bold text-white">System Info</h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach([
                    ['PHP', $stats['php_version']],
                    ['Laravel', $stats['laravel_version']],
                    ['Environment', $stats['env']],
                    ['Debug Mode', $stats['debug'] ? 'ON' : 'OFF'],
                    ['App URL', $stats['app_url']],
                    ['Server Time', now()->format('d M Y H:i').' WIB'],
                ] as [$k,$v])
                <div class="p-3 rounded-lg bg-slate-800/60">
                    <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-1">{{ $k }}</p>
                    <p class="text-xs font-semibold text-white truncate">{{ $v }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ═══ MAINTENANCE MODE ═══ --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-white/5 flex items-center gap-3">
                <div class="w-9 h-9 bg-amber-500/20 rounded-lg flex items-center justify-center">
                    <i data-lucide="construction" class="w-5 h-5 text-amber-400"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-white">Mode Maintenance</h2>
                    <p class="text-[10px] text-slate-400">Aktifkan untuk menampilkan halaman maintenance ke user</p>
                </div>
                @php $maintenanceOn = \App\Models\AppSetting::getInstance()->maintenance_mode ?? false; @endphp
                <span class="ml-auto badge {{ $maintenanceOn ? 'bg-red-500/20 text-red-300 border border-red-500/30' : 'bg-green-500/15 text-green-300 border border-green-500/30' }}">
                    {{ $maintenanceOn ? '● AKTIF' : '○ NONAKTIF' }}
                </span>
            </div>
            <form action="{{ route('developer.maintenance', $secret) }}" method="POST" class="p-5 space-y-4">
                @csrf
                <div class="flex items-center justify-between p-4 rounded-xl bg-slate-800/60">
                    <div>
                        <p class="text-sm font-semibold text-white">Status Maintenance</p>
                        <p class="text-xs text-slate-400">Admin/Operator tetap bisa akses</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="maintenance_mode" value="0">
                        <input type="checkbox" name="maintenance_mode" value="1" class="sr-only peer"
                               {{ $maintenanceOn ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                    </label>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Pesan Maintenance</label>
                    <textarea name="maintenance_message" rows="2" placeholder="Sistem sedang dalam pemeliharaan..."
                              class="w-full px-3 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-sm text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none resize-none">{{ \App\Models\AppSetting::getInstance()->maintenance_message }}</textarea>
                </div>
                <button type="submit" class="flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-900 rounded-xl text-sm font-bold transition-all">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Status Maintenance
                </button>
            </form>
        </div>

        {{-- ═══ APK MANAGEMENT ═══ --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-white/5 flex items-center gap-3">
                <div class="w-9 h-9 bg-violet-500/20 rounded-lg flex items-center justify-center">
                    <i data-lucide="smartphone" class="w-5 h-5 text-violet-400"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-white">Manajemen APK</h2>
                    <p class="text-[10px] text-slate-400">Upload & kelola APK mobile ICB CT</p>
                </div>
            </div>
            <div class="p-5 space-y-5">

                {{-- Status APK --}}
                @if($appSetting?->apk_file)
                <div class="flex items-center gap-4 p-4 rounded-xl bg-green-500/10 border border-green-500/25">
                    <div class="w-11 h-11 bg-green-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i data-lucide="package-check" class="w-5 h-5 text-green-400"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-green-300">APK Terpasang</p>
                        <p class="text-xs text-green-400/80 truncate">
                            {{ $appSetting->apk_name ?? 'ICB CT Presensi' }} ·
                            {{ $appSetting->apk_version_label ?? 'v1.0.0' }} ·
                            {{ $appSetting->apk_size_human ?? '-' }}
                        </p>
                        @if($appSetting->apk_uploaded_at)
                        <p class="text-[10px] text-green-500/70 mt-0.5">Diupload {{ $appSetting->apk_uploaded_at->diffForHumans() }}</p>
                        @endif
                    </div>
                    <form action="{{ route('developer.apk.delete', $secret) }}" method="POST" onsubmit="return confirm('Hapus APK ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-500/20 hover:bg-red-500/30 text-red-300 border border-red-500/30 transition-colors">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                        </button>
                    </form>
                </div>
                @else
                <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-800/60 border border-slate-700/50">
                    <i data-lucide="package-x" class="w-5 h-5 text-slate-500 flex-shrink-0"></i>
                    <p class="text-sm text-slate-400">Belum ada APK yang diupload.</p>
                </div>
                @endif

                {{-- Upload Form --}}
                <form action="{{ route('developer.apk', $secret) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    {{-- Drop Zone --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-2">
                            Upload File APK
                            <span class="text-[10px] font-normal text-slate-500 ml-1">(max 100MB · .apk) — metadata auto-detect</span>
                        </label>
                        <div class="relative border-2 border-dashed border-slate-700 hover:border-violet-500/50 rounded-xl p-6 text-center transition-colors cursor-pointer"
                             x-data="{ filename: '' }" x-ignore
                             id="apkDropZone">
                            <input type="file" name="apk_file" accept=".apk" id="apkFileInput"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                   onchange="handleApkFile(this)">
                            <i data-lucide="upload-cloud" class="w-10 h-10 mx-auto text-slate-500 mb-2"></i>
                            <p class="text-sm font-semibold text-slate-400" id="apkFileName">Drag & drop atau klik untuk pilih APK</p>
                            <p class="text-xs text-slate-600 mt-1">Format: .apk · Maks 100MB</p>
                        </div>
                    </div>

                    {{-- Fields --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                                Nama Aplikasi <span class="text-[10px] font-normal text-slate-500">auto dari APK</span>
                            </label>
                            <div class="relative">
                                <i data-lucide="type" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-500"></i>
                                <input type="text" name="apk_name" id="apkNameInput"
                                       value="{{ old('apk_name', $appSetting?->apk_name ?? 'ICB CT Presensi') }}"
                                       placeholder="ICB CT Presensi"
                                       class="w-full pl-8 pr-3 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-sm text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                                Versi <span class="text-[10px] font-normal text-slate-500">auto dari APK</span>
                            </label>
                            <div class="relative">
                                <i data-lucide="tag" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-500"></i>
                                <input type="text" name="apk_version" id="apkVersionInput"
                                       value="{{ old('apk_version', $appSetting?->apk_version ?? '1.0.0') }}"
                                       placeholder="1.0.0"
                                       class="w-full pl-8 pr-3 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-sm text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                                Min. Android <span class="text-[10px] font-normal text-slate-500">auto dari APK</span>
                            </label>
                            <div class="relative">
                                <i data-lucide="smartphone" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-500"></i>
                                <input type="text" name="apk_min_android" id="apkAndroidInput"
                                       value="{{ old('apk_min_android', $appSetting?->apk_min_android ?? 'Android 8.0+') }}"
                                       placeholder="Android 8.0+"
                                       class="w-full pl-8 pr-3 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-sm text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Changelog</label>
                            <div class="relative">
                                <i data-lucide="file-text" class="absolute left-3 top-3 w-3.5 h-3.5 text-slate-500"></i>
                                <textarea name="apk_changelog" rows="1" placeholder="Perubahan di versi ini..."
                                          class="w-full pl-8 pr-3 py-2.5 rounded-xl bg-slate-800 border border-slate-700 text-sm text-white focus:ring-2 focus:ring-violet-500 focus:border-transparent outline-none resize-none">{{ old('apk_changelog', $appSetting?->apk_changelog) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2 border-t border-white/5">
                        <button type="submit" class="flex items-center gap-2 px-5 py-2.5 bg-violet-600 hover:bg-violet-500 text-white rounded-xl text-sm font-bold transition-all shadow-lg">
                            <i data-lucide="save" class="w-4 h-4"></i> Simpan APK
                        </button>
                        @if($appSetting?->apk_url)
                        <a href="{{ $appSetting->apk_url }}" target="_blank"
                           class="flex items-center gap-2 px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-xl text-sm font-semibold transition-all">
                            <i data-lucide="download" class="w-4 h-4"></i> Download APK
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- ═══ QUICK ACTIONS ═══ --}}
        <div class="card p-5">
            <div class="flex items-center gap-2 mb-4 pb-4 border-b border-white/5">
                <i data-lucide="zap" class="w-4 h-4 text-gold-400"></i>
                <h2 class="text-sm font-bold text-white">Quick Actions</h2>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('developer.clear-cache', $secret) }}"
                   onclick="return confirm('Clear semua cache?')"
                   class="flex items-center gap-2 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-sm text-white rounded-xl font-semibold transition-all">
                    <i data-lucide="refresh-cw" class="w-4 h-4 text-teal-400"></i> Clear Cache
                </a>
                <a href="{{ url('/run-migrate-secret?key=vexalyn19052009') }}" target="_blank"
                   class="flex items-center gap-2 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-sm text-white rounded-xl font-semibold transition-all">
                    <i data-lucide="database" class="w-4 h-4 text-blue-400"></i> Run Migrate
                </a>
                <a href="{{ url('/dashboard') }}"
                   class="flex items-center gap-2 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-sm text-white rounded-xl font-semibold transition-all">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-purple-400"></i> Ke Dashboard
                </a>
                <a href="{{ url('/settings') }}"
                   class="flex items-center gap-2 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-sm text-white rounded-xl font-semibold transition-all">
                    <i data-lucide="settings" class="w-4 h-4 text-slate-400"></i> Settings
                </a>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center py-4">
            <p class="text-[11px] text-slate-600">
                Developer Dashboard · ICB CT Presensi Guru ·
                <span class="text-violet-500">Vexalyn Dev</span> ·
                {{ now()->format('Y') }}
            </p>
        </div>

    </div>

    <script>
        // Init Lucide icons
        lucide.createIcons();

        // Auto-fill dari filename APK
        function handleApkFile(input) {
            const file = input.files[0];
            if (!file) return;

            document.getElementById('apkFileName').textContent = file.name;
            document.getElementById('apkDropZone').style.borderColor = '#7C3AED';

            const base = file.name.replace(/\.apk$/i, '');
            const vMatch = base.match(/[vV]?(\d+\.\d+(?:\.\d+)?(?:\.\d+)?)/);

            if (vMatch) {
                document.getElementById('apkVersionInput').value = vMatch[1];
                const cleanName = base.replace(/[-_\s]*[vV]?\d+\.\d+(?:\.\d+)?(?:\.\d+)?[-_\s]*/g, '')
                                      .replace(/[-_]/g, ' ').trim();
                if (cleanName) document.getElementById('apkNameInput').value = cleanName;
            } else {
                const cleanName = base.replace(/[-_]/g, ' ').trim();
                if (cleanName) document.getElementById('apkNameInput').value = cleanName;
            }
        }

        // Toggle checkbox maintenance (fix untuk hidden input)
        document.querySelectorAll('input[type=checkbox][name=maintenance_mode]').forEach(cb => {
            cb.addEventListener('change', function() {
                this.previousElementSibling.value = this.checked ? '1' : '0';
            });
        });
    </script>
</body>
</html>
