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

                    <!-- Gallery upload button (kamera aktif) -->
                    <button x-show="scanning" @click="openGallery()"
                            class="absolute top-3 right-3 w-10 h-10 bg-black/40 backdrop-blur-sm rounded-xl flex items-center justify-center text-white hover:bg-black/60 active:scale-95 transition-all z-10">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </button>

                    <!-- Hidden file input untuk galeri -->
                    <input type="file" id="qr-gallery-input" accept="image/*" class="hidden" @change="handleGalleryUpload($event)">

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
                            <div class="qr-laser absolute left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-gold-400 to-transparent animate-scan-laser"
                                style="top: 0;"></div>
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
                                        {{ $schedule->subject->name ?? '-' }} â€¢ Jam ke-{{ $schedule->period }}
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
        <!-- Backdrop -->
        <div x-show="showSharedSpaceSheet" x-cloak
             class="fixed inset-0 z-[998] bg-black/60"
             style="backdrop-filter:blur(4px);"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closeSharedSpaceSheet()">
        </div>

        <!-- Sheet panel -->
        <div id="shared-space-sheet"
             x-show="showSharedSpaceSheet" x-cloak
             class="fixed bottom-0 left-0 right-0 z-[999] flex flex-col bg-white dark:bg-slate-900 shadow-2xl"
             style="max-height:92dvh;border-radius:24px 24px 0 0;will-change:transform;"
             x-transition:enter="transition ease-out duration-350"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-280"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             @keydown.escape.window="closeSharedSpaceSheet()">

            <!-- Drag handle -->
            <div id="sheet-drag-handle"
                 class="flex-shrink-0 flex flex-col items-center pt-3 pb-1 cursor-grab active:cursor-grabbing select-none touch-none">
                <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-700"></div>
            </div>

            <!-- Header -->
            <div class="flex-shrink-0 px-5 pt-1 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-base font-bold text-navy-800 dark:text-white leading-tight"
                           x-text="mode === 'in' ? 'Presensi Masuk' : 'Presensi Keluar'"></p>
                        <p class="text-xs text-slate-400 truncate mt-0.5"
                           x-text="sharedSpaceLocation || 'Ruangan Bersama'"></p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold flex-shrink-0"
                          :class="mode==='in'
                              ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                              : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'">
                        <span class="w-1.5 h-1.5 rounded-full animate-pulse flex-shrink-0"
                              :class="mode==='in' ? 'bg-emerald-500' : 'bg-red-500'"></span>
                        <span x-text="mode==='in' ? 'Masuk' : 'Keluar'"></span>
                    </span>
                    <button @click="closeSharedSpaceSheet()"
                            class="flex-shrink-0 w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-red-500 hover:text-white active:scale-95 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <!-- Step indicator -->
                <div class="flex items-center gap-1.5 mt-3" x-show="showSharedSpaceSheet && mode==='in'">
                    <template x-for="(step,i) in [{l:'Kelas',d:!!sharedSpaceSelectedClass},{l:'Mapel',d:!!sharedSpaceSelectedSubject},{l:'Jam',d:!!sharedSpacePeriod}]" :key="i">
                        <div class="flex items-center gap-1.5" :class="i<2?'flex-1':''">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-black flex-shrink-0 transition-all duration-300"
                                 :class="step.d ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900' : 'bg-slate-200 dark:bg-slate-700 text-slate-400'">
                                <svg x-show="step.d" class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                </svg>
                                <span x-show="!step.d" x-text="i+1"></span>
                            </div>
                            <span class="text-[10px] font-semibold transition-colors duration-300 flex-shrink-0"
                                  :class="step.d ? 'text-navy-800 dark:text-gold-400' : 'text-slate-400'"
                                  x-text="step.l"></span>
                            <div x-show="i<2" class="flex-1 h-px"
                                 :class="step.d ? 'bg-navy-800/25 dark:bg-gold-400/25' : 'bg-slate-200 dark:bg-slate-700'"></div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Scrollable body -->
            <div class="flex-1 overflow-y-auto overscroll-contain px-5 py-4 space-y-4">

                <!-- MODE IN -->
                <div x-show="showSharedSpaceSheet && mode==='in'" class="space-y-4">

                    <!-- KELAS -->
                    <div class="relative">
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 mb-1.5 uppercase tracking-widest">
                            Kelas <span class="text-red-500">*</span>
                        </label>
                        <button type="button" @click="openKelas=!openKelas;openMapel=false"
                                class="w-full flex items-center gap-3 px-4 py-3.5 transition-all duration-200 text-left"
                                style="border-radius:14px;"
                                :style="openKelas ? 'border:2px solid rgb(30 58 138);box-shadow:0 4px 16px rgba(30,58,138,.08)' : (sharedSpaceSelectedClass ? 'border:2px solid #a5b4fc;' : 'border:2px solid #e2e8f0;')"
                                :class="openKelas
                                    ? 'bg-navy-50/50 dark:bg-navy-900/20'
                                    : sharedSpaceSelectedClass
                                        ? 'bg-indigo-50/30 dark:bg-navy-900/10'
                                        : 'bg-slate-50 dark:bg-slate-800/60'">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 transition-all duration-200"
                                 :class="sharedSpaceSelectedClass ? 'bg-navy-800 dark:bg-gold-400' : openKelas ? 'bg-navy-100 dark:bg-navy-800/60' : 'bg-white dark:bg-slate-700 shadow-sm'">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"
                                     :class="sharedSpaceSelectedClass ? 'text-white dark:text-navy-900' : openKelas ? 'text-navy-800 dark:text-gold-400' : 'text-slate-400'">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="block text-sm truncate transition-colors"
                                      :class="sharedSpaceSelectedClass ? 'font-semibold text-slate-800 dark:text-white' : 'font-medium text-slate-400 dark:text-slate-500'"
                                      x-text="sharedSpaceSelectedClass ? (sharedSpaceClasses.find(c=>c.id==sharedSpaceSelectedClass)?.name || 'Dipilih') : 'Pilih kelas...'"></span>
                                <span x-show="sharedSpaceSelectedClass && sharedSpaceClasses.find(c=>c.id==sharedSpaceSelectedClass)?.code"
                                      class="block text-[10px] text-slate-400 font-mono mt-0.5"
                                      x-text="sharedSpaceClasses.find(c=>c.id==sharedSpaceSelectedClass)?.code || ''"></span>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <button x-show="sharedSpaceSelectedClass" type="button" @click.stop="sharedSpaceSelectedClass=''"
                                        class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-600 flex items-center justify-center hover:bg-red-500 group transition-all">
                                    <svg class="w-3 h-3 text-slate-400 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                <svg class="w-4 h-4 flex-shrink-0 transition-all duration-300"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                                     :class="openKelas ? 'rotate-180 text-navy-800 dark:text-gold-400' : 'text-slate-400'">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </div>
                        </button>
                        <div x-show="openKelas"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2 scale-[.97]"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-[.97]"
                             class="absolute left-0 right-0 z-20 mt-1.5 bg-white dark:bg-slate-800 overflow-hidden"
                             style="border-radius:16px;border:1px solid #e2e8f0;top:100%;box-shadow:0 20px 40px rgba(0,0,0,.12);">
                            <div class="p-2.5 border-b border-slate-100 dark:border-slate-700">
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                                    </svg>
                                    <input type="text" x-model="searchKelas" placeholder="Cari kelas..."
                                           @click.stop @keydown.escape.stop="openKelas=false"
                                           class="w-full pl-9 pr-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-700/60 border-0 focus:outline-none text-slate-800 dark:text-white placeholder:text-slate-400"
                                           style="border-radius:10px;">
                                </div>
                            </div>
                            <div class="max-h-52 overflow-y-auto overscroll-contain py-1">
                                <template x-for="cls in sharedSpaceClasses.filter(c=>!searchKelas||c.name.toLowerCase().includes(searchKelas.toLowerCase())||(c.code&&c.code.toLowerCase().includes(searchKelas.toLowerCase())))" :key="cls.id">
                                    <button type="button" @click.stop="sharedSpaceSelectedClass=cls.id;openKelas=false;searchKelas=''"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-left text-sm transition-all duration-150"
                                            :class="sharedSpaceSelectedClass==cls.id ? 'bg-navy-50 dark:bg-navy-900/30 text-navy-800 dark:text-gold-400' : 'hover:bg-slate-50 dark:hover:bg-slate-700/40 text-slate-700 dark:text-slate-200'">
                                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0"
                                             :class="sharedSpaceSelectedClass==cls.id ? 'bg-navy-800 dark:bg-gold-400' : 'bg-slate-100 dark:bg-slate-700'">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"
                                                 :class="sharedSpaceSelectedClass==cls.id ? 'text-white dark:text-navy-900' : 'text-slate-400'">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span class="block font-medium truncate" x-text="cls.name"></span>
                                            <span x-show="cls.code" class="block text-[10px] text-slate-400 font-mono" x-text="cls.code"></span>
                                        </div>
                                        <svg x-show="sharedSpaceSelectedClass==cls.id" class="w-4 h-4 flex-shrink-0 text-navy-800 dark:text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                        </svg>
                                    </button>
                                </template>
                                <div x-show="sharedSpaceClasses.length===0" class="text-center py-8">
                                    <svg class="w-5 h-5 text-slate-300 mx-auto mb-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <p class="text-xs text-slate-400">Memuat data...</p>
                                </div>
                                <p x-show="sharedSpaceClasses.length>0 && !sharedSpaceClasses.filter(c=>!searchKelas||c.name.toLowerCase().includes(searchKelas.toLowerCase())||(c.code&&c.code.toLowerCase().includes(searchKelas.toLowerCase()))).length"
                                   class="text-center text-xs text-slate-400 py-6">Tidak ditemukan</p>
                            </div>
                        </div>
                    </div>

                    <!-- MATA PELAJARAN -->
                    <div class="relative">
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 mb-1.5 uppercase tracking-widest">
                            Mata Pelajaran <span class="text-red-500">*</span>
                        </label>
                        <button type="button" @click="openMapel=!openMapel;openKelas=false"
                                class="w-full flex items-center gap-3 px-4 py-3.5 transition-all duration-200 text-left"
                                style="border-radius:14px;"
                                :style="openMapel ? 'border:2px solid rgb(30 58 138);box-shadow:0 4px 16px rgba(30,58,138,.08)' : (sharedSpaceSelectedSubject ? 'border:2px solid #a5b4fc;' : 'border:2px solid #e2e8f0;')"
                                :class="openMapel
                                    ? 'bg-navy-50/50 dark:bg-navy-900/20'
                                    : sharedSpaceSelectedSubject
                                        ? 'bg-indigo-50/30 dark:bg-navy-900/10'
                                        : 'bg-slate-50 dark:bg-slate-800/60'">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 transition-all duration-200"
                                 :class="sharedSpaceSelectedSubject ? 'bg-navy-800 dark:bg-gold-400' : openMapel ? 'bg-navy-100 dark:bg-navy-800/60' : 'bg-white dark:bg-slate-700 shadow-sm'">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"
                                     :class="sharedSpaceSelectedSubject ? 'text-white dark:text-navy-900' : openMapel ? 'text-navy-800 dark:text-gold-400' : 'text-slate-400'">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="block text-sm truncate transition-colors"
                                      :class="sharedSpaceSelectedSubject ? 'font-semibold text-slate-800 dark:text-white' : 'font-medium text-slate-400 dark:text-slate-500'"
                                      x-text="sharedSpaceSelectedSubject ? (sharedSpaceSubjects.find(s=>s.id==sharedSpaceSelectedSubject)?.name || 'Dipilih') : 'Pilih mata pelajaran...'"></span>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <button x-show="sharedSpaceSelectedSubject" type="button" @click.stop="sharedSpaceSelectedSubject=''"
                                        class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-600 flex items-center justify-center hover:bg-red-500 group transition-all">
                                    <svg class="w-3 h-3 text-slate-400 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                <svg class="w-4 h-4 flex-shrink-0 transition-all duration-300"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                                     :class="openMapel ? 'rotate-180 text-navy-800 dark:text-gold-400' : 'text-slate-400'">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </div>
                        </button>
                        <div x-show="openMapel"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2 scale-[.97]"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-[.97]"
                             class="absolute left-0 right-0 z-20 mt-1.5 bg-white dark:bg-slate-800 overflow-hidden"
                             style="border-radius:16px;border:1px solid #e2e8f0;top:100%;box-shadow:0 20px 40px rgba(0,0,0,.12);">
                            <div class="p-2.5 border-b border-slate-100 dark:border-slate-700">
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                                    </svg>
                                    <input type="text" x-model="searchMapel" placeholder="Cari mata pelajaran..."
                                           @click.stop @keydown.escape.stop="openMapel=false"
                                           class="w-full pl-9 pr-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-700/60 border-0 focus:outline-none text-slate-800 dark:text-white placeholder:text-slate-400"
                                           style="border-radius:10px;">
                                </div>
                            </div>
                            <div class="max-h-52 overflow-y-auto overscroll-contain py-1">
                                <template x-for="subject in sharedSpaceSubjects.filter(s=>!searchMapel||s.name.toLowerCase().includes(searchMapel.toLowerCase()))" :key="subject.id">
                                    <button type="button" @click.stop="sharedSpaceSelectedSubject=subject.id;openMapel=false;searchMapel=''"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-left text-sm transition-all duration-150"
                                            :class="sharedSpaceSelectedSubject==subject.id ? 'bg-navy-50 dark:bg-navy-900/30 text-navy-800 dark:text-gold-400' : 'hover:bg-slate-50 dark:hover:bg-slate-700/40 text-slate-700 dark:text-slate-200'">
                                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0"
                                             :class="sharedSpaceSelectedSubject==subject.id ? 'bg-navy-800 dark:bg-gold-400' : 'bg-slate-100 dark:bg-slate-700'">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"
                                                 :class="sharedSpaceSelectedSubject==subject.id ? 'text-white dark:text-navy-900' : 'text-slate-400'">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                                            </svg>
                                        </div>
                                        <span class="flex-1 truncate font-medium" x-text="subject.name"></span>
                                        <svg x-show="sharedSpaceSelectedSubject==subject.id" class="w-4 h-4 flex-shrink-0 text-navy-800 dark:text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                        </svg>
                                    </button>
                                </template>
                                <div x-show="sharedSpaceSubjects.length===0" class="text-center py-8">
                                    <svg class="w-5 h-5 text-slate-300 mx-auto mb-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <p class="text-xs text-slate-400">Memuat data...</p>
                                </div>
                                <p x-show="sharedSpaceSubjects.length>0 && !sharedSpaceSubjects.filter(s=>!searchMapel||s.name.toLowerCase().includes(searchMapel.toLowerCase())).length"
                                   class="text-center text-xs text-slate-400 py-6">Tidak ditemukan</p>
                            </div>
                        </div>
                    </div>

                    <!-- JAM KE- -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 mb-1.5 uppercase tracking-widest flex items-center gap-2">
                            Jam Ke- <span class="text-red-500 font-normal">*</span>
                            <span x-show="sharedSpacePeriod"
                                  x-transition:enter="transition ease-out duration-200"
                                  x-transition:enter-start="opacity-0 scale-90"
                                  x-transition:enter-end="opacity-100 scale-100"
                                  class="px-2 py-0.5 bg-navy-100 dark:bg-navy-900/40 text-navy-800 dark:text-gold-400 text-[10px] font-black normal-case tracking-normal"
                                  style="border-radius:8px;"
                                  x-text="'JP ' + sharedSpacePeriod"></span>
                        </label>
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="j in [1,2,3,4,5,6,7,8,9,10,11,12]" :key="j">
                                <button type="button" @click="sharedSpacePeriod = j"
                                        class="flex flex-col items-center justify-center font-bold transition-all duration-150 active:scale-95 touch-manipulation select-none"
                                        style="border-radius:12px;height:52px;"
                                        :class="sharedSpacePeriod == j
                                            ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow-lg scale-[1.03]'
                                            : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-navy-300 hover:bg-white'">
                                    <span class="text-base font-extrabold leading-none" x-text="j"></span>
                                    <span class="text-[9px] leading-none mt-0.5 font-medium opacity-50">JP</span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- MODE OUT -->
                <div x-show="showSharedSpaceSheet && mode==='out'" class="space-y-3">
                    <template x-if="sharedSpaceActiveSessions.length > 0">
                        <div class="space-y-2">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilih sesi yang ingin diselesaikan</p>
                            <template x-for="session in sharedSpaceActiveSessions" :key="session.id">
                                <div class="cursor-pointer transition-all duration-200 active:scale-[.98]"
                                     style="border-radius:16px;"
                                     :style="sharedSpaceSelectedSession==session.id ? 'border:2px solid rgb(30 58 138);box-shadow:0 4px 16px rgba(30,58,138,.08)' : 'border:2px solid #e2e8f0;'"
                                     :class="sharedSpaceSelectedSession==session.id ? 'bg-navy-50 dark:bg-navy-900/20' : 'bg-white dark:bg-slate-800'"
                                     @click="sharedSpaceSelectedSession = session.id">
                                    <div class="flex items-center gap-3 p-3.5">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xs font-black transition-all duration-200"
                                             :class="sharedSpaceSelectedSession==session.id ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900' : 'bg-slate-100 dark:bg-slate-700 text-slate-500'"
                                             x-text="session.classroom_name.slice(0,3).toUpperCase()"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold truncate"
                                               :class="sharedSpaceSelectedSession==session.id ? 'text-navy-800 dark:text-gold-400' : 'text-slate-800 dark:text-white'"
                                               x-text="session.classroom_name"></p>
                                            <p class="text-xs text-slate-400 truncate mt-0.5" x-text="session.subject_name + ' · Jam ke-' + session.period"></p>
                                            <div class="flex items-center gap-2 mt-1.5">
                                                <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                    </svg>
                                                    <span x-text="'Masuk ' + session.check_in_time"></span>
                                                </span>
                                                <span class="text-[10px] font-semibold px-1.5 py-0.5"
                                                      style="border-radius:6px;"
                                                      :class="session.duration_minutes>=30 ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'"
                                                      x-text="session.duration_minutes + ' mnt'"></span>
                                            </div>
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all duration-200"
                                             :class="sharedSpaceSelectedSession==session.id ? 'border-navy-800 bg-navy-800 dark:border-gold-400 dark:bg-gold-400' : 'border-slate-300 dark:border-slate-600'">
                                            <svg x-show="sharedSpaceSelectedSession==session.id" class="w-3 h-3 text-white dark:text-navy-900"
                                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="sharedSpaceActiveSessions.length===0">
                        <div class="text-center py-12">
                            <div class="w-14 h-14 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-600 dark:text-slate-300">Tidak Ada Sesi Aktif</p>
                            <p class="text-xs text-slate-400 mt-1">Lakukan scan masuk terlebih dahulu</p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Fixed bottom button -->
            <div class="flex-shrink-0 px-5 pt-3 pb-7 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                <button x-show="showSharedSpaceSheet && mode==='in'"
                        @click="submitSharedSpaceAttendance()"
                        :disabled="!showSharedSpaceSheet||!sharedSpaceSelectedClass||!sharedSpaceSelectedSubject||!sharedSpacePeriod||sharedSpaceSubmitting"
                        class="w-full py-4 font-bold text-sm flex items-center justify-center gap-2.5 transition-all duration-200"
                        style="border-radius:14px;"
                        :class="(!showSharedSpaceSheet||!sharedSpaceSelectedClass||!sharedSpaceSelectedSubject||!sharedSpacePeriod||sharedSpaceSubmitting)
                            ? 'bg-slate-100 dark:bg-slate-800 text-slate-400 cursor-not-allowed'
                            : 'bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-[.98]'">
                    <svg x-show="showSharedSpaceSheet && sharedSpaceSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <svg x-show="showSharedSpaceSheet && !sharedSpaceSubmitting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/>
                    </svg>
                    <span x-text="showSharedSpaceSheet ? (sharedSpaceSubmitting ? 'Menyimpan...' : (!sharedSpaceSelectedClass||!sharedSpaceSelectedSubject||!sharedSpacePeriod ? 'Lengkapi form terlebih dahulu' : 'Simpan Presensi Masuk')) : ''"></span>
                </button>
                <button x-show="showSharedSpaceSheet && mode==='out'"
                        @click="submitSharedSpaceCheckOut()"
                        :disabled="!showSharedSpaceSheet||!sharedSpaceSelectedSession||sharedSpaceSubmitting"
                        class="w-full py-4 font-bold text-sm flex items-center justify-center gap-2.5 transition-all duration-200"
                        style="border-radius:14px;"
                        :class="(!showSharedSpaceSheet||!sharedSpaceSelectedSession||sharedSpaceSubmitting)
                            ? 'bg-slate-100 dark:bg-slate-800 text-slate-400 cursor-not-allowed'
                            : 'bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-[.98]'">
                    <svg x-show="showSharedSpaceSheet && sharedSpaceSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <svg x-show="showSharedSpaceSheet && !sharedSpaceSubmitting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                    </svg>
                    <span x-text="showSharedSpaceSheet ? (sharedSpaceSubmitting ? 'Menyimpan...' : 'Selesaikan Sesi Ini') : ''"></span>
                </button>
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
                gracePeriod: 0,
                scanBeforeStart: 0,

                // Shared space state
                showSharedSpaceModal: false,
                showSharedSpaceSheet: false,
                sharedSpaceSubmitting: false,
                sharedSpaceLocation: '',
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
                        if (val) {
                            document.body.style.overflow = 'hidden';
                            setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 50);
                        } else {
                            document.body.style.overflow = '';
                            this.openKelas = false;
                            this.openMapel = false;
                            this.searchKelas = '';
                            this.searchMapel = '';
                        }
                    });
                    this.$watch('showSharedSpaceSheet', (val) => {
                        if (val) {
                            // Init drag handle
                            this._initDragHandle();
                        }
                    });                    this.$watch('openKelas', (val) => {
                        if (val) setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 30);
                    });
                    this.$watch('openMapel', (val) => {
                        if (val) setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 30);
                    });
                },

                closeSharedSpace() {
                    this.showSharedSpaceModal = false;
                    // Reset content styles immediately
                    const content = document.getElementById('shared-space-content');
                    if (content) {
                        content.style.removeProperty('transform');
                        content.style.removeProperty('opacity');
                        content.style.removeProperty('overflow');
                    }
                    setTimeout(() => { document.body.style.overflow = ''; }, 250);
                },

                startScanner() {
                    startQrVideo(this);
                },

                openGallery() {
                    document.getElementById('qr-gallery-input').click();
                },

                handleGalleryUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    if (!file.type.startsWith('image/')) {
                        this.showWarning = true;
                        this.warningMessage = 'File harus berupa gambar (PNG, JPG, JPEG)';
                        setTimeout(() => { this.showWarning = false; }, 3000);
                        return;
                    }

                    // Reset input untuk allow upload ulang file yang sama
                    event.target.value = '';

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.processQrFromImage(e.target.result);
                    };
                    reader.readAsDataURL(file);
                },

                processQrFromImage(imageSrc) {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    const img = new Image();

                    img.onload = () => {
                        canvas.width = img.width;
                        canvas.height = img.height;
                        ctx.drawImage(img, 0, 0);

                        try {
                            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                            const code = jsQR(imageData.data, imageData.width, imageData.height);

                            if (code && code.data) {
                                this.handleQrDetected(code.data);
                            } else {
                                this.showWarning = true;
                                this.warningMessage = 'QR Code tidak ditemukan dalam gambar. Pastikan QR Code terlihat jelas.';
                                setTimeout(() => { this.showWarning = false; }, 4000);
                            }
                        } catch (error) {
                            console.error('Error processing QR from image:', error);
                            this.showWarning = true;
                            this.warningMessage = 'Gagal memproses gambar. Coba lagi dengan gambar lain.';
                            setTimeout(() => { this.showWarning = false; }, 3000);
                        }
                    };

                    img.onerror = () => {
                        this.showWarning = true;
                        this.warningMessage = 'Gagal memuat gambar. Coba pilih gambar lain.';
                        setTimeout(() => { this.showWarning = false; }, 3000);
                    };

                    img.src = imageSrc;
                },

                handleQrDetected(qrData) {
                    // Feedback visual saat QR terdeteksi
                    this.stopScanner();
                    this.showResult = true;
                    this.resultSuccess = true;
                    this.resultMessage = 'QR Code berhasil terdeteksi dari galeri!';
                    
                    // Proses QR seperti biasa â€” panggil fungsi yang sama seperti dari kamera
                    setTimeout(() => {
                        // Langsung kirim scan seperti dari kamera
                        this.sendScan(qrData, this.userLatitude, this.userLongitude);
                    }, 800);
                },

                stopScanner() {
                    stopQrVideo(this);
                },

                processScan(qrData) {
                    this.scannedQrData = qrData;
                    // Langsung kirim scan â€” GPS berjalan di background jika tersedia
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
                            // â”€â”€ Handle grace period (429) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
                            if (status === 429 && data.warning) {
                                this.startGracePeriodCountdown(data);
                                return;
                            }
                            // â”€â”€ Handle batch scan errors (too_early, class_ended, etc.) â”€â”€
                            if (!data.success && data.error_type) {
                                this.handleBatchScanError(status, data);
                                return;
                            }
                            if (data.is_shared_space) {
                                const classroomId = data.classroom?.id || this.extractClassroomId(qrData);
                                this.openSharedSpaceSheet(classroomId, data.classroom?.name || 'Ruangan Bersama');
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
                    if (!this.sharedSpaceSelectedClass || !this.sharedSpaceSelectedSubject || !this.sharedSpacePeriod) return;
                    this.sharedSpaceSubmitting = true;
                    this._post('{{ route("teacher.class-attendance.save-shared") }}', {
                        classroom_id: this.sharedSpaceLocationId,
                        selected_classroom_id: this.sharedSpaceSelectedClass,
                        subject_id: this.sharedSpaceSelectedSubject,
                        period: this.sharedSpacePeriod,
                        mode: 'in',
                    })
                    .then(({ status, data }) => {
                        this.sharedSpaceSubmitting = false;
                        this.closeSharedSpaceSheet(true);
                        setTimeout(() => { this.handleScanResponse(status, data); }, 400);
                    });
                },

                // Submit presensi KELUAR shared space
                submitSharedSpaceCheckOut() {
                    if (!this.sharedSpaceSelectedSession) return;
                    this.sharedSpaceSubmitting = true;
                    this._post('{{ route("teacher.class-attendance.save-shared") }}', {
                        classroom_id: this.sharedSpaceLocationId,
                        attendance_id: this.sharedSpaceSelectedSession,
                        mode: 'out',
                    })
                    .then(({ status, data }) => {
                        this.sharedSpaceSubmitting = false;
                        this.closeSharedSpaceSheet(true);
                        setTimeout(() => { this.handleScanResponse(status, data); }, 400);
                    });
                },

                // Open bottom sheet shared space
                openSharedSpaceSheet(classroomId, locationName) {
                    this.sharedSpaceLocationId = classroomId;
                    this.sharedSpaceLocation   = locationName;
                    this.sharedSpaceSelectedClass   = '';
                    this.sharedSpaceSelectedSubject = '';
                    this.sharedSpacePeriod          = '';
                    this.sharedSpaceSelectedSession = '';
                    this.sharedSpaceSubmitting      = false;

                    // Fetch classes, subjects, active sessions
                    fetch(`/teacher/class-attendance/shared-space/data?classroom_id=${classroomId}&mode=${this.mode}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.sharedSpaceClasses        = data.classes         || [];
                        this.sharedSpaceSubjects       = data.subjects        || [];
                        this.sharedSpaceActiveSessions = data.active_sessions || [];
                    })
                    .catch(() => {});

                    this.showSharedSpaceSheet = true;
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 80);
                },

                // Drag handle for bottom sheet
                _initDragHandle() {
                    setTimeout(() => {
                        const sheet   = document.getElementById('shared-space-sheet');
                        const handle  = document.getElementById('sheet-drag-handle');
                        if (!sheet || !handle) return;

                        let startY = 0, startH = 0, dragging = false;

                        const onStart = (e) => {
                            dragging = true;
                            startY   = (e.touches ? e.touches[0].clientY : e.clientY);
                            startH   = sheet.getBoundingClientRect().height;
                            sheet.style.transition = 'none';
                        };
                        const onMove = (e) => {
                            if (!dragging) return;
                            const dy      = startY - (e.touches ? e.touches[0].clientY : e.clientY);
                            const newH    = Math.min(window.innerHeight, Math.max(300, startH + dy));
                            sheet.style.height = newH + 'px';
                        };
                        const onEnd = (e) => {
                            if (!dragging) return;
                            dragging = false;
                            sheet.style.transition = '';
                            const h = sheet.getBoundingClientRect().height;
                            if (h < 200) {
                                this.closeSharedSpaceSheet();
                            } else if (h > window.innerHeight * 0.75) {
                                sheet.style.height = '100dvh';
                                sheet.style.borderRadius = '0';
                            } else {
                                sheet.style.height = '';
                                sheet.style.borderRadius = '';
                            }
                        };

                        handle.addEventListener('touchstart', onStart, { passive: true });
                        document.addEventListener('touchmove', onMove,  { passive: true });
                        document.addEventListener('touchend',  onEnd);
                        handle.addEventListener('mousedown', onStart);
                        document.addEventListener('mousemove', onMove);
                        document.addEventListener('mouseup',  onEnd);
                    }, 100);
                },

                closeSharedSpaceSheet(fromSubmit = false) {
                    this.showSharedSpaceSheet = false;
                    if (!fromSubmit) document.body.style.overflow = '';
                    else setTimeout(() => { document.body.style.overflow = ''; }, 400);
                    this.openKelas = false;
                    this.openMapel = false;
                    this.searchKelas = '';
                    this.searchMapel = '';
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
                                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">${s.subject} â€¢ Jam ke-${s.period}</p>
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
                                reminderText.textContent = '[Reminder] ' + classReminders.join(' | ');
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

        .animate-scan-laser {
            animation: smoothScanLaser 2.2s ease-in-out infinite;
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

        @keyframes smoothScanLaser {
            0% { 
                top: 0; 
                opacity: 0.4;
                transform: scaleX(0.7);
            }
            20% { 
                opacity: 1;
                transform: scaleX(1);
            }
            50% { 
                top: calc(50% - 1px); 
                opacity: 1;
                transform: scaleX(1);
            }
            80% { 
                opacity: 1;
                transform: scaleX(1);
            }
            100% { 
                top: calc(100% - 2px); 
                opacity: 0.4;
                transform: scaleX(0.7);
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