@extends('layouts.teacher')

@section('page-title', 'Jadwal Mengajar')

@section('content')
<div class="fade-in space-y-6">
    
    <!-- Header -->
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
            <i data-lucide="calendar-range" class="w-6 h-6 text-white dark:text-navy-900"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Jadwal Mengajar Saya</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Jadwal mengajar mingguan Anda</p>
        </div>
    </div>

    <!-- Schedule Grid -->
    @php
        $dayOrder = [1, 2, 3, 4, 5, 6, 0]; // Senin - Minggu
        $dayColors = [
            1 => 'from-blue-500 to-cyan-500',
            2 => 'from-emerald-500 to-teal-500',
            3 => 'from-violet-500 to-purple-500',
            4 => 'from-orange-500 to-amber-500',
            5 => 'from-pink-500 to-rose-500',
            6 => 'from-indigo-500 to-blue-500',
            0 => 'from-red-500 to-rose-500'
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($dayOrder as $dayIndex)
            @if($schedules->has($dayIndex))
            <div class="card overflow-hidden hover:shadow-xl transition-all duration-300">
                <!-- Day Header -->
                <div class="px-4 py-3 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-2.5">
                        <div class="w-1 h-8 bg-gradient-to-b {{ $dayColors[$dayIndex] }} rounded-full"></div>
                        <div class="flex-1">
                            <h3 class="text-sm font-bold text-navy-800 dark:text-white flex items-center gap-2">
                                {{ $dayNames[$dayIndex] }}
                                @if($dayIndex == $today)
                                <span class="px-2 py-0.5 bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400 rounded text-[10px] font-bold">Hari Ini</span>
                                @endif
                            </h3>
                        </div>
                        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium bg-slate-100 dark:bg-slate-700 px-2.5 py-1 rounded-full">{{ $schedules[$dayIndex]->count() }} kelas</span>
                    </div>
                </div>
                
                <!-- Schedules List -->
                <div class="p-3 space-y-2 bg-slate-50/50 dark:bg-slate-900/50">
                    @foreach($schedules[$dayIndex]->sortBy('period') as $schedule)
                    <div class="p-3 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-gold-400 dark:hover:border-gold-500 hover:shadow-md transition-all">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center flex-shrink-0 shadow-sm">
                                <span class="text-xs font-bold text-white dark:text-navy-900">{{ $schedule->period }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-navy-800 dark:text-white truncate">{{ $schedule->classroom->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $schedule->subject?->name ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-700 flex items-center justify-end gap-1.5 text-xs text-slate-600 dark:text-slate-400 font-mono font-medium">
                            <i data-lucide="clock" class="w-3 h-3"></i>
                            <span>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    </div>

    @if($schedules->isEmpty())
    <div class="card p-12 text-center">
        <div class="w-20 h-20 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="calendar-off" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
        </div>
        <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-2">Belum Ada Jadwal</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400">Anda belum memiliki jadwal mengajar minggu ini.</p>
    </div>
    @endif
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