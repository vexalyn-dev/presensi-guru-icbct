@extends('layouts.app')
@section('page-title', 'Jadwal Mengajar')
@section('content')
<div class="fade-in space-y-6">

    {{-- ── HEADER ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i data-lucide="calendar-range" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-navy-800 dark:text-white tracking-tight">Jadwal Mengajar Guru</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola jadwal mengajar per kelas dan mata pelajaran</p>
            </div>
        </div>

        {{-- ── TOGGLE VIEW ── --}}
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
    </div>

    {{-- ════════════════════════════════════════
         LIST VIEW
         ════════════════════════════════════════ --}}
    <div id="view-list" class="space-y-4">
        @forelse($teachers as $teacher)
            @php
                $groupedSchedules = $teacher->teachingSchedules->groupBy('day_of_week');
                $dayNames = [1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat',6=>'Sabtu',0=>'Minggu'];
                $sortedDays = collect([1,2,3,4,5,6,0]);
                $activeDays = $sortedDays->filter(fn($d) => $groupedSchedules->has($d));
                $dayColors = [
                    1=>'from-blue-500 to-cyan-500',2=>'from-emerald-500 to-teal-500',
                    3=>'from-violet-500 to-purple-500',4=>'from-orange-500 to-amber-500',
                    5=>'from-pink-500 to-rose-500',6=>'from-indigo-500 to-blue-500',0=>'from-red-500 to-rose-500'
                ];
            @endphp
            <div class="card p-5 hover:shadow-xl transition-all duration-300 group">
                <div class="flex items-start gap-4 mb-5 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <img src="{{ $teacher->photo_url }}"
                         class="w-14 h-14 rounded-xl object-cover border-2 border-slate-200 dark:border-slate-700 group-hover:border-gold-400 transition-colors shadow-md flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-bold text-navy-800 dark:text-white truncate mb-1.5">{{ $teacher->name }}</h3>
                        @if($teacher->subject)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-gradient-to-r from-gold-400 to-gold-500 text-navy-900 rounded-md text-xs font-bold">
                                <i data-lucide="book-open" class="w-3 h-3"></i>
                                {{ $teacher->subject }}
                            </span>
                        @else
                            <span class="text-xs text-slate-400 italic">Belum ada mata pelajaran</span>
                        @endif
                    </div>
                    <a href="{{ route('teaching-schedules.edit', $teacher) }}"
                       class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 bg-navy-800 dark:bg-gold-400 hover:opacity-90 text-white dark:text-navy-900 rounded-lg text-xs font-bold transition-all shadow-md">
                        <i data-lucide="{{ $teacher->teachingSchedules->isEmpty() ? 'plus' : 'edit-2' }}" class="w-3.5 h-3.5"></i>
                        {{ $teacher->teachingSchedules->isEmpty() ? 'Buat Jadwal' : 'Edit Jadwal' }}
                    </a>
                </div>

                @if($teacher->teachingSchedules->isEmpty())
                    <div class="flex items-center justify-center py-8 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                        <div class="text-center">
                            <i data-lucide="calendar-off" class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-2"></i>
                            <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada jadwal mengajar</p>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($activeDays as $dayIndex)
                            @php $daySchedules = $groupedSchedules->get($dayIndex)->sortBy('period'); @endphp
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                <div class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1 h-6 bg-gradient-to-b {{ $dayColors[$dayIndex] }} rounded-full"></div>
                                        <h4 class="text-xs font-bold text-navy-800 dark:text-white">{{ $dayNames[$dayIndex] }}</h4>
                                    </div>
                                    <span class="text-[10px] font-semibold text-slate-500 bg-white dark:bg-slate-700 px-2 py-0.5 rounded-full border border-slate-200 dark:border-slate-600">
                                        {{ $daySchedules->count() }} kelas
                                    </span>
                                </div>
                                <div class="p-2.5 space-y-1.5">
                                    @foreach($daySchedules as $schedule)
                                    <div class="flex items-center gap-2.5 p-2 bg-white dark:bg-slate-800 rounded-lg border border-slate-100 dark:border-slate-700 hover:border-gold-400 transition-colors">
                                        <div class="w-6 h-6 rounded bg-navy-800 dark:bg-gold-400 flex items-center justify-center flex-shrink-0">
                                            <span class="text-[10px] font-bold text-white dark:text-navy-900">{{ $schedule->period }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-navy-800 dark:text-white truncate">{{ $schedule->classroom->code }}</p>
                                            @if($schedule->subject?->name)
                                            <p class="text-[10px] text-slate-500 truncate">{{ $schedule->subject->name }}</p>
                                            @endif
                                        </div>
                                        <span class="text-[10px] font-mono text-slate-500 flex-shrink-0">
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        </span>
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
                <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="calendar-off" class="w-10 h-10 text-slate-400"></i>
                </div>
                <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-1">Belum Ada Guru</h3>
                <p class="text-sm text-slate-500">Tidak ada guru aktif untuk ditampilkan.</p>
            </div>
        @endforelse
    </div>

    {{-- ════════════════════════════════════════
         GRID VIEW
         ════════════════════════════════════════ --}}
    <div id="view-grid" class="hidden grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($teachers as $teacher)
            @php
                $totalClasses  = $teacher->teachingSchedules->count();
                $activeDayNums = $teacher->teachingSchedules->pluck('day_of_week')->unique()->sort();
                $dayNamesShort = [1=>'Sen',2=>'Sel',3=>'Rab',4=>'Kam',5=>'Jum',6=>'Sab',0=>'Min'];
                $dayFull       = [1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat',6=>'Sabtu',0=>'Minggu'];
                $pillColors    = [
                    1=>'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                    2=>'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                    3=>'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400',
                    4=>'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                    5=>'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400',
                    6=>'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400',
                    0=>'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                ];
            @endphp

            <div class="card group overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 flex flex-col">

                {{-- Card Top: navy gradient band --}}
                <div class="relative bg-gradient-to-br from-navy-800 to-slate-900 dark:from-navy-900 dark:to-slate-950 px-5 pt-6 pb-14">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gold-400/10 rounded-full translate-x-1/3 -translate-y-1/2 pointer-events-none"></div>
                    {{-- Total badge --}}
                    <div class="absolute top-4 right-4">
                        @if($totalClasses > 0)
                        <span class="px-2.5 py-1 bg-gold-400/20 border border-gold-400/30 text-gold-400 text-xs font-bold rounded-full">
                            {{ $totalClasses }} kelas
                        </span>
                        @else
                        <span class="px-2.5 py-1 bg-slate-700 text-slate-400 text-xs font-medium rounded-full">
                            Belum ada jadwal
                        </span>
                        @endif
                    </div>
                    {{-- Foto --}}
                    <img src="{{ $teacher->photo_url }}"
                         class="w-16 h-16 rounded-2xl object-cover border-2 border-gold-400/40 shadow-xl group-hover:scale-105 transition-transform duration-300">
                </div>

                {{-- Card Body --}}
                <div class="relative px-5 pb-5 flex-1 flex flex-col -mt-8">
                    {{-- Nama + mapel --}}
                    <div class="mb-4">
                        <h3 class="text-base font-bold text-navy-800 dark:text-white truncate">{{ $teacher->name }}</h3>
                        @if($teacher->subject)
                        <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 bg-gold-400 text-navy-900 rounded-md text-[11px] font-bold max-w-full truncate">
                            <i data-lucide="book-open" class="w-3 h-3 flex-shrink-0"></i>
                            <span class="truncate">{{ $teacher->subject }}</span>
                        </span>
                        @else
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 italic">Belum ada mata pelajaran</p>
                        @endif
                    </div>

                    {{-- Hari aktif --}}
                    @if($activeDayNums->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        @foreach($activeDayNums as $d)
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $pillColors[$d] }}">
                            {{ $dayFull[$d] }}
                        </span>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-slate-400 italic mb-4">Tidak ada hari aktif</p>
                    @endif

                    {{-- Preview 3 jadwal pertama --}}
                    @if($totalClasses > 0)
                    <div class="space-y-1.5 mb-4">
                        @foreach($teacher->teachingSchedules->sortBy('day_of_week')->take(3) as $sch)
                        <div class="flex items-center gap-2 px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-100 dark:border-slate-700">
                            <div class="w-5 h-5 rounded bg-navy-800 dark:bg-gold-400 flex items-center justify-center flex-shrink-0">
                                <span class="text-[9px] font-black text-white dark:text-navy-900">{{ $sch->period }}</span>
                            </div>
                            <span class="text-xs font-semibold text-navy-800 dark:text-white flex-shrink-0">{{ $dayNamesShort[$sch->day_of_week] }}</span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 truncate flex-1">{{ $sch->classroom->code }}</span>
                            <span class="text-[10px] font-mono text-slate-400 flex-shrink-0">
                                {{ \Carbon\Carbon::parse($sch->start_time)->format('H:i') }}
                            </span>
                        </div>
                        @endforeach
                        @if($totalClasses > 3)
                        <p class="text-center text-[10px] text-slate-400 font-medium pt-0.5">+{{ $totalClasses - 3 }} jadwal lainnya</p>
                        @endif
                    </div>
                    @endif

                    {{-- Action button --}}
                    <div class="mt-auto">
                        <a href="{{ route('teaching-schedules.edit', $teacher) }}"
                           class="w-full flex items-center justify-center gap-2 py-2.5 bg-navy-800 dark:bg-gold-400 hover:opacity-90 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-md hover:shadow-lg active:scale-95">
                            <i data-lucide="{{ $totalClasses === 0 ? 'plus' : 'edit-2' }}" class="w-4 h-4"></i>
                            {{ $totalClasses === 0 ? 'Buat Jadwal' : 'Edit Jadwal' }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full card p-12 text-center">
                <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="calendar-off" class="w-10 h-10 text-slate-400"></i>
                </div>
                <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-1">Belum Ada Guru</h3>
                <p class="text-sm text-slate-500">Tidak ada guru aktif untuk ditampilkan.</p>
            </div>
        @endforelse
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();
    var saved = localStorage.getItem('schedule_view') || 'list';
    applyView(saved, false); // false = no animation on initial load
});

function setView(v) {
    var current = localStorage.getItem('schedule_view') || 'list';
    if (current === v) return; // sudah di view yang sama, skip
    localStorage.setItem('schedule_view', v);
    applyView(v, true);
}

function applyView(v, animate) {
    var listEl  = document.getElementById('view-list');
    var gridEl  = document.getElementById('view-grid');
    var btnList = document.getElementById('btn-list');
    var btnGrid = document.getElementById('btn-grid');
    var indicator = document.getElementById('toggle-indicator');

    // ── Update toggle indicator position ──
    if (indicator) {
        if (v === 'grid') {
            indicator.style.transform = 'translateX(100%)';
        } else {
            indicator.style.transform = 'translateX(0%)';
        }
    }

    // ── Update button active states ──
    [btnList, btnGrid].forEach(btn => {
        btn.classList.remove('text-navy-800', 'dark:text-white', 'text-slate-500', 'dark:text-slate-400');
    });
    var activeBtn   = v === 'list' ? btnList : btnGrid;
    var inactiveBtn = v === 'list' ? btnGrid : btnList;
    activeBtn.classList.add('text-navy-800', 'dark:text-white');
    inactiveBtn.classList.add('text-slate-500', 'dark:text-slate-400');

    if (!animate) {
        // Initial load — tampilkan langsung tanpa animasi
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

    // ── Animasi keluar → masuk ──
    var outEl = v === 'grid' ? listEl : gridEl;
    var inEl  = v === 'grid' ? gridEl : listEl;

    // Tentukan arah animasi: grid dari kanan, list dari kiri
    var inFrom = v === 'grid' ? 'translateX(32px)' : 'translateX(-32px)';

    // Step 1: fade + slide OUT
    outEl.style.transition = 'opacity 0.22s ease, transform 0.22s cubic-bezier(0.4,0,1,1)';
    outEl.style.opacity    = '0';
    outEl.style.transform  = v === 'grid' ? 'translateX(-24px)' : 'translateX(24px)';
    outEl.style.pointerEvents = 'none';

    setTimeout(function() {
        // Sembunyikan outEl, tampilkan inEl
        outEl.style.display   = 'none';
        outEl.style.opacity   = '';
        outEl.style.transform = '';
        outEl.style.transition = '';
        outEl.style.pointerEvents = '';

        // Set inEl — siap untuk animasi masuk
        inEl.style.display    = v === 'grid' ? 'grid' : 'flex';
        if (v === 'list') inEl.style.flexDirection = 'column';
        inEl.style.opacity    = '0';
        inEl.style.transform  = inFrom;
        inEl.style.transition = 'none';

        // Force reflow
        void inEl.offsetWidth;

        // Step 2: fade + slide IN dengan spring-like easing
        inEl.style.transition = 'opacity 0.28s ease, transform 0.32s cubic-bezier(0.22,1,0.36,1)';
        inEl.style.opacity    = '1';
        inEl.style.transform  = 'translateX(0)';

        // Animasi stagger pada child cards
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

        // Cleanup transitions setelah selesai
        setTimeout(function() {
            inEl.style.transition = '';
            inEl.style.opacity    = '';
            inEl.style.transform  = '';
        }, 400);

    }, 230);
}
</script>

<style>
.fade-in { animation: fadeIn 0.45s ease-out forwards; }
@keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

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

/* Grid view layout */
#view-grid { display: none; }
</style>
@endsection
