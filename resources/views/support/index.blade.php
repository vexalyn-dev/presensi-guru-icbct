@extends(activeLayout())
@section('page-title', 'Pusat Bantuan')
@php
    $user = auth()->user();
    $rp = $user->canAccessAdmin() ? 'admin.support' : ($user->isGuruPiket() ? 'piket.support' : 'teacher.support');
@endphp

@section('content')
<div class="space-y-6 fade-in">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i data-lucide="life-buoy" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Pusat Bantuan</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Laporkan masalah atau kirim permintaan ke tim developer</p>
            </div>
        </div>
        <a href="{{ route($rp . '.history') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm w-fit">
            <i data-lucide="clock" class="w-4 h-4"></i>
            Riwayat Laporan
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 flex-shrink-0"></i>
        <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('warning'))
    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 flex items-center gap-3">
        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0"></i>
        <p class="text-sm font-medium text-amber-800 dark:text-amber-300">{{ session('warning') }}</p>
    </div>
    @endif

    {{-- TYPE TABS --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="type-tabs">
        <button type="button" onclick="setType('bug')"
                class="type-btn active-type card p-4 text-left hover:shadow-md hover:-translate-y-0.5 transition-all" data-type="bug">
            <div class="w-10 h-10 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-xl flex items-center justify-center mb-3">
                <i data-lucide="bug" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
            </div>
            <p class="text-sm font-bold text-navy-800 dark:text-white">Laporkan Bug</p>
            <p class="text-[11px] text-slate-500 mt-0.5">Temukan & laporkan masalah</p>
        </button>
        <button type="button" onclick="setType('feature')"
                class="type-btn card p-4 text-left hover:shadow-md hover:-translate-y-0.5 transition-all" data-type="feature">
            <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 rounded-xl flex items-center justify-center mb-3">
                <i data-lucide="lightbulb" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
            </div>
            <p class="text-sm font-bold text-navy-800 dark:text-white">Request Fitur</p>
            <p class="text-[11px] text-slate-500 mt-0.5">Usulkan fitur baru</p>
        </button>
        <button type="button" onclick="setType('maintenance')"
                class="type-btn card p-4 text-left hover:shadow-md hover:-translate-y-0.5 transition-all" data-type="maintenance">
            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl flex items-center justify-center mb-3">
                <i data-lucide="wrench" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
            </div>
            <p class="text-sm font-bold text-navy-800 dark:text-white">Maintenance</p>
            <p class="text-[11px] text-slate-500 mt-0.5">Permintaan pemeliharaan</p>
        </button>
        <button type="button" onclick="setType('question')"
                class="type-btn card p-4 text-left hover:shadow-md hover:-translate-y-0.5 transition-all" data-type="question">
            <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800 rounded-xl flex items-center justify-center mb-3">
                <i data-lucide="help-circle" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
            </div>
            <p class="text-sm font-bold text-navy-800 dark:text-white">Pertanyaan</p>
            <p class="text-[11px] text-slate-500 mt-0.5">Tanya & diskusikan masalah</p>
        </button>
    </div>

    {{-- FORM --}}
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex items-center gap-3">
            <div class="w-9 h-9 bg-navy-800 dark:bg-gold-400 rounded-xl flex items-center justify-center">
                <i id="form-icon" data-lucide="bug" class="w-4 h-4 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h3 id="form-title" class="text-base font-bold text-navy-800 dark:text-white">Laporkan Bug</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Isi form dengan lengkap dan jelas</p>
            </div>
        </div>

        <form action="{{ route($rp . '.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5" id="support-form">
            @csrf
            <input type="hidden" name="type" id="type-input" value="bug">
            <input type="hidden" name="meta_browser"    id="meta_browser">
            <input type="hidden" name="meta_os"         id="meta_os">
            <input type="hidden" name="meta_device"     id="meta_device">
            <input type="hidden" name="meta_resolution" id="meta_resolution">
            <input type="hidden" name="meta_timezone"   id="meta_timezone">
            <input type="hidden" name="meta_language"   id="meta_language">
            <input type="hidden" name="meta_url"        id="meta_url">
            <input type="hidden" name="meta_user_agent" id="meta_user_agent">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Judul <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="type" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                        <input type="text" name="title" id="title-input" required minlength="5" maxlength="200"
                               value="{{ old('title') }}"
                               placeholder="Mis: Tombol simpan tidak berfungsi..."
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
                    </div>
                    @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Prioritas <span class="text-red-500">*</span></label>
                    <div class="relative" id="priority-dropdown">
                        <input type="hidden" name="priority" id="priority-input" value="{{ old('priority') }}">
                        <button type="button" onclick="toggleDropdown('priority-menu')"
                                class="w-full flex items-center justify-between px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm transition-all hover:bg-white dark:hover:bg-slate-700 focus:outline-none">
                            <span id="priority-label" class="flex items-center gap-2.5 text-slate-400 dark:text-slate-400">-- Pilih Prioritas --</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform flex-shrink-0" id="priority-chevron"></i>
                        </button>
                        <div id="priority-menu"
                             class="hidden absolute top-full left-0 right-0 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-xl z-40 overflow-hidden">
                            @foreach([
                                ['value'=>'low',      'label'=>'Rendah',  'dot'=>'bg-green-500',  'bg'=>'bg-green-100 dark:bg-green-900/30',  'text'=>'text-green-700 dark:text-green-400',  'icon'=>'circle-check'],
                                ['value'=>'medium',   'label'=>'Sedang',  'dot'=>'bg-amber-500',  'bg'=>'bg-amber-100 dark:bg-amber-900/30',  'text'=>'text-amber-700 dark:text-amber-400',  'icon'=>'alert-circle'],
                                ['value'=>'high',     'label'=>'Tinggi',  'dot'=>'bg-orange-500', 'bg'=>'bg-orange-100 dark:bg-orange-900/30','text'=>'text-orange-700 dark:text-orange-400','icon'=>'alert-triangle'],
                                ['value'=>'critical', 'label'=>'Kritis',  'dot'=>'bg-red-500',    'bg'=>'bg-red-100 dark:bg-red-900/30',      'text'=>'text-red-700 dark:text-red-400',      'icon'=>'flame'],
                            ] as $opt)
                            <button type="button"
                                    onclick="selectPriority('{{ $opt['value'] }}', '{{ $opt['label'] }}', '{{ $opt['bg'] }}', '{{ $opt['text'] }}', '{{ $opt['icon'] }}')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <div class="w-7 h-7 rounded-lg {{ $opt['bg'] }} flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="{{ $opt['icon'] }}" class="w-3.5 h-3.5 {{ $opt['text'] }}"></i>
                                </div>
                                <span class="{{ $opt['text'] }} font-semibold">{{ $opt['label'] }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @error('priority')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div id="category-field">
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Kategori <span class="text-red-500">*</span></label>
                    <div class="relative" id="category-dropdown">
                        <input type="hidden" name="category" id="category-input" value="{{ old('category') }}">
                        <button type="button" onclick="toggleDropdown('category-menu')"
                                class="w-full flex items-center justify-between px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm transition-all hover:bg-white dark:hover:bg-slate-700 focus:outline-none">
                            <span id="category-label" class="flex items-center gap-2.5 text-slate-400 dark:text-slate-400">-- Pilih Kategori --</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform flex-shrink-0" id="category-chevron"></i>
                        </button>
                        <div id="category-menu"
                             class="hidden absolute top-full left-0 right-0 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-xl z-40 overflow-hidden">
                            @foreach([
                                ['v'=>'UI',        'icon'=>'layout',       'bg'=>'bg-blue-100 dark:bg-blue-900/30',    'c'=>'text-blue-600 dark:text-blue-400'],
                                ['v'=>'Login',     'icon'=>'log-in',       'bg'=>'bg-purple-100 dark:bg-purple-900/30','c'=>'text-purple-600 dark:text-purple-400'],
                                ['v'=>'Presensi',  'icon'=>'scan-line',    'bg'=>'bg-green-100 dark:bg-green-900/30',  'c'=>'text-green-600 dark:text-green-400'],
                                ['v'=>'Database',  'icon'=>'database',     'bg'=>'bg-amber-100 dark:bg-amber-900/30',  'c'=>'text-amber-600 dark:text-amber-400'],
                                ['v'=>'API',       'icon'=>'code-2',       'bg'=>'bg-cyan-100 dark:bg-cyan-900/30',    'c'=>'text-cyan-600 dark:text-cyan-400'],
                                ['v'=>'Performa',  'icon'=>'zap',          'bg'=>'bg-orange-100 dark:bg-orange-900/30','c'=>'text-orange-600 dark:text-orange-400'],
                                ['v'=>'Keamanan',  'icon'=>'shield-alert', 'bg'=>'bg-red-100 dark:bg-red-900/30',      'c'=>'text-red-600 dark:text-red-400'],
                                ['v'=>'Lainnya',   'icon'=>'more-horizontal','bg'=>'bg-slate-100 dark:bg-slate-700',   'c'=>'text-slate-600 dark:text-slate-400'],
                            ] as $cat)
                            <button type="button"
                                    onclick="selectCategory('{{ $cat['v'] }}', '{{ $cat['icon'] }}', '{{ $cat['bg'] }}', '{{ $cat['c'] }}')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors text-slate-700 dark:text-slate-300">
                                <div class="w-7 h-7 rounded-lg {{ $cat['bg'] }} flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="{{ $cat['icon'] }}" class="w-3.5 h-3.5 {{ $cat['c'] }}"></i>
                                </div>
                                {{ $cat['v'] }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @error('category')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Deskripsi <span class="text-red-500">*</span></label>
                <textarea name="description" id="desc-input" required minlength="10" maxlength="5000" rows="5"
                          placeholder="Jelaskan masalah yang terjadi secara detail..."
                          class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all resize-none">{{ old('description') }}</textarea>
                @error('description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Bug extra fields --}}
            <div id="bug-fields" class="space-y-4 border-t border-slate-100 dark:border-slate-800 pt-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Detail Bug</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Langkah Reproduksi</label>
                        <textarea name="steps_to_reproduce" rows="3"
                                  placeholder="1. Buka halaman...&#10;2. Klik tombol..."
                                  class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 resize-none">{{ old('steps_to_reproduce') }}</textarea>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-1">Hasil yang Diharapkan</label>
                            <input type="text" name="expected_result" placeholder="Seharusnya..." value="{{ old('expected_result') }}"
                                   class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-1">Yang Terjadi</label>
                            <input type="text" name="actual_result" placeholder="Yang sebenarnya terjadi..." value="{{ old('actual_result') }}"
                                   class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Modul / Halaman</label>
                        <input type="text" name="affected_module" placeholder="Mis: Presensi Harian" value="{{ old('affected_module') }}"
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Tingkat Dampak</label>
                        <div class="relative" id="impact-dropdown">
                            <input type="hidden" name="impact_level" id="impact-input">
                            <button type="button" onclick="toggleDropdown('impact-menu')"
                                    class="w-full flex items-center justify-between px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm transition-all hover:bg-white dark:hover:bg-slate-700 focus:outline-none">
                                <span id="impact-label" class="flex items-center gap-2.5 text-slate-400">-- Pilih Dampak --</span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform" id="impact-chevron"></i>
                            </button>
                            <div id="impact-menu" class="hidden absolute top-full left-0 right-0 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-xl z-40 overflow-hidden">
                                @foreach([
                                    ['v'=>'Hanya saya',             'icon'=>'user',         'bg'=>'bg-slate-100 dark:bg-slate-700',     'c'=>'text-slate-600 dark:text-slate-400'],
                                    ['v'=>'Beberapa pengguna',      'icon'=>'users',        'bg'=>'bg-amber-100 dark:bg-amber-900/30',  'c'=>'text-amber-600 dark:text-amber-400'],
                                    ['v'=>'Semua pengguna',         'icon'=>'users-round',  'bg'=>'bg-orange-100 dark:bg-orange-900/30','c'=>'text-orange-600 dark:text-orange-400'],
                                    ['v'=>'Seluruh sistem terganggu','icon'=>'alert-octagon','bg'=>'bg-red-100 dark:bg-red-900/30',     'c'=>'text-red-600 dark:text-red-400'],
                                ] as $imp)
                                <button type="button"
                                        onclick="selectImpact('{{ $imp['v'] }}', '{{ $imp['icon'] }}', '{{ $imp['bg'] }}', '{{ $imp['c'] }}')"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors text-slate-700 dark:text-slate-300">
                                    <div class="w-7 h-7 rounded-lg {{ $imp['bg'] }} flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="{{ $imp['icon'] }}" class="w-3.5 h-3.5 {{ $imp['c'] }}"></i>
                                    </div>
                                    {{ $imp['v'] }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Feature fields --}}
            <div id="feature-fields" class="hidden space-y-4 border-t border-slate-100 dark:border-slate-800 pt-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Detail Fitur</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Tujuan Fitur</label>
                        <textarea name="purpose" rows="3" placeholder="Mengapa fitur ini dibutuhkan?"
                                  class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Manfaat</label>
                        <textarea name="benefit" rows="3" placeholder="Apa manfaatnya?"
                                  class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- Maintenance fields --}}
            <div id="maintenance-fields" class="hidden border-t border-slate-100 dark:border-slate-800 pt-4 space-y-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Detail Maintenance</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Jenis Maintenance</label>
                        <div class="relative" x-data="{
                            open: false,
                            selected: '',
                            selectedIcon: 'wrench',
                            selectedBg: '',
                            selectedColor: '',
                            options: [
                                {v:'Update Sistem',      icon:'refresh-cw',     bg:'bg-blue-100',   c:'text-blue-600'},
                                {v:'Perbaikan Database', icon:'database',       bg:'bg-amber-100',  c:'text-amber-600'},
                                {v:'Backup Data',        icon:'hard-drive',     bg:'bg-green-100',  c:'text-green-600'},
                                {v:'Optimasi Performa',  icon:'zap',            bg:'bg-purple-100', c:'text-purple-600'},
                                {v:'Keamanan',           icon:'shield',         bg:'bg-red-100',    c:'text-red-600'},
                                {v:'Lainnya',            icon:'more-horizontal',bg:'bg-slate-100',  c:'text-slate-600'},
                            ],
                            select(opt) {
                                this.selected = opt.v;
                                this.selectedIcon = opt.icon;
                                this.selectedBg = opt.bg;
                                this.selectedColor = opt.c;
                                this.open = false;
                                document.getElementById('maintenance-type-input').value = opt.v;
                                this.$nextTick(() => { if(window.lucide) lucide.createIcons(); });
                            }
                        }" @click.outside="open = false">
                            <input type="hidden" name="maintenance_type" id="maintenance-type-input">

                            <!-- Trigger Button -->
                            <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm transition-all hover:bg-white dark:hover:bg-slate-700 focus:outline-none"
                                    :class="open && 'border-navy-800 dark:border-gold-400'">
                                <span class="flex items-center gap-2.5">
                                    <template x-if="!selected">
                                        <span class="flex items-center gap-2 text-slate-400">
                                            <i data-lucide="wrench" class="w-4 h-4"></i>
                                            -- Pilih Jenis --
                                        </span>
                                    </template>
                                    <template x-if="selected">
                                        <span class="flex items-center gap-2.5">
                                            <span :class="selectedBg + ' w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0'">
                                                <i :data-lucide="selectedIcon" :class="selectedColor + ' w-3.5 h-3.5'"></i>
                                            </span>
                                            <span class="font-semibold text-navy-800 dark:text-white" x-text="selected"></span>
                                        </span>
                                    </template>
                                </span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform duration-200"
                                   :class="open && 'rotate-180'"></i>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                 class="absolute top-full left-0 right-0 mt-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-2xl z-[9999] overflow-hidden"
                                 style="display:none;">
                                <template x-for="opt in options" :key="opt.v">
                                    <button type="button" @click="select(opt)"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                                        <span :class="opt.bg + ' dark:opacity-80 w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform'">
                                            <i :data-lucide="opt.icon" :class="opt.c + ' w-4 h-4'"></i>
                                        </span>
                                        <span class="font-medium text-slate-700 dark:text-slate-200" x-text="opt.v"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Jadwal yang Diinginkan</label>
                        <input type="text" name="preferred_schedule" placeholder="Mis: Sabtu malam 00:00 WIB"
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                    </div>
                </div>
            </div>

            {{-- Lampiran --}}
            <div class="border-t border-slate-100 dark:border-slate-800 pt-5">
                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-3">
                    Lampiran <span class="text-slate-400 font-normal text-xs">(opsional, maks. 5 file)</span>
                </label>
                <div id="dropzone"
                     class="border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl p-6 text-center cursor-pointer hover:border-navy-800 dark:hover:border-gold-400 transition-colors"
                     onclick="document.getElementById('attach-input').click()"
                     ondragover="event.preventDefault()"
                     ondrop="handleDrop(event)">
                    <input type="file" name="attachments[]" id="attach-input" multiple
                           accept=".png,.jpg,.jpeg,.webp,.pdf,.mp4" class="hidden" onchange="handleFiles(this.files)">
                    <i data-lucide="upload-cloud" class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-2"></i>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Seret & lepas atau klik untuk pilih</p>
                    <p class="text-xs text-slate-400 mt-1">PNG, JPG, WEBP, PDF, MP4 • Maks. 10MB per file</p>
                </div>
                <ul id="file-list" class="mt-3 space-y-2"></ul>
            </div>

            {{-- Info Sistem --}}
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/40">
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-3">Info Sistem (Otomatis Terisi)</p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div><span class="text-slate-400">Browser:</span> <span id="show-browser" class="font-medium text-navy-800 dark:text-white ml-1">-</span></div>
                    <div><span class="text-slate-400">OS:</span> <span id="show-os" class="font-medium text-navy-800 dark:text-white ml-1">-</span></div>
                    <div><span class="text-slate-400">Device:</span> <span id="show-device" class="font-medium text-navy-800 dark:text-white ml-1">-</span></div>
                    <div><span class="text-slate-400">IP:</span> <span id="show-ip" class="font-medium text-navy-800 dark:text-white ml-1">{{ request()->ip() }}</span></div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                <p class="text-xs text-slate-400 flex items-center gap-1.5">
                    <i data-lucide="shield" class="w-3.5 h-3.5 text-green-500"></i>
                    Dikirim aman via HTTPS
                </p>
                <button type="button" onclick="handleSubmit()" id="submit-btn"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:opacity-90 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-95">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-5 flex items-center gap-4">
            <div class="w-11 h-11 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="zap" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-navy-800 dark:text-white">Respon Cepat</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Diproses dalam 1×24 jam</p>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-11 h-11 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="shield-check" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-navy-800 dark:text-white">Aman & Terenkripsi</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Data via HTTPS + HMAC</p>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-11 h-11 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="bar-chart-2" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-navy-800 dark:text-white">Tracking Realtime</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Pantau status tiket Anda</p>
            </div>
        </div>
    </div>
</div>


<script>
var typeConfig = {
    bug:         { title: 'Laporkan Bug',          icon: 'bug' },
    feature:     { title: 'Request Fitur',          icon: 'lightbulb' },
    maintenance: { title: 'Permohonan Maintenance', icon: 'wrench' },
    question:    { title: 'Pertanyaan / Bantuan',   icon: 'help-circle' }
};

function setType(t) {
    // Update hidden input
    document.getElementById('type-input').value = t;

    // Update tab active state
    document.querySelectorAll('.type-btn').forEach(function(btn) {
        btn.classList.remove('ring-2','ring-navy-800','dark:ring-gold-400','shadow-lg','scale-[1.02]');
        if (btn.dataset.type === t) {
            btn.classList.add('ring-2','ring-navy-800','shadow-lg');
        }
    });

    // Update form header
    var cfg = typeConfig[t] || typeConfig.bug;
    document.getElementById('form-title').textContent = cfg.title;
    var iconEl = document.getElementById('form-icon');
    iconEl.setAttribute('data-lucide', cfg.icon);
    if (window.lucide) lucide.createIcons();

    // Show/hide extra fields
    ['bug-fields','feature-fields','maintenance-fields'].forEach(function(id) {
        document.getElementById(id).classList.add('hidden');
    });
    var showId = t === 'feature' ? 'feature-fields' : t === 'maintenance' ? 'maintenance-fields' : t === 'bug' ? 'bug-fields' : null;
    if (showId) document.getElementById(showId).classList.remove('hidden');

    // Show/hide category
    var catField = document.getElementById('category-field');
    var catInput = document.getElementById('category-input');
    if (t === 'bug') {
        catField.classList.remove('hidden');
    } else {
        catField.classList.add('hidden');
        if (catInput) catInput.value = '';
    }

    // Update placeholders
    var titles = { bug:'Mis: Tombol simpan tidak berfungsi...', feature:'Mis: Fitur export laporan ke PDF...', maintenance:'Mis: Pembersihan database mingguan...', question:'Mis: Cara mengatur jadwal mengajar?' };
    var descs  = { bug:'Jelaskan bug yang terjadi secara detail...', feature:'Jelaskan fitur yang Anda inginkan...', maintenance:'Jelaskan kebutuhan maintenance...', question:'Tulis pertanyaan Anda di sini...' };
    document.getElementById('title-input').placeholder = titles[t] || 'Judul laporan...';
    document.getElementById('desc-input').placeholder  = descs[t]  || 'Deskripsi...';
}

function handleSubmit() {
    var form = document.getElementById('support-form');

    // Validasi client-side cepat sebelum submit
    var title = document.getElementById('title-input').value.trim();
    var desc  = document.getElementById('desc-input').value.trim();
    var prio  = document.getElementById('priority-input').value;
    if (!title || title.length < 5) { document.getElementById('title-input').focus(); return; }
    if (!desc  || desc.length  < 10){ document.getElementById('desc-input').focus();  return; }
    if (!prio)                       { return; }

    // Tampilkan overlay loading
    showSupportOverlay('loading');

    // Submit form via AJAX supaya bisa tangkap redirect
    var formData = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        redirect: 'follow'
    })
    .then(function(res) {
        var ct = res.headers.get('content-type') || '';
        if (ct.includes('application/json')) {
            return res.json().then(function(data) {
                if (data.success) {
                    showSupportOverlay('success', data.message || 'Laporan berhasil dikirim!', data.redirect || null);
                } else {
                    showSupportOverlay('error', data.message || 'Gagal mengirim laporan.');
                }
            });
        }
        showSupportOverlay('success', 'Laporan berhasil dikirim!', res.url);
    })
    .catch(function() {
        hideSupportOverlay();
        form.submit();
    });
}


