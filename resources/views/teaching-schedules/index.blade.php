@extends('layouts.app')

@section('page-title', 'Jadwal Mengajar')

@section('content')
    <div class="fade-in space-y-6">

        <!-- Header -->
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

        <!-- Alert -->
        @if(session('success'))
            <div
                class="card p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800">
                <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                    <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Teacher List -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse($teachers as $teacher)
                <div class="card p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <!-- Teacher Header -->
                    <div class="flex items-start gap-4 mb-5 pb-5 border-b border-slate-200 dark:border-slate-700">
                        <img src="{{ $teacher->photo_url }}"
                            class="w-16 h-16 rounded-2xl object-cover border-2 border-slate-200 dark:border-slate-700 shadow-lg">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-navy-800 dark:text-white truncate mb-1">{{ $teacher->name }}</h3>
                            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                                <span class="truncate">{{ $teacher->email }}</span>
                            </div>
                            @if($teacher->subject)
                                <div class="mt-1">
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400 rounded text-[10px] font-bold uppercase">
                                        {{ $teacher->subject }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Schedules -->
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jadwal
                                Mengajar</h4>
                            <span
                                class="px-2 py-0.5 bg-navy-100 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 rounded-full text-[10px] font-bold">
                                {{ $teacher->teachingSchedules->count() }} Kelas
                            </span>
                        </div>

                        @if($teacher->teachingSchedules->isEmpty())
                            <div
                                class="p-4 bg-slate-50 dark:bg-slate-700/30 rounded-xl border border-dashed border-slate-200 dark:border-slate-700 text-center">
                                <i data-lucide="calendar-off" class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-2"></i>
                                <p class="text-xs text-slate-400 dark:text-slate-500">Belum ada jadwal mengajar</p>
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach($teacher->teachingSchedules->take(5) as $schedule)
                                    <div
                                        class="flex items-center gap-3 p-2.5 bg-slate-50 dark:bg-slate-700/30 rounded-lg border border-slate-100 dark:border-slate-700">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center flex-shrink-0">
                                            <span class="text-[9px] font-bold text-white dark:text-navy-900 uppercase">
                                                {{ substr(\App\Models\TeachingSchedule::getDayName($schedule->day_of_week), 0, 3) }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-navy-800 dark:text-white truncate">
                                                {{ $schedule->classroom->name }}</p>
                                            <p class="text-[10px] text-slate-500 dark:text-slate-400">
                                                Jam {{ $schedule->period }} •
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach

                                @if($teacher->teachingSchedules->count() > 5)
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 text-center pt-2">
                                        + {{ $teacher->teachingSchedules->count() - 5 }} jadwal lainnya
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Edit Button -->
                    <a href="{{ route('teaching-schedules.edit', $teacher) }}"
                        class="w-full px-4 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                        <span>Edit Jadwal Mengajar</span>
                    </a>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="card p-12 text-center">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i data-lucide="users" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
                        </div>
                        <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-2">Belum Ada Guru</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto">Tidak ada guru aktif untuk
                            ditampilkan.</p>
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