@extends(auth()->user()->isAdmin() ? 'layouts.app' : 'layouts.teacher')
@section('page-title', 'Pusat Bantuan')
@section('content')
<div class="space-y-6 fade-in" x-data="supportCenter()">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i data-lucide="life-buoy" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Pusat Bantuan</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Laporkan masalah atau kirim permintaan ke Tim Vexalyn</p>
            </div>
        </div>
        <a href="{{ route('support.history') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
            <i data-lucide="clock" class="w-4 h-4"></i>
            Riwayat Laporan
        </a>
    </div>

    {{-- FLASH MESSAGES --}}
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

    {{-- TYPE SELECTOR --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @php
        $types = [
            'bug'         => ['icon'=>'bug',         'label'=>'Laporkan Bug',         'sub'=>'Temukan & laporkan masalah',      'bg'=>'bg-red-50 dark:bg-red-900/20',     'border'=>'border-red-200 dark:border-red-800',     'icon_class'=>'text-red-600 dark:text-red-400',    'active_bg'=>'bg-red-600 dark:bg-red-500'],
            'feature'     => ['icon'=>'lightbulb',   'label'=>'Request Fitur',         'sub'=>'Usulkan fitur baru',              'bg'=>'bg-amber-50 dark:bg-amber-900/20', 'border'=>'border-amber-200 dark:border-amber-800', 'icon_class'=>'text-amber-600 dark:text-amber-400','active_bg'=>'bg-amber-500 dark:bg-amber-400'],
            'maintenance' => ['icon'=>'wrench',       'label'=>'Maintenance',           'sub'=>'Permintaan pemeliharaan',         'bg'=>'bg-blue-50 dark:bg-blue-900/20',   'border'=>'border-blue-200 dark:border-blue-800',   'icon_class'=>'text-blue-600 dark:text-blue-400',  'active_bg'=>'bg-blue-600 dark:bg-blue-500'],
            'question'    => ['icon'=>'help-circle',  'label'=>'Pertanyaan',            'sub'=>'Tanya & diskusikan masalah',      'bg'=>'bg-purple-50 dark:bg-purple-900/20','border'=>'border-purple-200 dark:border-purple-800','icon_class'=>'text-purple-600 dark:text-purple-400','active_bg'=>'bg-purple-600 dark:bg-purple-500'],
        ];
        @endphp

        @foreach($types as $key => $t)
        <button type="button" @click="setType('{{ $key }}')"
                :class="activeType === '{{ $key }}' ? 'ring-2 ring-navy-800 dark:ring-gold-400 shadow-lg scale-[1.02]' : 'hover:shadow-md hover:-translate-y-0.5'"
                class="card p-4 text-left transition-all duration-200 cursor-pointer group">
            <div class="w-10 h-10 {{ $t['bg'] }} border {{ $t['border'] }} rounded-xl flex items-center justify-center mb-3 transition-all group-hover:scale-110">
                <i data-lucide="{{ $t['icon'] }}" class="w-5 h-5 {{ $t['icon_class'] }}"></i>
            </div>
            <p class="text-sm font-bold text-navy-800 dark:text-white leading-tight">{{ $t['label'] }}</p>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $t['sub'] }}</p>
        </button>
        @endforeach
    </div>

    {{-- FORM CONTAINER --}}
    <div class="card overflow-hidden">
        {{-- Form header --}}
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex items-center gap-3">
            <div class="w-9 h-9 bg-navy-800 dark:bg-gold-400 rounded-xl flex items-center justify-center">
                <i :data-lucide="formIcon" class="w-4 h-4 text-white dark:text-navy-900" x-effect="if(window.lucide) lucide.createIcons()"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-navy-800 dark:text-white" x-text="formTitle"></h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Isi form dengan lengkap dan jelas</p>
            </div>
        </div>

        <form action="{{ route('support.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5" id="support-form" @submit="onSubmit">
            @csrf
            <input type="hidden" name="type" :value="activeType">

            {{-- Auto-detect metadata --}}
            <input type="hidden" name="meta_browser"    id="meta_browser">
            <input type="hidden" name="meta_os"         id="meta_os">
            <input type="hidden" name="meta_device"     id="meta_device">
            <input type="hidden" name="meta_resolution" id="meta_resolution">
            <input type="hidden" name="meta_timezone"   id="meta_timezone">
            <input type="hidden" name="meta_language"   id="meta_language">
            <input type="hidden" name="meta_url"        id="meta_url">
            <input type="hidden" name="meta_user_agent" id="meta_user_agent">

            {{-- FIELD BERSAMA --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                        Judul <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="type" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                        <input type="text" name="title" required minlength="5" maxlength="200"
                               value="{{ old('title') }}"
                               :placeholder="titlePlaceholder"
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('title') ? 'border-red-400' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
                    </div>
                    @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Prioritas --}}
                <div>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Prioritas <span class="text-red-500">*</span></label>
                    <select name="priority" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('priority') ? 'border-red-400' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
                        <option value="">-- Pilih Prioritas --</option>
                        <option value="low"      {{ old('priority')=='low'      ? 'selected':'' }}>🟢 Rendah</option>
                        <option value="medium"   {{ old('priority')=='medium'   ? 'selected':'' }}>🟡 Sedang</option>
                        <option value="high"     {{ old('priority')=='high'     ? 'selected':'' }}>🟠 Tinggi</option>
                        <option value="critical" {{ old('priority')=='critical' ? 'selected':'' }}>🔴 Kritis</option>
                    </select>
                    @error('priority')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Kategori (bug only) --}}
                <div x-show="activeType === 'bug'" x-transition>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Kategori <span class="text-red-500">*</span></label>
                    <select name="category" :required="activeType === 'bug'"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('category') ? 'border-red-400' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach(['UI','Login','Presensi','Database','API','Performa','Keamanan','Lainnya'] as $cat)
                        <option value="{{ $cat }}" {{ old('category')==$cat ? 'selected':'' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Maintenance type --}}
                <div x-show="activeType === 'maintenance'" x-transition>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Jenis Maintenance</label>
                    <select name="maintenance_type"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach(['Update Sistem','Perbaikan Database','Backup Data','Optimasi Performa','Keamanan','Migrasi Data','Lainnya'] as $m)
                        <option value="{{ $m }}">{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                    Deskripsi <span class="text-red-500">*</span>
                </label>
                <textarea name="description" required minlength="10" maxlength="5000" rows="5"
                          :placeholder="descPlaceholder"
                          class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('description') ? 'border-red-400' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all resize-none">{{ old('description') }}</textarea>
                @error('description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- BUG EXTRA FIELDS --}}
            <div x-show="activeType === 'bug'" x-transition class="space-y-5 border-t border-slate-100 dark:border-slate-800 pt-5">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Detail Bug</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Langkah Reproduksi</label>
                        <textarea name="steps_to_reproduce" rows="4" placeholder="1. Buka halaman...&#10;2. Klik tombol...&#10;3. Terjadi error..."
                                  class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 resize-none">{{ old('steps_to_reproduce') }}</textarea>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Hasil yang Diharapkan</label>
                            <textarea name="expected_result" rows="1" placeholder="Seharusnya..."
                                      class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 resize-none">{{ old('expected_result') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Yang Terjadi</label>
                            <textarea name="actual_result" rows="1" placeholder="Yang sebenarnya terjadi..."
                                      class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 resize-none">{{ old('actual_result') }}</textarea>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Modul / Halaman</label>
                        <input type="text" name="affected_module" placeholder="Mis: Halaman Presensi Harian" value="{{ old('affected_module') }}"
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Tingkat Dampak</label>
                        <select name="impact_level"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                            <option value="">-- Pilih Dampak --</option>
                            <option value="Hanya saya">Hanya saya</option>
                            <option value="Beberapa pengguna">Beberapa pengguna</option>
                            <option value="Semua pengguna">Semua pengguna</option>
                            <option value="Seluruh sistem">Seluruh sistem terganggu</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- FEATURE EXTRA FIELDS --}}
            <div x-show="activeType === 'feature'" x-transition class="space-y-5 border-t border-slate-100 dark:border-slate-800 pt-5">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Detail Fitur</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Tujuan Fitur</label>
                        <textarea name="purpose" rows="3" placeholder="Mengapa fitur ini dibutuhkan?"
                                  class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Manfaat</label>
                        <textarea name="benefit" rows="3" placeholder="Apa manfaat yang didapat?"
                                  class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- MAINTENANCE SCHEDULE --}}
            <div x-show="activeType === 'maintenance'" x-transition class="border-t border-slate-100 dark:border-slate-800 pt-5">
                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Jadwal yang Diinginkan</label>
                <input type="text" name="preferred_schedule" placeholder="Mis: Sabtu/Minggu pagi, 00:00-06:00 WIB"
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
            </div>

            {{-- LAMPIRAN --}}
            <div class="border-t border-slate-100 dark:border-slate-800 pt-5">
                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-3">
                    Lampiran <span class="text-slate-400 font-normal">(opsional, maks. 5 file)</span>
                </label>
                <div id="dropzone"
                     class="relative border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl p-6 text-center cursor-pointer hover:border-navy-800 dark:hover:border-gold-400 transition-colors"
                     onclick="document.getElementById('attachment-input').click()"
                     ondragover="event.preventDefault(); this.classList.add('border-navy-800','dark:border-gold-400')"
                     ondragleave="this.classList.remove('border-navy-800','dark:border-gold-400')"
                     ondrop="handleDrop(event)">
                    <input type="file" name="attachments[]" id="attachment-input" multiple accept=".png,.jpg,.jpeg,.webp,.pdf,.mp4"
                           class="hidden" onchange="handleFiles(this.files)">
                    <i data-lucide="upload-cloud" class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Seret & lepas file di sini</p>
                    <p class="text-xs text-slate-400 mt-1">PNG, JPG, WEBP, PDF, MP4 • Maks. 10MB per file</p>
                </div>
                {{-- Preview list --}}
                <ul id="file-list" class="mt-3 space-y-2"></ul>
            </div>

            {{-- Metadata preview --}}
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/40">
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-3">Info Sistem (otomatis terisi)</p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs text-slate-600 dark:text-slate-400">
                    <div><span class="text-slate-400">Browser:</span> <span id="show-browser" class="font-medium text-navy-800 dark:text-white">—</span></div>
                    <div><span class="text-slate-400">OS:</span> <span id="show-os" class="font-medium text-navy-800 dark:text-white">—</span></div>
                    <div><span class="text-slate-400">Device:</span> <span id="show-device" class="font-medium text-navy-800 dark:text-white">—</span></div>
                    <div><span class="text-slate-400">Resolusi:</span> <span id="show-res" class="font-medium text-navy-800 dark:text-white">—</span></div>
                </div>
            </div>

            {{-- SUBMIT --}}
            <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                <p class="text-xs text-slate-400 flex items-center gap-1.5">
                    <i data-lucide="shield" class="w-3.5 h-3.5 text-green-500"></i>
                    Dikirim aman ke Vexalyn Dev Center via HTTPS
                </p>
                <button type="submit"
                        :disabled="submitting"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:opacity-90 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span x-show="!submitting" class="flex items-center gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        Kirim Laporan
                    </span>
                    <span x-show="submitting" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        Mengirim...
                    </span>
                </button>
            </div>
        </form>
    </div>

    {{-- INFO CARD --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-5 flex items-center gap-4">
            <div class="w-11 h-11 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="zap" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-navy-800 dark:text-white">Respon Cepat</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Tiket diproses dalam 1×24 jam</p>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-11 h-11 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="shield-check" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-navy-800 dark:text-white">Aman & Terenkripsi</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Data dikirim via HTTPS + HMAC</p>
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
function supportCenter() {
    return {
        activeType: '{{ $activeType }}',
        submitting: false,

        get formTitle() {
            const titles = { bug:'Laporkan Bug', feature:'Request Fitur', maintenance:'Permohonan Maintenance', question:'Pertanyaan / Bantuan' };
            return titles[this.activeType] || 'Kirim Laporan';
        },
        get formIcon() {
            const icons = { bug:'bug', feature:'lightbulb', maintenance:'wrench', question:'help-circle' };
            return icons[this.activeType] || 'send';
        },
        get titlePlaceholder() {
            const p = { bug:'Mis: Tombol simpan tidak berfungsi di halaman edit guru', feature:'Mis: Fitur export laporan bulanan ke PDF', maintenance:'Mis: Pembersihan database dan optimasi query', question:'Mis: Cara mengatur jadwal mengajar?' };
            return p[this.activeType] || 'Judul laporan Anda...';
        },
        get descPlaceholder() {
            const p = { bug:'Jelaskan bug yang terjadi secara detail...', feature:'Jelaskan fitur yang Anda inginkan...', maintenance:'Jelaskan kebutuhan maintenance...', question:'Tulis pertanyaan Anda di sini...' };
            return p[this.activeType] || '';
        },
        setType(t) {
            this.activeType = t;
            document.querySelector('[name="type"]').value = t;
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },
        onSubmit() {
            this.submitting = true;
            return true;
        }
    };
}

// Auto-detect metadata
document.addEventListener('DOMContentLoaded', function() {
    var ua = navigator.userAgent;

    // Browser
    var browser = 'Unknown';
    if (/Edg\//.test(ua)) browser = 'Microsoft Edge';
    else if (/Chrome\//.test(ua)) browser = 'Google Chrome';
    else if (/Firefox\//.test(ua)) browser = 'Mozilla Firefox';
    else if (/Safari\//.test(ua) && !/Chrome/.test(ua)) browser = 'Safari';

    // OS
    var os = 'Unknown';
    if (/Windows/.test(ua)) os = 'Windows';
    else if (/Macintosh/.test(ua)) os = 'macOS';
    else if (/Android/.test(ua)) os = 'Android';
    else if (/iPhone|iPad/.test(ua)) os = 'iOS';
    else if (/Linux/.test(ua)) os = 'Linux';

    // Device
    var device = 'Desktop';
    if (/Mobile|Android|iPhone/.test(ua)) device = 'Mobile';
    else if (/Tablet|iPad/.test(ua)) device = 'Tablet';

    var res = window.screen.width + 'x' + window.screen.height;

    // Isi hidden inputs
    document.getElementById('meta_browser').value    = browser;
    document.getElementById('meta_os').value         = os;
    document.getElementById('meta_device').value     = device;
    document.getElementById('meta_resolution').value = res;
    document.getElementById('meta_timezone').value   = Intl.DateTimeFormat().resolvedOptions().timeZone;
    document.getElementById('meta_language').value   = navigator.language;
    document.getElementById('meta_url').value        = window.location.href;
    document.getElementById('meta_user_agent').value = ua;

    // Tampilkan di preview
    document.getElementById('show-browser').textContent = browser;
    document.getElementById('show-os').textContent      = os;
    document.getElementById('show-device').textContent  = device;
    document.getElementById('show-res').textContent     = res;

    if (window.lucide) lucide.createIcons();
});

// File upload handler
var selectedFiles = [];

function handleFiles(files) {
    for (var i = 0; i < files.length; i++) {
        if (selectedFiles.length >= 5) { alert('Maksimal 5 file.'); break; }
        if (files[i].size > 10 * 1024 * 1024) { alert(files[i].name + ' terlalu besar (maks 10MB)'); continue; }
        selectedFiles.push(files[i]);
    }
    renderFileList();
}

function handleDrop(e) {
    e.preventDefault();
    handleFiles(e.dataTransfer.files);
}

function renderFileList() {
    var list = document.getElementById('file-list');
    list.innerHTML = '';
    selectedFiles.forEach(function(f, i) {
        var ext = f.name.split('.').pop().toUpperCase();
        var icon = ['MP4'].includes(ext) ? 'video' : ['PDF'].includes(ext) ? 'file-text' : 'image';
        var li = document.createElement('li');
        li.className = 'flex items-center justify-between px-4 py-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700';
        li.innerHTML = '<div class="flex items-center gap-3 min-w-0"><div class="w-8 h-8 bg-navy-100 dark:bg-navy-900/30 rounded-lg flex items-center justify-center flex-shrink-0"><i data-lucide="' + icon + '" class="w-4 h-4 text-navy-700 dark:text-navy-300"></i></div><div class="min-w-0"><p class="text-xs font-semibold text-navy-800 dark:text-white truncate">' + f.name + '</p><p class="text-[10px] text-slate-400">' + (f.size/1024).toFixed(0) + ' KB • ' + ext + '</p></div></div><button type="button" onclick="removeFile(' + i + ')" class="text-slate-400 hover:text-red-500 transition-colors flex-shrink-0"><i data-lucide="x" class="w-4 h-4"></i></button>';
        list.appendChild(li);
    });
    if (window.lucide) lucide.createIcons();

    // Rebuild DataTransfer untuk input
    var dt = new DataTransfer();
    selectedFiles.forEach(function(f) { dt.items.add(f); });
    document.getElementById('attachment-input').files = dt.files;
}

function removeFile(i) {
    selectedFiles.splice(i, 1);
    renderFileList();
}
</script>

<style>
.fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
</style>
@endsection