// ── SUPPORT SUBMIT OVERLAY
function toggleDropdown(menuId) {
    var menu    = document.getElementById(menuId);
    var isOpen  = !menu.classList.contains('hidden');
    // Tutup semua dropdown dulu
    document.querySelectorAll('[id$="-menu"]').forEach(function(m) {
        m.classList.add('hidden');
        var chevronId = m.id.replace('-menu', '-chevron');
        var ch = document.getElementById(chevronId);
        if (ch) ch.style.transform = 'rotate(0deg)';
    });
    if (!isOpen) {
        // Portal: pindah ke body agar tidak terpotong overflow
        var btnId = 'btn-' + menuId;
        var btn = document.getElementById(btnId);
        if (btn) {
            if (menu.parentElement !== document.body) {
                document.body.appendChild(menu);
            }
            // Pakai requestAnimationFrame supaya browser sempat render posisi button
            requestAnimationFrame(function() {
                var rect = btn.getBoundingClientRect();
                menu.style.position  = 'fixed';
                menu.style.top       = (rect.bottom + 4) + 'px';
                menu.style.left      = rect.left + 'px';
                menu.style.width     = rect.width + 'px';
                menu.style.zIndex    = '9999';
                menu.classList.remove('hidden');
                if (window.lucide) lucide.createIcons();
            });
        } else {
            menu.classList.remove('hidden');
        }
        var chevronId = menuId.replace('-menu', '-chevron');
        var ch = document.getElementById(chevronId);
        if (ch) ch.style.transform = 'rotate(180deg)';
    }
}

