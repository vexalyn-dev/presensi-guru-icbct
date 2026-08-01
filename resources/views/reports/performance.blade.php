@extends('layouts.app')
@section('page-title', 'Laporan Kinerja')
@section('content')

@php
    $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $selectedMonthLabel = $months[$month - 1] . ' ' . $year;
    $selectedUserLabel  = $userId ? ($users->firstWhere('id', $userId)?->name ?? 'Semua Guru') : 'Semua Guru';
    $totalSesi   = count($performance) > 0 ? array_sum(array_column($performance, 'total_sesi')) : 0;
    $avgAllMenit = count($performance) > 0 ? round(array_sum(array_column($performance, 'avg_menit')) / count($performance)) : 0;
@endphp

<div class="space-y-6 fade-in">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
         style="animation: slideDown 0.4s ease-out both;">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                <i data-lucide="trending-up" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white tracking-tight">Laporan Kinerja & Analitik</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Rata-rata durasi mengajar dan tren kehadiran kelas</p>
            </div>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5"
         style="animation: slideDown 0.4s ease-out 0.05s both;" x-data="{}">
        <form method="GET" action="{{ route('reports.performance') }}" class="flex flex-wrap gap-3 items-end">

            {{-- Dropdown Bulan --}}
            <div class="flex-1 min-w-[150px]">
                <input type="hidden" name="month" id="perf-month" value="{{ $month }}">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Bulan</label>
                <button type="button" onclick="toggleDD('dd-pmonth')"
                        class="w-full flex items-center justify-between px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium text-navy-800 dark:text-white hover:bg-white dark:hover:bg-slate-700 transition-all focus:outline-none"
                        id="btn-pmonth">
                    <span id="lbl-pmonth">{{ $months[$month - 1] }}</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform" id="chev-pmonth"></i>
                </button>
                <div id="dd-pmonth" class="dd-portal hidden bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl z-[9999] overflow-hidden max-h-64 overflow-y-auto">
                    @foreach($months as $mi => $ml)
                    <button type="button"
                            onclick="pickDD('dd-pmonth','perf-month','lbl-pmonth','btn-pmonth','chev-pmonth',{{ $mi+1 }},'{{ $ml }}')"
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
                <input type="hidden" name="user_id" id="perf-user" value="{{ $userId }}">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Guru</label>
                <button type="button" onclick="toggleDD('dd-puser')"
                        class="w-full flex items-center justify-between px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium text-navy-800 dark:text-white hover:bg-white dark:hover:bg-slate-700 transition-all focus:outline-none"
                        id="btn-puser">
                    <span class="truncate" id="lbl-puser">{{ $selectedUserLabel }}</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform" id="chev-puser"></i>
                </button>
                <div id="dd-puser" class="dd-portal hidden bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl z-[9999] overflow-hidden max-h-64 overflow-y-auto">
                    <button type="button" onclick="pickDD('dd-puser','perf-user','lbl-puser','btn-puser','chev-puser','','Semua Guru')"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors {{ !$userId ? 'font-bold text-navy-800 dark:text-gold-400' : 'text-slate-600 dark:text-slate-300' }}">
                        <i data-lucide="users" class="w-4 h-4 opacity-60"></i> Semua Guru
                    </button>
                    @foreach($users as $u)
                    <button type="button" onclick="pickDD('dd-puser','perf-user','lbl-puser','btn-puser','chev-puser','{{ $u->id }}','{{ addslashes($u->name) }}')"
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
        </form>
    </div>

    {{-- SUMMARY --}}
    @if(count($performance) > 0)
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach([
            ['icon'=>'users','label'=>'Guru Dianalisis','val'=>count($performance),'color'=>'slate'],
            ['icon'=>'play-circle','label'=>'Total Sesi Valid','val'=>$totalSesi,'color'=>'blue'],
            ['icon'=>'timer','label'=>'Rata-rata Durasi','val'=>floor($avgAllMenit/60).'j '.($avgAllMenit%60).'m','color'=>'emerald'],
        ] as $i => $sc)
        <div class="card p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300"
             style="animation: slideUp 0.4s ease-out {{ 0.1 + $i*0.07 }}s both;">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                    {{ $sc['color']=='slate' ? 'bg-slate-100 dark:bg-slate-800' : ($sc['color']=='blue' ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-emerald-100 dark:bg-emerald-900/30') }}">
                    <i data-lucide="{{ $sc['icon'] }}" class="w-5 h-5
                        {{ $sc['color']=='slate' ? 'text-slate-500 dark:text-slate-400' : ($sc['color']=='blue' ? 'text-blue-600 dark:text-blue-400' : 'text-emerald-600 dark:text-emerald-400') }}"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400">{{ $sc['label'] }}</p>
                    <p class="text-2xl font-extrabold tabular-nums
                        {{ $sc['color']=='slate' ? 'text-navy-800 dark:text-white' : ($sc['color']=='blue' ? 'text-blue-600 dark:text-blue-400' : 'text-emerald-600 dark:text-emerald-400') }}">
                        {{ $sc['val'] }}
                    </p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- GRAFIK --}}
    <div class="card p-5" style="animation: slideUp 0.4s ease-out 0.35s both;">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-navy-800 dark:text-white">Tren Sesi Mengajar</h3>
            <span class="text-xs text-slate-400">{{ $selectedMonthLabel }}</span>
        </div>
        @if(count($chartData) > 0)
        <canvas id="trendChart" style="max-height:220px;"></canvas>
        @else
        <div class="flex flex-col items-center justify-center py-10 gap-3">
            <i data-lucide="bar-chart-2" class="w-10 h-10 text-slate-300 dark:text-slate-600"></i>
            <p class="text-sm text-slate-400">Belum ada data grafik untuk periode ini.</p>
        </div>
        @endif
    </div>

    {{-- TABEL KINERJA --}}
    <div class="card overflow-hidden" style="animation: slideUp 0.4s ease-out 0.45s both;">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-navy-800 dark:text-white">Rata-rata Durasi per Guru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Diurutkan: durasi terpanjang</p>
            </div>
            <span class="text-xs px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 font-semibold">{{ count($performance) }} guru</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider w-10">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Guru</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Sesi</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Rata-rata Durasi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider min-w-[180px]">Visualisasi (maks 2j)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                    @forelse($performance as $i => $row)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors"
                        style="animation: fadeInRow 0.3s ease-out {{ $i * 0.04 }}s both;">
                        <td class="px-5 py-3.5 text-slate-400 text-xs font-mono">{{ $i + 1 }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($row['user']->name ?? 'X', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-navy-800 dark:text-white leading-tight">{{ $row['user']->name ?? '-' }}</p>
                                    <p class="text-[10px] text-slate-400 font-mono">{{ $row['user']->teacher_code ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-center text-slate-600 dark:text-slate-300 font-semibold">{{ $row['total_sesi'] }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="font-bold font-mono text-base {{ $row['avg_menit'] >= 90 ? 'text-emerald-600 dark:text-emerald-400' : ($row['avg_menit'] >= 45 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">
                                {{ $row['rata_rata_durasi'] }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            @php $pctBar = min(100, ($row['avg_menit'] / 120) * 100); @endphp
                            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700 ease-out {{ $row['avg_menit'] >= 90 ? 'bg-gradient-to-r from-emerald-400 to-emerald-500' : ($row['avg_menit'] >= 45 ? 'bg-gradient-to-r from-amber-400 to-amber-500' : 'bg-gradient-to-r from-red-400 to-red-500') }}"
                                     style="width: {{ $pctBar }}%"></div>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">{{ round($pctBar) }}% dari 2 jam target</p>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-16 text-center">
                        <i data-lucide="inbox" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                        <p class="text-slate-400 text-sm font-medium">Tidak ada data untuk {{ $selectedMonthLabel }}.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Dropdown Portal (sama persis dengan attendance.blade.php)
var _ddPortalOpen = null;
function toggleDD(ddId){var btnId='btn-'+ddId.replace('dd-','');var btn=document.getElementById(btnId);var dd=document.getElementById(ddId);if(!btn||!dd)return;if(_ddPortalOpen&&_ddPortalOpen!==ddId){closeDD(_ddPortalOpen);}if(dd.classList.contains('hidden')){openDD(ddId,btn);}else{closeDD(ddId);}}
function openDD(ddId,btn){var dd=document.getElementById(ddId);if(dd.parentElement!==document.body){document.body.appendChild(dd);}var rect=btn.getBoundingClientRect();dd.style.position='fixed';dd.style.top=(rect.bottom+4)+'px';dd.style.left=rect.left+'px';dd.style.width=rect.width+'px';dd.classList.remove('hidden');dd.style.opacity='0';dd.style.transform='translateY(-6px)';dd.style.transition='opacity .18s ease, transform .18s ease';requestAnimationFrame(function(){dd.style.opacity='1';dd.style.transform='translateY(0)';});var chev=btn.querySelector('i');if(chev)chev.style.transform='rotate(180deg)';_ddPortalOpen=ddId;}
function closeDD(ddId){var dd=document.getElementById(ddId);if(!dd)return;dd.style.opacity='0';dd.style.transform='translateY(-6px)';setTimeout(function(){dd.classList.add('hidden');},170);var btnId='btn-'+ddId.replace('dd-','');var btn=document.getElementById(btnId);if(btn){var chev=btn.querySelector('i');if(chev)chev.style.transform='';}if(_ddPortalOpen===ddId)_ddPortalOpen=null;}
function pickDD(ddId,inputId,labelId,btnId,chevId,value,label){document.getElementById(inputId).value=value;document.getElementById(labelId).textContent=label;closeDD(ddId);}
document.addEventListener('click',function(e){if(!_ddPortalOpen)return;var dd=document.getElementById(_ddPortalOpen);var btnId='btn-'+_ddPortalOpen.replace('dd-','');var btn=document.getElementById(btnId);if(dd&&!dd.contains(e.target)&&btn&&!btn.contains(e.target)){closeDD(_ddPortalOpen);}});
window.addEventListener('scroll',function(){if(!_ddPortalOpen)return;var btnId='btn-'+_ddPortalOpen.replace('dd-','');var btn=document.getElementById(btnId);var dd=document.getElementById(_ddPortalOpen);if(btn&&dd){var rect=btn.getBoundingClientRect();dd.style.top=(rect.bottom+4)+'px';dd.style.left=rect.left+'px';}},true);
document.addEventListener('DOMContentLoaded', function() {
    if (window.lucide) lucide.createIcons();

    @if(count($chartData) > 0)
    var isDark    = document.documentElement.classList.contains('dark');
    var gridColor = isDark ? 'rgba(148,163,184,0.08)' : 'rgba(148,163,184,0.2)';
    var tickColor = isDark ? '#94a3b8' : '#64748b';

    var ctx = document.getElementById('trendChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'), array_keys($chartData))) !!},
            datasets: [{
                label: 'Sesi Mengajar',
                data: {!! json_encode(array_values($chartData)) !!},
                borderColor: '#3b82f6',
                backgroundColor: function(ctx) {
                    var g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 200);
                    g.addColorStop(0, 'rgba(59,130,246,0.18)');
                    g.addColorStop(1, 'rgba(59,130,246,0)');
                    return g;
                },
                fill: true, tension: 0.45,
                pointBackgroundColor: '#3b82f6',
                pointRadius: 4, pointHoverRadius: 7,
                borderWidth: 2.5,
            }]
        },
        options: {
            responsive: true, animation: { duration: 800, easing: 'easeOutQuart' },
            plugins: {
                legend: { labels: { color: tickColor, font: { size: 12 } } },
                tooltip: { mode: 'index', intersect: false, backgroundColor: isDark ? '#1e293b' : '#fff', titleColor: isDark ? '#f1f5f9' : '#1e3a5f', bodyColor: isDark ? '#94a3b8' : '#475569', borderColor: isDark ? '#334155' : '#e2e8f0', borderWidth: 1 }
            },
            scales: {
                y: { beginAtZero: true, ticks: { color: tickColor, stepSize: 1 }, grid: { color: gridColor } },
                x: { ticks: { color: tickColor }, grid: { color: gridColor } }
            }
        }
    });
    @endif
});
</script>

<style>
.fade-in      { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn   { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
@keyframes slideDown{ from { opacity:0; transform:translateY(-12px);} to { opacity:1; transform:translateY(0); } }
@keyframes slideUp  { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
@keyframes fadeInRow{ from { opacity:0; transform:translateX(-6px); } to { opacity:1; transform:translateX(0); } }
</style>
@endsection
