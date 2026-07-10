@extends('layouts.teacher')

@section('page-title', 'Presensi Kelas')

@section('content')
<div class="fade-in space-y-6">
    
    <!-- Header -->
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
            <i data-lucide="scan" class="w-6 h-6 text-white dark:text-navy-900"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Presensi Kelas</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Scan QR Code di setiap kelas yang Anda ajar</p>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="card p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800">
        <div class="flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
            <p class="text-sm font-medium text-green-800 dark:text-green-300 whitespace-pre-line">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="card p-4 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800">
        <div class="flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
            <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Today's Classes -->
        <div class="lg:col-span-2">
            <div class="card p-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                            <i data-lucide="calendar" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-navy-800 dark:text-white">Jadwal Mengajar Hari Ini</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-xs font-bold">
                        {{ $todaySchedules->count() }} Kelas
                    </span>
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
                        $attendance = $todayClassAttendances[$key] ?? null;
                        $isDone = $attendance && $attendance->check_out_time;
                        $isInProgress = $attendance && $attendance->check_in_time && !$attendance->check_out_time;
                    @endphp
                    <div class="p-4 rounded-xl border-2 transition-all {{ $isDone ? 'bg-green-50 dark:bg-green-900/10 border-green-200 dark:border-green-800' : ($isInProgress ? 'bg-yellow-50 dark:bg-yellow-900/10 border-yellow-200 dark:border-yellow-800' : 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700') }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center shadow-lg">
                                    <span class="text-white dark:text-navy-900 font-bold text-sm">{{ $schedule->period }}</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-navy-800 dark:text-white">{{ $schedule->classroom->name }}</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $schedule->subject?->name ?? '-' }}</p>
                                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($isDone)
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-bold">
                                    <i data-lucide="check-circle" class="w-3 h-3"></i>
                                    Selesai
                                </span>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">
                                    {{ $attendance->check_in_time }} - {{ $attendance->check_out_time }}
                                </p>
                                @elseif($isInProgress)
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full text-xs font-bold">
                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                    Berlangsung
                                </span>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">
                                    Masuk: {{ $attendance->check_in_time }}
                                </p>
                                @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-full text-xs font-bold">
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
        </div>

        <!-- QR Scanner -->
        <div class="space-y-4">
            <div class="card p-6">
                <h3 class="text-sm font-bold text-navy-800 dark:text-white mb-4 flex items-center gap-2">
                    <i data-lucide="camera" class="w-4 h-4"></i>
                    Scan QR Kelas
                </h3>
                
                <div class="relative aspect-square bg-slate-900 rounded-2xl overflow-hidden mb-4">
                    <video id="class-video" class="absolute inset-0 w-full h-full object-cover" autoplay playsinline></video>
                    <div id="class-overlay" class="absolute inset-0 flex items-center justify-center bg-slate-900/80 text-white">
                        <div class="text-center px-4">
                            <i data-lucide="scan" class="w-10 h-10 mx-auto mb-3 opacity-50"></i>
                            <p class="text-xs mb-4">Arahkan kamera ke QR Code di pintu kelas</p>
                            <button type="button" onclick="startClassScan()" class="px-4 py-2 bg-gold-500 text-navy-900 font-bold rounded-lg text-sm">
                                Mulai Scan
                            </button>
                        </div>
                    </div>
                    <div id="class-scan-frame" class="absolute inset-4 border-2 border-gold-400 rounded-lg opacity-0 pointer-events-none transition-opacity"></div>
                </div>

                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex items-start gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5"></i>
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            Scan pertama untuk <strong>MASUK</strong> kelas, scan kedua untuk <strong>KELUAR</strong> kelas.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Form for Submission -->
<form id="class-attendance-form" method="POST" action="{{ route('teacher.class-attendance.store') }}" style="display: none;">
    @csrf
    <input type="hidden" name="qr_data" id="class-qr-data-input">
</form>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    let classVideo = document.getElementById('class-video');
    let classStream = null;
    let classScanning = false;

    function startClassScan() {
        document.getElementById('class-overlay').classList.add('hidden');
        document.getElementById('class-scan-frame').style.opacity = '1';
        
        navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'environment' } 
        }).then(s => {
            classStream = s;
            classVideo.srcObject = classStream;
            classScanning = true;
            scanClassFrame();
        }).catch(err => {
            alert('Kamera tidak dapat diakses: ' + err.message);
            stopClassScan();
        });
    }

    function scanClassFrame() {
        if (!classScanning) return;
        
        if (classVideo.readyState === classVideo.HAVE_ENOUGH_DATA) {
            const canvas = document.createElement('canvas');
            canvas.width = classVideo.videoWidth;
            canvas.height = classVideo.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(classVideo, 0, 0);
            
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            
            if (code) {
                classScanning = false;
                stopClassScan();
                document.getElementById('class-qr-data-input').value = code.data;
                document.getElementById('class-attendance-form').submit();
                return;
            }
        }
        
        setTimeout(scanClassFrame, 150);
    }

    function stopClassScan() {
        if (classStream) {
            classStream.getTracks().forEach(t => t.stop());
            classStream = null;
        }
        classScanning = false;
        document.getElementById('class-overlay').classList.remove('hidden');
        document.getElementById('class-scan-frame').style.opacity = '0';
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
    });
</script>

<style>
    .fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection