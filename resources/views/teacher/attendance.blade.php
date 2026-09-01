@extends('layouts.teacher')

@section('page-title', 'Presensi Harian')

@section('content')
    <div class="fade-in space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 flex-shrink-0">
                    <i data-lucide="scan-line" class="w-5 h-5 sm:w-6 sm:h-6 text-white dark:text-navy-900"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-navy-800 dark:text-white">Presensi Harian</h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Scan QR Code untuk presensi datang dan pulang</p>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="card p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 animate-fade-in">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="check" class="w-4 h-4 text-white"></i>
                    </div>
                    <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="card p-4 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800 animate-fade-in">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="x" class="w-4 h-4 text-white"></i>
                    </div>
                    <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Presensi Area -->
            <div class="lg:col-span-2 space-y-6">

                <div class="card p-4 sm:p-6 bg-gradient-to-br from-white to-slate-50 dark:from-slate-800 dark:to-slate-900 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 flex-shrink-0">
                                <i data-lucide="calendar-check" class="w-5 h-5 sm:w-6 sm:h-6 text-white dark:text-navy-900"></i>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-bold text-navy-800 dark:text-white">Status Hari Ini</h2>
                                <div class="flex items-center gap-2 mt-0.5">
                                        <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                                        <span class="text-slate-300 dark:text-slate-600 hidden sm:inline">•</span>
                                    </div>
                            </div>
                        </div>
                        @if($todayAttendance)
            @php
                $statusBadgeClass = match($todayAttendance->status) {
                    'Hadir', 'Tepat Waktu' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                    'Terlambat'            => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                    'Alpha'                => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                    'Izin', 'Sakit'        => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                    default                => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400',
                };
                $statusLabel = $todayAttendance->status === 'Tepat Waktu' ? 'Hadir' : $todayAttendance->status;
            @endphp
            <span class="px-3 py-1.5 sm:px-4 sm:py-2 rounded-full text-xs sm:text-sm font-bold {{ $statusBadgeClass }}">
                {{ $statusLabel }}
            </span>
        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:gap-4">
                        <div class="p-3 sm:p-4 rounded-2xl border-2 {{ $todayAttendance && $todayAttendance->check_in ? 'bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-green-200 dark:border-green-800' : 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700' }}">
                            <div class="flex items-center gap-2 sm:gap-3 mb-2">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl {{ $todayAttendance && $todayAttendance->check_in ? 'bg-green-500' : 'bg-slate-300 dark:bg-slate-600' }} flex items-center justify-center transition-colors flex-shrink-0">
                                    <i data-lucide="clock" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Jam Masuk</p>
                                    @if($scheduleStart)
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500">Jadwal: {{ \Carbon\Carbon::parse($scheduleStart)->format('H:i') }}</p>
                                    @else
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500">Belum diatur</p>
                                    @endif
                                </div>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-bold {{ $todayAttendance && $todayAttendance->check_in ? 'text-green-700 dark:text-green-400' : 'text-slate-400' }}">
                                @if($todayAttendance && $todayAttendance->check_in)
                                    {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') }}
                                @elseif($scheduleStart)
                                    {{ \Carbon\Carbon::parse($scheduleStart)->format('H:i') }}
                                @else
                                    --:--
                                @endif
                            </h3>
                        </div>

                        <div class="p-3 sm:p-4 rounded-2xl border-2 {{ $todayAttendance && $todayAttendance->check_out ? 'bg-gradient-to-br from-navy-50 to-slate-50 dark:from-navy-900/20 dark:to-slate-900/20 border-navy-200 dark:border-navy-800' : 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700' }}">
                            <div class="flex items-center gap-2 sm:gap-3 mb-2">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl {{ $todayAttendance && $todayAttendance->check_out ? 'bg-navy-800 dark:bg-gold-400' : 'bg-slate-300 dark:bg-slate-600' }} flex items-center justify-center transition-colors flex-shrink-0">
                                    <i data-lucide="clock" class="w-4 h-4 sm:w-5 sm:h-5 text-white dark:text-navy-900"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Jam Pulang</p>
                                    @if($scheduleEnd)
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500">Jadwal: {{ \Carbon\Carbon::parse($scheduleEnd)->format('H:i') }}</p>
                                    @else
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500">Belum diatur</p>
                                    @endif
                                </div>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-bold {{ $todayAttendance && $todayAttendance->check_out ? 'text-navy-800 dark:text-gold-400' : 'text-slate-400' }}">
                                @if($todayAttendance && $todayAttendance->check_out)
                                    {{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('H:i') }}
                                @elseif($scheduleEnd)
                                    {{ \Carbon\Carbon::parse($scheduleEnd)->format('H:i') }}
                                @else
                                    --:--
                                @endif
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- QR Scanner Card -->
                <div class="card p-4 sm:p-6 bg-gradient-to-br from-white to-slate-50 dark:from-slate-800 dark:to-slate-900 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                                <i data-lucide="scan-line" class="w-5 h-5 sm:w-6 sm:h-6 text-white dark:text-navy-900"></i>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-xl font-bold text-navy-800 dark:text-white">Scan QR Code</h2>
                                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Scan atau unggah QR untuk presensi</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button onclick="openTeacherScanner()"
                                class="flex-1 py-4 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:-translate-y-0.5 active:scale-[.98]">
                            <i data-lucide="camera" class="w-5 h-5"></i>
                            Scan dengan Kamera
                        </button>
                        <button onclick="document.getElementById('teacher-gallery-trigger').click()"
                                class="flex-1 py-4 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-600 active:scale-[.98] border-2 border-slate-200 dark:border-slate-600">
                            <i data-lucide="image" class="w-5 h-5"></i>
                            Unggah dari Galeri
                        </button>
                        <input type="file" id="teacher-gallery-trigger" accept="image/*" class="hidden" onchange="handleTeacherGalleryUpload(this)">
                    </div>

                    <div class="mt-4 p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start gap-2">
                            <i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5"></i>
                            <p class="text-xs text-blue-700 dark:text-blue-300">Presensi dapat dilakukan kapan saja. Scan QR Code di bawah atau unggah gambar QR dari galeri.</p>
                        </div>
                    </div>
                </div>

                <!-- QR Code Display Card -->
                <div class="card p-4 sm:p-6 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                                <i data-lucide="qr-code" class="w-5 h-5 sm:w-6 sm:h-6 text-white dark:text-navy-900"></i>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-xl font-bold text-navy-800 dark:text-white">QR Code Presensi Anda</h2>
                                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Tunjukkan QR Code ini untuk presensi</p>
                            </div>
                        </div>
                        <div class="hidden sm:block">
                            <span class="px-3 py-1 bg-navy-100 dark:bg-gold-900/30 text-navy-700 dark:text-gold-400 rounded-full text-xs font-bold">
                                {{ auth()->user()->name }}
                            </span>
                        </div>
                    </div>

                    <!-- QR Code Container (responsive square) -->
                    <div class="flex flex-col items-center justify-center p-4 sm:p-8 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border-2 border-slate-200 dark:border-slate-700">
                        <!-- QR + foto overlay wrapper -->
                        <div class="relative bg-white dark:bg-slate-800 p-3 sm:p-6 rounded-2xl shadow-lg border border-slate-100 dark:border-slate-700 w-48 h-48 sm:w-64 sm:h-64 flex items-center justify-center">
                            @if($qrCodeUrl)
                                <img src="{{ $qrCodeUrl }}" id="qr-code-img"
                                     alt="QR Code Presensi"
                                     class="w-full h-full object-contain">
                            @else
                                <div class="w-full h-full bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center">
                                    <p class="text-sm text-slate-500 dark:text-slate-400">QR Code tidak tersedia</p>
                                </div>
                            @endif

                        </div>
                        <div class="mt-6 text-center space-y-1">
                            <p class="text-sm font-bold text-navy-800 dark:text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Scan QR Code ini untuk presensi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Recent History -->
            <div class="space-y-6">
                <div class="card p-5">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                                <i data-lucide="history" class="w-5 h-5 text-white dark:text-navy-900"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-navy-800 dark:text-white">Riwayat 7 Hari</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Presensi terakhir</p>
                            </div>
                        </div>
                        <a href="{{ route('teacher.history') }}" class="text-xs font-semibold text-navy-800 dark:text-gold-400 hover:underline">
                            Lihat Semua
                        </a>
                    </div>

                    @if($recentAttendance->isEmpty())
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="inbox" class="w-8 h-8 text-slate-400 dark:text-slate-500"></i>
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada riwayat absensi</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($recentAttendance as $att)
                                <div class="p-3 bg-slate-50 dark:bg-slate-700/30 rounded-xl border border-slate-200 dark:border-slate-700 hover:shadow-md transition-all">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <p class="text-sm font-bold text-navy-800 dark:text-white">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($att->date)->locale('id')->isoFormat('dddd') }}</p>
                                        </div>
                                        @php
                                            $histBadge = match($att->status) {
                                                'Hadir', 'Tepat Waktu' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                                'Terlambat'            => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                'Alpha'                => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                                'Izin', 'Sakit'        => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                                default                => 'bg-slate-100 text-slate-600',
                                            };
                                            $histLabel = $att->status === 'Tepat Waktu' ? 'Hadir' : $att->status;
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $histBadge }}">
                                            {{ $histLabel }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400">
                                            <i data-lucide="clock" class="w-3 h-3 text-green-500"></i>
                                            <span class="font-mono">{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '-' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400">
                                            <i data-lucide="clock" class="w-3 h-3 text-navy-600 dark:text-gold-400"></i>
                                            <span class="font-mono">{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- QR Scanner Modal -->
    <div id="qr-scanner-modal" x-show="showScanner" x-cloak
         class="fixed inset-0 z-[999] flex items-end sm:items-center justify-center"
         style="display:none;">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"
             @click="showScanner=false;stopCamera()"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <!-- Scanner Sheet -->
        <div class="relative w-full max-w-md mx-auto bg-white dark:bg-slate-800 rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full sm:scale-95 sm:opacity-0"
             x-transition:enter-end="translate-y-0 sm:scale-100 sm:opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0 sm:scale-100"
             x-transition:leave-end="translate-y-full sm:scale-95 sm:opacity-0"
             @click.stop>

            <!-- Drag Handle (mobile) -->
            <div class="flex justify-center pt-3 pb-2 sm:hidden">
                <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
            </div>

            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-slate-900 dark:to-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gold-400/20 border border-gold-400/30 flex items-center justify-center">
                        <i data-lucide="scan-line" class="w-5 h-5 text-gold-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white">Scan QR Code</p>
                        <p class="text-xs text-white/50">Arahkan ke QR atau unggah gambar</p>
                    </div>
                </div>
                <button @click="showScanner=false;stopCamera()" class="w-8 h-8 rounded-lg bg-red-500/20 hover:bg-red-500 text-red-400 hover:text-white flex items-center justify-center transition-all">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Camera View -->
            <div class="px-6 pt-4">
                <div class="relative rounded-2xl overflow-hidden bg-slate-900 aspect-[4/3]">
                    <video id="teacher-scanner-video" autoplay playsinline muted
                           class="w-full h-full object-cover"></video>
                    <!-- Idle overlay -->
                    <div id="teacher-scanner-idle" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/80 text-white gap-3">
                        <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center border border-white/20">
                            <i data-lucide="scan" class="w-7 h-7 text-white"></i>
                        </div>
                        <p class="text-sm font-medium text-slate-300 text-center px-4">Tekan tombol di bawah untuk mulai scan</p>
                    </div>
                    <!-- Scan box overlay -->
                    <div id="teacher-scanner-overlay" class="absolute inset-0 hidden">
                        <div class="absolute inset-0 bg-black/50"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-48 h-48 relative">
                                <div class="absolute inset-0 rounded-xl" style="box-shadow: 0 0 0 9999px rgba(0,0,0,0.5);"></div>
                                <span class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-gold-400 rounded-tl-xl"></span>
                                <span class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-gold-400 rounded-tr-xl"></span>
                                <span class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-gold-400 rounded-bl-xl"></span>
                                <span class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-gold-400 rounded-br-xl"></span>
                                <div class="absolute left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-gold-400 to-transparent scanner-laser" style="top:0;animation:qrLaser 1.8s ease-in-out infinite;"></div>
                            </div>
                        </div>
                        <p class="absolute bottom-4 left-0 right-0 text-center text-xs text-white/80 font-medium">Arahkan QR Code ke dalam kotak</p>
                    </div>
                    <!-- Gallery upload button (top-right) -->
                    <button type="button" onclick="document.getElementById('teacher-gallery-input').click()"
                            class="absolute top-3 right-3 flex items-center gap-2 px-3 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white rounded-xl transition-all border border-white/30 text-xs font-medium">
                        <i data-lucide="image" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">Galeri</span>
                    </button>
                    <input type="file" id="teacher-gallery-input" accept="image/*" class="hidden" onchange="handleTeacherGalleryUpload(this)">
                </div>

                <!-- Controls -->
                <div class="flex gap-3 mt-4">
                    <button id="teacher-scan-btn" onclick="startTeacherScan()"
                            class="flex-1 py-3.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all hover:shadow-lg active:scale-[.98]">
                        <i data-lucide="camera" class="w-4 h-4"></i>
                        Mulai Scan
                    </button>
                    <button id="teacher-stop-btn" onclick="stopTeacherScan()" x-show="teacherScanning"
                            class="flex-1 py-3.5 bg-red-500 text-white rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all hover:bg-red-600 active:scale-[.98]">
                        <i data-lucide="square" class="w-4 h-4"></i>
                        Stop Scan
                    </button>
                </div>

                <!-- Result area -->
                <div id="teacher-scan-result" class="hidden mt-4 p-4 rounded-xl border-2"
                     :class="teacherScanSuccess ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-red-500 bg-red-50 dark:bg-red-900/20'">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                             :class="teacherScanSuccess ? 'bg-green-500' : 'bg-red-500'">
                            <i data-lucide="check" x-show="teacherScanSuccess" class="w-5 h-5 text-white"></i>
                            <i data-lucide="x" x-show="!teacherScanSuccess" class="w-5 h-5 text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-sm"
                               :class="teacherScanSuccess ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300'"
                               x-text="teacherScanMessage"></p>
                            <p x-show="teacherScanDebug" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" x-text="teacherScanDebug"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden canvas for QR decoding -->
            <canvas id="teacher-qr-canvas" class="hidden"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script>
        // ── Teacher QR Scanner ──
        let _teacherStream = null;
        let _teacherScanning = false;
        let _teacherTickId = null;
        const _QR_INTERVAL = 150;
        let _lastTeacherTick = 0;

        window.startTeacherScan = function() {
            if (!_teacherScanning) {
                navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
                })
                .then(stream => {
                    _teacherStream = stream;
                    _teacherScanning = true;
                    const video = document.getElementById('teacher-scanner-video');
                    video.srcObject = stream;
                    video.play();
                    document.getElementById('teacher-scanner-idle').classList.add('hidden');
                    document.getElementById('teacher-scanner-overlay').classList.remove('hidden');
                    document.getElementById('teacher-scan-btn').classList.add('hidden');
                    document.getElementById('teacher-stop-btn').classList.remove('hidden');
                    document.getElementById('teacher-scan-result').classList.add('hidden');
                    if (window.lucide) lucide.createIcons();
                    _lastTeacherTick = 0;
                    requestAnimationFrame(_teacherTick);
                })
                .catch(err => {
                    console.error('Camera error:', err);
                    alert('Kamera tidak dapat diakses. Gunakan tombol Galeri untuk unggah QR dari foto.');
                });
            }
        };

        window.stopTeacherScan = function() {
            _teacherScanning = false;
            if (_teacherStream) {
                _teacherStream.getTracks().forEach(t => t.stop());
                _teacherStream = null;
            }
            const video = document.getElementById('teacher-scanner-video');
            if (video) video.srcObject = null;
            document.getElementById('teacher-scanner-idle').classList.remove('hidden');
            document.getElementById('teacher-scanner-overlay').classList.add('hidden');
            document.getElementById('teacher-scan-btn').classList.remove('hidden');
            document.getElementById('teacher-stop-btn').classList.add('hidden');
            if (_teacherTickId) cancelAnimationFrame(_teacherTickId);
        };

        function _teacherTick(ts) {
            if (!_teacherScanning) return;
            if (ts - _lastTeacherTick < _QR_INTERVAL) {
                _teacherTickId = requestAnimationFrame(_teacherTick);
                return;
            }
            _lastTeacherTick = ts;
            const video = document.getElementById('teacher-scanner-video');
            const canvas = document.getElementById('teacher-qr-canvas');
            if (!video || !canvas || video.readyState < 2) {
                _teacherTickId = requestAnimationFrame(_teacherTick);
                return;
            }
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d', { willReadFrequently: true });
            ctx.drawImage(video, 0, 0);
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'attemptBoth' });
            if (code) {
                stopTeacherScan();
                _teacherProcessQR(code.data);
            } else {
                _teacherTickId = requestAnimationFrame(_teacherTick);
            }
        }

        window.handleTeacherGalleryUpload = function(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        const canvas = document.getElementById('teacher-qr-canvas');
                        canvas.width = img.width;
                        canvas.height = img.height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0);
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'attemptBoth' });
                        if (code) {
                            _teacherProcessQR(code.data);
                        } else {
                            _teacherShowResult(false, 'QR Code tidak ditemukan dalam gambar.', '');
                        }
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
            input.value = '';
        };

        function _teacherProcessQR(data) {
            // Show loading
            _teacherShowResult(null, 'Memproses presensi...', '');
            const now = new Date();
            const hours = now.getHours();
            const mode = hours < 12 ? 'masuk' : 'keluar';

            // Get GPS
            let lat = null, lng = null;
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    pos => { lat = pos.coords.latitude; lng = pos.coords.longitude; },
                    () => {},
                    { enableHighAccuracy: false, timeout: 8000 }
                );
            }

            // Submit
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            fetch('{{ route("teacher.attendance.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ qr_data: data, mode: mode, latitude: lat, longitude: lng }),
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    _teacherShowResult(true, data.message || 'Presensi berhasil!', '');
                    // Confetti-like animation
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    _teacherShowResult(false, data.message || 'Gagal memproses presensi.', '');
                }
            })
            .catch(err => {
                _teacherShowResult(false, 'Terjadi kesalahan koneksi.', '');
            });
        }

        function _teacherShowResult(success, message, debug) {
            const el = document.getElementById('teacher-scan-result');
            el.classList.remove('hidden');
            const icon = el.querySelector('[data-lucide="check"], [data-lucide="x"]');
            if (success === true) {
                el.className = 'mt-4 p-4 rounded-xl border-2 border-green-500 bg-green-50 dark:bg-green-900/20 flex items-center gap-3';
                if (icon) { icon.setAttribute('data-lucide', 'check'); icon.classList.remove('hidden'); }
            } else if (success === false) {
                el.className = 'mt-4 p-4 rounded-xl border-2 border-red-500 bg-red-50 dark:bg-red-900/20 flex items-center gap-3';
                if (icon) { icon.setAttribute('data-lucide', 'x'); icon.classList.remove('hidden'); }
            } else {
                el.className = 'mt-4 p-4 rounded-xl border-2 border-blue-300 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20 flex items-center gap-3';
            }
            const msgEl = el.querySelector('p.font-bold');
            if (msgEl) msgEl.textContent = message;
            const dbgEl = el.querySelector('p.text-slate-500');
            if (dbgEl) dbgEl.textContent = debug || '';
            if (window.lucide) lucide.createIcons();
        }

        // Expose to Alpine
        window.teacherScanning = false;
    </script>

    <style>
        @keyframes qrLaser {
            0% { top: 0; opacity: 1; }
            50% { top: calc(100% - 2px); opacity: 1; }
            100% { top: 0; opacity: 1; }
        }
        .scanner-laser { animation: qrLaser 1.8s ease-in-out infinite; }
    </style>

    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
    </style>
@endsection