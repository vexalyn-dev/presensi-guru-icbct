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
             <span class="px-3 py-1.5 sm:px-4 sm:py-2 rounded-full text-xs sm:text-sm font-bold {{ $statusBadgeClass }}" data-key="status_badge">
                {{ $statusLabel }}
            </span>
        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:gap-4">
                         <div class="p-3 sm:p-4 rounded-2xl border-2 {{ $todayAttendance && $todayAttendance->check_in ? 'bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-green-200 dark:border-green-800' : 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700' }}" data-key="checkin_card">
                             <div class="flex items-center gap-2 sm:gap-3 mb-2">
                                 <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl {{ $todayAttendance && $todayAttendance->check_in ? 'bg-green-500' : 'bg-slate-300 dark:bg-slate-600' }} flex items-center justify-center transition-colors flex-shrink-0" data-key="checkin_icon">
                                     <i data-lucide="clock" class="w-4 h-4 sm:w-5 sm:w-5 text-white"></i>
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
                             <h3 class="text-xl sm:text-2xl font-bold {{ $todayAttendance && $todayAttendance->check_in ? 'text-green-700 dark:text-green-400' : 'text-slate-400' }}" data-key="check_in_time">
                                 @if($todayAttendance && $todayAttendance->check_in)
                                     {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') }}
                                 @elseif($scheduleStart)
                                     {{ \Carbon\Carbon::parse($scheduleStart)->format('H:i') }}
                                 @else
                                     --:--
                                 @endif
                             </h3>
                         </div>

                         <div class="p-3 sm:p-4 rounded-2xl border-2 {{ $todayAttendance && $todayAttendance->check_out ? 'bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border-red-200 dark:border-red-800' : 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700' }}" data-key="checkout_card">
                             <div class="flex items-center gap-2 sm:gap-3 mb-2">
                                 <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl {{ $todayAttendance && $todayAttendance->check_out ? 'bg-red-500' : 'bg-slate-300 dark:bg-slate-600' }} flex items-center justify-center transition-colors flex-shrink-0" data-key="checkout_icon">
                                     <i data-lucide="log-out" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
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
                             <h3 class="text-xl sm:text-2xl font-bold {{ $todayAttendance && $todayAttendance->check_out ? 'text-red-700 dark:text-red-400' : 'text-slate-400' }}" data-key="check_out_time">
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
                            <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada riwayat Presensi</p>
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

    {{-- ══ ATTENDANCE REALTIME MODAL ══ --}}
    {{-- Data config untuk JS — inject via hidden element, bukan di dalam script block --}}
    @php
        $jsHasCheckIn  = (!empty($todayAttendance) && !empty($todayAttendance->check_in))  ? 'true' : 'false';
        $jsHasCheckOut = (!empty($todayAttendance) && !empty($todayAttendance->check_out)) ? 'true' : 'false';
    @endphp
    <div id="at-config"
         data-poll-url="{{ url('/teacher/attendance/poll-status') }}"
         data-has-checkin="{{ $jsHasCheckIn }}"
         data-has-checkout="{{ $jsHasCheckOut }}"
         style="display:none;"></div>

    {{-- Overlay blur gelap, persis desain login --}}
    <div id="at-overlay" style="
        position:fixed; inset:0; z-index:9999;
        background:rgba(10,15,30,0.82);
        backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);
        display:flex; flex-direction:column;
        align-items:center; justify-content:center;
        opacity:0; pointer-events:none;
        transition:opacity 0.25s ease;">

        <div style="
            background:#fff; border-radius:24px;
            padding:36px 40px 32px; width:240px;
            text-align:center; box-shadow:0 24px 56px rgba(0,0,0,0.35);
            position:relative; overflow:hidden;">

            {{-- STATE: LOADING --}}
            <div id="at-state-loading">
                <div style="position:relative;width:72px;height:72px;margin:0 auto 20px;">
                    <div style="position:absolute;inset:0;border-radius:50%;border:4px solid #E2E8F0;border-top-color:#0F172A;animation:atSpin 0.9s linear infinite;"></div>
                    <div style="position:absolute;top:8px;left:8px;right:8px;bottom:8px;border-radius:50%;border:4px solid transparent;border-bottom-color:#FACC15;animation:atSpinRev 0.7s linear infinite;"></div>
                </div>
                <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:16px;">
                    <span style="width:7px;height:7px;background:#0F172A;border-radius:50%;animation:atBounce 0.6s ease-in-out infinite;"></span>
                    <span style="width:7px;height:7px;background:#0F172A;border-radius:50%;animation:atBounce 0.6s ease-in-out 0.15s infinite;"></span>
                    <span style="width:7px;height:7px;background:#0F172A;border-radius:50%;animation:atBounce 0.6s ease-in-out 0.3s infinite;"></span>
                </div>
                <p style="font-size:0.88rem;font-weight:700;color:#0F172A;margin-bottom:3px;">Memproses presensi…</p>
                <p style="font-size:0.75rem;color:#94A3B8;font-weight:400;">Mohon tunggu sebentar</p>
            </div>

            {{-- STATE: SUCCESS CHECK-IN --}}
            <div id="at-state-checkin" style="display:none;">
                <div style="margin-bottom:20px;">
                    <svg viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:72px;height:72px;display:block;margin:0 auto;">
                        <circle cx="36" cy="36" r="32" stroke="#22C55E" stroke-width="4"
                                stroke-dasharray="201" stroke-dashoffset="201"
                                style="animation:atCircleIn 0.45s ease forwards;"/>
                        <path d="M20 36 L31 47 L52 25" stroke="#22C55E" stroke-width="4"
                              stroke-linecap="round" stroke-linejoin="round"
                              stroke-dasharray="60" stroke-dashoffset="60"
                              style="animation:atCheckIn 0.4s ease 0.3s forwards;"/>
                    </svg>
                </div>
                <p style="font-size:0.88rem;font-weight:700;color:#16A34A;margin-bottom:3px;">Presensi Masuk Berhasil!</p>
                <p id="at-checkin-time" style="font-size:0.75rem;color:#94A3B8;font-weight:400;"></p>
            </div>

            {{-- STATE: SUCCESS CHECK-OUT --}}
            <div id="at-state-checkout" style="display:none;">
                <div style="margin-bottom:20px;">
                    <svg viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:72px;height:72px;display:block;margin:0 auto;">
                        <circle cx="36" cy="36" r="32" stroke="#22C55E" stroke-width="4"
                                stroke-dasharray="201" stroke-dashoffset="201"
                                style="animation:atCircleIn 0.45s ease forwards;"/>
                        <path d="M20 36 L31 47 L52 25" stroke="#22C55E" stroke-width="4"
                              stroke-linecap="round" stroke-linejoin="round"
                              stroke-dasharray="60" stroke-dashoffset="60"
                              style="animation:atCheckIn 0.4s ease 0.3s forwards;"/>
                    </svg>
                </div>
                <p style="font-size:0.88rem;font-weight:700;color:#16A34A;margin-bottom:3px;">Presensi Pulang Berhasil!</p>
                <p id="at-checkout-time" style="font-size:0.75rem;color:#94A3B8;font-weight:400;"></p>
            </div>

            {{-- STATE: ALREADY SCANNED --}}
            <div id="at-state-already" style="display:none;">
                <div style="margin-bottom:20px;">
                    <svg viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg"
                         style="width:72px;height:72px;display:block;margin:0 auto;animation:atXIn 0.35s ease forwards;">
                        <circle cx="36" cy="36" r="32" stroke="#F59E0B" stroke-width="4"/>
                        <path d="M36 24 L36 40" stroke="#F59E0B" stroke-width="4" stroke-linecap="round"/>
                        <circle cx="36" cy="48" r="2.5" fill="#F59E0B"/>
                    </svg>
                </div>
                <p style="font-size:0.88rem;font-weight:700;color:#D97706;margin-bottom:3px;">QR Sudah Tercatat</p>
                <p id="at-already-info" style="font-size:0.75rem;color:#94A3B8;font-weight:400;">Presensi hari ini sudah lengkap</p>
            </div>

            {{-- STATE: FAILED --}}
            <div id="at-state-failed" style="display:none;">
                <div style="margin-bottom:20px;">
                    <svg viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg"
                         style="width:72px;height:72px;display:block;margin:0 auto;animation:atXIn 0.35s ease forwards;">
                        <circle cx="36" cy="36" r="32" stroke="#EF4444" stroke-width="4"/>
                        <path d="M24 24 L48 48 M48 24 L24 48" stroke="#EF4444" stroke-width="4" stroke-linecap="round"/>
                    </svg>
                </div>
                <p style="font-size:0.88rem;font-weight:700;color:#DC2626;margin-bottom:3px;" id="at-fail-title">Gagal!</p>
                <p id="at-fail-sub" style="font-size:0.75rem;color:#94A3B8;font-weight:400;">Silakan coba lagi</p>
            </div>

        </div>
    </div>

    <style>
        @keyframes atSpin    { to { transform: rotate(360deg); } }
        @keyframes atSpinRev { to { transform: rotate(-360deg); } }
        @keyframes atBounce  { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
        @keyframes atCheckIn {
            0%   { stroke-dashoffset: 60; opacity: 0; }
            100% { stroke-dashoffset: 0;  opacity: 1; }
        }
        @keyframes atCircleIn {
            0%   { stroke-dashoffset: 180; }
            100% { stroke-dashoffset: 0; }
        }
        @keyframes atXIn {
            0%   { opacity: 0; transform: scale(0.5); }
            60%  { transform: scale(1.15); }
            100% { opacity: 1; transform: scale(1); }
        }
        #at-overlay.show { opacity: 1 !important; pointer-events: all !important; }
    </style>

    <script>
    (function () {
        var cfg     = document.getElementById('at-config');
        var pollUrl = cfg ? cfg.dataset.pollUrl : '/teacher/attendance/poll-status';
        var overlay = document.getElementById('at-overlay');

        var modalVisible = false;
        var closeTimer   = null;

        // Polling interval — lebih pendek untuk responsif
        var POLL_MS    = 300; // 300ms cukup responsif tanpa overwhelm server
        var POLL_FAST  = 100; // setelah scan, poll cepat selama beberapa detik
        var fastPollCount = 0;
        var MAX_FAST_POLLS = 30; // 30 x 100ms = 3 detik polling cepat setelah scan

        var prevCheckInTs  = null;
        var prevCheckOutTs = null;
        var initialized    = false;

        // Inisialisasi lastKnownState dari kondisi presensi saat halaman dimuat
        var _hasCI = cfg && cfg.dataset.hasCheckin === 'true';
        var _hasCO = cfg && cfg.dataset.hasCheckout === 'true';
        var lastKnownState = _hasCI && _hasCO ? 'both'
                           : _hasCI            ? 'checkin'
                           : _hasCO            ? 'checkout'
                           :                     'none';

        // Waktu server terakhir diketahui (dari poll response) untuk menghindari clock skew
        var serverTimeOffset = 0; // selisih server - browser dalam detik
        var serverTimeKnown  = false;

        function showState(name) {
            ['at-state-loading','at-state-checkin','at-state-checkout','at-state-already','at-state-failed']
                .forEach(function(id) {
                    var el = document.getElementById(id);
                    if (el) el.style.display = 'none';
                });
            var el = document.getElementById('at-state-' + name);
            if (el) el.style.display = 'block';
        }

        function restartSvgAnim(stateId) {
            var svgEl = document.querySelector('#' + stateId + ' svg');
            if (svgEl) {
                var clone = svgEl.cloneNode(true);
                svgEl.parentNode.replaceChild(clone, svgEl);
            }
        }

        function closeModal(callback) {
            if (closeTimer) { clearTimeout(closeTimer); closeTimer = null; }
            overlay.classList.remove('show');
            modalVisible = false;
            if (callback) setTimeout(callback, 350);
        }

        // Tampilkan loading spinner segera, lalu transisi ke state final
        function showOverlayLoading() {
            if (closeTimer) { clearTimeout(closeTimer); closeTimer = null; }
            showState('loading');
            overlay.classList.add('show');
            modalVisible = true;
        }

        function showOverlay(state, timeStr, extra) {
            if (closeTimer) { clearTimeout(closeTimer); closeTimer = null; }

            // Tampilkan loading dulu, baru state final setelah brief delay
            // agar transisi loading → ceklis terasa smooth
            showState('loading');
            overlay.classList.add('show');
            modalVisible = true;

            // Transisi ke state sebenarnya dengan sedikit delay (200ms)
            // sehingga loading spinner sempat terlihat
            setTimeout(function() {
                showState(state);

                if (state === 'checkin') {
                    var el = document.getElementById('at-checkin-time');
                    if (el) el.textContent = timeStr ? 'Jam masuk: ' + timeStr + ' WIB' : '';
                    restartSvgAnim('at-state-checkin');
                } else if (state === 'checkout') {
                    var el = document.getElementById('at-checkout-time');
                    if (el) el.textContent = timeStr ? 'Jam pulang: ' + timeStr + ' WIB' : '';
                    restartSvgAnim('at-state-checkout');
                } else if (state === 'already') {
                    var infoEl = document.getElementById('at-already-info');
                    if (infoEl && extra) {
                        var msg = '';
                        if (extra.check_in && extra.check_out) {
                            msg = 'Sudah scan masuk: ' + extra.check_in + ' WIB\ndan pulang: ' + extra.check_out + ' WIB';
                        } else if (extra.check_in) {
                            msg = 'Sudah scan masuk: ' + extra.check_in + ' WIB';
                        } else if (extra.check_out) {
                            msg = 'Sudah scan keluar: ' + extra.check_out + ' WIB';
                        } else {
                            msg = 'Presensi hari ini sudah lengkap';
                        }
                        infoEl.textContent = msg;
                    }
                    restartSvgAnim('at-state-already');
                } else if (state === 'failed') {
                    var titleEl = document.getElementById('at-fail-title');
                    var subEl   = document.getElementById('at-fail-sub');
                    if (titleEl && extra && extra.message) titleEl.textContent = extra.message;
                    if (subEl) subEl.textContent = extra && extra.retry ? 'Silakan coba lagi' : 'Hubungi operator jika masalah berlanjut';
                    restartSvgAnim('at-state-failed');
                }
            }, 200);

            // Tutup otomatis setelah 3.5 detik (loading 200ms + state 3300ms)
            closeTimer = setTimeout(function () {
                closeModal();
            }, 3500);
        }

        var pollTimer = null;

        function schedulePoll(fast) {
            if (pollTimer) clearTimeout(pollTimer);
            var delay = (fast || fastPollCount > 0) ? POLL_FAST : POLL_MS;
            if (fastPollCount > 0) fastPollCount--;
            pollTimer = setTimeout(function() { poll(); }, delay);
        }

        // Mulai mode fast polling (setelah modal muncul, polling cepat untuk update statusbar)
        function startFastPoll() {
            fastPollCount = MAX_FAST_POLLS;
        }

        function updateStatusBar(data) {
            var ciEl = document.querySelector('[data-key="check_in_time"]');
            if (ciEl && data.check_in) {
                ciEl.textContent = data.check_in;
                ciEl.classList.remove('text-slate-400');
                ciEl.classList.add('text-green-700', 'dark:text-green-400');
            }
            var coEl = document.querySelector('[data-key="check_out_time"]');
            if (coEl && data.check_out) {
                coEl.textContent = data.check_out;
                coEl.classList.remove('text-slate-400');
                coEl.classList.add('text-red-700', 'dark:text-red-400');
            }
            var badgeEl = document.querySelector('[data-key="status_badge"]');
            if (badgeEl && data.status) {
                var label = data.status === 'Tepat Waktu' ? 'Hadir' : data.status;
                badgeEl.textContent = label;
                badgeEl.className = 'px-3 py-1.5 sm:px-4 sm:py-2 rounded-full text-xs sm:text-sm font-bold';
                if (data.status === 'Hadir' || data.status === 'Tepat Waktu') {
                    badgeEl.classList.add('bg-green-100', 'text-green-700', 'dark:bg-green-900/30', 'dark:text-green-400');
                } else if (data.status === 'Terlambat') {
                    badgeEl.classList.add('bg-yellow-100', 'text-yellow-700', 'dark:bg-yellow-900/30', 'dark:text-yellow-400');
                } else if (data.status === 'Alpha') {
                    badgeEl.classList.add('bg-red-100', 'text-red-700', 'dark:bg-red-900/30', 'dark:text-red-400');
                } else if (data.status === 'Izin' || data.status === 'Sakit') {
                    badgeEl.classList.add('bg-blue-100', 'text-blue-700', 'dark:bg-blue-900/30', 'dark:text-blue-400');
                }
            }
            var ciCard = document.querySelector('[data-key="checkin_card"]');
            if (ciCard && data.check_in) {
                ciCard.className = 'p-3 sm:p-4 rounded-2xl border-2 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-green-200 dark:border-green-800';
            }
            var coCard = document.querySelector('[data-key="checkout_card"]');
            if (coCard && data.check_out) {
                coCard.className = 'p-3 sm:p-4 rounded-2xl border-2 bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border-red-200 dark:border-red-800';
            }
            var ciIcon = document.querySelector('[data-key="checkin_icon"]');
            if (ciIcon && data.check_in) {
                ciIcon.className = 'w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-green-500 flex items-center justify-center transition-colors flex-shrink-0';
            }
            var coIcon = document.querySelector('[data-key="checkout_icon"]');
            if (coIcon && data.check_out) {
                coIcon.className = 'w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-red-500 flex items-center justify-center transition-colors flex-shrink-0';
            }
        }

        function poll() {
            var fetchStart = Math.floor(Date.now() / 1000);

            fetch(pollUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin',
            })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data) { schedulePoll(); return; }

                // Hitung server time offset dari timestamp check_in/out terbaru
                // untuk menghindari clock skew antara browser dan server
                var ciTs = data.check_in_ts  || null;
                var coTs = data.check_out_ts || null;

                if (!initialized) {
                    prevCheckInTs  = ciTs;
                    prevCheckOutTs = coTs;
                    initialized    = true;
                    updateStatusBar(data);
                    schedulePoll();
                    return;
                }

                if (modalVisible) { schedulePoll(); return; }

                // GRACE_SEC diperbesar ke 600 detik (10 menit) untuk menghindari
                // clock skew antara browser dan server timezone
                var GRACE_SEC = 600;
                var now = fetchStart; // gunakan waktu saat fetch dimulai, bukan sekarang

                var isNewCheckIn  = ciTs && ciTs !== prevCheckInTs && (now - ciTs) <= GRACE_SEC;
                var isNewCheckOut = coTs && coTs !== prevCheckOutTs && (now - coTs) <= GRACE_SEC;

                // Check-in BARU
                if (isNewCheckIn) {
                    prevCheckInTs  = ciTs;
                    lastKnownState = coTs ? 'both' : 'checkin';
                    updateStatusBar(data);
                    showOverlay('checkin', data.check_in);
                    startFastPoll();
                    schedulePoll();
                    return;
                }

                // Check-out BARU
                if (isNewCheckOut) {
                    prevCheckOutTs = coTs;
                    lastKnownState = 'both';
                    updateStatusBar(data);
                    showOverlay('checkout', data.check_out);
                    startFastPoll();
                    schedulePoll();
                    return;
                }

                prevCheckInTs  = ciTs;
                prevCheckOutTs = coTs;

                // State change detection untuk "QR Sudah Tercatat"
                // Hanya trigger jika state benar-benar berubah DAN belum pernah dinotifikasi
                var newState = 'none';
                if (ciTs && coTs) newState = 'both';
                else if (ciTs) newState = 'checkin';
                else if (coTs) newState = 'checkout';

                if (newState !== 'none' && newState !== lastKnownState && !modalVisible) {
                    lastKnownState = newState;
                    updateStatusBar(data);
                    // "already" hanya tampil jika state sudah 'both' (keduanya sudah scan)
                    // Untuk state 'checkin' atau 'checkout' saja, tampilkan notifikasi berhasil
                    if (newState === 'both') {
                        showOverlay('already', null, { check_in: data.check_in, check_out: data.check_out });
                    } else if (newState === 'checkin') {
                        showOverlay('checkin', data.check_in);
                    } else if (newState === 'checkout') {
                        showOverlay('checkout', data.check_out);
                    }
                    schedulePoll();
                    return;
                }
                lastKnownState = newState;

                schedulePoll();
            })
            .catch(function () { schedulePoll(); });
        }

        schedulePoll();

        // Klik overlay tutup manual
        overlay.addEventListener('click', function () {
            closeModal();
        });
    })();
    </script>
@endsection