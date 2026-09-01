@extends('layouts.teacher')

@section('page-title', 'Presensi Kelas')



@section('content')
    <div class="fade-in space-y-6" x-data="classAttendance()">

        <!-- Header -->
        <div class="flex items-center gap-3 sm:gap-4">
            <div
                class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white dark:text-navy-900" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5zM13.5 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5z" />
                </svg>
            </div>
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-navy-800 dark:text-white truncate">Presensi Kelas</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 truncate">
                    {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
        </div>

        <!-- Progress Stats -->
        <div class="grid grid-cols-3 gap-2 sm:gap-4">
            <div class="card p-3 sm:p-4">
                <p class="text-[10px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 truncate">Total Kelas</p>
                <p class="text-xl sm:text-2xl font-bold text-navy-800 dark:text-white mt-0.5" id="stat-total-classes">{{ $totalClasses }}</p>
            </div>
            <div class="card p-3 sm:p-4">
                <p class="text-[10px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 truncate">Berlangsung</p>
                <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mt-0.5" id="stat-in-progress">{{ $inProgressClasses }}
                </p>
            </div>
            <div class="card p-3 sm:p-4">
                <p class="text-[10px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 truncate">Selesai</p>
                <p class="text-xl sm:text-2xl font-bold text-green-600 dark:text-green-400 mt-0.5" id="stat-completed">{{ $completedClasses }}
                </p>
            </div>
        </div>

        <!-- Grace Period Warning Banner -->
        <div x-show="showWarning"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="card p-4 bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-300 dark:border-amber-700">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-amber-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-amber-800 dark:text-amber-300 text-sm">Perhatian</p>
                    <p class="text-xs text-amber-700 dark:text-amber-400 mt-1" x-text="warningMessage"></p>
                    <div x-show="remainingSeconds > 0" class="mt-3">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2 bg-amber-200 dark:bg-amber-900/30 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-500 transition-all duration-1000 rounded-full"
                                     :style="`width: ${Math.max(0, (remainingSeconds / (gracePeriod * 60)) * 100)}%`"></div>
                            </div>
                            <span class="text-xs font-bold text-amber-700 dark:text-amber-400 tabular-nums"
                                  x-text="formatTime(remainingSeconds)"></span>
                        </div>
                    </div>
                </div>
                <button @click="showWarning = false; if(warningTimer) clearInterval(warningTimer)"
                        class="p-1 hover:bg-amber-100 dark:hover:bg-amber-900/30 rounded-lg transition-colors flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Early Scan Warning Banner (purple) -->
        <div x-show="showWarning && remainingSeconds > 0 && warningMessage === 'Tunggu hingga waktu scan tiba...'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="card p-4 bg-purple-50 dark:bg-purple-900/20 border-2 border-purple-300 dark:border-purple-700">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full bg-purple-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-purple-800 dark:text-purple-300 text-sm">Terlalu Awal</p>
                    <p class="text-xs text-purple-700 dark:text-purple-400 mt-0.5" x-text="resultMessage"></p>
                    <div class="mt-2 flex items-center gap-2">
                        <div class="flex-1 h-1.5 bg-purple-200 dark:bg-purple-900/40 rounded-full overflow-hidden">
                            <div class="h-full bg-purple-500 transition-all duration-1000 rounded-full"
                                 :style="`width: ${Math.max(0, (remainingSeconds / (scanBeforeStart * 60)) * 100)}%`"></div>
                        </div>
                        <span class="text-xs font-bold text-purple-700 dark:text-purple-400 tabular-nums flex-shrink-0"
                              x-text="formatTime(remainingSeconds)"></span>
                    </div>
                </div>
                <button @click="showWarning=false; if(warningTimer) clearInterval(warningTimer)"
                        class="p-1 hover:bg-purple-100 dark:hover:bg-purple-900/30 rounded-lg transition-colors flex-shrink-0">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mode Toggle -->
        <div class="card p-4 sm:p-6">
            <h3 class="text-base font-bold text-navy-800 dark:text-white mb-3 sm:mb-4">Mode Scan</h3>

            <div class="flex gap-2 p-1 bg-slate-100 dark:bg-slate-700/80 rounded-xl">
                <button @click="mode = 'in'"
                    :class="mode === 'in' ? 'bg-green-500 text-white shadow-lg' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="flex-1 px-3 py-2.5 sm:px-4 sm:py-3 rounded-lg font-bold text-sm transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                    </svg>
                    <span>Masuk</span>
                </button>
                <button @click="mode = 'out'"
                    :class="mode === 'out' ? 'bg-red-500 text-white shadow-lg' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="flex-1 px-3 py-2.5 sm:px-4 sm:py-3 rounded-lg font-bold text-sm transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                    <span>Keluar</span>
                </button>
            </div>

            <!-- Info Box -->
            <div class="mt-4 p-3.5 sm:p-4 rounded-xl transition-all"
                :class="mode === 'in' ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800'">
                <div class="flex items-start gap-2.5">
                    <div x-show="mode === 'in'" class="w-4 h-4 mt-0.5 text-green-600 dark:text-green-400 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                    </div>
                    <div x-show="mode === 'out'" class="w-4 h-4 mt-0.5 text-red-600 dark:text-red-400 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                    </div>
                    <div class="text-xs min-w-0">
                        <p class="font-bold mb-0.5"
                            :class="mode === 'in' ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300'">
                            Mode <span x-text="mode === 'in' ? 'Masuk' : 'Keluar'"></span>
                        </p>
                        <p :class="mode === 'in' ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400'">
                            <span
                                x-text="mode === 'in' ? 'Scan QR saat masuk kelas untuk memulai presensi' : 'Scan QR saat keluar kelas untuk menyelesaikan presensi'"></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Aturan Scan Info -->
            <div class="mt-3 p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                <div class="flex items-start gap-2">
                    <svg class="w-3.5 h-3.5 mt-0.5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-xs text-blue-700 dark:text-blue-300">
                        <p class="font-bold mb-1">Aturan Scan:</p>
                        <ul class="space-y-0.5 list-disc list-inside">
                            <li>Presensi dapat dilakukan kapan saja untuk jadwal mengajar hari ini</li>
                            <li>Tidak ada batasan waktu minimal atau maksimal untuk scan masuk & keluar kelas</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Scanner -->
        <div class="card p-4 sm:p-6">
            <h3 class="text-base font-bold text-navy-800 dark:text-white mb-4">Scan QR Code</h3>

            <!-- Camera Viewport -->
            <div class="flex justify-center mb-4">
                <div
                    class="relative rounded-2xl overflow-hidden bg-slate-900 w-full max-w-[320px] sm:max-w-[360px] h-[320px] sm:h-[360px] shadow-inner">
                    <!-- Video feed -->
                    <video id="qr-video" class="absolute inset-0 w-full h-full object-cover" autoplay playsinline
                        muted></video>

                    <!-- Idle overlay -->
                    <div id="qr-idle-overlay"
                        class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/90 text-white gap-3 p-4 text-center">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white/10 flex items-center justify-center">
                            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75v4.5m0-4.5h-4.5m4.5 0L15 9m5.25 11.25v-4.5m0 4.5h-4.5m4.5 0L15 15" />
                            </svg>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-slate-300">Tekan tombol untuk mulai scan</p>
                    </div>

                    <!-- Scan box overlay -->
                    <div id="qr-scan-overlay" class="absolute inset-0 hidden">
                        <div class="absolute inset-0 bg-black/50"></div>
                        <div id="qr-scan-box" class="absolute"
                            style="top:50%;left:50%;transform:translate(-50%,-50%);width:200px;height:200px;">
                            <div class="absolute inset-0 rounded-lg" style="box-shadow: 0 0 0 9999px rgba(0,0,0,0.5);">
                            </div>
                            <span
                                class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-gold-400 rounded-tl-lg"></span>
                            <span
                                class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-gold-400 rounded-tr-lg"></span>
                            <span
                                class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-gold-400 rounded-bl-lg"></span>
                            <span
                                class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-gold-400 rounded-br-lg"></span>
                            <div class="qr-laser absolute left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-gold-400 to-transparent"
                                style="top:0;"></div>
                        </div>
                        <p
                            class="absolute bottom-4 sm:bottom-6 left-0 right-0 text-center text-[11px] sm:text-xs text-white/80 font-medium px-2">
                            Arahkan QR Code ke dalam kotak</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-2 max-w-xs sm:max-w-sm mx-auto">
                <button @click="startScanner()" x-show="!scanning"
                    class="flex-1 px-4 py-3 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all hover:opacity-90 shadow-md active:scale-95">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0c-.693.04-1.344.436-1.736 1.039l-.821 1.316Z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008v-.008Z" />
                    </svg>
                    <span>Mulai Scan</span>
                </button>
                <button @click="stopScanner()" x-show="scanning"
                    class="flex-1 px-4 py-3 bg-red-500 text-white rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all hover:bg-red-600 shadow-md active:scale-95">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <rect x="5" y="5" width="14" height="14" rx="2" />
                    </svg>
                    <span>Stop Scan</span>
                </button>
            </div>
        </div>

        <!-- Result Toast -->
        <div x-show="showResult" x-transition class="card p-4 sm:p-6 border-2"
            :class="resultSuccess ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-red-500 bg-red-50 dark:bg-red-900/20'">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                    :class="resultSuccess ? 'bg-green-500' : 'bg-red-500'">
                    <svg x-show="resultSuccess" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <svg x-show="!resultSuccess" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-sm sm:text-base leading-snug"
                        :class="resultSuccess ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300'"
                        x-text="resultMessage"></p>
                    <div x-show="resultData" class="mt-2 space-y-1 text-xs"
                        :class="resultSuccess ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400'">
                        <template x-if="resultData?.classroom">
                            <p><strong>Kelas:</strong> <span x-text="resultData.classroom"></span></p>
                        </template>
                        <template x-if="resultData?.subject">
                            <p><strong>Mapel:</strong> <span x-text="resultData.subject"></span></p>
                        </template>
                        <template x-if="resultData?.duration">
                            <p><strong>Durasi:</strong> <span x-text="resultData.duration"></span></p>
                        </template>
                        <template x-if="resultData?.status">
                            <p><strong>Status:</strong> <span x-text="resultData.status"></span></p>
                        </template>
                        <template x-if="resultData?.debug?.message">
                            <p><strong>Debug:</strong> <span x-text="resultData.debug.message"></span></p>
                        </template>
                        <template x-if="resultData?.debug?.distance">
                            <p><strong>Jarak:</strong> <span x-text="resultData.debug.distance"></span></p>
                        </template>
                        <template x-if="resultData?.debug?.elapsed">
                            <p><strong>Waktu berlalu:</strong> <span x-text="resultData.debug.elapsed"></span></p>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Hari Ini (UPDATED LOGIC) -->
        <div class="card p-4 sm:p-6">
            <h3 class="text-base font-bold text-navy-800 dark:text-white mb-4">Jadwal Hari Ini</h3>
            <div class="space-y-3" id="schedule-container">
                @foreach($schedules as $schedule)
                    @php
                        $now = now();
                        $startTime = \Carbon\Carbon::parse($schedule->start_time);
                        $endTime = \Carbon\Carbon::parse($schedule->end_time);
                        $att = $schedule->classAttendances->first();

                        // Logika Status Badge (3 menit grace period)
                        $graceEnd = $endTime->copy()->addMinutes(3);
                        $isEnded = $now->greaterThan($graceEnd);

                        // Prioritas: jika guru sudah scan masuk, tampilkan Hadir/Terlambat
                        // bukan Berakhir, meski waktu kelas sudah lewat
                        if ($att && $att->check_in_time) {
                            if ($att->status === 'Terlambat') {
                                $badgeText = 'Terlambat';
                                $theme = 'yellow'; // Kuning
                            } else {
                                $badgeText = 'Hadir';
                                $theme = 'green'; // Hijau
                            }
                        } elseif ($isEnded) {
                            $badgeText = 'Berakhir';
                            $theme = 'red'; // Merah - hanya jika belum scan masuk
                        } elseif ($now->greaterThanOrEqualTo($startTime)) {
                            $badgeText = 'Berlangsung';
                            $theme = 'blue'; // Biru
                        } else {
                            $badgeText = 'Belum';
                            $theme = 'slate'; // Abu-abu
                        }
                    @endphp

                    <div class="p-3.5 sm:p-4 rounded-xl border-2 transition-all" :class="{
                             'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800': '{{ $theme }}' === 'red',
                             'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800': '{{ $theme }}' === 'green',
                             'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800': '{{ $theme }}' === 'yellow',
                             'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800': '{{ $theme }}' === 'blue',
                             'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700': '{{ $theme }}' === 'slate'
                         }">
                        <div class="flex items-center justify-between gap-2.5">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" :class="{
                                         'bg-red-100 dark:bg-red-900/30': '{{ $theme }}' === 'red',
                                         'bg-green-100 dark:bg-green-900/30': '{{ $theme }}' === 'green',
                                         'bg-yellow-100 dark:bg-yellow-900/30': '{{ $theme }}' === 'yellow',
                                         'bg-blue-100 dark:bg-blue-900/30': '{{ $theme }}' === 'blue',
                                         'bg-slate-200 dark:bg-slate-600': '{{ $theme }}' === 'slate'
                                     }">
                                    @if($theme === 'red')
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif($theme === 'green')
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0Z" />
                                        </svg>
                                    @elseif($theme === 'yellow')
                                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0Z" />
                                        </svg>
                                    @elseif($theme === 'blue')
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0Z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="9" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-navy-800 dark:text-white truncate">
                                        {{ $schedule->classroom->code
                                            ? strtoupper(str_replace('-', ' ', $schedule->classroom->code))
                                            : ($schedule->classroom->name ?? '-') }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                        {{ $schedule->subject->name ?? '-' }} • Jam ke-{{ $schedule->period }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                        {{ $startTime->format('H:i') }} - {{ $endTime->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold flex-shrink-0 text-center" :class="{
                                      'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400': '{{ $theme }}' === 'red',
                                      'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400': '{{ $theme }}' === 'green',
                                      'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400': '{{ $theme }}' === 'yellow',
                                      'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400': '{{ $theme }}' === 'blue',
                                      'bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-400': '{{ $theme }}' === 'slate'
                                  }">
                                {{ $badgeText }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ══ SHARED SPACE BOTTOM SHEET ══ -->
        <div x-show="showSharedSpaceModal" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[999] flex items-end justify-center pointer-events-none"
             @keydown.escape.window="closeSharedSpace()">

            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm pointer-events-auto"
                 @click="closeSharedSpace()"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"></div>

            <!-- Sheet -->
            <div id="shared-space-sheet"
                 class="relative w-full max-w-lg mx-auto bg-white dark:bg-slate-800 shadow-2xl flex flex-col pointer-events-auto"
                 style="transform:translateY(100%);transition:transform 0.35s cubic-bezier(0.32,0.72,0,1);"
                 :style="showSharedSpaceModal ? 'transform:translateY(0)' : 'transform:translateY(100%)'"
                 @keydown.escape.window="closeSharedSpace()"
                 @click.stop>

                <!-- Drag Handle -->
                <div class="flex-shrink-0 flex justify-center pt-3 pb-1.5 cursor-grab active:cursor-grabbing select-none"
                     @touchstart="handleDragStart($event,'touch')" @touchmove="handleDragMove($event)" @touchend="handleDragEnd()"
                     @mousedown="handleDragStart($event,'mouse')">
                    <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
                </div>

                <!-- Header -->
                <div class="flex-shrink-0 relative px-5 py-4 bg-gradient-to-br from-navy-800 via-navy-900 to-slate-900 overflow-hidden">
                    <div class="absolute -top-6 -right-6 w-24 h-24 rounded-full bg-gold-400/10 blur-xl"></div>
                    <div class="absolute -bottom-4 -left-4 w-16 h-16 rounded-full bg-white/5 blur-lg"></div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gold-400/20 border border-gold-400/30 flex items-center justify-center backdrop-blur-sm">
                                <i data-lucide="building-2" class="w-5 h-5 text-gold-400"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white" x-text="mode==='in' ? 'Presensi Masuk' : 'Presensi Keluar'"></p>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full animate-pulse" :class="mode==='in'?'bg-green-400':'bg-red-400'"></span>
                                    <p class="text-xs text-white/60 truncate" x-text="sharedSpaceLocation || 'Ruangan Bersama'"></p>
                                </div>
                            </div>
                        </div>
                        <!-- Close (red) -->
                        <button @click="closeSharedSpace()"
                                class="w-8 h-8 rounded-lg bg-red-500/20 hover:bg-red-500 text-red-400 hover:text-white flex items-center justify-center transition-all">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Body (scrollable) -->
                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4"
                     style="padding-bottom:calc(1.25rem + env(safe-area-inset-bottom));">

                    <!-- ── MODE IN ── -->
                    <div x-show="mode==='in'" class="space-y-4">

                        <!-- KELAS -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Kelas <span class="text-red-500">*</span></label>
                            <button type="button" @click="openKelas=!openKelas;openMapel=false"
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border-2 transition-all text-left"
                                    :class="openKelas?'border-navy-800 dark:border-gold-400 shadow-md':(sharedSpaceSelectedClass?'border-green-400 dark:border-green-500 bg-green-50 dark:bg-green-900/20':'border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50')">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" :class="sharedSpaceSelectedClass?'bg-green-500':'bg-slate-200 dark:bg-slate-600'">
                                    <i data-lucide="users" class="w-4 h-4" :class="sharedSpaceSelectedClass?'text-white':'text-slate-500'"></i>
                                </div>
                                <span class="flex-1 text-sm min-w-0 font-medium truncate"
                                      :class="sharedSpaceSelectedClass?'text-slate-800 dark:text-white':'text-slate-400 dark:text-slate-500'"
                                      x-text="sharedSpaceSelectedClass?(sharedSpaceClasses.find(c=>c.id==sharedSpaceSelectedClass)?.name||'Kelas dipilih'):'Pilih kelas...'"></span>
                                <div class="flex items-center gap-1 flex-shrink-0">
                                    <button x-show="sharedSpaceSelectedClass" type="button" @click.stop="sharedSpaceSelectedClass=''"
                                            class="w-5 h-5 rounded-full bg-slate-300 dark:bg-slate-500 flex items-center justify-center hover:bg-red-500 transition-colors">
                                        <i data-lucide="x" class="w-3 h-3 text-white"></i>
                                    </button>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="openKelas?'rotate-180':''"></i>
                                </div>
                            </button>
                            <!-- Dropdown inline -->
                            <div x-show="openKelas"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-1"
                                 @click.away="openKelas=false"
                                 class="mt-1.5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-600 shadow-lg overflow-hidden">
                                <div class="p-2 border-b border-slate-100 dark:border-slate-700">
                                    <div class="relative">
                                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                                        <input type="text" x-model="searchKelas" placeholder="Cari kelas..." @click.stop @keydown.escape="openKelas=false"
                                               class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 rounded-lg border-0 focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-400 text-slate-800 dark:text-white placeholder:text-slate-400">
                                    </div>
                                </div>
                                <div class="max-h-44 overflow-y-auto py-1">
                                    <template x-for="cls in sharedSpaceClasses.filter(c=>!searchKelas||c.name.toLowerCase().includes(searchKelas.toLowerCase())||(c.code&&c.code.toLowerCase().includes(searchKelas.toLowerCase())))" :key="cls.id">
                                        <button type="button" @click.stop="sharedSpaceSelectedClass=cls.id;openKelas=false;searchKelas=''"
                                                class="w-full flex items-center gap-3 px-4 py-2.5 text-left text-sm transition-colors"
                                                :class="sharedSpaceSelectedClass==cls.id?'bg-green-50 dark:bg-green-900/30 font-semibold text-green-700 dark:text-green-400':'hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200'">
                                            <span class="flex-1 truncate" x-text="cls.code?cls.name+' ('+cls.code+')':cls.name"></span>
                                            <i x-show="sharedSpaceSelectedClass==cls.id" data-lucide="check" class="w-4 h-4 text-green-600 dark:text-green-400 flex-shrink-0"></i>
                                        </button>
                                    </template>
                                    <p x-show="sharedSpaceClasses.filter(c=>!searchKelas||c.name.toLowerCase().includes(searchKelas.toLowerCase())||(c.code&&c.code.toLowerCase().includes(searchKelas.toLowerCase()))).length===0" class="text-center text-xs text-slate-400 py-4">Tidak ditemukan</p>
                                </div>
                            </div>
                        </div>

                        <!-- MATA PELAJARAN -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Mata Pelajaran <span class="text-red-500">*</span></label>
                            <button type="button" @click="openMapel=!openMapel;openKelas=false"
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border-2 transition-all text-left"
                                    :class="openMapel?'border-navy-800 dark:border-gold-400 shadow-md':(sharedSpaceSelectedSubject?'border-green-400 dark:border-green-500 bg-green-50 dark:bg-green-900/20':'border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50')">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" :class="sharedSpaceSelectedSubject?'bg-green-500':'bg-slate-200 dark:bg-slate-600'">
                                    <i data-lucide="book-open" class="w-4 h-4" :class="sharedSpaceSelectedSubject?'text-white':'text-slate-500'"></i>
                                </div>
                                <span class="flex-1 text-sm min-w-0 font-medium truncate"
                                      :class="sharedSpaceSelectedSubject?'text-slate-800 dark:text-white':'text-slate-400 dark:text-slate-500'"
                                      x-text="sharedSpaceSelectedSubject?(sharedSpaceSubjects.find(s=>s.id==sharedSpaceSelectedSubject)?.name||'Mapel dipilih'):'Pilih mata pelajaran...'"></span>
                                <div class="flex items-center gap-1 flex-shrink-0">
                                    <button x-show="sharedSpaceSelectedSubject" type="button" @click.stop="sharedSpaceSelectedSubject=''"
                                            class="w-5 h-5 rounded-full bg-slate-300 dark:bg-slate-500 flex items-center justify-center hover:bg-red-500 transition-colors">
                                        <i data-lucide="x" class="w-3 h-3 text-white"></i>
                                    </button>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="openMapel?'rotate-180':''"></i>
                                </div>
                            </button>
                            <div x-show="openMapel"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-1"
                                 @click.away="openMapel=false"
                                 class="mt-1.5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-600 shadow-lg overflow-hidden">
                                <div class="p-2 border-b border-slate-100 dark:border-slate-700">
                                    <div class="relative">
                                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                                        <input type="text" x-model="searchMapel" placeholder="Cari mata pelajaran..." @click.stop @keydown.escape="openMapel=false"
                                               class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 rounded-lg border-0 focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-400 text-slate-800 dark:text-white placeholder:text-slate-400">
                                    </div>
                                </div>
                                <div class="max-h-44 overflow-y-auto py-1">
                                    <template x-for="subject in sharedSpaceSubjects.filter(s=>!searchMapel||s.name.toLowerCase().includes(searchMapel.toLowerCase()))" :key="subject.id">
                                        <button type="button" @click.stop="sharedSpaceSelectedSubject=subject.id;openMapel=false;searchMapel=''"
                                                class="w-full flex items-center gap-3 px-4 py-2.5 text-left text-sm transition-colors"
                                                :class="sharedSpaceSelectedSubject==subject.id?'bg-green-50 dark:bg-green-900/30 font-semibold text-green-700 dark:text-green-400':'hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200'">
                                            <span class="flex-1 truncate" x-text="subject.name"></span>
                                            <i x-show="sharedSpaceSelectedSubject==subject.id" data-lucide="check" class="w-4 h-4 text-green-600 dark:text-green-400 flex-shrink-0"></i>
                                        </button>
                                    </template>
                                    <p x-show="sharedSpaceSubjects.filter(s=>!searchMapel||s.name.toLowerCase().includes(searchMapel.toLowerCase())).length===0" class="text-center text-xs text-slate-400 py-4">Tidak ditemukan</p>
                                </div>
                            </div>
                        </div>

                        <!-- JAM KE- (square buttons) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">
                                Jam Ke- <span class="text-red-500 font-normal normal-case">*</span>
                                <span x-show="sharedSpacePeriod" class="ml-1 px-2 py-0.5 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400 rounded-full text-[10px] font-bold normal-case" x-text="'JP '+sharedSpacePeriod"></span>
                            </label>
                            <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                                <template x-for="jam in [1,2,3,4,5,6,7,8,9,10,11,12]" :key="jam">
                                    <button type="button" @click="sharedSpacePeriod=jam"
                                            class="aspect-square flex flex-col items-center justify-center rounded-xl font-bold transition-all active:scale-95 touch-manipulation"
                                            :class="sharedSpacePeriod==jam?'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow-md scale-105':'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-navy-50 dark:hover:bg-navy-900/20 border-2 border-transparent hover:border-navy-200 dark:hover:border-navy-700'">
                                        <span class="text-base font-extrabold leading-none" x-text="jam"></span>
                                        <span class="text-[9px] leading-none mt-0.5 opacity-60">JP</span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Simpan Presensi Masuk -->
                        <div>
                            <button @click="submitSharedSpaceAttendance()"
                                    :disabled="!sharedSpaceSelectedClass||!sharedSpaceSelectedSubject||!sharedSpacePeriod"
                                    class="w-full py-4 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:-translate-y-0.5 active:scale-[.98] disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none disabled:translate-y-0 disabled:hover:translate-y-0">
                                <i data-lucide="log-in" class="w-4 h-4"></i>
                                Simpan Presensi Masuk
                            </button>
                        </div>
                    </div>

                    <!-- ── MODE OUT ── -->
                    <div x-show="mode==='out'" class="space-y-4">
                        <template x-if="sharedSpaceActiveSessions.length>0">
                            <div class="space-y-3">
                                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Pilih sesi untuk diselesaikan</p>
                                <template x-for="session in sharedSpaceActiveSessions" :key="session.id">
                                    <div class="relative rounded-xl border-2 cursor-pointer transition-all active:scale-[.98]"
                                         :class="sharedSpaceSelectedSession==session.id?'border-navy-800 dark:border-gold-400 bg-navy-50 dark:bg-navy-900/20':'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/50 hover:border-slate-300'"
                                         @click="sharedSpaceSelectedSession=session.id">
                                        <div class="flex items-center gap-3 p-3.5">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-black transition-colors"
                                                 :class="sharedSpaceSelectedSession==session.id?'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900':'bg-slate-100 dark:bg-slate-600 text-slate-500'"
                                                 x-text="session.classroom_name.slice(0,3).toUpperCase()"></div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold truncate" :class="sharedSpaceSelectedSession==session.id?'text-navy-800 dark:text-gold-400':'text-slate-800 dark:text-white'" x-text="session.classroom_name"></p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate" x-text="session.subject_name+' · Jam ke-'+session.period"></p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[10px] text-slate-400 flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i><span x-text="'Masuk '+session.check_in_time"></span></span>
                                                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full" :class="session.duration_minutes>=30?'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400':'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'" x-text="session.duration_minutes+' mnt'"></span>
                                                </div>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all"
                                                 :class="sharedSpaceSelectedSession==session.id?'border-navy-800 dark:border-gold-400 bg-navy-800 dark:bg-gold-400':'border-slate-300 dark:border-slate-500'">
                                                <i x-show="sharedSpaceSelectedSession==session.id" data-lucide="check" class="w-3 h-3 text-white dark:text-navy-900"></i>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <button @click="submitSharedSpaceCheckOut()" :disabled="!sharedSpaceSelectedSession"
                                        class="w-full py-4 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:-translate-y-0.5 active:scale-[.98] disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none disabled:translate-y-0 disabled:hover:translate-y-0">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    Selesaikan Sesi Ini
                                </button>
                            </div>
                        </template>
                        <template x-if="sharedSpaceActiveSessions.length===0">
                            <div class="text-center py-10">
                                <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center mx-auto mb-3"><i data-lucide="inbox" class="w-7 h-7 text-slate-400"></i></div>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Tidak Ada Sesi Aktif</p>
                                <p class="text-xs text-slate-400 mt-1">Lakukan scan masuk terlebih dahulu</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>


        <!-- Dynamic Class Selection Modal -->
        <div x-show="showClassSelection" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6"
                @click.away="showClassSelection = false">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-navy-800 dark:text-white">Pilih Kelas</h3>
                    <button @click="showClassSelection = false"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                    Anda sedang di lokasi <span class="font-semibold text-navy-800 dark:text-white"
                        x-text="selectedLocation"></span>.
                    Pilih kelas yang sedang Anda ajarkan:
                </p>

                <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    <template x-for="schedule in classSchedules" :key="schedule.id">
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer transition-all"
                            @click="selectClass(schedule)">
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <p class="font-bold text-navy-800 dark:text-white truncate"
                                        x-text="schedule.classroom_name"></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate"
                                        x-text="schedule.subject"></p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-sm font-semibold text-navy-800 dark:text-white"
                                        x-text="'Jam ke-' + schedule.period"></p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500"
                                        x-text="schedule.start_time + ' - ' + schedule.end_time"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- jsQR Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

    <script>
        // Scanner globals
        let _qrStream = null;
        let _qrScanning = false;
        let _qrAlpine = null;
        let _lastTickTime = 0;
        const _QR_INTERVAL_MS = 80;
        const _qrCanvas = document.createElement('canvas');
        const _qrCtx = _qrCanvas.getContext('2d', { willReadFrequently: true });
        const _qrCanvas2 = document.createElement('canvas');
        const _qrCtx2 = _qrCanvas2.getContext('2d', { willReadFrequently: true });

        function _tryDecode(canvas, ctx, video, sx, sy, sw, sh, dw, dh) {
            canvas.width = dw; canvas.height = dh;
            ctx.drawImage(video, sx, sy, sw, sh, 0, 0, dw, dh);
            const img = ctx.getImageData(0, 0, dw, dh);
            return jsQR(img.data, dw, dh, { inversionAttempts: 'attemptBoth' });
        }

        function startQrVideo(alpineCtx) {
            _qrAlpine = alpineCtx;
            const video = document.getElementById('qr-video');
            const idle = document.getElementById('qr-idle-overlay');
            const scanOverlay = document.getElementById('qr-scan-overlay');

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Akses kamera tidak didukung di peramban ini atau membutuhkan jaringan aman (HTTPS). Silakan gunakan tombol "Unggah QR".');
                return;
            }

            const constraintsList = [
                { video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } } },
                { video: { facingMode: 'environment' } },
                { video: true }
            ];

            function tryNextConstraint(index) {
                if (index >= constraintsList.length) {
                    alert('Gagal mengakses kamera. Pastikan izin kamera sudah diberikan di peramban Anda atau coba tombol "Unggah QR".');
                    return;
                }
                navigator.mediaDevices.getUserMedia(constraintsList[index])
                    .then(stream => {
                        _qrStream = stream;
                        video.srcObject = stream;
                        video.play();
                        if (idle) idle.classList.add('hidden');
                        if (scanOverlay) scanOverlay.classList.remove('hidden');
                        _qrScanning = true;
                        if (alpineCtx) alpineCtx.scanning = true;
                        _lastTickTime = 0;
                        requestAnimationFrame(tickQr);
                    })
                    .catch(err => {
                        console.warn('Camera constraint index ' + index + ' failed:', err);
                        tryNextConstraint(index + 1);
                    });
            }

            tryNextConstraint(0);
        }

        function stopQrVideo(alpineCtx) {
            _qrScanning = false;
            if (_qrStream) {
                _qrStream.getTracks().forEach(t => t.stop());
                _qrStream = null;
            }
            const video = document.getElementById('qr-video');
            if (video) video.srcObject = null;
            document.getElementById('qr-idle-overlay')?.classList.remove('hidden');
            document.getElementById('qr-scan-overlay')?.classList.add('hidden');
            if (alpineCtx) alpineCtx.scanning = false;
        }

        function tickQr(ts) {
            if (!_qrScanning) return;

            if (ts - _lastTickTime < _QR_INTERVAL_MS) {
                requestAnimationFrame(tickQr);
                return;
            }
            _lastTickTime = ts;

            const video = document.getElementById('qr-video');
            if (!video || video.readyState < 2) { requestAnimationFrame(tickQr); return; }

            const vw = video.videoWidth, vh = video.videoHeight;
            if (!vw || !vh) { requestAnimationFrame(tickQr); return; }

            const scale = Math.min(1, 400 / vw);
            let code = _tryDecode(_qrCanvas, _qrCtx, video, 0, 0, vw, vh, Math.round(vw * scale), Math.round(vh * scale));

            if (!code) {
                const cx = Math.round(vw * 0.2), cy = Math.round(vh * 0.2);
                const cw = Math.round(vw * 0.6), ch = Math.round(vh * 0.6);
                const dw = Math.min(cw, 360);
                code = _tryDecode(_qrCanvas2, _qrCtx2, video, cx, cy, cw, ch, dw, Math.round(ch * dw / cw));
            }

            if (code && code.data) {
                stopQrVideo(_qrAlpine);
                if (_qrAlpine) _qrAlpine.processScan(code.data);
                return;
            }
            requestAnimationFrame(tickQr);
        }

        function classAttendance() {
            return {
                mode: 'in',
                scanning: false,
                showResult: false,
                resultSuccess: false,
                resultMessage: '',
                resultData: null,

                showClassSelection: false,
                selectedLocation: '',
                classSchedules: [],
                selectedScheduleId: null,
                scannedQrData: '',
                userLatitude: null,
                userLongitude: null,

                // Grace period warning state
                showWarning: false,
                warningMessage: '',
                warningTimer: null,
                remainingSeconds: 0,
                gracePeriod: {{ $gracePeriodSetting ?? 5 }},
                scanBeforeStart: {{ $scanBeforeStart ?? 15 }},

                // Shared space state
                showSharedSpaceModal: false,
                sharedSpaceFull: false,                sharedSpaceLocation: '',
                sharedSpaceLocationId: '',
                sharedSpaceClasses: [],
                sharedSpaceSubjects: [],
                sharedSpaceActiveSessions: [],
                sharedSpaceSelectedClass: '',
                sharedSpaceSelectedSubject: '',
                sharedSpacePeriod: '',
                sharedSpaceSelectedSession: '',

                // Dropdown open & search state
                openKelas: false,
                searchKelas: '',
                openMapel: false,
                searchMapel: '',
                jamViewMode: 'grid',

                init() {
                    this.$watch('showSharedSpaceModal', (val) => {
                        if (!val) {
                            this.openKelas = false;
                            this.openMapel = false;
                            this.searchKelas = '';
                            this.searchMapel = '';
                            this.sharedSpaceFull = false;
                        }
                    });
                },

                closeSharedSpace() {
                    this.showSharedSpaceModal = false;
                    this.sharedSpaceFull = false;
                },

                toggleFullScreen() {
                    this.sharedSpaceFull = !this.sharedSpaceFull;
                    if (this.sharedSpaceFull) {
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow = '';
                    }
                },

                handleTouchStart(e) {
                    this._touchStartY = e.touches[0].clientY;
                    this._touchDelta = 0;
                },

                handleTouchMove(e) {
                    if (!this._touchStartY) return;
                    this._touchDelta = this._touchStartY - e.touches[0].clientY;
                },

                handleTouchEnd() {
                    if (this._touchDelta > 40 && !this.sharedSpaceFull) {
                        this.toggleFullScreen();
                    }
                    this._touchStartY = null;
                    this._touchDelta = 0;
                },

                handleDragStart(e) {
                    this._dragStartY = e.clientY;
                    this._dragDelta = 0;
                    const onMove = (ev) => { this._dragDelta = this._dragStartY - ev.clientY; };
                    const onUp = () => {
                        document.removeEventListener('mousemove', onMove);
                        document.removeEventListener('mouseup', onUp);
                        if (this._dragDelta > 30 && !this.sharedSpaceFull) {
                            this.toggleFullScreen();
                        }
                        this._dragStartY = null;
                        this._dragDelta = 0;
                    };
                    document.addEventListener('mousemove', onMove);
                    document.addEventListener('mouseup', onUp);
                },

                startScanner() {
                    startQrVideo(this);
                },

                stopScanner() {
                    stopQrVideo(this);
                },

                processScan(qrData) {
                    this.scannedQrData = qrData;
                    // Langsung kirim scan — GPS berjalan di background jika tersedia
                    // Tidak perlu tunggu GPS karena kelas tidak wajib punya koordinat
                    this.sendScan(qrData, this.userLatitude, this.userLongitude);

                    // Update GPS di background untuk scan berikutnya
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            pos => {
                                this.userLatitude  = pos.coords.latitude;
                                this.userLongitude = pos.coords.longitude;
                            },
                            () => {},
                            { enableHighAccuracy: false, timeout: 10000, maximumAge: 60000 }
                        );
                    }
                },

                sendScan(qrData, latitude = null, longitude = null) {
                    const payload = { qr_data: qrData, mode: this.mode };
                    if (latitude !== null && longitude !== null) {
                        payload.latitude = latitude;
                        payload.longitude = longitude;
                    }

                    this._post('{{ route("teacher.class-attendance.scan") }}', payload)
                        .then(({ status, data }) => {
                            // ── Handle grace period (429) ──────────────────
                            if (status === 429 && data.warning) {
                                this.startGracePeriodCountdown(data);
                                return;
                            }
                            // ── Handle batch scan errors (too_early, class_ended, etc.) ──
                            if (!data.success && data.error_type) {
                                this.handleBatchScanError(status, data);
                                return;
                            }
                            if (data.is_shared_space) {
                                this.showSharedSpaceModal = true;
                                this.sharedSpaceLocation = data.classroom?.name || '';
                                this.sharedSpaceLocationId = data.classroom?.id || this.extractClassroomId(qrData);
                                this.sharedSpaceClasses = data.all_classes || [];
                                this.sharedSpaceSubjects = data.subjects || [];
                                this.sharedSpaceActiveSessions = data.active_sessions || [];
                                this.sharedSpaceSelectedClass = '';
                                this.sharedSpaceSelectedSubject = '';
                                this.sharedSpacePeriod = '';
                                this.sharedSpaceSelectedSession = '';
                                this.openKelas = false;
                                this.searchKelas = '';
                                this.openMapel = false;
                                this.searchMapel = '';
                                this.jamViewMode = 'grid';
                            } else if (data.schedules) {
                                this.showClassSelection = true;
                                this.selectedLocation = data.message;
                                this.classSchedules = data.schedules;
                            } else {
                                this.handleScanResponse(status, data);
                            }
                        });
                },

                selectClass(schedule) {
                    this.selectedScheduleId = schedule.id;
                    this.showClassSelection = false;
                    this.processScanWithSchedule();
                },

                processScanWithSchedule() {
                    const payload = {
                        qr_data: this.scannedQrData,
                        mode: this.mode,
                        schedule_id: this.selectedScheduleId
                    };

                    if (this.userLatitude !== null && this.userLongitude !== null) {
                        payload.latitude = this.userLatitude;
                        payload.longitude = this.userLongitude;
                    }

                    this._post('{{ route("teacher.class-attendance.scan") }}', payload)
                        .then(({ status, data }) => { this.handleScanResponse(status, data); });
                },

                // Submit presensi MASUK shared space (ON-DEMAND)
                submitSharedSpaceAttendance() {
                    if (!this.sharedSpaceSelectedClass || !this.sharedSpaceSelectedSubject || !this.sharedSpacePeriod) {
                        alert('Lengkapi kelas, mata pelajaran, dan jam ke- terlebih dahulu.');
                        return;
                    }
                    this._post('{{ route("teacher.class-attendance.save-shared") }}', {
                        classroom_id: this.sharedSpaceLocationId,
                        selected_classroom_id: this.sharedSpaceSelectedClass,
                        subject_id: this.sharedSpaceSelectedSubject,
                        period: this.sharedSpacePeriod,
                        mode: 'in',
                    })
                        .then(({ status, data }) => {
                            this.closeSharedSpace();
                            this.handleScanResponse(status, data);
                        });
                },

                // Submit presensi KELUAR shared space
                submitSharedSpaceCheckOut() {
                    if (!this.sharedSpaceSelectedSession) {
                        alert('Pilih sesi yang ingin diselesaikan.');
                        return;
                    }
                    this._post('{{ route("teacher.class-attendance.save-shared") }}', {
                        classroom_id: this.sharedSpaceLocationId,
                        attendance_id: this.sharedSpaceSelectedSession,
                        mode: 'out',
                    })
                        .then(({ status, data }) => {
                            this.closeSharedSpace();
                            this.handleScanResponse(status, data);
                        });
                },

                // Batch scan prevention error handler
                handleBatchScanError(status, data) {
                    this.showResult  = true;
                    this.resultSuccess = false;
                    this.resultMessage = data.message || 'Tidak dapat memproses scan.';
                    this.resultData  = data.data || null;

                    if (data.error_type === 'too_early' && data.data?.minutes_wait > 0) {
                        this.startCountdown(data.data.minutes_wait * 60);
                    }

                    setTimeout(() => { this.showResult = false; }, 6000);
                },

                // Countdown untuk "terlalu awal"
                startCountdown(seconds) {
                    if (this.warningTimer) clearInterval(this.warningTimer);
                    this.remainingSeconds = seconds;
                    this.showWarning      = true;
                    this.warningMessage   = 'Tunggu hingga waktu scan tiba...';

                    this.warningTimer = setInterval(() => {
                        this.remainingSeconds--;
                        if (this.remainingSeconds <= 0) {
                            clearInterval(this.warningTimer);
                            this.showWarning = false;
                        }
                    }, 1000);
                },

                // Grace period countdown
                startGracePeriodCountdown(data) {
                    if (this.warningTimer) clearInterval(this.warningTimer);
                    this.showWarning      = true;
                    this.warningMessage   = data.message || 'Tunggu sebelum scan kelas berikutnya';
                    this.remainingSeconds = data.remaining_seconds || (this.gracePeriod * 60);
                    this.gracePeriod      = data.grace_period || this.gracePeriod;

                    this.warningTimer = setInterval(() => {
                        this.remainingSeconds--;
                        if (this.remainingSeconds <= 0) {
                            clearInterval(this.warningTimer);
                            this.showWarning = false;
                        }
                    }, 1000);
                },

                formatTime(seconds) {
                    const m = Math.floor(seconds / 60);
                    const s = seconds % 60;
                    return `${m}:${String(s).padStart(2,'0')}`;
                },

                // Safe fetch helper
                _post(url, payload) {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    return fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(payload),
                    })
                        .then(res => {
                            const contentType = res.headers.get('content-type') || '';
                            if (contentType.includes('application/json')) {
                                return res.json().then(data => ({ status: res.status, data }));
                            }
                            return res.text().then(text => ({
                                status: res.status,
                                data: {
                                    success: false,
                                    message: res.status === 419
                                        ? 'Sesi habis, silakan refresh halaman.'
                                        : `Server error (${res.status}). Coba refresh halaman.`,
                                }
                            }));
                        })
                        .catch(err => ({
                            status: 0,
                            data: { success: false, message: 'Tidak ada koneksi internet atau server tidak dapat dijangkau.' }
                        }));
                },

                extractClassroomId(qrData) {
                    try {
                        const parsed = JSON.parse(qrData);
                        if (parsed.classroom_id) return parsed.classroom_id;
                    } catch (e) { }
                    const parts = String(qrData).split('|');
                    return parts[0] || null;
                },

                handleScanResponse(status, data) {
                    this.showResult = true;
                    this.resultSuccess = (status >= 200 && status < 300) && data?.success;
                    this.resultMessage = data?.message || 'Terjadi kesalahan sistem';
                    this.resultData = data?.data || data || null;

                    if (this.resultSuccess) {
                        // AJAX refresh instead of full page reload
                        setTimeout(() => this.refreshScheduleData(), 1500);
                    }
                },

                refreshScheduleData() {
                    fetch('{{ route("teacher.class-attendance.refresh") }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        // Update stats cards
                        const statTotal = document.getElementById('stat-total-classes');
                        const statProgress = document.getElementById('stat-in-progress');
                        const statComplete = document.getElementById('stat-completed');
                        if (statTotal) statTotal.textContent = data.stats.total;
                        if (statProgress) statProgress.textContent = data.stats.inProgress;
                        if (statComplete) statComplete.textContent = data.stats.completed;

                        // Update schedule cards
                        const container = document.getElementById('schedule-container');
                        if (container && data.schedules) {
                            const themeClasses = {
                                red:    { bg: 'bg-red-50 dark:bg-red-900/20', border: 'border-red-200 dark:border-red-800', iconBg: 'bg-red-100 dark:bg-red-900/30', badgeBg: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' },
                                green:  { bg: 'bg-green-50 dark:bg-green-900/20', border: 'border-green-200 dark:border-green-800', iconBg: 'bg-green-100 dark:bg-green-900/30', badgeBg: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' },
                                yellow: { bg: 'bg-yellow-50 dark:bg-yellow-900/20', border: 'border-yellow-200 dark:border-yellow-800', iconBg: 'bg-yellow-100 dark:bg-yellow-900/30', badgeBg: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' },
                                blue:   { bg: 'bg-blue-50 dark:bg-blue-900/20', border: 'border-blue-200 dark:border-blue-800', iconBg: 'bg-blue-100 dark:bg-blue-900/30', badgeBg: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' },
                                slate:  { bg: 'bg-slate-50 dark:bg-slate-700/30', border: 'border-slate-200 dark:border-slate-700', iconBg: 'bg-slate-200 dark:bg-slate-600', badgeBg: 'bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-400' },
                            };
                            container.innerHTML = data.schedules.map(s => {
                                const tc = themeClasses[s.theme] || themeClasses.slate;
                                return `<div class="p-3.5 sm:p-4 rounded-xl border-2 transition-all ${tc.bg} ${tc.border}">
                                    <div class="flex items-center justify-between gap-2.5">
                                        <div class="flex items-center gap-3 min-w-0 flex-1">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 ${tc.iconBg}">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0Z"/>
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-bold text-navy-800 dark:text-white truncate">${s.classroom}</p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">${s.subject} • Jam ke-${s.period}</p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">${s.start} - ${s.end}</p>
                                            </div>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold flex-shrink-0 ${tc.badgeBg}">${s.badge}</span>
                                    </div>
                                </div>`;
                            }).join('');
                        }

                        // Update reminder marquee
                        const reminderBar = document.getElementById('reminder-bar');
                        const reminderText = document.getElementById('reminder-text');
                        if (data.reminders) {
                            // Only keep class-specific scan reminders
                            const classReminders = data.reminders.filter(r => r.includes('belum scan') || r.includes('scan keluar'));
                            if (classReminders.length > 0 && reminderText) {
                                reminderText.textContent = '⚠️ ' + classReminders.join('  •  ');
                                if (reminderBar) reminderBar.style.display = '';
                            } else if (reminderBar) {
                                reminderBar.style.display = 'none';
                            }
                        }
                    })
                    .catch(err => console.error('Refresh error:', err));
                }
            }
        }
    </script>

    <style>
        .qr-laser {
            animation: qrLaser 1.8s ease-in-out infinite;
        }

        @keyframes qrLaser {
            0% {
                top: 0;
                opacity: 1;
            }

            50% {
                top: calc(100% - 2px);
                opacity: 1;
            }

            100% {
                top: 0;
                opacity: 1;
            }
        }

        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        [x-cloak] {
            display: none !important;
        }

    </style>
@endsection