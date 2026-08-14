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
                        <div class="mt-4 flex items-center justify-center gap-2">
                            <div id="qr-status-dot" class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                QR Code berlaku <span id="qr-timer" data-expiration="{{ $qrExpiration ?? 30 }}">{{ $qrExpiration ?? 30 }}</span> detik
                            </p>
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

        /* QR timer color transitions + bounce animation */
        #qr-timer {
            display: inline-block;
            transition: color 0.5s ease, text-shadow 0.5s ease;
            font-variant-numeric: tabular-nums;
            font-weight: 800;
            will-change: color, transform, text-shadow;
        }

        .timer-green {
            color: #10b981;
            text-shadow: 0 0 12px rgba(16,185,129,0.25);
        }

        .timer-yellow {
            color: #f59e0b;
            text-shadow: 0 0 12px rgba(245,158,11,0.25);
        }

        .timer-red {
            color: #ef4444;
            text-shadow: 0 0 16px rgba(239,68,68,0.35);
        }

        /* Deg-deg bounce saat ≤10 */
        @keyframes timerBounce {
            0%, 100% { transform: scale(1);    }
            30%       { transform: scale(1.25); }
            60%       { transform: scale(0.92); }
        }

        @keyframes timerShake {
            0%, 100% { transform: translateX(0) scale(1.08); }
            20%       { transform: translateX(-3px) scale(1.08); }
            40%       { transform: translateX(3px) scale(1.08); }
            60%       { transform: translateX(-2px) scale(1.08); }
            80%       { transform: translateX(2px) scale(1.08); }
        }

        .timer-bounce {
            animation: timerBounce 0.45s cubic-bezier(.36,.07,.19,.97);
        }

        .timer-shake {
            animation: timerShake 0.5s cubic-bezier(.36,.07,.19,.97);
        }

        /* QR status dot */
        #qr-status-dot {
            transition: background-color 0.4s ease;
        }
        .dot-green  { background-color: #10b981; animation: pulse-slow 2s ease-in-out infinite; }
        .dot-yellow { background-color: #f59e0b; animation: pulse-slow 1.2s ease-in-out infinite; }
        /* Merah: berkedip sesuai detikan (1x per detik) */
        .dot-red    { background-color: #ef4444; animation: pulse-tick 1s steps(1, end) infinite; }

        @keyframes pulse-slow {
            0%, 100% { opacity: 1;    transform: scale(1); }
            50%       { opacity: 0.45; transform: scale(0.85); }
        }
        @keyframes pulse-tick {
            0%, 49% { opacity: 1; transform: scale(1.2); }
            50%, 100%{ opacity: 0.2; transform: scale(0.8); }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();

            const timerElement = document.getElementById('qr-timer');
            const dotEl        = document.getElementById('qr-status-dot');
            const qrCodeImage  = document.getElementById('qr-code-img');
            const qrExpiration = Number(timerElement?.dataset.expiration ?? 30);
            let countdown = qrExpiration;
            let lastZone  = '';   // 'green' | 'yellow' | 'red'

            const getZone = (v) => {
                if (v <= 10) return 'red';
                if (v <= 20) return 'yellow';
                return 'green';
            };

            const triggerBounce = () => {
                if (!timerElement) return;
                const cls = countdown <= 5 ? 'timer-shake' : 'timer-bounce';
                timerElement.classList.remove('timer-bounce', 'timer-shake');
                // force reflow so animation restarts
                void timerElement.offsetWidth;
                timerElement.classList.add(cls);
                setTimeout(() => timerElement.classList.remove('timer-bounce', 'timer-shake'), 520);
            };

            const setTimerColor = (value) => {
                if (!timerElement) return;
                const zone = getZone(value);

                // Update timer colour
                timerElement.classList.remove('timer-green', 'timer-yellow', 'timer-red');
                timerElement.classList.add('timer-' + zone);

                // Update dot colour — ikut zona, animasi sesuai kecepatan zona
                if (dotEl) {
                    dotEl.classList.remove('dot-green', 'dot-yellow', 'dot-red');
                    dotEl.classList.add('dot-' + zone);
                }

                // Bounce on zone change
                if (zone !== lastZone) {
                    lastZone = zone;
                    triggerBounce();
                }
            };

            const updateTimer = () => {
                if (!timerElement) return;
                timerElement.textContent = countdown;
                setTimerColor(countdown);
                // Always bounce every tick when ≤10
                if (countdown <= 10 && countdown > 0) {
                    triggerBounce();
                }
            };

            const refreshQrCode = () => {
                if (!qrCodeImage) return;
                fetch('{{ route("teacher.attendance.refresh-qr") }}')
                    .then(r => r.json())
                    .then(data => { if (data.qrCodeUrl) qrCodeImage.src = data.qrCodeUrl; })
                    .catch(() => {});

                countdown = qrExpiration;
                lastZone  = '';
                // Reset dot ke hijau saat QR refresh
                if (dotEl) {
                    dotEl.classList.remove('dot-green', 'dot-yellow', 'dot-red');
                    dotEl.classList.add('dot-green');
                }
                updateTimer();
            };

            // Init
            if (dotEl) dotEl.classList.add('dot-green');
            updateTimer();

            setInterval(() => {
                countdown = Math.max(0, countdown - 1);
                updateTimer();
                if (countdown <= 0) refreshQrCode();
            }, 1000);
        });
    </script>
@endsection