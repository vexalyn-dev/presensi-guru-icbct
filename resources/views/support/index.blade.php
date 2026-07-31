@extends(auth()->user()->isAdmin() ? 'layouts.app' : 'layouts.teacher')
@section('page-title', 'Pusat Bantuan')
@php $rp = auth()->user()->isAdmin() ? 'admin.support' : 'teacher.support'; @endphp

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
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Laporkan masalah atau kirim permintaan ke Vexalyn Dev Center</p>
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
                            <span id="priority-label" class="text-slate-400 dark:text-slate-400">-- Pilih Prioritas --</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform" id="priority-chevron"></i>
                        </button>
                        <div id="priority-menu"
                             class="hidden absolute top-full left-0 right-0 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-xl z-40 overflow-hidden">
                            @foreach([
                                ['value'=>'low',      'label'=>'Rendah',  'dot'=>'bg-green-500'],
                                ['value'=>'medium',   'label'=>'Sedang',  'dot'=>'bg-amber-500'],
                                ['value'=>'high',     'label'=>'Tinggi',  'dot'=>'bg-orange-500'],
                                ['value'=>'critical', 'label'=>'Kritis',  'dot'=>'bg-red-500'],
                            ] as $opt)
                            <button type="button"
                                    onclick="selectOption('priority', '{{ $opt['value'] }}', '{{ $opt['label'] }}')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-left hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                <span class="w-2.5 h-2.5 rounded-full {{ $opt['dot'] }} flex-shrink-0"></span>
                                {{ $opt['label'] }}
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
                            <span id="category-label" class="text-slate-400 dark:text-slate-400">-- Pilih Kategori --</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform" id="category-chevron"></i>
                        </button>
                        <div id="category-menu"
                             class="hidden absolute top-full left-0 right-0 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-xl z-40 overflow-hidden">
                            @foreach(['UI','Login','Presensi','Database','API','Performa','Keamanan','Lainnya'] as $cat)
                            <button type="button"
                                    onclick="selectOption('category', '{{ $cat }}', '{{ $cat }}')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-left hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                <i data-lucide="tag" class="w-3.5 h-3.5 text-slate-400 flex-shrink-0"></i>
                                {{ $cat }}
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
                        <select name="impact_level" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                            <option value="">-- Pilih Dampak --</option>
                            <option>Hanya saya</option>
                            <option>Beberapa pengguna</option>
                            <option>Semua pengguna</option>
                            <option>Seluruh sistem terganggu</option>
                        </select>
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
                        <div class="relative">
                            <input type="hidden" name="maintenance_type" id="maintenance-type-input">
                            <button type="button" onclick="toggleDropdown('maint-menu')"
                                    class="w-full flex items-center justify-between px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm transition-all hover:bg-white dark:hover:bg-slate-700 focus:outline-none">
                                <span id="maint-type-label" class="text-slate-400">-- Pilih Jenis --</span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400" id="maint-chevron"></i>
                            </button>
                            <div id="maint-menu" class="hidden absolute top-full left-0 right-0 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-xl z-40 overflow-hidden">
                                @foreach(['Update Sistem','Perbaikan Database','Backup Data','Optimasi Performa','Keamanan','Lainnya'] as $m)
                                <button type="button"
                                        onclick="selectMaintType('{{ $m }}')"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-left hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                    <i data-lucide="wrench" class="w-3.5 h-3.5 text-slate-400 flex-shrink-0"></i>
                                    {{ $m }}
                                </button>
                                @endforeach
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
                    <div><span class="text-slate-400">Browser:</span> <span id="show-browser" class="font-medium text-navy-800 dark:text-white ml-1">—</span></div>
                    <div><span class="text-slate-400">OS:</span> <span id="show-os" class="font-medium text-navy-800 dark:text-white ml-1">—</span></div>
                    <div><span class="text-slate-400">Device:</span> <span id="show-device" class="font-medium text-navy-800 dark:text-white ml-1">—</span></div>
                    <div><span class="text-slate-400">Resolusi:</span> <span id="show-res" class="font-medium text-navy-800 dark:text-white ml-1">—</span></div>
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