// Tutup dropdown saat klik di luar
document.addEventListener('click', function(e) {
    var openMenus = document.querySelectorAll('[id$="-menu"]:not(.hidden)');
    openMenus.forEach(function(menu) {
        var btnId = 'btn-' + menu.id;
        var btn   = document.getElementById(btnId);
        if (btn && !btn.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add('hidden');
            menu.style.pointerEvents = 'none';
            var chevronId = menu.id.replace('-menu', '-chevron');
            var ch = document.getElementById(chevronId);
            if (ch) ch.style.transform = 'rotate(0deg)';
        }
    });
});

function selectOption(key, value, label) {
    document.getElementById(key + '-input').value = value;
    var lbl = document.getElementById(key + '-label');
    if (lbl) { lbl.textContent = label; lbl.classList.remove('text-slate-400','dark:text-slate-400'); lbl.classList.add('text-navy-800','dark:text-white','font-medium'); }
    document.getElementById(key + '-menu').classList.add('hidden');
    var chevronId = key + '-chevron';
    var ch = document.getElementById(chevronId);
    if (ch) ch.style.transform = 'rotate(0deg)';
}


function setLabelWithIcon(labelId, menuId, bg, iconName, iconColor, text) {
    var lbl = document.getElementById(labelId);
    if (!lbl) return;
    lbl.innerHTML = '<div class="w-6 h-6 rounded-lg ' + bg + ' flex items-center justify-center flex-shrink-0"><i data-lucide="' + iconName + '" class="w-3.5 h-3.5 ' + iconColor + '"></i></div><span class="font-semibold text-navy-800 dark:text-white">' + text + '</span>';
    document.getElementById(menuId).classList.add('hidden');
    var chId = menuId.replace('-menu', '-chevron');
    var ch = document.getElementById(chId);
    if (ch) ch.style.transform = 'rotate(0deg)';
    if (window.lucide) lucide.createIcons();
}

