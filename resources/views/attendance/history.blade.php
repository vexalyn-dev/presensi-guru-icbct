@extends('layouts.app')

@section('page-title', 'Riwayat Presensi')

@section('content')
<div class="space-y-6 animate-fade-in">
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Presensi -->
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar-range" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total Presensi</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['total'] }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1">Periode terpilih</p>
                </div>
            </div>
        </div>
        
        <!-- Hadir -->
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Hadir</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['hadir'] }}</h3>
                    <p class="text-[10px] text-green-500 mt-1">{{ $stats['total'] > 0 ? round(($stats['hadir'] / $stats['total']) * 100) : 0 }}% dari total</p>
                </div>
            </div>
        </div>
        
        <!-- Terlambat -->
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Terlambat</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['terlambat'] }}</h3>
                    <p class="text-[10px] text-yellow-500 mt-1">Butuh perhatian</p>
                </div>
            </div>
        </div>
        
        <!-- Alpha -->
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Alpha</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['alpha'] }}</h3>
                    <p class="text-[10px] text-red-500 mt-1">Tanpa keterangan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-4">
        <form action="{{ route('attendance.history') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="input-field text-sm py-2">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="input-field text-sm py-2">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Guru</label>
                <select name="teacher_id" class="input-field text-sm py-2">
                    <option value="">Semua Guru</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Status</label>
                <select name="status" class="input-field text-sm py-2">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary py-2 px-4 text-sm flex-1">
                    Filter
                </button>
                <a href="{{ route('attendance.export', request()->query()) }}" 
                   class="p-2 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                   title="Export CSV">
                    <i data-lucide="download" class="w-5 h-5"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Guru</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Lokasi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Masuk</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Keluar</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="px-6 py-4 text-sm">
                                <p class="font-medium text-navy-800 dark:text-white">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</p>
                                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($att->date)->isoFormat('dddd') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gold-400 to-gold-500 flex items-center justify-center text-navy-900 font-bold text-xs">
                                        {{ substr($att->user->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-medium text-navy-800 dark:text-white">{{ $att->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($att->location_name)
                                    <div class="flex items-center gap-1.5">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                        <span class="text-xs text-slate-600 dark:text-slate-300">{{ $att->location_name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ $att->check_in ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ $att->check_out ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'Hadir' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                        'Terlambat' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        'Izin' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                        'Alpha' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$att->status] ?? '' }}">
                                    {{ $att->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 max-w-xs truncate">
                                {{ $att->notes ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <i data-lucide="inbox" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                                <p class="text-slate-500 dark:text-slate-400">Tidak ada data presensi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                {{ $attendances->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection