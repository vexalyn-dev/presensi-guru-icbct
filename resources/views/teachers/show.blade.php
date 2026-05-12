@extends('layouts.app')

@section('page-title', 'Data Guru')

@section('content')
<div class="fade-in">
    
    <!-- Page Header with Modern Back Button -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <!-- Modern Back Button -->
            <a href="{{ route('teachers.index') }}" class="group flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600">
                <i data-lucide="arrow-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
                <span>Kembali</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Data Guru</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 realtime-datetime">Informasi lengkap guru</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Profile & Attendance (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Profile Card -->
            <div class="card p-6">
                <div class="flex items-start gap-6">
                    <img src="{{ $teacher->photo_url }}" class="w-28 h-28 rounded-2xl object-cover border-4 border-slate-200 dark:border-slate-700 shadow-lg flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold text-navy-800 dark:text-white mb-1 truncate">{{ $teacher->name }}</h2>
                        <p class="text-slate-500 dark:text-slate-400 mb-4 truncate">{{ $teacher->email }}</p>
                        
                        <!-- Badges -->
                        <div class="flex flex-wrap items-center gap-2 mb-4">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ $teacher->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-slate-100 text-slate-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $teacher->is_active ? 'bg-green-500' : 'bg-slate-400' }} mr-1.5"></span>
                                {{ $teacher->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                <i data-lucide="user" class="w-3 h-3 mr-1"></i>
                                {{ ucfirst($teacher->role) }}
                            </span>
                            @if($teacher->subject)
                            <div class="flex flex-col gap-2 mb-4">
                                <span class="inline-flex items-center w-fit px-3 py-1.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 uppercase tracking-wide">
                                    <i data-lucide="book" class="w-3 h-3 mr-1"></i>
                                    {{ $teacher->subject }}
                                </span>
                                @if($subjectDetails && $subjectDetails->description)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 italic">
                                        "{{ $subjectDetails->description }}"
                                    </p>
                                @endif
                            </div>
                            @endif
                        </div>
                        
                        <!-- Contact Info - BOTH FULL WIDTH, STACKED VERTICALLY -->
                        <div class="grid grid-cols-1 gap-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                            @if($teacher->phone)
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="phone" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0.5">Telepon</p>
                                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $teacher->phone }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($teacher->address)
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i data-lucide="map-pin" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0.5">Lokasi</p>
                                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300 break-words">{{ $teacher->address }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Attendance -->
            <div class="card overflow-hidden">
                <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-white dark:from-slate-800/50 dark:to-slate-800/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-navy-800 dark:text-white">Riwayat Presensi Terakhir</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">5 presensi terbaru</p>
                        </div>
                        <a href="{{ route('attendance.history') }}" class="text-xs text-gold-500 hover:text-gold-600 font-medium flex items-center gap-1 transition-colors group">
                            Lihat Semua
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Masuk</th>
                                <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jam Keluar</th>
                                <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @forelse($attendances as $att)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="text-sm font-medium text-navy-800 dark:text-white">
                                                {{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}
                                            </p>
                                            <p class="text-[10px] text-slate-500 dark:text-slate-400">
                                                {{ \Carbon\Carbon::parse($att->date)->locale('id')->isoFormat('dddd') }}
                                            </p>
                                        </div>
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
                                            $statusConfig = [
                                                'Hadir' => [
                                                    'bg' => 'bg-green-100 dark:bg-green-900/30',
                                                    'text' => 'text-green-700 dark:text-green-400',
                                                    'icon' => 'check-circle',
                                                    'iconColor' => 'text-green-600 dark:text-green-400'
                                                ],
                                                'Terlambat' => [
                                                    'bg' => 'bg-yellow-100 dark:bg-yellow-900/30',
                                                    'text' => 'text-yellow-700 dark:text-yellow-400',
                                                    'icon' => 'clock',
                                                    'iconColor' => 'text-yellow-600 dark:text-yellow-400'
                                                ],
                                                'Izin' => [
                                                    'bg' => 'bg-blue-100 dark:bg-blue-900/30',
                                                    'text' => 'text-blue-700 dark:text-blue-400',
                                                    'icon' => 'file-check',
                                                    'iconColor' => 'text-blue-600 dark:text-blue-400'
                                                ],
                                                'Alpha' => [
                                                    'bg' => 'bg-red-100 dark:bg-red-900/30',
                                                    'text' => 'text-red-700 dark:text-red-400',
                                                    'icon' => 'x-circle',
                                                    'iconColor' => 'text-red-600 dark:text-red-400'
                                                ],
                                            ];
                                            
                                            $config = $statusConfig[$att->status] ?? $statusConfig['Hadir'];
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                                            <i data-lucide="{{ $config['icon'] }}" class="w-3.5 h-3.5 {{ $config['iconColor'] }}"></i>
                                            {{ $att->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center">
                                                <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada riwayat presensi</p>
                                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Presensi akan muncul setelah guru melakukan check-in</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Sidebar (1/3 width) -->
        <div class="space-y-6">
            
            <!-- Quick Actions -->
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-gold-400 to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-gold-400/30">
                        <i data-lucide="zap" class="w-5 h-5 text-white"></i>
                    </div>
                    <h3 class="text-base font-bold text-navy-800 dark:text-white">Aksi Cepat</h3>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('teachers.qr', $teacher) }}" class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors group">
                        <div class="w-9 h-9 bg-gold-100 dark:bg-gold-900/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i data-lucide="qr-code" class="w-4 h-4 text-gold-600 dark:text-gold-400"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Lihat QR Code</span>
                    </a>
                    <a href="{{ route('teachers.edit', $teacher) }}" class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors group">
                        <div class="w-9 h-9 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i data-lucide="edit-2" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Edit Profile</span>
                    </a>
                    <a href="{{ route('attendance.history') }}" class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors group">
                        <div class="w-9 h-9 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i data-lucide="calendar" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Riwayat Lengkap</span>
                    </a>
                </div>
            </div>

            <!-- Attendance Stats -->
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-400/30">
                        <i data-lucide="circle-help" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Statistik Kehadiran</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Total presensi guru</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-4 h-4 text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs text-green-600 dark:text-green-400 font-medium">Hadir</p>
                                <p class="text-xl font-bold text-green-700 dark:text-green-300">{{ $stats['total_hadir'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl border border-yellow-200 dark:border-yellow-800">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                                <i data-lucide="clock" class="w-4 h-4 text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">Terlambat</p>
                                <p class="text-xl font-bold text-yellow-700 dark:text-yellow-300">{{ $stats['total_terlambat'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                <i data-lucide="file-check" class="w-4 h-4 text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">Izin</p>
                                <p class="text-xl font-bold text-blue-700 dark:text-blue-300">{{ $stats['total_izin'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center">
                                <i data-lucide="circle-x" class="w-4 h-4 text-white"></i>
                            </div>
                            <div>
                                <p class="text-xs text-red-600 dark:text-red-400 font-medium">Alpha</p>
                                <p class="text-xl font-bold text-red-700 dark:text-red-300">{{ $stats['total_alpha'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Real-time clock with WIB timezone
    function updateRealtimeClock() {
        const datetimeEl = document.querySelector('.realtime-datetime');
        if (!datetimeEl) return;
        
        const now = new Date();
        
        // Format with WIB timezone
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            timeZone: 'Asia/Jakarta',
            hour12: false
        };
        
        const formattedDateTime = now.toLocaleDateString('id-ID', options);
        datetimeEl.textContent = formattedDateTime;
    }

    // Update clock immediately and every second
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        updateRealtimeClock();
        setInterval(updateRealtimeClock, 1000);
    });
</script>
@endsection