@extends('layouts.teacher')

@section('page-title', 'Jadwal Kerja')

@section('content')
<div class="fade-in space-y-6">

    <!-- Header & Toggle View -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                <i data-lucide="briefcase" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Jadwal Kerja</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Jam kerja mingguan Anda</p>
            </div>
        </div>

        {{-- ── TOGGLE VIEW (SAMAIN KAYAK JADWAL MENGAJAR) ── --}}
        @if(count($scheduleData) > 0)
        <div class="relative flex items-center bg-slate-100 dark:bg-slate-800 rounded-xl p-1 self-start sm:self-auto select-none" style="min-width:120px;">
            {{-- Sliding pill indicator --}}
            <div id="toggle-indicator"></div>
            <button id="btn-list" onclick="setView('list')"
                    class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors duration-200 flex-1 justify-center"
                    title="Tampilan List">
                <i data-lucide="list" class="w-4 h-4"></i>
                <span class="hidden sm:inline">List</span>
            </button>
            <button id="btn-grid" onclick="setView('grid')"
                    class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors duration-200 flex-1 justify-center"
                    title="Tampilan Grid">
                <i data-lucide="layout-grid" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Grid</span>
            </button>
        </div>
        @endif
    </div>

    @if(count($scheduleData) > 0)
        <!-- Summary Card -->
        <div class="card p-6 bg-gradient-to-br from-navy-800 via-navy-900 to-slate-900 dark:from-gold-400 dark:via-gold-500 dark:to-yellow-500 text-white dark:text-navy-900 relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 dark:bg-navy-900/10 rounded-full -mr-16 -mt-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 dark:bg-navy-900/5 rounded-full -ml-12 -mb-12"></div>
            
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90 mb-2">Total Jam Kerja Mingguan</p>
                    <h2 class="text-4xl font-bold mb-2">{{ (floor($totalWeeklyHours) == $totalWeeklyHours) ? number_format($totalWeeklyHours, 0) : number_format($totalWeeklyHours, 1) }} Jam</h2>
                    <p class="text-sm opacity-75 flex items-center gap-2">
                        <i data-lucide="calendar-days" class="w-4 h-4"></i>
                        {{ $workDays }} hari kerja per minggu
                    </p>
                </div>
                <div class="w-20 h-20 bg-white/20 dark:bg-navy-900/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                    <i data-lucide="clock" class="w-10 h-10 text-white dark:text-navy-900"></i>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════
             LIST VIEW
             ════════════════════════════════════════ --}}
        <div id="view-list" class="space-y-4">
            @php
                $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $dayShort = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            @endphp

            @foreach($scheduleData as $schedule)
            <div class="card p-4 sm:p-5 relative overflow-hidden transition-all duration-300 hover:shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4
                {{ $schedule['is_today'] 
                    ? 'border-2 border-navy-800 dark:border-gold-400 bg-gradient-to-br from-navy-50 to-slate-50 dark:from-navy-900/20 dark:to-slate-900/20' 
                    : 'border border-slate-200 dark:border-slate-700' }}"
                x-data="workScheduleCard('{{ $schedule['start_time'] }}', '{{ $schedule['end_time'] }}', {{ $schedule['is_today'] ? 'true' : 'false' }})">

                <!-- Left: Day info & badge -->
                <div class="flex items-center gap-3.5 min-w-[200px]">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0
                        {{ $schedule['is_today'] 
                            ? 'bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 shadow-md' 
                            : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                        <i data-lucide="calendar" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-navy-800 dark:text-white">
                                {{ $dayNames[$schedule['day_of_week']] }}
                            </h3>
                            @if($schedule['is_today'])
                            <span class="px-2 py-0.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 text-[10px] font-bold rounded-md">
                                Hari Ini
                            </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Durasi: {{ (floor($schedule['duration_minutes'] / 60) == $schedule['duration_minutes'] / 60) ? number_format($schedule['duration_minutes'] / 60, 0) : number_format($schedule['duration_minutes'] / 60, 1) }} Jam ({{ $schedule['duration_text'] }})
                        </p>
                    </div>
                </div>

                <!-- Middle: Time badges (Jam Masuk & Jam Pulang) -->
                <div class="flex items-center gap-3">
                    <div class="px-3.5 py-2 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-green-500 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="log-in" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <p class="text-[9px] text-green-600 dark:text-green-400 font-semibold uppercase leading-none">Jam Masuk</p>
                            <p class="text-sm font-bold text-green-700 dark:text-green-300 leading-tight">{{ $schedule['start_time'] }}</p>
                        </div>
                    </div>

                    <div class="px-3.5 py-2 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-red-500 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="log-out" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <p class="text-[9px] text-red-600 dark:text-red-400 font-semibold uppercase leading-none">Jam Pulang</p>
                            <p class="text-sm font-bold text-red-700 dark:text-red-300 leading-tight">{{ $schedule['end_time'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Progress / Status -->
                <div class="flex flex-col md:items-end min-w-[200px]">
                    @if($schedule['is_today'])
                    <div class="w-full max-w-[200px] mb-1.5">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">Progress Hari Ini</span>
                            <span class="text-[10px] font-bold"
                                  :class="progressPercent >= 100 ? 'text-green-600 dark:text-green-400' : (progressPercent > 0 ? 'text-navy-800 dark:text-gold-400' : 'text-slate-400')"
                                  x-text="progressPercent.toFixed(1) + '%'"></span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-1000 ease-out"
                                 :class="progressPercent >= 100 ? 'bg-gradient-to-r from-green-500 to-emerald-600' : (progressPercent > 0 ? 'bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500' : 'bg-slate-300 dark:bg-slate-600')"
                                 :style="'width: ' + Math.min(progressPercent, 100) + '%'"></div>
                        </div>
                    </div>
                    <span class="px-3 py-0.5 rounded-full text-[10px] font-bold w-fit"
                          :class="{
                              'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400': progressPercent >= 100,
                              'bg-navy-100 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300': progressPercent > 0 && progressPercent < 100,
                              'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400': progressPercent === 0
                          }">
                        <i :data-lucide="progressPercent >= 100 ? 'check-circle' : (progressPercent > 0 ? 'clock' : 'circle')" class="w-3 h-3 inline mr-1"></i>
                        <span x-text="progressPercent >= 100 ? 'Selesai' : (progressPercent > 0 ? 'Sedang Berjalan' : 'Belum Dimulai')"></span>
                    </span>
                    @else
                    <span class="px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-full text-xs font-bold">
                        <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                        {{ (floor($schedule['duration_minutes'] / 60) == $schedule['duration_minutes'] / 60) ? number_format($schedule['duration_minutes'] / 60, 0) : number_format($schedule['duration_minutes'] / 60, 1) }} Jam
                    </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- ════════════════════════════════════════
             GRID VIEW
             ════════════════════════════════════════ --}}
        <div id="view-grid" class="hidden grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $dayShort = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            @endphp

            @foreach($scheduleData as $schedule)
            <div class="card p-5 relative overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1
                {{ $schedule['is_today'] 
                    ? 'border-2 border-navy-800 dark:border-gold-400 bg-gradient-to-br from-navy-50 to-slate-50 dark:from-navy-900/20 dark:to-slate-900/20' 
                    : 'border border-slate-200 dark:border-slate-700' }}"
                x-data="workScheduleCard('{{ $schedule['start_time'] }}', '{{ $schedule['end_time'] }}', {{ $schedule['is_today'] ? 'true' : 'false' }})">
                
                @if($schedule['is_today'])
                <div class="absolute top-0 right-0 px-3 py-1 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 text-xs font-bold rounded-bl-xl">
                    Hari Ini
                </div>
                @endif

                <!-- Header -->
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center
                            {{ $schedule['is_today'] 
                                ? 'bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 shadow-lg' 
                                : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                            <i data-lucide="calendar" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-navy-800 dark:text-white">
                                {{ $dayNames[$schedule['day_of_week']] }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $dayShort[$schedule['day_of_week']] }}
                            </p>
                        </div>
                    </div>
                    <div class="px-3 py-1.5 rounded-full text-xs font-bold
                        {{ $schedule['is_today'] 
                            ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900' 
                            : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                        <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                        {{ (floor($schedule['duration_minutes'] / 60) == $schedule['duration_minutes'] / 60) ? number_format($schedule['duration_minutes'] / 60, 0) : number_format($schedule['duration_minutes'] / 60, 1) }} Jam
                    </div>
                </div>

                <!-- Time Info -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="p-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-6 h-6 rounded-lg bg-green-500 flex items-center justify-center">
                                <i data-lucide="log-in" class="w-3.5 h-3.5 text-white"></i>
                            </div>
                            <p class="text-[10px] text-green-600 dark:text-green-400 font-semibold">Jam Masuk</p>
                        </div>
                        <p class="text-lg font-bold text-green-700 dark:text-green-300">
                            {{ $schedule['start_time'] }}
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-6 h-6 rounded-lg bg-red-500 flex items-center justify-center">
                                <i data-lucide="log-out" class="w-3.5 h-3.5 text-white"></i>
                            </div>
                            <p class="text-[10px] text-red-600 dark:text-red-400 font-semibold">Jam Pulang</p>
                        </div>
                        <p class="text-lg font-bold text-red-700 dark:text-red-300">
                            {{ $schedule['end_time'] }}
                        </p>
                    </div>
                </div>

                @if($schedule['is_today'])
                <!-- Real-time Progress Bar -->
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Progress Hari Ini</span>
                        <span class="text-xs font-bold" 
                              :class="progressPercent >= 100 ? 'text-green-600 dark:text-green-400' : (progressPercent > 0 ? 'text-navy-800 dark:text-gold-400' : 'text-slate-400')"
                              x-text="progressPercent.toFixed(1) + '%'"></span>
                    </div>
                    <div class="w-full h-3 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden relative">
                        <div class="h-full rounded-full transition-all duration-1000 ease-out"
                             :class="progressPercent >= 100 ? 'bg-gradient-to-r from-green-500 to-emerald-600' : (progressPercent > 0 ? 'bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500' : 'bg-slate-300 dark:bg-slate-600')"
                             :style="'width: ' + Math.min(progressPercent, 100) + '%'"></div>
                        
                        <!-- Current time indicator -->
                        <div x-show="progressPercent > 0 && progressPercent < 100"
                             class="absolute top-0 h-full w-1 bg-white dark:bg-navy-900 shadow-lg"
                             :style="'left: ' + Math.min(progressPercent, 100) + '%'"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2 text-[10px] text-slate-500 dark:text-slate-400">
                        <span x-text="currentTime"></span>
                        <span>Durasi: {{ $schedule['duration_text'] }}</span>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="flex items-center justify-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold"
                          :class="{
                              'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400': progressPercent >= 100,
                              'bg-navy-100 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300': progressPercent > 0 && progressPercent < 100,
                              'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400': progressPercent === 0
                          }">
                        <i :data-lucide="progressPercent >= 100 ? 'check-circle' : (progressPercent > 0 ? 'clock' : 'circle')" class="w-3 h-3 inline mr-1"></i>
                        <span x-text="progressPercent >= 100 ? 'Selesai' : (progressPercent > 0 ? 'Sedang Berjalan' : 'Belum Dimulai')"></span>
                    </span>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Info Card -->
        <div class="card p-5 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-1">Informasi Jadwal Kerja</h4>
                    <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                        Jadwal kerja ini ditentukan oleh admin dan digunakan sebagai acuan jam masuk dan pulang Anda. 
                        Pastikan untuk melakukan presensi harian sesuai dengan jadwal yang telah ditetapkan.
                    </p>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="card p-12 text-center">
            <div class="w-24 h-24 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-lucide="briefcase" class="w-12 h-12 text-slate-400 dark:text-slate-500"></i>
            </div>
            <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-2">Belum Ada Jadwal Kerja</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                Hubungi Operator untuk mengatur jadwal kerja Anda
            </p>
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 rounded-lg text-xs text-slate-600 dark:text-slate-400">
                <i data-lucide="mail" class="w-4 h-4"></i>
                operator@icbct.sch.id
            </div>
        </div>
    @endif
</div>

<script>
    function workScheduleCard(startTime, endTime, isToday) {
        return {
            currentTime: '--:--',
            progressPercent: 0,
            intervalId: null,
            init() {
                this.updateProgress();
                if (isToday) {
                    this.intervalId = setInterval(() => {
                        this.updateProgress();
                    }, 10000);
                }
            },
            updateProgress() {
                const now = new Date();
                const hours = now.getHours().toString().padStart(2, '0');
                const minutes = now.getMinutes().toString().padStart(2, '0');
                this.currentTime = `${hours}:${minutes}`;

                const [startH, startM] = startTime.split(':').map(Number);
                const [endH, endM] = endTime.split(':').map(Number);

                const startMinutes = startH * 60 + startM;
                const endMinutes = endH * 60 + endM;
                const currentMinutes = now.getHours() * 60 + now.getMinutes();

                if (currentMinutes < startMinutes) {
                    this.progressPercent = 0;
                } else if (currentMinutes >= endMinutes) {
                    this.progressPercent = 100;
                } else {
                    const totalDuration = endMinutes - startMinutes;
                    const elapsed = currentMinutes - startMinutes;
                    this.progressPercent = (elapsed / totalDuration) * 100;
                }
            },
            destroy() {
                if (this.intervalId) {
                    clearInterval(this.intervalId);
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
        var saved = localStorage.getItem('work_schedule_view') || 'grid';
        applyView(saved, false);

        setInterval(() => {
            if (window.lucide) lucide.createIcons();
        }, 1000);
    });

    function setView(v) {
        var current = localStorage.getItem('work_schedule_view') || 'grid';
        if (current === v) return;
        localStorage.setItem('work_schedule_view', v);
        applyView(v, true);
    }

    function applyView(v, animate) {
        var listEl  = document.getElementById('view-list');
        var gridEl  = document.getElementById('view-grid');
        var btnList = document.getElementById('btn-list');
        var btnGrid = document.getElementById('btn-grid');
        var indicator = document.getElementById('toggle-indicator');

        if (indicator) {
            if (v === 'grid') {
                indicator.style.transform = 'translateX(100%)';
            } else {
                indicator.style.transform = 'translateX(0%)';
            }
        }

        [btnList, btnGrid].forEach(btn => {
            if (btn) btn.classList.remove('text-navy-800', 'dark:text-white', 'text-slate-500', 'dark:text-slate-400');
        });
        var activeBtn   = v === 'list' ? btnList : btnGrid;
        var inactiveBtn = v === 'list' ? btnGrid : btnList;
        if (activeBtn) activeBtn.classList.add('text-navy-800', 'dark:text-white');
        if (inactiveBtn) inactiveBtn.classList.add('text-slate-500', 'dark:text-slate-400');

        if (!listEl || !gridEl) return;

        if (!animate) {
            if (v === 'grid') {
                listEl.style.display = 'none';
                gridEl.style.display = 'grid';
            } else {
                gridEl.style.display = 'none';
                listEl.style.display = 'flex';
                listEl.style.flexDirection = 'column';
            }
            if (window.lucide) lucide.createIcons();
            return;
        }

        var outEl = v === 'grid' ? listEl : gridEl;
        var inEl  = v === 'grid' ? gridEl : listEl;
        var inFrom = v === 'grid' ? 'translateX(32px)' : 'translateX(-32px)';

        outEl.style.transition = 'opacity 0.22s ease, transform 0.22s cubic-bezier(0.4,0,1,1)';
        outEl.style.opacity    = '0';
        outEl.style.transform  = v === 'grid' ? 'translateX(-24px)' : 'translateX(24px)';
        outEl.style.pointerEvents = 'none';

        setTimeout(function() {
            outEl.style.display   = 'none';
            outEl.style.opacity   = '';
            outEl.style.transform = '';
            outEl.style.transition = '';
            outEl.style.pointerEvents = '';

            inEl.style.display    = v === 'grid' ? 'grid' : 'flex';
            if (v === 'list') inEl.style.flexDirection = 'column';
            inEl.style.opacity    = '0';
            inEl.style.transform  = inFrom;
            inEl.style.transition = 'none';

            void inEl.offsetWidth;

            inEl.style.transition = 'opacity 0.28s ease, transform 0.32s cubic-bezier(0.22,1,0.36,1)';
            inEl.style.opacity    = '1';
            inEl.style.transform  = 'translateX(0)';

            var cards = inEl.querySelectorAll('.card, [class*="card"]');
            cards.forEach(function(card, i) {
                card.style.opacity   = '0';
                card.style.transform = 'translateY(16px) scale(0.97)';
                card.style.transition = 'none';
                setTimeout(function() {
                    card.style.transition = 'opacity 0.3s ease, transform 0.35s cubic-bezier(0.22,1,0.36,1)';
                    card.style.opacity   = '1';
                    card.style.transform = 'translateY(0) scale(1)';
                }, i * 45);
            });

            if (window.lucide) lucide.createIcons();

            setTimeout(function() {
                inEl.style.transition = '';
                inEl.style.opacity    = '';
                inEl.style.transform  = '';
            }, 400);
        }, 230);
    }
</script>

<style>
    .fade-in {
        animation: fadeIn 0.45s ease-out forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Toggle button group */
    #toggle-indicator {
        position: absolute;
        top: 4px; left: 4px;
        width: calc(50% - 4px);
        height: calc(100% - 8px);
        background: white;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(15,23,42,0.12);
        transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1);
        pointer-events: none;
        z-index: 0;
    }
    .dark #toggle-indicator {
        background: #334155;
        box-shadow: 0 1px 4px rgba(0,0,0,0.3);
    }
    #btn-list, #btn-grid {
        position: relative;
        z-index: 1;
        transition: color 0.2s ease;
    }
    #btn-list:active, #btn-grid:active {
        transform: scale(0.94);
    }

    /* Grid view layout default hidden until script runs */
    #view-grid { display: none; }
</style>
@endsection