function selectPriority(val, label, bg, color, icon) {
    document.getElementById('priority-input').value = val;
    setLabelWithIcon('priority-label', 'priority-menu', bg, icon, color, label);
}

function selectCategory(val, icon, bg, color) {
    document.getElementById('category-input').value = val;
    setLabelWithIcon('category-label', 'category-menu', bg, icon, color, val);
}

function selectImpact(val, icon, bg, color) {
    document.getElementById('impact-input').value = val;
    setLabelWithIcon('impact-label', 'impact-menu', bg, icon, color, val);
}

function selectMaintType(val, icon, bg, color) {
    document.getElementById('maintenance-type-input').value = val;

    // Update label
    var lbl = document.getElementById('maint-type-label');
    if (lbl) {
        lbl.innerHTML =
            '<div class="w-6 h-6 rounded-lg ' + bg + ' flex items-center justify-center flex-shrink-0">' +
            '<i data-lucide="' + icon + '" class="w-3.5 h-3.5 ' + color + '"></i></div>' +
            '<span class="font-semibold text-navy-800 dark:text-white">' + val + '</span>';
    }

    // Tutup dropdown
    closeMaintDropdown();
    if (window.lucide) lucide.createIcons();
}

function openMaintDropdown() {
    var menu = document.getElementById('maint-menu');
    var btn  = document.getElementById('btn-maint-menu');
    var chevron = document.getElementById('maint-chevron');

    if (!menu || !btn) return;

    var isOpen = !menu.classList.contains('hidden');

    // Tutup semua dropdown lain dulu
    document.querySelectorAll('[id$="-menu"]').forEach(function(m) {
        if (m.id !== 'maint-menu') {
            m.classList.add('hidden');
        }
    });

    if (isOpen) {
        closeMaintDropdown();
        return;
    }

    // Posisikan menu tepat di bawah button
    var rect = btn.getBoundingClientRect();
    menu.style.top   = (rect.bottom + window.scrollY + 4) + 'px';
    menu.style.left  = rect.left + 'px';
    menu.style.width = rect.width + 'px';
    menu.style.position = 'fixed';
    menu.style.top   = (rect.bottom + 4) + 'px';

    menu.classList.remove('hidden');
    if (chevron) chevron.style.transform = 'rotate(180deg)';
    if (window.lucide) lucide.createIcons();
}

