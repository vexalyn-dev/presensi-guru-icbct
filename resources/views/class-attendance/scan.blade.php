@extends('layouts.app')

@section('page-title', 'Presensi Kelas')

@section('content')
    <div class="fade-in space-y-6">

        <!-- Header -->
        <div class="flex items-center gap-4">
            <div
                class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i data-lucide="scan-line" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Presensi Per Kelas</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Scan QR saat masuk dan keluar kelas</p>
            </div>
        </div>

        <!-- Alert -->
        @if(session('success'))
            <div
                class="card p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800">
                <div class="flex items-start gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5"></i>
                    <p class="text-sm font-medium text-green-800 dark:text-green-300 whitespace-pre-line">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div
                class="card p-4 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800">
                <div class="flex items-start gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5"></i>
                    <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Jadwal Mengajar Hari Ini -->
        <div class="card p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-navy-800 dark:text-white">Jadwal Mengajar Hari Ini</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                    </p>
                </div>
            </div>

            @if($todaySchedules->isEmpty())
                <div class="text-center py-8">
                    <i data-lucide="calendar-off" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada jadwal mengajar hari ini</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($todaySchedules as $schedule)
                        @php
                            $key = $schedule->classroom_id . '_' . $schedule->period;
                            $attendance = $todayAttendances[$key] ?? null;
                            $isDone = $attendance && $attendance->check_out_time;
                            $isInProgress = $attendance && $attendance->check_in_time && !$attendance->check_out_time;
                        @endphp
                        <div
                            class="p-4 rounded-xl border-2 transition-all {{ $isDone ? 'bg-green-50 dark:bg-green-900/10 border-green-200 dark:border-green-800' : ($isInProgress ? 'bg-yellow-50 dark:bg-yellow-900/10 border-yellow-200 dark:border-yellow-800' : 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700') }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center shadow-lg">
                                        <span class="text-white dark:text-navy-900 font-bold text-sm">{{ $schedule->period }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-navy-800 dark:text-white">{{ $schedule->classroom->name }}
                                        </h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $schedule->subject?->name ?? 'Mata Pelajaran' }} •
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($isDone)
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-bold">
                                            <i data-lucide="check-circle" class="w-3 h-3"></i>
                                            Selesai
                                        </span>
                                    @elseif($isInProgress)
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full text-xs font-bold">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            Berlangsung
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-full text-xs font-bold">
                                            <i data-lucide="circle" class="w-3 h-3"></i>
                                            Belum
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Scanner -->
        <div class="card p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-gold-100 dark:bg-gold-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="scan-line" class="w-5 h-5 text-gold-600 dark:text-gold-400"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-navy-800 dark:text-white">Scan QR Kelas</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Arahkan kamera ke QR Code di pintu kelas</p>
                </div>
            </div>

            <form action="{{ route('class-attendance.store') }}" method="POST">
                @csrf
                <div class="relative aspect-video bg-slate-900 rounded-2xl overflow-hidden mb-4">
                    <video id="camera-video" class="absolute inset-0 w-full h-full object-cover" autoplay
                        playsinline></video>
                    <div id="camera-overlay"
                        class="absolute inset-0 flex items-center justify-center bg-slate-900/80 text-white">
                        <button type="button" onclick="startCamera()"
                            class="px-6 py-3 bg-gold-500 text-navy-900 font-bold rounded-xl">
                            Mulai Scan
                        </button>
                    </div>
                </div>
                <input type="hidden" name="qr_data" id="qr-data-input">
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script>
        let video = document.getElementById('camera-video');
        let stream = null;
        let scanning = false;

        function startCamera() {
            document.getElementById('camera-overlay').classList.add('hidden');

            navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' }
            }).then(s => {
                stream = s;
                video.srcObject = stream;
                scanning = true;
                scanFrame();
            }).catch(err => {
                alert('Kamera tidak dapat diakses');
            });
        }

        function scanFrame() {
            if (!scanning) return;

            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0);

                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height);

                if (code) {
                    scanning = false;
                    if (stream) stream.getTracks().forEach(t => t.stop());
                    document.getElementById('qr-data-input').value = code.data;
                    document.querySelector('form').submit();
                    return;
                }
            }

            setTimeout(scanFrame, 150);
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>
@endsection