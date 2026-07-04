@extends('layouts.app')

@section('page-title', 'Jadwal Guru')

@section('content')
    <div class="fade-in space-y-6">

        <!-- Premium Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                    <i data-lucide="calendar-clock" class="w-6 h-6 text-white dark:text-navy-900"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-navy-800 dark:text-white tracking-tight">Jadwal Mengajar
                        Guru</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola jadwal harian dan jam kerja setiap
                        guru</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="card p-5 group hover:shadow-lg transition-all">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="users" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Guru</p>
                        <h3 class="text-2xl font-bold text-navy-800 dark:text-white mt-1">{{ $teachers->count() }}</h3>
                    </div>
                </div>
            </div>

            <div class="card p-5 group hover:shadow-lg transition-all">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="calendar-check" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Punya Jadwal</p>
                        <h3 class="text-2xl font-bold text-navy-800 dark:text-white mt-1">
                            {{ $teachers->filter(fn($t) => $t->schedules->where('is_active', true)->isNotEmpty())->count() }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="card p-5 group hover:shadow-lg transition-all">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/30 dark:to-amber-800/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="calendar-x" class="w-6 h-6 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Belum Ada Jadwal</p>
                        <h3 class="text-2xl font-bold text-navy-800 dark:text-white mt-1">
                            {{ $teachers->filter(fn($t) => $t->schedules->where('is_active', true)->isEmpty())->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher List - 3x3 Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($teachers as $teacher)
                <div
                    class="card p-6 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 group h-full flex flex-col">

                    <!-- Teacher Header -->
                    <div class="flex items-start gap-4 mb-5">
                        <img src="{{ $teacher->photo_url }}"
                            class="w-16 h-16 rounded-2xl object-cover border-2 border-slate-200 dark:border-slate-700 group-hover:border-gold-400 dark:group-hover:border-gold-500 transition-colors shadow-lg flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-navy-800 dark:text-white truncate mb-1">{{ $teacher->name }}</h3>
                            <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                                <i data-lucide="book-open" class="w-3.5 h-3.5 flex-shrink-0"></i>
                                <span class="truncate">{{ $teacher->subject ?: 'Belum ada mata pelajaran' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Summary -->
                    <div class="space-y-2 mb-6 flex-grow">
                        @php
                            $activeSchedules = $teacher->schedules->where('is_active', true)->sortBy('day_of_week');
                        @endphp

                        @if($activeSchedules->isEmpty())
                            <div
                                class="flex items-center justify-center py-4 px-3 bg-slate-50 dark:bg-slate-700/30 rounded-lg border border-dashed border-slate-200 dark:border-slate-600">
                                <div class="text-center">
                                    <i data-lucide="calendar-off"
                                        class="w-5 h-5 text-slate-300 dark:text-slate-500 mx-auto mb-1"></i>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">Belum ada jadwal</p>
                                </div>
                            </div>
                        @else
                            <div class="space-y-1.5">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Jadwal Aktif</p>
                                @foreach($activeSchedules as $schedule)
                                    <div
                                        class="flex items-center gap-2 p-2 bg-slate-50 dark:bg-slate-700/30 rounded-lg border border-slate-100 dark:border-slate-700/50 hover:border-gold-300 dark:hover:border-gold-600 transition-colors group/schedule">
                                        <div
                                            class="w-8 h-8 rounded-md bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center flex-shrink-0 shadow-sm">
                                            <span class="text-[8px] font-bold text-white dark:text-navy-900 uppercase">
                                                {{ substr(\App\Models\TeacherSchedule::getDayName($schedule->day_of_week), 0, 3) }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[11px] font-semibold text-navy-800 dark:text-white">
                                                {{ \App\Models\TeacherSchedule::getDayName($schedule->day_of_week) }}
                                            </p>
                                            <p class="text-[9px] text-slate-500 dark:text-slate-400 font-mono">
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                            </p>
                                        </div>
                                        <i data-lucide="chevron-right"
                                            class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 group-hover/schedule:text-gold-500 transition-colors flex-shrink-0"></i>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Edit Button -->
                    <a href="{{ route('schedules.edit', $teacher) }}"
                        class="w-full px-4 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2 group/btn mt-auto">
                        <i data-lucide="edit-2" class="w-4 h-4 group-hover/btn:scale-110 transition-transform"></i>
                        <span>Edit Jadwal</span>
                    </a>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="card p-12 text-center">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i data-lucide="calendar-off" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
                        </div>
                        <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-2">Belum Ada Guru</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto">Tidak ada guru aktif untuk
                            ditampilkan. Tambahkan guru terlebih dahulu.</p>
                    </div>
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