function closeMaintDropdown() {
    var menu    = document.getElementById('maint-menu');
    var chevron = document.getElementById('maint-chevron');
    if (menu) menu.classList.add('hidden');
    if (chevron) chevron.style.transform = 'rotate(0deg)';
}

// Tutup maint dropdown saat klik di luar
document.addEventListener('click', function(e) {
    var btn  = document.getElementById('btn-maint-menu');
    var menu = document.getElementById('maint-menu');
    if (menu && btn && !btn.contains(e.target) && !menu.contains(e.target)) {
        closeMaintDropdown();
    }
});
document.addEventListener('click', function(e) {
    if (!e.target.closest('[id$="-dropdown"]') && !e.target.closest('[id$="-menu"]')) {
        document.querySelectorAll('[id$="-menu"]').forEach(function(m) {
            m.classList.add('hidden');
        });
        document.querySelectorAll('[id$="-chevron"]').forEach(function(c) {
            c.style.transform = 'rotate(0deg)';
        });
    }
});
document.addEventListener('DOMContentLoaded', function() {
    var ua = navigator.userAgent;
    var browser = 'Unknown';
    if (/Edg\//.test(ua)) browser = 'Microsoft Edge';
    else if (/Chrome\//.test(ua)) browser = 'Google Chrome';
    else if (/Firefox\//.test(ua)) browser = 'Mozilla Firefox';
    else if (/Safari\//.test(ua) && !/Chrome/.test(ua)) browser = 'Safari';

    var os = 'Unknown';
    if (/Windows/.test(ua)) os = 'Windows';
    else if (/Macintosh/.test(ua)) os = 'macOS';
    else if (/Android/.test(ua)) os = 'Android';
    else if (/iPhone|iPad/.test(ua)) os = 'iOS';
    else if (/Linux/.test(ua)) os = 'Linux';

    var device = /Mobile|Android|iPhone/.test(ua) ? 'Mobile' : /Tablet|iPad/.test(ua) ? 'Tablet' : 'Desktop';
    var res    = window.screen.width + 'x' + window.screen.height;

    document.getElementById('meta_browser').value    = browser;
    document.getElementById('meta_os').value         = os;
    document.getElementById('meta_device').value     = device;
    document.getElementById('meta_resolution').value = window.screen.width + 'x' + window.screen.height;
    document.getElementById('meta_timezone').value   = Intl.DateTimeFormat().resolvedOptions().timeZone;
    document.getElementById('meta_language').value   = navigator.language;
    document.getElementById('meta_url').value        = window.location.href;
    document.getElementById('meta_user_agent').value = ua;

    document.getElementById('show-browser').textContent = browser;
    document.getElementById('show-os').textContent      = os;
    document.getElementById('show-device').textContent  = device;
    var showRes = document.getElementById('show-res');
    if (showRes) showRes.textContent = window.screen.width + 'x' + window.screen.height;

    setType('bug');
    if (window.lucide) lucide.createIcons();
});

// ── SUPPORT SUBMIT OVERLAY
var _overlayRedirectUrl = null;

function showSupportOverlay(state, msg, redirectUrl) {
    var ov    = document.getElementById('support-ov');
    var ring  = document.getElementById('sov-ring');
    var dots  = document.getElementById('sov-dots');
    var iconS = document.getElementById('sov-icon-success');
    var iconE = document.getElementById('sov-icon-error');
    var lbl   = document.getElementById('sov-label');
    var sub   = document.getElementById('sov-sublabel');

    // Tampilkan dulu sebelum animasi
    ov.style.display = 'flex';
    requestAnimationFrame(function() {
        ov.classList.add('sov-show');
    });
    ring.style.display  = '';
    dots.style.display  = '';
    iconS.style.display = 'none';
    iconE.style.display = 'none';

    if (state === 'loading') {
        lbl.textContent = 'Mengirim laporan...';
        sub.textContent = 'Mohon tunggu sebentar';
    } else if (state === 'success') {
        _overlayRedirectUrl = redirectUrl || null;
        ring.style.display  = 'none';
        dots.style.display  = 'none';
        iconS.style.display = 'flex';
        lbl.textContent = 'Laporan Terkirim!';
        sub.textContent = 'Tunggu sebentar...';
        if (window.lucide) lucide.createIcons();
        // Setelah 1 detik: sembunyikan overlay, langsung tampilkan modal thanks
        setTimeout(function() {
            ov.classList.remove('sov-show');
            // Tunggu transition selesai baru tampilkan modal
            setTimeout(function() {
                showThanksModal(_overlayRedirectUrl);
            }, 280);
        }, 1000);
    } else if (state === 'error') {
        ring.style.display  = 'none';
        dots.style.display  = 'none';
        iconE.style.display = 'flex';
        lbl.textContent = 'Gagal Terkirim';
        sub.textContent = msg || 'Silakan coba lagi';
        setTimeout(hideSupportOverlay, 2500);
    }

    ov.classList.add('sov-show');
}

function hideSupportOverlay() {
    var ov = document.getElementById('support-ov');
    ov.classList.remove('sov-show');
    // Benar-benar sembunyikan setelah transisi selesai
    setTimeout(function() {
        ov.style.display = 'none';
    }, 280);
}

// Fix dropdown portal cleanup — hapus semua menu yang tertinggal di body
function cleanupPortaledMenus() {
    document.querySelectorAll('[id$="-menu"]').forEach(function(menu) {
        if (menu.parentElement === document.body) {
            menu.classList.add('hidden');
        }
    });
}

function showThanksModal(redirectUrl) {
    var modal = document.getElementById('thanks-modal');
    var box   = document.getElementById('thanks-box');
    var bar   = document.getElementById('thanks-progress');

    // Pindahkan ke body agar terlepas dari stacking context sidebar/layout
    if (modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }

    modal.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:99999;background:rgba(10,15,30,0.72);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);display:flex;align-items:center;justify-content:center;padding:16px;';
        requestAnimationFrame(function() {
            box.style.transform = 'translateY(0) scale(1)';
            box.style.opacity   = '1';
            // Mulai animasi progress bar mengecil ke 0 dalam 4 detik
            if (bar) {
                bar.style.transition = 'none';
                bar.style.width = '100%';
                requestAnimationFrame(function() {
                    bar.style.transition = 'width 4s linear';
                    bar.style.width = '0%';
                });
            }
            if (window.lucide) lucide.createIcons();
        });
    });
    _thanksRedirectTimer = setTimeout(function() {
        closeThanksModal(redirectUrl);
    }, 4000);
}

var _thanksRedirectTimer = null;

function closeThanksModal(redirectUrl) {
    clearTimeout(_thanksRedirectTimer);
    var box = document.getElementById('thanks-box');
    box.style.transform = 'translateY(20px) scale(0.96)';
    box.style.opacity   = '0';
    setTimeout(function() {
        document.getElementById('thanks-modal').classList.add('hidden');
        // Prioritas: redirectUrl argumen → _overlayRedirectUrl global → history.go(-1)
        var dest = redirectUrl || _overlayRedirectUrl || null;
        if (dest) window.location.href = dest;
        // Kalau tidak ada redirect URL sama sekali, biarkan user tetap di halaman
    }, 280);
}
// ─────────────────────────────────────────────────────────────
var selectedFiles = [];
function handleFiles(files) {
    for (var i = 0; i < files.length; i++) {
        if (selectedFiles.length >= 5) { alert('Maksimal 5 file.'); break; }
        if (files[i].size > 10 * 1024 * 1024) { alert(files[i].name + ' terlalu besar (maks 10MB)'); continue; }
        selectedFiles.push(files[i]);
    }
    renderFiles();
}
function handleDrop(e) { e.preventDefault(); handleFiles(e.dataTransfer.files); }
function renderFiles() {
    var list = document.getElementById('file-list');
    list.innerHTML = '';
    selectedFiles.forEach(function(f, i) {
        var li = document.createElement('li');
        li.className = 'flex items-center justify-between px-4 py-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700';
        li.innerHTML = '<div class="flex items-center gap-3 min-w-0"><div class="w-8 h-8 bg-navy-100 dark:bg-navy-800 rounded-lg flex items-center justify-center flex-shrink-0"><i data-lucide="paperclip" class="w-4 h-4 text-navy-600 dark:text-navy-300"></i></div><div class="min-w-0"><p class="text-xs font-semibold text-navy-800 dark:text-white truncate">' + f.name + '</p><p class="text-[10px] text-slate-400">' + (f.size/1024).toFixed(0) + ' KB</p></div></div><button type="button" onclick="removeFile(' + i + ')" class="text-slate-400 hover:text-red-500 transition-colors"><i data-lucide="x" class="w-4 h-4"></i></button>';
        list.appendChild(li);
    });
    var dt = new DataTransfer();
    selectedFiles.forEach(function(f) { dt.items.add(f); });
    document.getElementById('attach-input').files = dt.files;
    if (window.lucide) lucide.createIcons();
}
function removeFile(i) { selectedFiles.splice(i, 1); renderFiles(); }
</script>

<style>
.fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
.type-btn { cursor: pointer; }

/* ── SUPPORT SUBMIT OVERLAY ─────────────────────── */
#support-ov {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(10, 15, 30, 0.82);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    display: none;
    align-items: center; justify-content: center;
    opacity: 0;
    transition: opacity 0.25s ease;
}
#support-ov.sov-show { display: flex; opacity: 1; }

