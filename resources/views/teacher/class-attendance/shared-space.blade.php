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
        <div class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-4 py-3.5 sticky top-0 z-10">
            <div class="max-w-lg mx-auto flex items-center gap-3">
                <a href="{{ route('teacher.class-attendance') }}" class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors flex-shrink-0">
                    <i data-lucide="arrow-left" class="w-5 h-5 text-slate-600 dark:text-slate-300"></i>
                </a>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-navy-800 dark:text-white" x-text="mode === 'in' ? 'Presensi Masuk' : 'Presensi Keluar'"></p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $classroom->name ?? 'Ruangan Bersama' }}</p>
                </div>
                <a href="{{ route('teacher.class-attendance') }}" class="w-9 h-9 rounded-xl bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white flex items-center justify-center transition-all flex-shrink-0">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            </div>
        </div>

        <div class="px-4 py-6 max-w-lg mx-auto space-y-5">

            {{-- MODE IN --}}
            <template x-if="mode === 'in'">
                <div class="space-y-5">
                    <!-- Kelas -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">Kelas <span class="text-red-500">*</span></label>
                        <select x-model="selectedClass"
                                class="w-full px-4 py-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-semibold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-400 focus:border-navy-800 dark:focus:ring-gold-400 appearance-none cursor-pointer">
                            <option value="">Pilih kelas...</option>
                            @foreach($classes as $c)
                            <option value="{{ $c->id }}" @if(old('selected_classroom_id') == $c->id) selected @endif>{{ $c->name }} @if($c->code)({{ $c->code }})@endif</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mata Pelajaran -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">Mata Pelajaran <span class="text-red-500">*</span></label>
                        <select x-model="selectedSubject"
                                class="w-full px-4 py-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-semibold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-400 focus:border-navy-800 dark:focus:ring-gold-400 appearance-none cursor-pointer">
                            <option value="">Pilih mata pelajaran...</option>
                            @foreach($subjects as $s)
                            <option value="{{ $s->id }}" @if(old('subject_id') == $s->id) selected @endif>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jam Ke- -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">
                            Jam Ke- <span class="text-red-500">*</span>
                            <span x-show="selectedPeriod" class="ml-1.5 px-2.5 py-0.5 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400 rounded-full text-xs font-bold" x-text="'JP '+selectedPeriod"></span>
                        </label>
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="jam in [1,2,3,4,5,6,7,8,9,10,11,12]" :key="jam">
                                <button type="button" @click="selectedPeriod = jam"
                                        class="h-14 flex flex-col items-center justify-center rounded-2xl font-bold transition-all active:scale-95 touch-manipulation"
                                        :class="selectedPeriod == jam ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow-lg' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'">
                                    <span class="text-lg font-extrabold leading-none" x-text="jam"></span>
                                    <span class="text-[9px] leading-none mt-0.5 opacity-60">JP</span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="pt-2 pb-8">
                        <button @click="submitForm()" :disabled="!canSubmit"
                                class="w-full py-4 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 transition-all bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 shadow-xl shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-2xl hover:-translate-y-0.5 active:scale-[.98] disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none disabled:translate-y-0 disabled:hover:translate-y-0">
                            <i data-lucide="log-in" class="w-5 h-5"></i>
                            Simpan Presensi Masuk
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
                 :class="result?.success ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30'">
                <i data-lucide="check-circle" x-show="result?.success" class="w-10 h-10 text-green-600 dark:text-green-400"></i>
                <i data-lucide="x-circle" x-show="!result?.success" class="w-10 h-10 text-red-600 dark:text-red-400"></i>
            </div>
            <h2 class="text-xl font-extrabold text-navy-800 dark:text-white mb-2" x-text="result?.success ? 'Berhasil!' : 'Gagal'"></h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6" x-text="result?.message ?? ''"></p>
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
                selectedClass: @json(request('selected_classroom_id', '')),
                selectedSubject: @json(request('subject_id', '')),
                selectedPeriod: @json(request('period', '')),
                selectedSession: @json(request('attendance_id', '')),
                activeSessions: @json($activeSessions),
                classroomId: @json($classroom?->id ?? ''),

                get canSubmit() {
                    if (this.mode === 'out') return !!this.selectedSession;
                    return !!(this.selectedClass && this.selectedSubject && this.selectedPeriod);
                },

                init() {
                    setTimeout(() => { this.loading = false; }, 1000);
                    if (window.lucide) lucide.createIcons();
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
