@extends('layouts.app')

@section('page-title', 'Jadwal Mengajar')

@section('content')
    <div class="fade-in space-y-6">

        <!-- Premium Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                    <i data-lucide="calendar-range" class="w-6 h-6 text-white dark:text-navy-900"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-navy-800 dark:text-white tracking-tight">Jadwal Mengajar
                        Guru</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola jadwal mengajar per kelas dan mata
                        pelajaran</p>
                </div>
            </div>
        </div>

        <!-- Teacher List - List Format -->
        <div class="space-y-4">
            @forelse($teachers as $teacher)
                @php
                    $groupedSchedules = $teacher->teachingSchedules->groupBy('day_of_week');
                    $dayNames = [
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                        0 => 'Minggu'
                    ];
                    $sortedDays = collect([1, 2, 3, 4, 5, 6, 0]);
                    $activeDays = $sortedDays->filter(fn($d) => $groupedSchedules->has($d));
                @endphp
                <div class="card p-5 hover:shadow-xl transition-all duration-300 group">

                    <!-- Teacher Header with Badge -->
                    <div class="flex items-start gap-4 mb-6 pb-4 border-b border-slate-200 dark:border-slate-700">
                        <img src="{{ $teacher->photo_url }}"
                            class="w-14 h-14 rounded-xl object-cover border-2 border-slate-200 dark:border-slate-700 group-hover:border-gold-400 dark:group-hover:border-gold-500 transition-colors shadow-md flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-navy-800 dark:text-white truncate mb-1.5">{{ $teacher->name }}
                            </h3>
                            @if($teacher->subject)
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-gradient-to-r from-gold-400 to-gold-500 text-navy-900 rounded-md text-xs font-bold shadow-sm">
                                    <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                                    {{ $teacher->subject }}
                                </span>
                            @else
                                <span class="text-sm text-slate-400 dark:text-slate-500 italic">Belum ada mata pelajaran</span>
                            @endif
                        </div>
                        <a href="{{ route('teaching-schedules.edit', $teacher) }}"
                            class="px-4 py-2 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white dark:text-navy-900 rounded-lg text-xs font-bold transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                            <i data-lucide="{{ $teacher->teachingSchedules->isEmpty() ? 'plus' : 'edit-2' }}"
                                class="w-3.5 h-3.5"></i>
                            <span>{{ $teacher->teachingSchedules->isEmpty() ? 'Buat Jadwal' : 'Edit Jadwal' }}</span>
                        </a>
                    </div>

                    <!-- Schedule Summary - Grid Format -->
                    @if($teacher->teachingSchedules->isEmpty())
                        <div
                            class="flex items-center justify-center py-8 px-3 bg-slate-50 dark:bg-slate-700/30 rounded-lg border border-dashed border-slate-200 dark:border-slate-600">
                            <div class="text-center">
                                <i data-lucide="calendar-off" class="w-8 h-8 text-slate-300 dark:text-slate-500 mx-auto mb-2"></i>
                                <p class="text-sm text-slate-400 dark:text-slate-500 font-medium">Belum ada jadwal mengajar</p>
                            </div>
                        </div>
                    @else
                        <!-- Grid Layout for Days -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($activeDays as $dayIndex)
                                @php
                                    $daySchedules = $groupedSchedules->get($dayIndex)->sortBy('period');
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

                                <!-- Day Card -->
                                <div
                                    class="rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-white dark:bg-slate-800 hover:shadow-lg transition-all duration-300">
                                    <!-- Day Header -->
                                    <div class="px-4 py-3 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-1 h-8 bg-gradient-to-b {{ $dayColors[$dayIndex] }} rounded-full"></div>
                                                <h4 class="text-sm font-bold text-navy-800 dark:text-white">{{ $dayNames[$dayIndex] }}
                                                </h4>
                                            </div>
                                            <span
                                                class="text-xs text-slate-500 dark:text-slate-400 font-medium bg-slate-100 dark:bg-slate-700 px-2.5 py-1 rounded-full">{{ $daySchedules->count() }}
                                                kelas</span>
                                        </div>
                                    </div>

                                    <!-- Schedules List -->
                                    <div class="p-3 space-y-2">
                                        @foreach($daySchedules as $schedule)
                                            <div
                                                class="p-2.5 bg-slate-50 dark:bg-slate-700/30 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-gold-400 dark:hover:border-gold-500 hover:shadow-md transition-all">
                                                <div class="flex items-start gap-2.5">
                                                    <div
                                                        class="w-6 h-6 rounded bg-navy-800 dark:bg-gold-400 flex items-center justify-center flex-shrink-0">
                                                        <span
                                                            class="text-[10px] font-bold text-white dark:text-navy-900">{{ $schedule->period }}</span>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-xs font-bold text-navy-800 dark:text-white truncate">
                                                            {{ $schedule->classroom->code }}</p>
                                                        @if($schedule->subject?->name)
                                                            <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">
                                                                {{ $schedule->subject->name }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div
                                                    class="mt-2 pt-2 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-1.5 text-xs text-slate-600 dark:text-slate-400 font-mono font-medium">
                                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                                    <span>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="card p-12 text-center">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="calendar-off" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
                    </div>
                    <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-2">Belum Ada Guru</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto">Tidak ada guru aktif untuk
                        ditampilkan.</p>
                </div>
            @endforelse
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