{{-- Modal Dalam Pengembangan --}}
<div id="dev-modal" class="fixed inset-0 z-[9999] hidden" style="background:rgba(15,23,42,0.6);backdrop-filter:blur(8px);">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div id="dev-modal-box"
             class="bg-white dark:bg-slate-900 w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden border border-slate-200/60 dark:border-slate-700/60 transition-all duration-300"
             style="transform:translateY(30px) scale(0.96);opacity:0;">
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center mx-auto mb-5">
                    <i data-lucide="construction" class="w-8 h-8 text-amber-600 dark:text-amber-400"></i>
                </div>
                <h3 class="text-lg font-extrabold text-navy-800 dark:text-white mb-2">Fitur Dalam Pengembangan</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-6">
                    Pusat Bantuan sedang dalam proses integrasi dengan Vexalyn Dev Center dan akan segera hadir sepenuhnya.<br><br>
                    <span class="font-semibold text-navy-800 dark:text-slate-300">Terima kasih atas kesabaran Anda! 🙏</span>
                </p>
                <button onclick="closeDevModal()"
                        class="w-full py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:opacity-90 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all active:scale-95 shadow-lg">
                    Oke, Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var SUPPORT_ENABLED = {{ config('vexalyn.enabled') ? 'true' : 'false' }};

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
    if (!SUPPORT_ENABLED) { openDevModal(); return; }
    document.getElementById('support-form').submit();
}

function openDevModal() {
    var modal = document.getElementById('dev-modal');
    var box   = document.getElementById('dev-modal-box');
    modal.classList.remove('hidden');
    requestAnimationFrame(function() {
        requestAnimationFrame(function() {
            box.style.transform = 'translateY(0) scale(1)';
            box.style.opacity   = '1';
            if (window.lucide) lucide.createIcons();
        });
    });
}

function closeDevModal() {
    var box = document.getElementById('dev-modal-box');
    box.style.transform = 'translateY(30px) scale(0.96)';
    box.style.opacity   = '0';
    setTimeout(function() { document.getElementById('dev-modal').classList.add('hidden'); }, 300);
}

document.getElementById('dev-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDevModal();
});

// Dropdown helpers
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
        menu.classList.remove('hidden');
        var chevronId = menuId.replace('-menu', '-chevron');
        var ch = document.getElementById(chevronId);
        if (ch) ch.style.transform = 'rotate(180deg)';
    }
}

function selectOption(key, value, label) {
    document.getElementById(key + '-input').value = value;
    var lbl = document.getElementById(key + '-label');
    if (lbl) { lbl.textContent = label; lbl.classList.remove('text-slate-400','dark:text-slate-400'); lbl.classList.add('text-navy-800','dark:text-white','font-medium'); }
    document.getElementById(key + '-menu').classList.add('hidden');
    var chevronId = key + '-chevron';
    var ch = document.getElementById(chevronId);
    if (ch) ch.style.transform = 'rotate(0deg)';
}

function selectMaintType(val) {
    document.getElementById('maintenance-type-input').value = val;
    var lbl = document.getElementById('maint-type-label');
    if (lbl) { lbl.textContent = val; lbl.classList.remove('text-slate-400'); lbl.classList.add('text-navy-800','dark:text-white','font-medium'); }
    document.getElementById('maint-menu').classList.add('hidden');
    var ch = document.getElementById('maint-chevron');
    if (ch) ch.style.transform = 'rotate(0deg)';
}

// Tutup dropdown saat klik di luar
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
    document.getElementById('meta_resolution').value = res;
    document.getElementById('meta_timezone').value   = Intl.DateTimeFormat().resolvedOptions().timeZone;
    document.getElementById('meta_language').value   = navigator.language;
    document.getElementById('meta_url').value        = window.location.href;
    document.getElementById('meta_user_agent').value = ua;

    document.getElementById('show-browser').textContent = browser;
    document.getElementById('show-os').textContent      = os;
    document.getElementById('show-device').textContent  = device;
    document.getElementById('show-res').textContent     = res;

    // Init default active tab
    setType('bug');
    if (window.lucide) lucide.createIcons();
});

// File upload
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
</style>
@endsection
