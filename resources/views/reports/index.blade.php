@extends('layouts.app')

@section('page-title', 'Laporan Absensi')

@section('content')
    <div class="fade-in space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                    <i data-lucide="bar-chart-3" class="w-6 h-6 text-white dark:text-navy-900"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Laporan Absensi</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} —
                        {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    </p>
                </div>
            </div>
            <a href="{{ route('reports.export', ['view_mode' => $viewMode, 'start_date' => $startDate, 'end_date' => $endDate, 'search' => $search]) }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                <i data-lucide="download" class="w-4 h-4"></i>
                Export Excel
            </a>
        </div>

        <!-- Filter Card -->
        <div class="card p-5">
            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="hidden" name="view_mode" id="viewModeInput" value="{{ $viewMode }}">
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Cari Nama
                        Guru</label>
                    <div class="relative">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama guru..."
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all shadow-sm hover:shadow"
                            oninput="fetchReports()">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all shadow-sm hover:shadow"
                        onchange="document.getElementById('viewModeInput').value='custom'; fetchReports()">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all shadow-sm hover:shadow"
                        onchange="document.getElementById('viewModeInput').value='custom'; fetchReports()">
                </div>
                
                <div class="md:col-span-4" id="viewModeButtons">
                    <div class="flex gap-1 p-1 bg-slate-100 dark:bg-slate-700 rounded-lg inline-flex">
                        <button type="button" onclick="setViewMode('daily')"
                            class="px-4 py-2 rounded-md text-xs font-semibold transition-all text-center {{ $viewMode === 'daily' ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow' : 'text-slate-600 dark:text-slate-400 hover:bg-white/50 dark:hover:bg-slate-600' }}">
                            Harian
                        </button>
                        <button type="button" onclick="setViewMode('weekly')"
                            class="px-4 py-2 rounded-md text-xs font-semibold transition-all text-center {{ $viewMode === 'weekly' ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow' : 'text-slate-600 dark:text-slate-400 hover:bg-white/50 dark:hover:bg-slate-600' }}">
                            Mingguan
                        </button>
                        <button type="button" onclick="setViewMode('monthly')"
                            class="px-4 py-2 rounded-md text-xs font-semibold transition-all text-center {{ $viewMode === 'monthly' ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow' : 'text-slate-600 dark:text-slate-400 hover:bg-white/50 dark:hover:bg-slate-600' }}">
                            Bulanan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div id="report-container" class="space-y-6 transition-opacity duration-300">

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="card p-4 group hover:shadow-lg transition-all">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">Total Absensi</p>
                        <h3 class="text-xl font-bold text-navy-800 dark:text-white">{{ $totalAbsensi }}</h3>
                        <p class="text-[10px] text-blue-500">Laporan periode ini</p>
                    </div>
                </div>
            </div>

            <div class="card p-4 group hover:shadow-lg transition-all">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">Hadir</p>
                        <h3 class="text-xl font-bold text-navy-800 dark:text-white">{{ $totalStats['hadir'] }}</h3>
                        <p class="text-[10px] text-green-500">{{ $kehadiranRate }}% tingkat kehadiran</p>
                    </div>
                </div>
            </div>

            <div class="card p-4 group hover:shadow-lg transition-all">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="clock" class="w-5 h-5 text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">Terlambat</p>
                        <h3 class="text-xl font-bold text-navy-800 dark:text-white">{{ $totalStats['terlambat'] }}</h3>
                        <p class="text-[10px] text-yellow-600">Perlu tinjauan ulang</p>
                    </div>
                </div>
            </div>

            <div class="card p-4 group hover:shadow-lg transition-all">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="x-circle" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">Alpha/Izin</p>
                        <h3 class="text-xl font-bold text-navy-800 dark:text-white">
                            {{ $totalStats['alpha'] + $totalStats['izin'] + $totalStats['sakit'] }}</h3>
                        <p class="text-[10px] text-red-500">Ketidakhadiran guru</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="flex flex-wrap gap-3 p-4 card">
            <div class="flex items-center gap-2">
                <span
                    class="w-6 h-6 rounded bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 flex items-center justify-center text-xs font-bold">H</span>
                <span class="text-xs text-slate-600 dark:text-slate-400">Hadir</span>
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="w-6 h-6 rounded bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 flex items-center justify-center text-xs font-bold">T</span>
                <span class="text-xs text-slate-600 dark:text-slate-400">Telat</span>
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="w-6 h-6 rounded bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 flex items-center justify-center text-xs font-bold">A</span>
                <span class="text-xs text-slate-600 dark:text-slate-400">Alfa</span>
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="w-6 h-6 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 flex items-center justify-center text-xs font-bold">I</span>
                <span class="text-xs text-slate-600 dark:text-slate-400">Izin</span>
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="w-6 h-6 rounded bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-400 flex items-center justify-center text-xs font-bold">S</span>
                <span class="text-xs text-slate-600 dark:text-slate-400">Sakit</span>
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="w-6 h-6 rounded bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center text-xs font-bold">-</span>
                <span class="text-xs text-slate-600 dark:text-slate-400">Libur</span>
            </div>
        </div>

        <!-- Table -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50">
                            <th
                                class="sticky left-0 z-10 bg-slate-50 dark:bg-slate-800/50 px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-r border-slate-200 dark:border-slate-700 min-w-[200px]">
                                Nama</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-r border-slate-200 dark:border-slate-700 min-w-[120px]">
                                Mapel</th>
                            @foreach($dates as $date)
                                <th
                                    class="px-2 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700 min-w-[45px]">
                                    <div>{{ $date->format('d') }}</div>
                                    <div class="text-[9px] font-normal text-slate-400">{{ $date->format('D') }}</div>
                                </th>
                            @endforeach
                            <th
                                class="px-3 py-3 text-center text-xs font-semibold text-green-600 dark:text-green-400 border-b border-l-2 border-l-navy-200 dark:border-l-navy-800 border-slate-200 dark:border-slate-700 bg-green-50/50 dark:bg-green-900/10">
                                H</th>
                            <th
                                class="px-3 py-3 text-center text-xs font-semibold text-blue-600 dark:text-blue-400 border-b border-slate-200 dark:border-slate-700 bg-blue-50/50 dark:bg-blue-900/10">
                                I</th>
                            <th
                                class="px-3 py-3 text-center text-xs font-semibold text-cyan-600 dark:text-cyan-400 border-b border-slate-200 dark:border-slate-700 bg-cyan-50/50 dark:bg-cyan-900/10">
                                S</th>
                            <th
                                class="px-3 py-3 text-center text-xs font-semibold text-red-600 dark:text-red-400 border-b border-slate-200 dark:border-slate-700 bg-red-50/50 dark:bg-red-900/10">
                                A</th>
                            <th
                                class="px-3 py-3 text-center text-xs font-semibold text-yellow-600 dark:text-yellow-400 border-b border-slate-200 dark:border-slate-700 bg-yellow-50/50 dark:bg-yellow-900/10">
                                T</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($reportData as $report)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td
                                    class="sticky left-0 z-10 bg-white dark:bg-slate-900 px-4 py-3 border-r border-slate-200 dark:border-slate-700">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $report['user']->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($report['user']->name) . '&background=0F172A&color=fff' }}"
                                            class="w-8 h-8 rounded-full object-cover border-2 border-slate-200 dark:border-slate-700 flex-shrink-0">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-navy-800 dark:text-white truncate">
                                                {{ $report['user']->name }}</p>
                                            <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">
                                                {{ $report['user']->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 border-r border-slate-200 dark:border-slate-700">
                                    <p class="text-xs text-slate-700 dark:text-slate-300">
                                        {{ $report['user']->subject ?? '-' }}</p>
                                </td>
                                @foreach($dates as $date)
                                    @php
                                        $dateStr = $date->toDateString();
                                        $dayData = $report['days'][$dateStr] ?? ['code' => '-', 'status' => 'libur'];
                                        $code = $dayData['code'];

                                        $badgeClass = match ($code) {
                                            'H' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                            'T' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                            'I' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                            'S' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-400',
                                            'A' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                            default => 'bg-slate-100 text-slate-400 dark:bg-slate-700 dark:text-slate-500',
                                        };
                                    @endphp
                                    <td class="px-2 py-3 text-center border-slate-200 dark:border-slate-700">
                                        <span
                                            class="inline-flex items-center justify-center w-7 h-7 rounded text-xs font-bold {{ $badgeClass }}">
                                            {{ $code }}
                                        </span>
                                    </td>
                                @endforeach
                                <td
                                    class="px-3 py-3 text-center font-bold text-green-600 dark:text-green-400 border-l-2 border-l-navy-200 dark:border-l-navy-800 bg-green-50/30 dark:bg-green-900/10">
                                    {{ $report['summary']['H'] }}
                                </td>
                                <td
                                    class="px-3 py-3 text-center font-bold text-blue-600 dark:text-blue-400 bg-blue-50/30 dark:bg-blue-900/10">
                                    {{ $report['summary']['I'] }}
                                </td>
                                <td
                                    class="px-3 py-3 text-center font-bold text-cyan-600 dark:text-cyan-400 bg-cyan-50/30 dark:bg-cyan-900/10">
                                    {{ $report['summary']['S'] }}
                                </td>
                                <td
                                    class="px-3 py-3 text-center font-bold text-red-600 dark:text-red-400 bg-red-50/30 dark:bg-red-900/10">
                                    {{ $report['summary']['A'] }}
                                </td>
                                <td
                                    class="px-3 py-3 text-center font-bold text-yellow-600 dark:text-yellow-400 bg-yellow-50/30 dark:bg-yellow-900/10">
                                    {{ $report['summary']['T'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 2 + count($dates) + 5 }}" class="px-6 py-12 text-center">
                                    <i data-lucide="inbox"
                                        class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada data guru</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });

        let fetchTimeout;
        function fetchReports() {
            clearTimeout(fetchTimeout);
            fetchTimeout = setTimeout(() => {
                const form = document.getElementById('filterForm');
                const url = new URL('{{ route('reports.index') }}');
                const searchParams = new URLSearchParams(new FormData(form));
                url.search = searchParams.toString();
                
                document.getElementById('report-container').style.opacity = '0.5';

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        document.getElementById('report-container').innerHTML = doc.getElementById('report-container').innerHTML;
                        document.getElementById('report-container').style.opacity = '1';
                        
                        // Update form dates just in case the server calculated them
                        document.querySelector('input[name="start_date"]').value = doc.querySelector('input[name="start_date"]').value;
                        document.querySelector('input[name="end_date"]').value = doc.querySelector('input[name="end_date"]').value;
                        document.getElementById('viewModeInput').value = doc.getElementById('viewModeInput').value;
                        document.getElementById('viewModeButtons').innerHTML = doc.getElementById('viewModeButtons').innerHTML;
                        
                        // Reinitialize lucide icons inside the container
                        if (window.lucide) {
                            lucide.createIcons();
                        }
                        
                        // Update export button link
                        const exportBtn = document.querySelector('a[href*="reports/export"]');
                        if (exportBtn) {
                            const exportUrl = new URL(exportBtn.href);
                            exportUrl.searchParams.set('start_date', searchParams.get('start_date'));
                            exportUrl.searchParams.set('end_date', searchParams.get('end_date'));
                            exportUrl.searchParams.set('search', searchParams.get('search'));
                            exportUrl.searchParams.set('view_mode', searchParams.get('view_mode'));
                            exportBtn.href = exportUrl.toString();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching reports:', error);
                        document.getElementById('report-container').style.opacity = '1';
                    });
            }, 500); // 500ms debounce
        }
        
        function setViewMode(mode) {
            document.getElementById('viewModeInput').value = mode;
            // Clear inputs so backend uses current date as base
            document.querySelector('input[name="start_date"]').value = '';
            document.querySelector('input[name="end_date"]').value = '';
            fetchReports();
        }
    </script>
@endsection