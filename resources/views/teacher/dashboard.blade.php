@extends('layouts.teacher')

@section('page-title', 'Dashboard')

@section('content')
<div class="fade-in space-y-6">
    
    <!-- Welcome Card -->
    <div class="card p-6 bg-gradient-to-br from-navy-800 via-navy-900 to-slate-900 dark:from-gold-400 dark:via-gold-500 dark:to-gold-600 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-white/80 dark:text-navy-900/80">Semangat mengajar hari ini. Anda memiliki {{ $todaySchedules->count() }} jadwal mengajar.</p>
            </div>
            <div class="hidden sm:block">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i data-lucide="school" class="w-10 h-10 text-white dark:text-navy-900"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="card p-3 sm:p-5 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i data-lucide="calendar-check" class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">Hadir Bulan Ini</p>
                    <h3 class="text-xl sm:text-2xl font-bold text-navy-800 dark:text-white mt-0.5">{{ $monthlyStats['hadir'] }}</h3>
                    <p class="text-[10px] text-blue-500 mt-0.5">{{ $monthlyStats['total_days'] }} hari kerja</p>
                </div>
            </div>
        </div>

        <div class="card p-3 sm:p-5 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i data-lucide="clock" class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">Terlambat</p>
                    <h3 class="text-xl sm:text-2xl font-bold text-navy-800 dark:text-white mt-0.5">{{ $monthlyStats['terlambat'] }}</h3>
                    <p class="text-[10px] text-yellow-600 mt-0.5">Perlu perbaikan</p>
                </div>
            </div>
        </div>

        <div class="card p-3 sm:p-5 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i data-lucide="check-circle" class="w-5 h-5 sm:w-6 sm:h-6 text-green-600 dark:text-green-400"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">Izin/Sakit</p>
                    <h3 class="text-xl sm:text-2xl font-bold text-navy-800 dark:text-white mt-0.5">{{ $monthlyStats['izin'] }}</h3>
                    <p class="text-[10px] text-green-500 mt-0.5">Hari</p>
                </div>
            </div>
        </div>

        <div class="card p-3 sm:p-5 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i data-lucide="x-circle" class="w-5 h-5 sm:w-6 sm:h-6 text-red-600 dark:text-red-400"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">Alpha</p>
                    <h3 class="text-xl sm:text-2xl font-bold text-navy-800 dark:text-white mt-0.5">{{ $monthlyStats['alpha'] }}</h3>
                    <p class="text-[10px] text-red-500 mt-0.5">Tanpa keterangan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Schedule -->
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
                $isDone = $todayClassAttendances->where('classroom_id', $schedule->classroom_id)
                    ->where('period', $schedule->period)
                    ->whereNotNull('check_out_time')
                    ->isNotEmpty();
                $isInProgress = $todayClassAttendances->where('classroom_id', $schedule->classroom_id)
                    ->where('period', $schedule->period)
                    ->whereNotNull('check_in_time')
                    ->whereNull('check_out_time')
                    ->isNotEmpty();
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
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                <i data-lucide="clock" class="w-3.5 h-3.5 inline mr-1"></i>
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
                        @elseif($isInProgress)
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full text-xs font-bold">
                            <i data-lucide="clock" class="w-3 h-3"></i>
                            Berlangsung
                        </span>
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

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 gap-3 sm:gap-4">
        <a href="{{ route('teacher.attendance') }}" class="card p-4 sm:p-5 hover:shadow-xl transition-all group border-2 border-slate-200 dark:border-slate-700 hover:border-navy-800 dark:hover:border-gold-400">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i data-lucide="scan-line" class="w-5 h-5 sm:w-6 sm:h-6 text-white dark:text-navy-900"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-xs sm:text-sm font-bold text-navy-800 dark:text-white">Presensi Harian</h4>
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">Scan QR untuk datang & pulang</p>
                    @if($todayAttendance)
                    <div class="mt-1.5 flex flex-col gap-1">
                        @if($todayAttendance->check_in)
                        <span class="px-1.5 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded text-[9px] font-bold truncate">
                            ✓ Masuk: {{ $todayAttendance->check_in }}
                        </span>
                        @endif
                        @if($todayAttendance->check_out)
                        <span class="px-1.5 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded text-[9px] font-bold truncate">
                            ✓ Pulang: {{ $todayAttendance->check_out }}
                        </span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </a>

        <a href="{{ route('teacher.class-attendance') }}" class="card p-4 sm:p-5 hover:shadow-xl transition-all group border-2 border-slate-200 dark:border-slate-700 hover:border-navy-800 dark:hover:border-gold-400">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-gold-400 to-gold-500 dark:from-navy-800 dark:to-navy-900 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i data-lucide="scan" class="w-5 h-5 sm:w-6 sm:h-6 text-navy-900 dark:text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-xs sm:text-sm font-bold text-navy-800 dark:text-white">Presensi Kelas</h4>
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">Scan QR di setiap kelas</p>
                    <div class="mt-1.5">
                        <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded text-[9px] font-bold">
                            {{ $todayClassAttendances->whereNotNull('check_in_time')->count() }}/{{ $todaySchedules->count() }} selesai
                        </span>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<script>
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