@extends('layouts.app')
@section('page-title', 'Laporan Presensi')
@section('content')

@php
    $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $selectedMonthLabel = $months[$month - 1] . ' ' . $year;
    $selectedUserLabel  = $userId ? ($users->firstWhere('id', $userId)?->name ?? 'Semua Guru') : 'Semua Guru';
    $sumHadir  = count($report) > 0 ? array_sum(array_column($report, 'hadir')) : 0;
    $sumTelat  = count($report) > 0 ? array_sum(array_column($report, 'telat')) : 0;
    $sumAlpha  = count($report) > 0 ? array_sum(array_column($report, 'alpha')) : 0;
    $sumIncomp = count($report) > 0 ? array_sum(array_column($report, 'incomplete_scans')) : 0;
    $avgPct    = count($report) > 0 ? round(array_sum(array_column($report, 'persentase_ketepatan')) / count($report), 1) : 0;
@endphp

<div class="space-y-6 fade-in">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
         style="animation: slideDown 0.4s ease-out both;">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                <i data-lucide="clipboard-list" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white tracking-tight">Laporan Presensi</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kehadiran, ketepatan waktu &amp; scan tidak lengkap</p>
            </div>
        </div>
        <a href="{{ route('reports.attendance.export', request()->query()) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white rounded-xl text-sm font-semibold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-95 w-fit">
            <i data-lucide="download" class="w-4 h-4"></i> Export Excel
        </a>
    </div>

    {{-- FILTER CARD --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5"
         style="animation: slideDown 0.4s ease-out 0.05s both;">
        <form method="GET" action="{{ route('reports.attendance') }}" id="filter-form" class="flex flex-wrap gap-3 items-end">

            {{-- Dropdown Bulan --}}
            <div class="flex-1 min-w-[150px]">
                <input type="hidden" name="month" id="inp-month" value="{{ $month }}">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Bulan</label>
                <button type="button" onclick="toggleDD('dd-month')"
                        class="w-full flex items-center justify-between px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium text-navy-800 dark:text-white hover:bg-white dark:hover:bg-slate-700 transition-all focus:outline-none"
                        id="btn-month">
                    <span id="lbl-month">{{ $months[$month - 1] }}</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform" id="chev-month"></i>
                </button>
                <div id="dd-month" class="dd-portal hidden bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl z-[9999] overflow-hidden max-h-64 overflow-y-auto">
                    @foreach($months as $mi => $ml)
                    <button type="button"
                            onclick="pickDD('dd-month','inp-month','lbl-month','btn-month','chev-month',{{ $mi+1 }},'{{ $ml }}')"
                            class="w-full px-4 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors {{ $month==($mi+1) ? 'font-bold text-navy-800 dark:text-gold-400' : 'text-slate-600 dark:text-slate-300' }}">
                        {{ $ml }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Tahun --}}
            <div class="w-28">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Tahun</label>
                <input type="number" name="year" value="{{ $year }}" min="2020" max="2099"
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium text-navy-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
            </div>

            {{-- Dropdown Guru --}}
            <div class="flex-1 min-w-[200px]">
                <input type="hidden" name="user_id" id="inp-user" value="{{ $userId }}">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Guru</label>
                <button type="button" onclick="toggleDD('dd-user')"
                        class="w-full flex items-center justify-between px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium text-navy-800 dark:text-white hover:bg-white dark:hover:bg-slate-700 transition-all focus:outline-none"
                        id="btn-user">
                    <span class="truncate" id="lbl-user">{{ $selectedUserLabel }}</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform" id="chev-user"></i>
                </button>
                <div id="dd-user" class="dd-portal hidden bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl z-[9999] overflow-hidden max-h-64 overflow-y-auto">
                    <button type="button" onclick="pickDD('dd-user','inp-user','lbl-user','btn-user','chev-user','','Semua Guru')"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors {{ !$userId ? 'font-bold text-navy-800 dark:text-gold-400' : 'text-slate-600 dark:text-slate-300' }}">
                        <i data-lucide="users" class="w-4 h-4 opacity-60"></i> Semua Guru
                    </button>
                    @foreach($users as $u)
                    <button type="button" onclick="pickDD('dd-user','inp-user','lbl-user','btn-user','chev-user','{{ $u->id }}','{{ addslashes($u->name) }}')"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors {{ $userId==$u->id ? 'font-bold text-navy-800 dark:text-gold-400' : 'text-slate-600 dark:text-slate-300' }}">
                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center text-white dark:text-navy-900 text-[10px] font-bold flex-shrink-0">{{ strtoupper(substr($u->name,0,1)) }}</div>
                        <span class="truncate">{{ $u->name }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-bold hover:opacity-90 transition-all shadow-lg flex items-center gap-2">
                <i data-lucide="search" class="w-4 h-4"></i> Filter
            </button>
            @if(request()->hasAny(['month','year','user_id']))
            <a href="{{ route('reports.attendance') }}"
               class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-xl text-sm font-semibold transition-all flex items-center gap-1.5">
                <i data-lucide="x" class="w-4 h-4"></i> Reset
            </a>
            @endif
        </form>
    </div>

    {{-- SUMMARY CARDS --}}
    @if(count($report) > 0)
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        @php
        $statCards = [
            ['label'=>'Guru Dilaporkan', 'val'=>count($report),  'color'=>'slate', 'icon'=>'users'],
            ['label'=>'Total Hadir',     'val'=>$sumHadir,       'color'=>'emerald','icon'=>'check-circle'],
            ['label'=>'Terlambat',       'val'=>$sumTelat,       'color'=>'amber', 'icon'=>'clock'],
            ['label'=>'Alpha',           'val'=>$sumAlpha,       'color'=>'red',   'icon'=>'user-x'],
            ['label'=>'Rata-rata Ketepatan', 'val'=>$avgPct.'%', 'color'=>($avgPct>=90?'emerald':($avgPct>=70?'amber':'red')), 'icon'=>'target'],
        ];
        $colorMap = ['slate'=>['bg'=>'bg-slate-100 dark:bg-slate-800','text'=>'text-slate-600 dark:text-slate-400','val'=>'text-navy-800 dark:text-white'],'emerald'=>['bg'=>'bg-emerald-100 dark:bg-emerald-900/30','text'=>'text-emerald-600 dark:text-emerald-400','val'=>'text-emerald-700 dark:text-emerald-300'],'amber'=>['bg'=>'bg-amber-100 dark:bg-amber-900/30','text'=>'text-amber-600 dark:text-amber-400','val'=>'text-amber-700 dark:text-amber-300'],'red'=>['bg'=>'bg-red-100 dark:bg-red-900/30','text'=>'text-red-600 dark:text-red-400','val'=>'text-red-700 dark:text-red-300']];
        @endphp
        @foreach($statCards as $i => $sc)
        <div class="card p-4 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300"
             style="animation: slideUp 0.4s ease-out {{ 0.1 + $i * 0.06 }}s both;">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-9 h-9 {{ $colorMap[$sc['color']]['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="{{ $sc['icon'] }}" class="w-4 h-4 {{ $colorMap[$sc['color']]['text'] }}"></i>
                </div>
                <p class="text-xs text-slate-400">{{ $sc['label'] }}</p>
            </div>
            <p class="text-2xl font-extrabold {{ $colorMap[$sc['color']]['val'] }} tabular-nums">{{ $sc['val'] }}</p>
            <div class="mt-2 h-0.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full {{ str_replace('100','500',$colorMap[$sc['color']]['bg']) }} rounded-full transition-all duration-700"
                     style="width: 100%; background: currentColor; opacity:.3"></div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- TABEL --}}
    <div class="card overflow-hidden" style="animation: slideUp 0.4s ease-out 0.4s both;">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-navy-800 dark:text-white">Detail Per Guru</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $selectedMonthLabel }}</p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 text-xs font-semibold">{{ count($report) }} guru</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Guru</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Total</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-emerald-500 uppercase tracking-wider">Hadir</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-amber-500 uppercase tracking-wider">Terlambat</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-blue-500 uppercase tracking-wider">Izin/Sakit</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-red-500 uppercase tracking-wider">Alpha</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Ketepatan</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-orange-500 uppercase tracking-wider">Scan Incomplete</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                    @forelse($report as $i => $row)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors"
                        style="animation: fadeInRow 0.3s ease-out {{ $i * 0.04 }}s both;">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center text-white dark:text-navy-900 font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($row['user']->name ?? 'X', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-navy-800 dark:text-white text-sm leading-tight">{{ $row['user']->name ?? '-' }}</p>
                                    <p class="text-[10px] text-slate-400 font-mono">{{ $row['user']->teacher_code ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-center text-slate-600 dark:text-slate-300 font-semibold">{{ $row['total'] }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 text-base">{{ $row['hadir'] }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($row['telat'] > 0)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs font-bold">
                                <i data-lucide="clock" class="w-3 h-3"></i>{{ $row['telat'] }}
                            </span>
                            @else <span class="text-slate-300 dark:text-slate-600 text-xs">-</span> @endif
                        </td>
                        <td class="px-5 py-3.5 text-center text-blue-600 dark:text-blue-400 font-medium">{{ $row['izin_sakit'] ?: '-' }}</td>
                        <td class="px-5 py-3.5 text-center">
                            @if($row['alpha'] > 0)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-bold">
                                <i data-lucide="x" class="w-3 h-3"></i>{{ $row['alpha'] }}
                            </span>
                            @else <span class="text-slate-300 dark:text-slate-600 text-xs">-</span> @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @php $pct = $row['persentase_ketepatan']; @endphp
                            <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold
                                {{ $pct >= 90 ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : ($pct >= 70 ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400') }}">
                                {{ $pct }}%
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($row['incomplete_scans'] > 0)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 text-xs font-bold">
                                <i data-lucide="alert-triangle" class="w-3 h-3"></i>{{ $row['incomplete_scans'] }}
                            </span>
                            @else
                            <span class="text-emerald-500 flex justify-center"><i data-lucide="check" class="w-4 h-4"></i></span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-16 text-center">
                        <i data-lucide="inbox" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                        <p class="text-slate-400 text-sm font-medium">Tidak ada data presensi untuk {{ $selectedMonthLabel }}.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// ── Dropdown Portal ─────────────────────────────────────────
// Dropdown di-append ke body agar tidak terpotong card overflow
var _ddPortalOpen = null;

function toggleDD(ddId) {
    // Cari button via konvensi ID: dd-xxx → btn-xxx
    var btnId = 'btn-' + ddId.replace('dd-', '');
    var btn   = document.getElementById(btnId);
    var dd    = document.getElementById(ddId);
    if (!btn || !dd) return;

    // Tutup dropdown lain dulu
    if (_ddPortalOpen && _ddPortalOpen !== ddId) {
        closeDD(_ddPortalOpen);
    }

    if (dd.classList.contains('hidden')) {
        openDD(ddId, btn);
    } else {
        closeDD(ddId);
    }
}

function openDD(ddId, btn) {
    var dd = document.getElementById(ddId);
    // Pindahkan ke body agar tidak terpotong
    if (dd.parentElement !== document.body) {
        dd.dataset.originalParent = ddId + '-slot';
        document.body.appendChild(dd);
    }
    var rect = btn.getBoundingClientRect();
    dd.style.position = 'fixed';
    dd.style.top      = (rect.bottom + 4) + 'px';
    dd.style.left     = rect.left + 'px';
    dd.style.width    = rect.width + 'px';
    dd.classList.remove('hidden');
    dd.style.opacity  = '0';
    dd.style.transform = 'translateY(-6px)';
    dd.style.transition = 'opacity .18s ease, transform .18s ease';
    requestAnimationFrame(function() {
        dd.style.opacity  = '1';
        dd.style.transform = 'translateY(0)';
    });
    // Update chevron
    var chev = btn.querySelector('[data-lucide="chevron-down"]') || btn.querySelector('i');
    if (chev) chev.style.transform = 'rotate(180deg)';
    _ddPortalOpen = ddId;
}

function closeDD(ddId) {
    var dd = document.getElementById(ddId);
    if (!dd) return;
    dd.style.opacity  = '0';
    dd.style.transform = 'translateY(-6px)';
    setTimeout(function() { dd.classList.add('hidden'); }, 170);
    // Cari button yang terkait via ID naming convention (btn-xxx dari dd-xxx)
    var btnId = 'btn-' + ddId.replace('dd-','');
    var btn = document.getElementById(btnId);
    if (btn) {
        var chev = btn.querySelector('[data-lucide="chevron-down"]') || btn.querySelector('i');
        if (chev) chev.style.transform = '';
    }
    if (_ddPortalOpen === ddId) _ddPortalOpen = null;
}

function pickDD(ddId, inputId, labelId, btnId, chevId, value, label) {
    document.getElementById(inputId).value = value;
    document.getElementById(labelId).textContent = label;
    closeDD(ddId);
}

// Tutup saat klik di luar
document.addEventListener('click', function(e) {
    if (!_ddPortalOpen) return;
    var dd  = document.getElementById(_ddPortalOpen);
    var btnId = 'btn-' + _ddPortalOpen.replace('dd-','');
    var btn = document.getElementById(btnId);
    if (dd && !dd.contains(e.target) && btn && !btn.contains(e.target)) {
        closeDD(_ddPortalOpen);
    }
});

// Update posisi saat scroll/resize
window.addEventListener('scroll', function() {
    if (!_ddPortalOpen) return;
    var btnId = 'btn-' + _ddPortalOpen.replace('dd-','');
    var btn = document.getElementById(btnId);
    var dd  = document.getElementById(_ddPortalOpen);
    if (btn && dd) {
        var rect = btn.getBoundingClientRect();
        dd.style.top  = (rect.bottom + 4) + 'px';
        dd.style.left = rect.left + 'px';
    }
}, true);

document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });
</script>

<style>
.fade-in      { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn   { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
@keyframes slideDown{ from { opacity:0; transform:translateY(-12px); } to { opacity:1; transform:translateY(0); } }
@keyframes slideUp  { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
@keyframes fadeInRow{ from { opacity:0; transform:translateX(-6px); } to { opacity:1; transform:translateX(0); } }
</style>
@endsection