.sov-card {
    background: #fff;
    border-radius: 24px;
    padding: 36px 40px 32px;
    width: 240px;
    text-align: center;
    box-shadow: 0 24px 56px rgba(0,0,0,0.35);
    position: relative;
    overflow: hidden;
}
/* Dark mode card */
.dark .sov-card { background: #1e293b; }

.sov-ring-wrap {
    position: relative;
    width: 72px; height: 72px;
    margin: 0 auto 20px;
}
.sov-ring-outer {
    position: absolute; inset: 0; border-radius: 50%;
    border: 4px solid #E2E8F0;
    border-top-color: #0F172A;
    animation: sovSpin 0.9s linear infinite;
}
.sov-ring-inner {
    position: absolute; top: 8px; left: 8px; right: 8px; bottom: 8px;
    border-radius: 50%;
    border: 4px solid transparent;
    border-bottom-color: #FACC15;
    animation: sovSpinRev 0.7s linear infinite;
}
.sov-dots {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    margin-bottom: 16px;
}
.sov-dots span {
    width: 7px; height: 7px;
    background: #0F172A; border-radius: 50%;
}
.dark .sov-dots span { background: #FACC15; }
.sov-dots span:nth-child(1) { animation: sovBounce 0.6s ease-in-out infinite; }
.sov-dots span:nth-child(2) { animation: sovBounce 0.6s ease-in-out 0.15s infinite; }
.sov-dots span:nth-child(3) { animation: sovBounce 0.6s ease-in-out 0.30s infinite; }

.sov-icon-success, .sov-icon-error {
    align-items: center; justify-content: center;
    margin: 0 auto 20px;
    width: 72px; height: 72px;
}
.sov-label {
    font-size: 0.95rem; font-weight: 700; color: #0F172A; margin-bottom: 4px;
}
.dark .sov-label { color: #f1f5f9; }
.sov-sublabel {
    font-size: 0.78rem; color: #94A3B8; font-weight: 400;
}

@keyframes sovSpin    { to { transform: rotate(360deg); } }
@keyframes sovSpinRev { to { transform: rotate(-360deg); } }
@keyframes sovBounce  { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
@keyframes sovCheckIn {
    0%   { stroke-dashoffset: 60; opacity: 0; }
    100% { stroke-dashoffset: 0;  opacity: 1; }
}
@keyframes sovCircleIn {
    0%   { stroke-dashoffset: 180; }
    100% { stroke-dashoffset: 0; }
}
@keyframes sovScaleIn {
    0%  { transform: scale(0.5); opacity: 0; }
    60% { transform: scale(1.15); }
    100%{ transform: scale(1); opacity: 1; }
}
/* ─────────────────────────────────────────────────────── */
</style>

{{-- Support Submit Overlay --}}
<div id="support-ov">
    <div class="sov-card">
        {{-- Spinner --}}
        <div id="sov-ring" class="sov-ring-wrap">
            <div class="sov-ring-outer"></div>
            <div class="sov-ring-inner"></div>
        </div>

        {{-- Bouncing dots --}}
        <div id="sov-dots" class="sov-dots">
            <span></span><span></span><span></span>
        </div>

        {{-- Success icon --}}
        <div id="sov-icon-success" class="sov-icon-success" style="display:none">
            <svg viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="36" cy="36" r="32"
                    stroke="#22C55E" stroke-width="4"
                    fill="none"
                    stroke-dasharray="180" stroke-dashoffset="180"
                    style="animation: sovCircleIn 0.5s cubic-bezier(0.65,0,0.35,1) forwards"/>
                <path d="M20 37l12 12 20-22"
                    stroke="#22C55E" stroke-width="4.5"
                    stroke-linecap="round" stroke-linejoin="round"
                    fill="none"
                    stroke-dasharray="60" stroke-dashoffset="60"
                    style="animation: sovCheckIn 0.4s ease 0.45s forwards"/>
            </svg>
        </div>

        {{-- Error icon --}}
        <div id="sov-icon-error" class="sov-icon-success" style="display:none">
            <svg viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg"
                 style="animation: sovScaleIn 0.35s ease forwards">
                <circle cx="36" cy="36" r="32" stroke="#EF4444" stroke-width="4" fill="none"/>
                <path d="M24 24l24 24M48 24L24 48"
                    stroke="#EF4444" stroke-width="4.5"
                    stroke-linecap="round"/>
            </svg>
        </div>

        <p class="sov-label" id="sov-label">Mengirim laporan...</p>
        <p class="sov-sublabel" id="sov-sublabel">Mohon tunggu sebentar</p>
    </div>
</div>

{{-- Modal Terima Kasih --}}
<div id="thanks-modal" class="fixed inset-0 z-[9998] hidden" style="background:rgba(10,15,30,0.55);backdrop-filter:blur(6px);">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div id="thanks-box"
             class="bg-white dark:bg-slate-900 w-full max-w-sm rounded-3xl shadow-2xl overflow-hidden border border-slate-100 dark:border-slate-700/60 transition-all duration-300"
             style="transform:translateY(24px) scale(0.96);opacity:0;">

            {{-- Top accent bar --}}
            <div class="h-1.5 w-full bg-gradient-to-r from-green-400 via-emerald-500 to-teal-400"></div>

            <div class="p-8 text-center">
                {{-- Emoji / Icon --}}
                <div class="w-20 h-20 bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/20 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-sm">
                    <span class="text-4xl" role="img" aria-label="Thanks">🙏</span>
                </div>

                <h3 class="text-xl font-extrabold text-navy-800 dark:text-white mb-2 tracking-tight">
                    Makasih udah laporan ya!
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-1">
                    Laporan lo udah kita terima & langsung masuk ke sistem kita.
                </p>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-6">
                    Tim developer bakal segera ngecek dan nindaklanjutin. <span class="text-navy-800 dark:text-slate-200 font-semibold">Stay tuned! 🚀</span>
                </p>

                {{-- Progress bar auto-close --}}
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1 mb-5 overflow-hidden">
                    <div id="thanks-progress" class="h-full bg-gradient-to-r from-green-400 to-emerald-500 rounded-full" style="width:100%;transition:width 4s linear;"></div>
                </div>

                <button onclick="closeThanksModal()"
                        class="w-full py-3.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:opacity-90 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all active:scale-95 shadow-lg shadow-navy-800/20 dark:shadow-gold-400/20">
                    Siap, cek riwayat laporan →
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
