@extends('layouts.app')

@section('page-title', 'Laporan Absensi')

@section('content')
    <div class="fade-in space-y-6">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Laporan Absensi</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Lihat dan ekspor data absensi guru</p>
            </div>
            <button onclick="exportReport()" class="btn-primary flex items-center gap-2 w-fit">
                <i data-lucide="download" class="w-4 h-4"></i>
                Export CSV
            </button>
        </div>

        <!-- Filters -->
        <div class="card p-5">
            <form method="GET" action="{{ route('reports.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                            Tanggal Mulai
                        </label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                            Tanggal Akhir
                        </label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Teacher Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                            Guru
                        </label>
                        <select name="teacher_id" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Guru</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ $selectedTeacher == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-navy-800 hover:bg-navy-900 text-white rounded-xl text-sm font-semibold transition-colors">
                            Filter
                        </button>
                        <a href="{{ route('reports.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-colors">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Absensi -->
            <div class="card-hover card p-5 group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="file-text" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Total Absensi</p>
                        <h3 class="text-2xl font-bold text-navy-800 dark:text-white mt-1">{{ $stats->total ?? 0 }}</h3>
                        <p class="text-[10px] text-slate-400 mt-1">Laporan periode ini</p>
                    </div>
                </div>
            </div>

            <!-- Hadir -->
            <div class="card-hover card p-5 group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Hadir</p>
                        <h3 class="text-2xl font-bold text-navy-800 dark:text-white mt-1">{{ $stats->hadir ?? 0 }}</h3>
                        <p class="text-[10px] text-green-500 mt-1">{{ ($stats->total ?? 0) > 0 ? round((($stats->hadir ?? 0) / ($stats->total ?? 1)) * 100) : 0 }}% tingkat kehadiran</p>
                    </div>
                </div>
            </div>

            <!-- Terlambat -->
            <div class="card-hover card p-5 group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Terlambat</p>
                        <h3 class="text-2xl font-bold text-navy-800 dark:text-white mt-1">{{ $stats->terlambat ?? 0 }}</h3>
                        <p class="text-[10px] text-yellow-600 mt-1">Perlu tinjauan ulang</p>
                    </div>
                </div>
            </div>

            <!-- Alpha/Izin -->
            <div class="card-hover card p-5 group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Alpha/Izin</p>
                        <h3 class="text-2xl font-bold text-navy-800 dark:text-white mt-1">{{ ($stats->alpha ?? 0) + ($stats->izin ?? 0) }}</h3>
                        <p class="text-[10px] text-red-500 mt-1">Ketidakhadiran guru</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guru</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Masuk</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Keluar</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($attendances as $att)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-semibold text-navy-800 dark:text-white">{{ $att->name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $att->email }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-navy-800 dark:text-white">
                                        {{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ \Carbon\Carbon::parse($att->date)->locale('id')->isoFormat('dddd') }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($att->check_in)
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="clock" class="w-4 h-4 text-green-500"></i>
                                            <span class="text-sm font-mono text-slate-700 dark:text-slate-300">{{ $att->check_in }}</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($att->check_out)
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="clock" class="w-4 h-4 text-blue-500"></i>
                                            <span class="text-sm font-mono text-slate-700 dark:text-slate-300">{{ $att->check_out }}</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusStyles = [
                                            'Hadir' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                            'Terlambat' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                            'Izin' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                            'Alpha' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        ];
                                        $statusIcons = [
                                            'Hadir' => 'check-circle',
                                            'Terlambat' => 'clock',
                                            'Izin' => 'file-check',
                                            'Alpha' => 'x-circle',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusStyles[$att->status] ?? 'bg-slate-100' }}">
                                        <i data-lucide="{{ $statusIcons[$att->status] ?? 'circle' }}" class="w-3.5 h-3.5"></i>
                                        {{ $att->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center">
                                            <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada data absensi</p>
                                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Ubah filter untuk melihat data lain</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($attendances->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function exportReport() {
            // Get current filter values
            const startDate = document.querySelector('[name="start_date"]').value;
            const endDate = document.querySelector('[name="end_date"]').value;
            const teacherId = document.querySelector('[name="teacher_id"]').value;

            // Build URL with parameters
            let url = '{{ route("reports.export-csv") }}';
            url += `?start_date=${startDate}&end_date=${endDate}`;
            if (teacherId) {
                url += `&teacher_id=${teacherId}`;
            }

            // Open in new tab to download
            window.open(url, '_blank');
        }

        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>

    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endsection