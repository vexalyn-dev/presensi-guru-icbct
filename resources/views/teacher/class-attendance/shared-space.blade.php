@extends('layouts.teacher')

@section('page-title', 'Presensi Kelas')

@section('content')
<div class="fade-in" x-data="sharedSpaceForm()" x-init="init()">

    <!-- LOADING SCREEN -->
    <div x-show="loading" x-cloak
         class="fixed inset-0 z-[999] flex items-center justify-center bg-white dark:bg-slate-900">
        <div class="text-center px-6">
            <div class="relative w-20 h-20 mx-auto mb-5">
                <div class="absolute inset-0 rounded-full border-4 border-slate-200 dark:border-slate-700"></div>
                <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-navy-800 dark:border-t-gold-400 animate-spin"></div>
                <div class="absolute inset-2 rounded-full border-4 border-transparent border-b-navy-800 dark:border-b-gold-400" style="animation: spinRev 0.8s linear infinite;"></div>
            </div>
            <div class="flex items-center justify-center gap-1.5 mb-3">
                <span class="w-2 h-2 rounded-full bg-navy-800 dark:bg-gold-400 animate-bounce" style="animation-delay:0s"></span>
                <span class="w-2 h-2 rounded-full bg-navy-800 dark:bg-gold-400 animate-bounce" style="animation-delay:0.15s"></span>
                <span class="w-2 h-2 rounded-full bg-navy-800 dark:bg-gold-400 animate-bounce" style="animation-delay:0.3s"></span>
            </div>
            <p class="text-sm font-bold text-navy-800 dark:text-white">Memuat Form Presensi...</p>
            <p class="text-xs text-slate-400 mt-1">{{ $classroom->name ?? 'Ruangan Bersama' }}</p>
        </div>
    </div>

    <!-- FORM PAGE -->
    <div x-show="!loading && !submitted" x-cloak>
        <!-- Header -->
        <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border-b border-slate-100 dark:border-slate-800 px-4 py-3.5 sticky top-0 z-10 mb-4">
            <div class="max-w-lg mx-auto flex items-center gap-3">
                <a href="{{ route('teacher.class-attendance') }}" class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-600 active:scale-95 transition-all flex-shrink-0">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-slate-600 dark:text-slate-300"></i>
                </a>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-navy-800 dark:text-white leading-tight" x-text="mode === 'in' ? 'Presensi Masuk' : 'Presensi Keluar'"></p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 truncate mt-0.5">{{ $classroom->name ?? 'Ruangan Bersama' }}</p>
                </div>
                <!-- Mode badge -->
                <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold flex-shrink-0"
                      :class="mode === 'in' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'">
                    <span class="w-1.5 h-1.5 rounded-full animate-pulse inline-block"
                          :class="mode === 'in' ? 'bg-emerald-500' : 'bg-red-500'"></span>
                    <span x-text="mode === 'in' ? 'Masuk' : 'Keluar'"></span>
                </span>
                <a href="{{ route('teacher.class-attendance') }}" class="w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-400 hover:bg-red-500 hover:text-white active:scale-95 transition-all flex-shrink-0">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            </div>
        </div>

        <!-- Info Banner (jadwal only) -->
        <template x-if="mode === 'in' && scheduleStatus">
            <div class="max-w-lg mx-auto px-4 pt-3">
                <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-medium"
                     :class="scheduleValid ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800' : 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800'">
                    <i data-lucide="circle" x-show="scheduleValid" class="w-3 h-3 flex-shrink-0"></i>
                    <i data-lucide="alert-circle" x-show="!scheduleValid" class="w-3 h-3 flex-shrink-0"></i>
                    <span x-text="scheduleStatus"></span>
                </div>
            </div>
        </template>

        <div class="px-4 py-4 max-w-lg mx-auto space-y-4 bg-slate-50 dark:bg-slate-950">

            {{-- MODE IN --}}
            <template x-if="mode === 'in'">
                <div class="space-y-4 pb-4">

                    <!-- Step indicator -->
                    <div class="flex items-center gap-2 mb-1">
                        <template x-for="(step,i) in [{l:'Kelas',d:!!selectedClass},{l:'Mapel',d:!!selectedSubject},{l:'Jam',d:!!selectedPeriod}]" :key="i">
                            <div class="flex items-center gap-2" :class="i<2?'flex-1':''">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black flex-shrink-0 transition-all duration-300"
                                     :class="step.d?'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900':'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400'">
                                    <template x-if="step.d"><i data-lucide="check" class="w-3 h-3"></i></template>
                                    <span x-show="!step.d" x-text="i+1"></span>
                                </div>
                                <span class="text-[11px] font-semibold transition-colors duration-300"
                                      :class="step.d?'text-navy-800 dark:text-gold-400':'text-slate-400 dark:text-slate-500'"
                                      x-text="step.l"></span>
                                <div x-show="i<2" class="flex-1 h-px transition-colors duration-300"
                                     :class="step.d?'bg-navy-800/30 dark:bg-gold-400/30':'bg-slate-200 dark:bg-slate-700'"></div>
                            </div>
                        </template>
                    </div>
                    <!-- Kelas -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">
                            Kelas <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select x-model="selectedClass" @change="onSelectionChange()"
                                    class="w-full px-4 py-3.5 rounded-xl border-2 transition-all duration-200 text-sm font-semibold appearance-none cursor-pointer active:scale-[.98]"
                                    :class="!selectedClass 
                                        ? 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60 text-slate-400 dark:text-slate-500' 
                                        : (scheduleValid 
                                            ? 'border-navy-300 dark:border-navy-600 bg-navy-50/50 dark:bg-navy-900/10 text-navy-800 dark:text-white' 
                                            : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60 text-slate-800 dark:text-white')">
                                <option value="">Pilih kelas...</option>
                                @foreach($classes as $c)
                                <option value="{{ $c->id }}" @if(old('selected_classroom_id') == $c->id) selected @endif>{{ $c->name }} @if($c->code)({{ $c->code }})@endif</option>
                                @endforeach
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Mata Pelajaran -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">
                            Mata Pelajaran <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select x-model="selectedSubject" @change="onSelectionChange()"
                                    class="w-full px-4 py-3.5 rounded-xl border-2 transition-all duration-200 text-sm font-semibold appearance-none cursor-pointer active:scale-[.98]"
                                    :class="!selectedSubject 
                                        ? 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60 text-slate-400 dark:text-slate-500' 
                                        : (scheduleValid 
                                            ? 'border-navy-300 dark:border-navy-600 bg-navy-50/50 dark:bg-navy-900/10 text-navy-800 dark:text-white' 
                                            : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60 text-slate-800 dark:text-white')">
                                <option value="">Pilih mata pelajaran...</option>
                                @foreach($subjects as $s)
                                <option value="{{ $s->id }}" @if(old('subject_id') == $s->id) selected @endif>{{ $s->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Jam Ke- -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider flex items-center gap-2">
                            Jam Ke- <span class="text-red-500 font-normal">*</span>
                            <span x-show="selectedPeriod" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                                  class="px-2 py-0.5 bg-navy-100 dark:bg-navy-900/40 text-navy-800 dark:text-gold-400 rounded-lg text-[10px] font-black"
                                  x-text="'JP ' + selectedPeriod"></span>
                        </label>
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="jam in [1,2,3,4,5,6,7,8,9,10,11,12]" :key="`jam-${jam}`">
                                <button type="button" @click="selectedPeriod = jam; onSelectionChange()"
                                        class="h-14 flex flex-col items-center justify-center rounded-xl font-bold transition-all duration-150 active:scale-95 touch-manipulation select-none"
                                        :class="(typeof selectedPeriod !== 'undefined' && selectedPeriod == jam)
                                            ? ((typeof scheduleValid !== 'undefined' && scheduleValid) 
                                                ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow-lg shadow-navy-800/25 dark:shadow-gold-400/25 scale-[1.02]' 
                                                : 'bg-slate-400 dark:bg-slate-600 text-white dark:text-slate-200 shadow-lg')
                                            : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-navy-300 dark:hover:border-navy-600 hover:bg-slate-50 dark:hover:bg-slate-700'">
                                    <span class="text-base font-extrabold leading-none" x-text="typeof jam !== 'undefined' ? jam : ''"></span>
                                    <span class="text-[9px] leading-none mt-0.5 opacity-50 font-medium">JP</span>
                                </button>
                            </template>
                        </div>
                    </div>>
                                    <span class="text-lg font-extrabold leading-none" x-text="jam"></span>
                                    <span class="text-[9px] leading-none mt-0.5 opacity-60">JP</span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="pt-3">
                        <button @click="submitForm()" :disabled="!canSubmit || validating"
                                class="w-full py-4 rounded-xl font-bold text-sm flex items-center justify-center gap-2.5 transition-all duration-200 relative overflow-hidden"
                                :class="!canSubmit || validating
                                    ? 'opacity-35 cursor-not-allowed shadow-none translate-y-0 bg-gradient-to-r from-slate-400 to-slate-500 dark:from-slate-500 dark:to-slate-600 text-white'
                                    : 'bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 shadow-lg shadow-navy-800/25 dark:shadow-gold-400/25 hover:shadow-xl hover:shadow-navy-800/30 dark:hover:shadow-gold-400/30 hover:-translate-y-0.5 active:scale-[.98]'">
                            <svg x-show="validating" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <i data-lucide="log-in" x-show="!validating" class="w-4 h-4"></i>
                            <span x-text="validating ? 'Memeriksa Jadwal...' : (!canSubmit ? 'Lengkapi form terlebih dahulu' : (scheduleValid ? 'Simpan Presensi Masuk' : 'Jadwal Tidak Valid'))">
                            </span>
                        </button>
                    </div>
                </div>
            </template>

            {{-- MODE OUT --}}
            <template x-if="mode === 'out'">
                <div class="space-y-3 pb-8">
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Pilih sesi untuk diselesaikan</p>
                    <template x-if="activeSessions.length > 0">
                        <div class="space-y-2">
                            <template x-for="session in activeSessions" :key="session.id">
                                <div class="rounded-2xl border-2 cursor-pointer transition-all active:scale-[.98]"
                                     :class="selectedSession == session.id ? 'border-navy-800 dark:border-gold-400 bg-navy-50 dark:bg-navy-900/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-slate-300'"
                                     @click="selectedSession = session.id">
                                    <div class="flex items-center gap-3 p-4">
                                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 text-xs font-black bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900"
                                             x-text="session.classroom_name.slice(0,3).toUpperCase()"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-navy-800 dark:text-white truncate" x-text="session.classroom_name"></p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate" x-text="session.subject_name + ' · Jam ke-' + session.period"></p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[10px] text-slate-400 flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i><span x-text="'Masuk ' + session.check_in_time"></span></span>
                                                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full" :class="session.duration_minutes >= 30 ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'" x-text="session.duration_minutes + ' mnt'"></span>
                                            </div>
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all"
                                             :class="selectedSession == session.id ? 'border-navy-800 dark:border-gold-400 bg-navy-800 dark:bg-gold-400' : 'border-slate-300 dark:border-slate-500'">
                                            <i x-show="selectedSession == session.id" data-lucide="check" class="w-3 h-3 text-white dark:text-navy-900"></i>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <button @click="submitForm()" :disabled="!selectedSession"
                                    class="w-full py-4 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 transition-all bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 shadow-xl shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-2xl hover:-translate-y-0.5 active:scale-[.98] disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none disabled:translate-y-0 disabled:hover:translate-y-0 mt-3">
                                <i data-lucide="log-out" class="w-5 h-5"></i>
                                Selesaikan Sesi Ini
                            </button>
                        </div>
                    </template>
                    <template x-if="activeSessions.length === 0">
                        <div class="text-center py-16">
                            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4"><i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i></div>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Tidak Ada Sesi Aktif</p>
                            <p class="text-xs text-slate-400 mt-1">Lakukan scan masuk terlebih dahulu</p>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

    <!-- RESULT SCREEN -->
    <div x-show="submitted" x-cloak
         class="fixed inset-0 z-[999] bg-white dark:bg-slate-900 flex items-center justify-center p-6"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        <div class="text-center max-w-xs">
            <div class="w-20 h-20 mx-auto mb-5 rounded-full flex items-center justify-center"
                 :class="(typeof result !== 'undefined' && result && result.success) ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30'">
                <template x-if="typeof result !== 'undefined' && result && result.success">
                    <i data-lucide="check-circle" class="w-10 h-10 text-green-600 dark:text-green-400"></i>
                </template>
                <template x-if="typeof result === 'undefined' || !result || !result.success">
                    <i data-lucide="x-circle" class="w-10 h-10 text-red-600 dark:text-red-400"></i>
                </template>
            </div>
            <h2 class="text-xl font-extrabold text-navy-800 dark:text-white mb-2" 
                x-text="(typeof result !== 'undefined' && result && result.success) ? 'Berhasil!' : 'Gagal'"></h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 whitespace-pre-line" 
               x-text="(typeof result !== 'undefined' && result && result.message) ? result.message : 'Memproses data...'"></p>
            <button @click="window.location.href = '{{ route('teacher.class-attendance') }}'"
                    class="px-8 py-3 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-xl font-bold text-sm hover:opacity-90 transition-all active:scale-95">
                Kembali ke Dashboard
            </button>
        </div>
    </div>

    <script>
        function sharedSpaceForm() {
            return {
                mode: @json($mode),
                loading: true,
                submitted: false,
                result: null,
                validating: false,
                scheduleValid: false,
                scheduleStatus: '',
                selectedClass: @json(request('selected_classroom_id', '')),
                selectedSubject: @json(request('subject_id', '')),
                selectedPeriod: @json(request('period', '')),
                selectedSession: @json(request('attendance_id', '')),
                activeSessions: @json($activeSessions),
                classroomId: @json($classroom?->id ?? ''),

                get canSubmit() {
                    if (this.mode === 'out') return !!this.selectedSession;
                    return this.scheduleValid && !this.validating;
                },

                init() {
                    setTimeout(() => { this.loading = false; }, 800);
                    if (window.lucide) lucide.createIcons();
                    // Auto-validate if pre-filled from URL params
                    if (this.selectedClass && this.selectedSubject && this.selectedPeriod) {
                        this.validateSelection();
                    }
                },

                onSelectionChange() {
                    // Debounce: tunggu user selesai pilih sebelum validasi
                    if (this._debounce) clearTimeout(this._debounce);
                    this._debounce = setTimeout(() => this.validateSelection(), 300);
                },

                async validateSelection() {
                    if (!this.selectedClass || !this.selectedSubject || !this.selectedPeriod) {
                        this.scheduleValid = false;
                        this.scheduleStatus = '';
                        return;
                    }

                    this.validating = true;
                    this.scheduleStatus = 'Memeriksa jadwal...';

                    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    try {
                        const res = await fetch('{{ route("teacher.class-attendance.validate-schedule") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({
                                classroom_id: this.selectedClass,
                                subject_id: this.selectedSubject,
                                period: this.selectedPeriod,
                            }),
                        });
                        const data = await res.json();
                        this.scheduleValid = data.valid;
                        this.scheduleStatus = data.message || (data.valid ? 'Jadwal valid' : 'Jadwal tidak ditemukan');
                    } catch (e) {
                        this.scheduleValid = false;
                        this.scheduleStatus = 'Gagal memverifikasi jadwal.';
                    } finally {
                        this.validating = false;
                    }
                },

                async submitForm() {
                    const payload = {
                        classroom_id: this.classroomId,
                        mode: this.mode,
                    };
                    if (this.mode === 'in') {
                        payload.selected_classroom_id = this.selectedClass;
                        payload.subject_id = this.selectedSubject;
                        payload.period = this.selectedPeriod;
                    } else {
                        payload.attendance_id = this.selectedSession;
                    }

                    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    try {
                        const res = await fetch('{{ route("teacher.class-attendance.save-shared") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify(payload),
                        });
                        const data = await res.json();
                        this.result = data;
                    } catch (e) {
                        this.result = { success: false, message: 'Terjadi kesalahan koneksi.' };
                    }
                    this.submitted = true;
                    setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 50);
                }
            };
        }
    </script>
</div>

<style>
    @keyframes spin { to { transform: rotate(360deg); } }
    @keyframes spinRev { to { transform: rotate(-360deg); } }
    .animate-spin { animation: spin 0.9s linear infinite; }
    [x-cloak] { display: none !important; }
</style>
@endsection
