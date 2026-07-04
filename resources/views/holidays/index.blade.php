@extends('layouts.app')

@section('page-title', 'Kalender Libur')

@section('content')
<div class="fade-in space-y-6">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                <i data-lucide="calendar-off" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-navy-800 dark:text-white tracking-tight">Kalender Libur</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola hari libur nasional dan sekolah</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- ✅ Button Import - Navy/Gold Theme -->
            <form action="{{ route('holidays.fetch-national') }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="px-5 py-2.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex items-center gap-2">
                    <i data-lucide="download-cloud" class="w-4 h-4"></i>
                    Import Libur Nasional
                </button>
            </form>
            
            <!-- ✅ Button Tambah - Gold Outline Style -->
            <button onclick="openAddModal()"
                    class="px-5 py-2.5 bg-gold-400 hover:bg-gold-500 text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg shadow-gold-400/30 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Libur
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-5 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="calendar" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Sabtu & Minggu</p>
                    <h3 class="text-lg font-bold text-green-600 dark:text-green-400 mt-1">Otomatis Libur</h3>
                </div>
            </div>
        </div>

        <div class="card p-5 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="flag" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Libur Nasional</p>
                    <h3 class="text-lg font-bold text-navy-800 dark:text-white mt-1">{{ \App\Models\Holiday::where('type', 'national')->count() }}</h3>
                </div>
            </div>
        </div>

        <div class="card p-5 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="graduation-cap" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Libur Sekolah</p>
                    <h3 class="text-lg font-bold text-navy-800 dark:text-white mt-1">{{ \App\Models\Holiday::where('type', 'school')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ CARD 1: LIBUR MENDATANG (FULL WIDTH - ATAS) -->
    <div class="card p-6">
        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                    <i data-lucide="calendar-clock" class="w-5 h-5 text-white dark:text-navy-900"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-navy-800 dark:text-white">Libur Mendatang</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Jadwal hari libur yang akan datang</p>
                </div>
            </div>
            <span class="px-3 py-1 bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400 text-xs font-semibold rounded-full">
                {{ $upcomingHolidays->count() }} Libur
            </span>
        </div>

        <!-- Grid Libur Mendatang -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($upcomingHolidays as $holiday)
            <div class="group relative p-4 bg-gradient-to-br from-slate-50 to-white dark:from-slate-700/30 dark:to-slate-800/30 rounded-xl border border-slate-200/60 dark:border-slate-700/60 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                <!-- Type Badge -->
                <div class="flex items-start justify-between mb-3">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-full
                        {{ $holiday->type === 'national' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' }}">
                        <i data-lucide="{{ $holiday->type === 'national' ? 'flag' : 'graduation-cap' }}" class="w-3 h-3"></i>
                        {{ $holiday->type === 'national' ? 'Nasional' : 'Sekolah' }}
                    </span>
                    
                    @if($holiday->is_recurring)
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-slate-400" title="Berulang setiap tahun"></i>
                    @endif
                </div>

                <!-- Holiday Name -->
                <h4 class="text-sm font-bold text-navy-800 dark:text-white mb-2 line-clamp-2 min-h-[2.5rem]">
                    {{ $holiday->name }}
                </h4>

                <!-- Date Info -->
                <div class="flex items-center gap-2 pt-3 border-t border-slate-200/60 dark:border-slate-700/60">
                    <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-lg flex flex-col items-center justify-center flex-shrink-0">
                        <span class="text-[9px] font-bold text-white dark:text-navy-900 uppercase">
                            {{ $holiday->date->format('M') }}
                        </span>
                        <span class="text-sm font-bold text-white dark:text-navy-900 leading-none">
                            {{ $holiday->date->format('d') }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-navy-800 dark:text-white truncate">
                            {{ $holiday->date->locale('id')->isoFormat('dddd') }}
                        </p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">
                            {{ $holiday->date->locale('id')->isoFormat('D MMMM YYYY') }}
                        </p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="calendar-x" class="w-8 h-8 text-slate-400"></i>
                    </div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Tidak ada libur mendatang</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Tambahkan hari libur baru atau import dari nasional</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- ✅ CARD 2: DAFTAR HARI LIBUR (FULL WIDTH - BAWAH) -->
    <div class="card overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-white dark:from-slate-800/50 dark:to-slate-800/30">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                        <i data-lucide="list" class="w-5 h-5 text-white dark:text-navy-900"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Daftar Hari Libur</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Semua hari libur yang terdaftar</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-navy-100 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 text-xs font-semibold rounded-full">
                    Total: {{ $holidays->total() }}
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Libur</th>
                        <th class="px-6 py-4 text-left text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-4 text-center text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Berulang</th>
                        <th class="px-6 py-4 text-right text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($holidays as $holiday)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-lg flex flex-col items-center justify-center flex-shrink-0">
                                    <span class="text-[9px] font-bold text-white dark:text-navy-900 uppercase leading-none">
                                        {{ $holiday->date->format('M') }}
                                    </span>
                                    <span class="text-sm font-bold text-white dark:text-navy-900 leading-none">
                                        {{ $holiday->date->format('d') }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-navy-800 dark:text-white">{{ $holiday->date->format('d M Y') }}</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ $holiday->date->locale('id')->isoFormat('dddd') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-navy-800 dark:text-white">{{ $holiday->name }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold rounded-full
                                {{ $holiday->type === 'national' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' }}">
                                <i data-lucide="{{ $holiday->type === 'national' ? 'flag' : 'graduation-cap' }}" class="w-3 h-3"></i>
                                {{ $holiday->type === 'national' ? 'Nasional' : 'Sekolah' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($holiday->is_recurring)
                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-gold-600 dark:text-gold-400">
                                <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                                Ya
                            </span>
                            @else
                            <span class="text-[10px] text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button onclick="openDeleteModal({{ json_encode(['id' => $holiday->id, 'name' => $holiday->name]) }})"
                                    class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all hover:-translate-y-0.5">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center">
                                    <i data-lucide="calendar-off" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Belum ada hari libur</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Mulai dengan menambahkan hari libur pertama</p>
                                </div>
                                <button onclick="openAddModal()"
                                        class="px-4 py-2 bg-gold-400 hover:bg-gold-500 text-navy-900 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                    Tambah Libur
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($holidays->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30">
            {{ $holidays->links() }}
        </div>
        @endif
    </div>
</div>

<!-- ✅ ADD HOLIDAY MODAL -->
<div id="addHolidayModal"
     style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:1rem;">

    <!-- Backdrop -->
    <div onclick="closeAddModal()"
         style="position:absolute; inset:0; background:rgba(15,23,42,0.65); backdrop-filter:blur(4px);"></div>

    <!-- Modal Content -->
    <div onclick="event.stopPropagation()"
         id="addHolidayModalBox"
         style="position:relative; z-index:1; max-width:28rem; width:100%; transition:transform 0.25s ease, opacity 0.25s ease; transform:scale(0.9); opacity:0;"
         class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 p-6">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="calendar-plus" class="w-5 h-5 text-white dark:text-navy-900"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-navy-800 dark:text-white">Tambah Hari Libur</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Tambahkan hari libur baru</p>
                </div>
            </div>
            <button onclick="closeAddModal()"
                    class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5 text-slate-500"></i>
            </button>
        </div>

        <form action="{{ route('holidays.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Tanggal</label>
                <div class="relative">
                    <i data-lucide="calendar" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="date" name="date" required
                           class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Nama Libur</label>
                <div class="relative">
                    <i data-lucide="tag" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="name" required placeholder="Contoh: Hari Raya Idul Fitri"
                           class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Tipe</label>
                <div class="relative">
                    <i data-lucide="layers" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
                    <select name="type" required
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all appearance-none cursor-pointer">
                        <option value="national">🇮🇩 Libur Nasional</option>
                        <option value="school">🎓 Libur Sekolah</option>
                    </select>
                    <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
                </div>
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-700/30 rounded-xl border border-slate-200/60 dark:border-slate-600/60">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_recurring" value="1"
                           class="w-5 h-5 rounded border-slate-300 text-navy-600 focus:ring-navy-500">
                    <div>
                        <p class="text-sm font-semibold text-navy-800 dark:text-white">Berulang setiap tahun</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Libur ini akan otomatis aktif setiap tahun</p>
                    </div>
                </label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeAddModal()"
                        class="flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-all hover:-translate-y-0.5">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ✅ DELETE CONFIRMATION MODAL -->
<div id="deleteHolidayModal"
     style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:1rem;">

    <!-- Backdrop -->
    <div onclick="closeDeleteModal()"
         style="position:absolute; inset:0; background:rgba(15,23,42,0.65); backdrop-filter:blur(4px);"></div>

    <!-- Modal Content -->
    <div onclick="event.stopPropagation()"
         id="deleteHolidayModalBox"
         style="position:relative; z-index:1; max-width:28rem; width:100%; transition:transform 0.25s ease, opacity 0.25s ease; transform:scale(0.9); opacity:0;"
         class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 p-6">
        
        <!-- Header -->
        <div class="flex items-start gap-4 mb-6">
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-1">Hapus Hari Libur?</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Apakah Anda yakin ingin menghapus <span id="deleteHolidayName" class="font-semibold text-navy-800 dark:text-white"></span>? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
        </div>
        
        <!-- Form -->
        <form id="deleteHolidayForm" method="POST">
            @csrf
            @method('DELETE')

            <!-- Warning Box -->
            <div class="p-4 bg-red-50 dark:bg-red-900/10 rounded-xl border border-red-200 dark:border-red-800/50 mb-4">
                <div class="flex items-start gap-3">
                    <i data-lucide="info" class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0"></i>
                    <p class="text-xs text-red-700 dark:text-red-300 leading-relaxed">
                        Guru mungkin akan absen di tanggal ini jika tidak ada libur terdaftar. Pastikan ini bukan hari libur penting.
                    </p>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()"
                        class="px-5 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-all hover:-translate-y-0.5">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-red-500/30 hover:shadow-xl hover:shadow-red-500/40 hover:-translate-y-0.5 flex items-center gap-2">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // ============================================
    // Modal State
    // ============================================
    let _deleteHolidayId = null;
    let _deleteHolidayName = '';

    // ============================================
    // Add Holiday Modal
    // ============================================
    function openAddModal() {
        const modal = document.getElementById('addHolidayModal');
        const box   = document.getElementById('addHolidayModalBox');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => requestAnimationFrame(() => {
            box.style.transform = 'scale(1)';
            box.style.opacity   = '1';
        }));
    }

    function closeAddModal() {
        const modal = document.getElementById('addHolidayModal');
        const box   = document.getElementById('addHolidayModalBox');
        box.style.transform = 'scale(0.9)';
        box.style.opacity   = '0';
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 250);
    }

    // ============================================
    // Delete Holiday Modal
    // ============================================
    function openDeleteModal(holiday) {
        _deleteHolidayId   = holiday.id;
        _deleteHolidayName = holiday.name;

        // Update Alpine state + DOM
        document.getElementById('deleteHolidayName').textContent = holiday.name;
        document.getElementById('deleteHolidayForm').action = '/holidays/' + holiday.id;

        const modal = document.getElementById('deleteHolidayModal');
        const box   = document.getElementById('deleteHolidayModalBox');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => requestAnimationFrame(() => {
            box.style.transform = 'scale(1)';
            box.style.opacity   = '1';
        }));
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteHolidayModal');
        const box   = document.getElementById('deleteHolidayModalBox');
        box.style.transform = 'scale(0.9)';
        box.style.opacity   = '0';
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 250);
    }

    // ============================================
    // Escape key closes modals
    // ============================================
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (document.getElementById('addHolidayModal').style.display === 'flex') closeAddModal();
            if (document.getElementById('deleteHolidayModal').style.display === 'flex') closeDeleteModal();
        }
    });

    // ============================================
    // Re-init lucide icons on DOM ready
    // ============================================
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
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
    
    .animate-slide-up {
        animation: slideUp 0.5s ease-out forwards;
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
</style>
@endsection