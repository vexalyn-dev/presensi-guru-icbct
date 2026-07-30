@extends('layouts.teacher')

@section('page-title', 'Dashboard')

@section('content')
<div class="fade-in space-y-3 sm:space-y-6 w-full max-w-full px-3 sm:px-0">
    
    <!-- Welcome Card -->
    <div class="card p-4 sm:p-6 bg-gradient-to-br from-navy-800 via-navy-900 to-slate-900 dark:from-gold-400 dark:to-gold-400 text-white overflow-hidden">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0 flex-1">
                <h2 class="text-base sm:text-2xl font-bold mb-1 sm:mb-2 truncate">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-white/80 dark:text-navy-900/80 text-xs sm:text-base leading-snug">
                    Semangat mengajar hari ini. Anda memiliki 
                    <span class="font-bold">{{ $todaySchedules->count() }}</span> jadwal mengajar.
                </p>
            </div>
            <div class="flex-shrink-0">
                <div class="w-12 h-12 sm:w-20 sm:h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i data-lucide="school" class="w-6 h-6 sm:w-10 sm:h-10 text-white dark:text-navy-900"></i>
                </div>
            </div>
        </div>
    </div>

    @php
        $reminderMsgs = [];
        foreach ($todaySchedules as $s) {
            $att = $todayClassAttendances->first(function ($att) use ($s) {
                if ($att->teaching_schedule_id == $s->id) {
                    return true;
                }
                $classMatch = ($att->classroom_id == $s->classroom_id) || ($att->selected_classroom_id == $s->classroom_id);
                return $classMatch && ($att->subject_id == $s->subject_id) && ($att->period == $s->period);
            });

            $cn = $s->classroom->code ? strtoupper(str_replace('-', ' ', $s->classroom->code)) : ($s->classroom->name ?? '-');
            
            $warningIcon = '<svg class="w-3.5 h-3.5 text-amber-600 dark:text-gold-400 inline-block mr-1 flex-shrink-0 align-text-bottom" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
            
            if (!$att || !$att->check_in_time) {
                $reminderMsgs[] = '<span class="inline-flex items-center">' . $warningIcon . 'Anda belum scan masuk kelas ' . e($cn) . '</span>';
            } elseif ($att->check_in_time && !$att->check_out_time) {
                $reminderMsgs[] = '<span class="inline-flex items-center">' . $warningIcon . 'Anda belum scan keluar kelas ' . e($cn) . '</span>';
            }
        }
    @endphp

    @if(!empty($reminderMsgs))
        <!-- Marquee Card - Mobile Optimized -->
        <div class="relative overflow-hidden rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/80 p-1.5 sm:p-2 flex items-center gap-2 shadow-sm">
            <div class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-navy-800 text-white flex-shrink-0 relative">
                <span class="absolute inline-flex h-full w-full rounded-lg bg-navy-800 opacity-20 animate-ping"></span>
                <i data-lucide="bell" class="w-3.5 h-3.5 sm:w-4 sm:h-4 relative z-10 text-white"></i>
            </div>
            
            <div class="relative flex-1 min-w-0 overflow-hidden py-0.5 marquee-container">
                <div class="marquee-track flex gap-6 sm:gap-8 whitespace-nowrap text-[10px] sm:text-xs font-bold text-black dark:text-white tracking-wide uppercase">
                    <span>{!! implode(' &nbsp; • &nbsp; ', $reminderMsgs) !!}</span>
                    <span>{!! implode(' &nbsp; • &nbsp; ', $reminderMsgs) !!}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Cards - 2 columns on mobile, 4 on desktop -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-4">
        <div class="card p-3 sm:p-5 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-2 sm:gap-4">
                <div class="w-9 h-9 sm:w-12 sm:h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i data-lucide="calendar-check" class="w-4 h-4 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate leading-tight">Hadir Bulan Ini</p>
                    <h3 class="text-base sm:text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['hadir'] }}</h3>
                    <p class="text-[10px] sm:text-[10px] text-blue-500 leading-tight">Bulan ini</p>
                </div>
            </div>
        </div>

        <div class="card p-3 sm:p-5 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-2 sm:gap-4">
                <div class="w-9 h-9 sm:w-12 sm:h-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i data-lucide="clock" class="w-4 h-4 sm:w-6 sm:h-6 text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate leading-tight">Terlambat</p>
                    <h3 class="text-base sm:text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['terlambat'] }}</h3>
                    <p class="text-[10px] sm:text-[10px] text-yellow-600 leading-tight">Perlu perbaikan</p>
                </div>
            </div>
        </div>

        <div class="card p-3 sm:p-5 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-2 sm:gap-4">
                <div class="w-9 h-9 sm:w-12 sm:h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i data-lucide="file-text" class="w-4 h-4 sm:w-6 sm:h-6 text-green-600 dark:text-green-400"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate leading-tight">Izin/Sakit</p>
                    <h3 class="text-base sm:text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['izin'] }}</h3>
                    <p class="text-[10px] sm:text-[10px] text-green-500 leading-tight">Disetujui</p>
                </div>
            </div>
        </div>

        <div class="card p-3 sm:p-5 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-2 sm:gap-4">
                <div class="w-9 h-9 sm:w-12 sm:h-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i data-lucide="x-circle" class="w-4 h-4 sm:w-6 sm:h-6 text-red-600 dark:text-red-400"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate leading-tight">Alpha</p>
                    <h3 class="text-base sm:text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['alpha'] }}</h3>
                    <p class="text-[10px] sm:text-[10px] text-red-500 leading-tight">Tanpa keterangan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Presensi Harian -->
    <div class="card p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="scan-line" class="w-4 h-4 sm:w-5 sm:h-5 text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-navy-800 dark:text-white">Presensi Harian</h3>
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
            </div>
            <a href="{{ route('teacher.attendance') }}" class="text-[10px] sm:text-xs font-semibold text-navy-800 dark:text-gold-400 hover:underline">Detail</a>
        </div>

        @if($todayAttendance)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Check In -->
                <div class="p-3 sm:p-4 rounded-2xl border-2
                    {{ $todayAttendance->check_in
                        ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800'
                        : 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700' }}">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 rounded-lg {{ $todayAttendance->check_in ? 'bg-green-500' : 'bg-slate-300 dark:bg-slate-600' }} flex items-center justify-center">
                            <i data-lucide="log-in" class="w-3.5 h-3.5 text-white"></i>
                        </div>
                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">Jam Masuk</p>
                    </div>
                    <p class="text-lg sm:text-xl font-bold {{ $todayAttendance->check_in ? 'text-green-700 dark:text-green-400' : 'text-slate-400' }}">
                        {{ $todayAttendance->check_in ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') : '--:--' }}
                    </p>
                    @if($todayAttendance->status)
                    @php
                        $statusBadge = match($todayAttendance->status) {
                            'Tepat Waktu', 'Hadir' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                            'Terlambat'            => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                            'Alpha'                => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                            'Izin', 'Sakit'        => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                            default                => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400',
                        };
                        $statusLabel = $todayAttendance->status === 'Tepat Waktu' ? 'Hadir' : $todayAttendance->status;
                    @endphp
                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-bold {{ $statusBadge }}">
                        {{ $statusLabel }}
                    </span>
                    @endif
                </div>
                <!-- Check Out -->
                <div class="p-3 sm:p-4 rounded-2xl border-2
                    {{ $todayAttendance->check_out
                        ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800'
                        : 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700' }}">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 rounded-lg {{ $todayAttendance->check_out ? 'bg-green-500' : 'bg-slate-300 dark:bg-slate-600' }} flex items-center justify-center">
                            <i data-lucide="log-out" class="w-3.5 h-3.5 text-white"></i>
                        </div>
                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">Jam Pulang</p>
                    </div>
                    <p class="text-lg sm:text-xl font-bold {{ $todayAttendance->check_out ? 'text-green-700 dark:text-green-400' : 'text-slate-400' }}">
                        {{ $todayAttendance->check_out ? \Carbon\Carbon::parse($todayAttendance->check_out)->format('H:i') : '--:--' }}
                    </p>
                    @if($todayAttendance->check_out_status)
                    @php
                        $checkoutBadge = match($todayAttendance->check_out_status) {
                            'Tepat Waktu' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                            default       => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                        };
                    @endphp
                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-bold {{ $checkoutBadge }}">
                        {{ $todayAttendance->check_out_status === 'Tepat Waktu' ? 'Tepat Waktu' : ucfirst($todayAttendance->check_out_status) }}
                    </span>
                    @endif
                </div>
            </div>
        @else
            <div class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-2xl border border-yellow-200 dark:border-yellow-800">
                <div class="w-9 h-9 sm:w-10 sm:h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="alert-circle" class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div>
                    <p class="text-xs sm:text-sm font-bold text-yellow-800 dark:text-yellow-300">Belum Presensi Hari Ini</p>
                    <p class="text-[10px] sm:text-xs text-yellow-700 dark:text-yellow-400 mt-0.5">Scan QR di meja operator untuk presensi masuk</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Jadwal Mengajar Hari Ini -->
    <div class="card p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4 sm:mb-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar" class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-navy-800 dark:text-white">Jadwal Mengajar</h3>
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
            </div>
            <span class="px-2.5 py-1 sm:px-3 sm:py-1.5 bg-navy-100 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 rounded-full text-[10px] sm:text-xs font-bold">
                {{ $todaySchedules->count() }} Kelas
            </span>
        </div>

        @if($todaySchedules->count() > 0)
            @php
                $totalClasses = $todaySchedules->count();
                $completedClasses = 0;
                $now = now();
                
                foreach ($todaySchedules as $s) {
                    $att = $todayClassAttendances->first(function ($att) use ($s) {
                        if ($att->teaching_schedule_id == $s->id) {
                            return true;
                        }
                        $classMatch = ($att->classroom_id == $s->classroom_id) || ($att->selected_classroom_id == $s->classroom_id);
                        return $classMatch && ($att->subject_id == $s->subject_id) && ($att->period == $s->period);
                    });
                    
                    $endTime = \Carbon\Carbon::parse($s->end_time);
                    $graceEnd = $endTime->copy()->addMinutes(3);
                    $isEnded = $now->greaterThan($graceEnd);
                    
                    if ($att && $att->check_in_time && $att->check_out_time) {
                        $completedClasses++;
                    }
                }
                
                $progress = $totalClasses > 0 ? ($completedClasses / $totalClasses) * 100 : 0;
            @endphp
            
            <!-- Progress Bar -->
            <div class="mb-4 sm:mb-5">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[10px] sm:text-xs font-semibold text-slate-600 dark:text-slate-400">Progress Mengajar</p>
                    <p class="text-[10px] sm:text-xs font-bold text-navy-800 dark:text-white">{{ round($progress) }}%</p>
                </div>
                <div class="w-full h-2.5 sm:h-3 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-400 rounded-full transition-all duration-500" 
                          style="width: {{ $progress }}%"></div>
                </div>
                <div class="flex items-center justify-between mt-2 text-[10px] sm:text-xs">
                    <span class="text-green-600 dark:text-green-400 font-semibold flex items-center gap-1">
                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0Z" /></svg>
                        {{ $completedClasses }} Selesai
                    </span>
                    <span class="text-slate-600 dark:text-slate-400 font-semibold flex items-center gap-1">
                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5" /></svg>
                        {{ $totalClasses - $completedClasses }} Belum
                    </span>
                </div>
            </div>

            <!-- List Jadwal -->
            <div class="space-y-2.5 sm:space-y-3">
                @foreach($todaySchedules as $schedule)
                    @php
                        $att = $todayClassAttendances->first(function ($att) use ($schedule) {
                            if ($att->teaching_schedule_id == $schedule->id) {
                                return true;
                            }
                            $classMatch = ($att->classroom_id == $schedule->classroom_id) || ($att->selected_classroom_id == $schedule->classroom_id);
                            return $classMatch && ($att->subject_id == $schedule->subject_id) && ($att->period == $schedule->period);
                        });

                        $startTime = \Carbon\Carbon::parse($schedule->start_time);
                        $endTime = \Carbon\Carbon::parse($schedule->end_time);
                        $graceEnd = $endTime->copy()->addMinutes(3);
                        $isEnded = $now->greaterThan($graceEnd);

                        if ($att && $att->check_in_time) {
                            if ($att->status === 'Terlambat') {
                                $badgeText = 'Terlambat';
                                $theme = 'yellow';
                            } else {
                                $badgeText = 'Hadir';
                                $theme = 'green';
                            }
                        } elseif ($isEnded) {
                            $badgeText = 'Berakhir';
                            $theme = 'red';
                        } elseif ($now->greaterThanOrEqualTo($startTime)) {
                            $badgeText = 'Berlangsung';
                            $theme = 'blue';
                        } else {
                            $badgeText = 'Belum';
                            $theme = 'slate';
                        }
                    @endphp

                    <div class="p-3 sm:p-4 rounded-xl border-2 transition-all
                        {{ $theme === 'red' ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : '' }}
                        {{ $theme === 'green' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : '' }}
                        {{ $theme === 'yellow' ? 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800' : '' }}
                        {{ $theme === 'blue' ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' : '' }}
                        {{ $theme === 'slate' ? 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700' : '' }}">
                        
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0 flex-1">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                    {{ $theme === 'red' ? 'bg-red-100 dark:bg-red-900/30' : '' }}
                                    {{ $theme === 'green' ? 'bg-green-100 dark:bg-green-900/30' : '' }}
                                    {{ $theme === 'yellow' ? 'bg-yellow-100 dark:bg-yellow-900/30' : '' }}
                                    {{ $theme === 'blue' ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}
                                    {{ $theme === 'slate' ? 'bg-slate-200 dark:bg-slate-600' : '' }}">
                                    @if($theme === 'red')
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    @elseif($theme === 'green')
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0Z" /></svg>
                                    @elseif($theme === 'yellow')
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0Z" /></svg>
                                    @elseif($theme === 'blue')
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0Z" /></svg>
                                    @else
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9" /></svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs sm:text-sm font-bold text-navy-800 dark:text-white truncate">
                                        {{ $schedule->classroom->code
                                            ? strtoupper(str_replace('-', ' ', $schedule->classroom->code))
                                            : ($schedule->classroom->name ?? '-') }}
                                    </p>
                                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">
                                        {{ $schedule->subject->name ?? '-' }} • Jam ke-{{ $schedule->period }}
                                    </p>
                                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">
                                        {{ $startTime->format('H:i') }} - {{ $endTime->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                            
                            <span class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold flex-shrink-0 text-center
                                {{ $theme === 'red' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : '' }}
                                {{ $theme === 'green' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : '' }}
                                {{ $theme === 'yellow' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' : '' }}
                                {{ $theme === 'blue' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : '' }}
                                {{ $theme === 'slate' ? 'bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-400' : '' }}">
                                {{ $badgeText }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 sm:py-12">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="calendar-x" class="w-8 h-8 sm:w-10 sm:h-10 text-slate-400 dark:text-slate-500"></i>
                </div>
                <p class="text-xs sm:text-sm font-semibold text-navy-800 dark:text-white mb-1">Tidak ada jadwal mengajar hari ini</p>
                <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400">Nikmati hari libur Anda!</p>
            </div>
        @endif
    </div>

    <!-- Jadwal Kerja -->
    <div class="card p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4 sm:mb-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="briefcase" class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600 dark:text-purple-400"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-navy-800 dark:text-white">Jadwal Kerja</h3>
                    <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400">Jam kerja mingguan Anda</p>
                </div>
            </div>
            <span class="px-2.5 py-1 sm:px-3 sm:py-1.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full text-[10px] sm:text-xs font-bold">
                {{ $workSchedule->count() }} Hari
            </span>
        </div>

        @if($workSchedule->count() > 0)
            <div class="space-y-2.5 sm:space-y-3">
                @foreach($workSchedule as $work)
                @php
                    $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    $isToday = $work->day_of_week === now()->dayOfWeek;
                @endphp
                <div class="p-3 sm:p-4 rounded-xl border-2 transition-all
                    {{ $isToday 
                        ? 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-800' 
                        : 'bg-slate-50 dark:bg-slate-700/30 border-slate-200 dark:border-slate-700' }}">
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5 sm:gap-3">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                {{ $isToday 
                                    ? 'bg-purple-100 dark:bg-purple-900/30' 
                                    : 'bg-slate-200 dark:bg-slate-600' }}">
                                <i data-lucide="calendar" class="w-4 h-4 sm:w-5 sm:h-5 {{ $isToday ? 'text-purple-600 dark:text-purple-400' : 'text-slate-500 dark:text-slate-400' }}"></i>
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm font-bold text-navy-800 dark:text-white">
                                    {{ $dayNames[$work->day_of_week] }}
                                    @if($isToday)
                                    <span class="ml-1.5 sm:ml-2 px-1.5 py-0.5 sm:px-2 bg-purple-500 text-white rounded-full text-[9px] sm:text-[10px]">Hari Ini</span>
                                    @endif
                                </p>
                                <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400">
                                    {{ \Carbon\Carbon::parse($work->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($work->end_time)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            @php
                                $dayMinutes = \Carbon\Carbon::parse($work->start_time)->diffInMinutes(\Carbon\Carbon::parse($work->end_time));
                                $dayHours = $dayMinutes / 60;
                                $dayHoursDisplay = (floor($dayHours) == $dayHours) ? number_format($dayHours, 0) : number_format($dayHours, 1);
                            @endphp
                            <p class="text-xs sm:text-sm font-semibold text-navy-800 dark:text-white">
                                {{ $dayHoursDisplay }} Jam
                            </p>
                            <p class="text-[9px] sm:text-[10px] text-slate-500 dark:text-slate-400">
                                Total kerja
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Summary -->
                <div class="mt-3 sm:mt-4 p-3 sm:p-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl border border-purple-200 dark:border-purple-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] sm:text-xs text-purple-600 dark:text-purple-400 font-semibold">Total Jam Kerja Mingguan</p>
                            <p class="text-xl sm:text-2xl font-bold text-purple-800 dark:text-purple-300">
                                @php
                                    $totalMinutes = $workSchedule->sum(fn($w) => \Carbon\Carbon::parse($w->start_time)->diffInMinutes(\Carbon\Carbon::parse($w->end_time)));
                                    $totalWeeklyHoursClean = $totalMinutes / 60;
                                    $totalWeeklyHoursDisplay = (floor($totalWeeklyHoursClean) == $totalWeeklyHoursClean) ? number_format($totalWeeklyHoursClean, 0) : number_format($totalWeeklyHoursClean, 1);
                                @endphp
                                {{ $totalWeeklyHoursDisplay }} Jam
                            </p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-500 rounded-full flex items-center justify-center">
                            <i data-lucide="clock" class="w-5 h-5 sm:w-6 sm:h-6 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-8 sm:py-12">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="briefcase" class="w-8 h-8 sm:w-10 sm:h-10 text-slate-400 dark:text-slate-500"></i>
                </div>
                <p class="text-xs sm:text-sm font-semibold text-navy-800 dark:text-white mb-1">Belum ada jadwal kerja</p>
                <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400">Hubungi admin untuk mengatur jadwal kerja Anda</p>
            </div>
        @endif
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

    .marquee-container {
        mask-image: linear-gradient(to right, transparent, black 1rem, black calc(100% - 1rem), transparent);
        -webkit-mask-image: linear-gradient(to right, transparent, black 1rem, black calc(100% - 1rem), transparent);
    }
    
    .marquee-track {
        display: inline-flex;
        animation: marquee-dashboard 25s linear infinite;
    }
    
    .marquee-track:hover {
        animation-play-state: paused;
    }
    
    @keyframes marquee-dashboard {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
</style>
@endsection