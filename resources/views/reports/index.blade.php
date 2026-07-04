@extends('layouts.app')

@section('page-title', 'Laporan Absensi')

@section('content')
<div class="fade-in space-y-6" x-data="reportApp()">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                <i data-lucide="bar-chart-3" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-navy-800 dark:text-white tracking-tight">Laporan Absensi</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Lihat dan ekspor data absensi guru</p>
            </div>
        </div>
        <button @click="exportReport" class="btn-primary flex items-center gap-2 w-fit">
            <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
            Export Excel
        </button>
    </div>

    <!-- Modern Filters with AJAX -->
    <div class="card p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- Start Date -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">
                    Tanggal Mulai
                </label>
                <div class="relative">
                    <i data-lucide="calendar" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="date" 
                           x-model="filters.start_date"
                           @change="filterData"
                           class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
                </div>
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">
                    Tanggal Akhir
                </label>
                <div class="relative">
                    <i data-lucide="calendar" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="date" 
                           x-model="filters.end_date"
                           @change="filterData"
                           class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
                </div>
            </div>

            <!-- Teacher Dropdown (Modern with Search) -->
            <div x-data="{ open: false, search: '' }" @click.outside="open = false">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">
                    Guru
                </label>
                <div class="relative">
                    <button type="button" 
                            @click="open = !open"
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 flex items-center justify-between hover:border-navy-300 dark:hover:border-gold-600 transition-colors">
                        <span class="truncate" x-text="getTeacherName()">Semua Guru</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0 ml-2" :class="{'rotate-180': open}"></i>
                    </button>
                    
                    <!-- Dropdown Menu with Animation -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                         class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden max-h-72"
                         x-cloak>
                        
                        <!-- Search Input -->
                        <div class="p-2 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 sticky top-0">
                            <div class="relative">
                                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
                                <input type="text" 
                                       x-model="search" 
                                       placeholder="Cari guru..." 
                                       class="w-full pl-9 pr-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                            </div>
                        </div>
                        
                        <!-- Teachers List -->
                        <div class="overflow-y-auto max-h-48">
                            <!-- All Teachers Option -->
                            <button type="button" 
                                    @click="selectTeacher(null); open = false; filterData()"
                                    class="w-full px-4 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2"
                                    :class="!filters.teacher_id ? 'bg-navy-50 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 font-semibold' : 'text-slate-700 dark:text-slate-300'">
                                <i data-lucide="list" class="w-4 h-4" x-show="!filters.teacher_id"></i>
                                <span class="w-2 h-2 bg-slate-400 rounded-full" x-show="filters.teacher_id"></span>
                                Semua Guru
                            </button>
                            
                            <!-- Teacher Options -->
                            <template x-for="teacher in filteredTeachers" :key="teacher.id">
                                <button type="button" 
                                        @click="selectTeacher(teacher.id); open = false; filterData()"
                                        class="w-full px-4 py-2.5 text-left text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2"
                                        :class="filters.teacher_id == teacher.id ? 'bg-navy-50 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 font-semibold' : 'text-slate-700 dark:text-slate-300'">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center text-white dark:text-navy-900 font-bold text-xs flex-shrink-0">
                                        <span x-text="teacher.name.charAt(0).toUpperCase()"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium truncate" x-text="teacher.name"></p>
                                        <p class="text-[10px] text-slate-400 truncate" x-text="teacher.email"></p>
                                    </div>
                                    <i data-lucide="check" class="w-4 h-4 text-navy-600 dark:text-gold-400 flex-shrink-0" x-show="filters.teacher_id == teacher.id"></i>
                                </button>
                            </template>
                            
                            <!-- No Results -->
                            <div x-show="filteredTeachers.length === 0 && search" class="p-4 text-center text-slate-500 dark:text-slate-400 text-xs">
                                <i data-lucide="user-x" class="w-6 h-6 mx-auto mb-1 opacity-50"></i>
                                <p>Tidak ditemukan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Active Filters Indicator -->
        <div x-show="hasActiveFilters" class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700 flex items-center gap-2">
            <i data-lucide="filter" class="w-4 h-4 text-slate-400"></i>
            <span class="text-xs text-slate-500 dark:text-slate-400">Filter aktif:</span>
            <template x-if="filters.start_date">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-navy-100 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 rounded text-xs">
                    Mulai: <span x-text="filters.start_date"></span>
                    <button @click="filters.start_date = ''; filterData()" class="hover:text-red-500"><i data-lucide="x" class="w-3 h-3"></i></button>
                </span>
            </template>
            <template x-if="filters.end_date">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-navy-100 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 rounded text-xs">
                    Akhir: <span x-text="filters.end_date"></span>
                    <button @click="filters.end_date = ''; filterData()" class="hover:text-red-500"><i data-lucide="x" class="w-3 h-3"></i></button>
                </span>
            </template>
            <template x-if="filters.teacher_id">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-navy-100 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 rounded text-xs">
                    Guru: <span x-text="getTeacherName()"></span>
                    <button @click="filters.teacher_id = null; filterData()" class="hover:text-red-500"><i data-lucide="x" class="w-3 h-3"></i></button>
                </span>
            </template>
            <button @click="resetFilters" class="ml-2 text-xs text-slate-500 hover:text-navy-600 dark:hover:text-gold-400 underline">Reset semua</button>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div x-show="loading" class="flex justify-center py-12" x-cloak>
        <div class="flex items-center gap-3">
            <div class="w-6 h-6 border-2 border-navy-800 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-sm text-slate-600 dark:text-slate-400">Memuat data...</span>
        </div>
    </div>

    <!-- Statistics Cards (Updated with AJAX) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" x-show="!loading">
        <!-- Total Absensi -->
        <div class="card-hover card p-5 group animate-slide-up" style="animation-delay: 0.1s">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="file-text" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total Absensi</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white mt-1" x-text="stats.total">{{ $stats->total ?? 0 }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1">Laporan periode ini</p>
                </div>
            </div>
        </div>

        <!-- Hadir -->
        <div class="card-hover card p-5 group animate-slide-up" style="animation-delay: 0.15s">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Hadir</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white mt-1" x-text="stats.hadir">{{ $stats->hadir ?? 0 }}</h3>
                    <p class="text-[10px] text-green-500 mt-1" x-text="stats.total > 0 ? Math.round((stats.hadir / stats.total) * 100) + '% tingkat kehadiran' : '0% tingkat kehadiran'">{{ ($stats->total ?? 0) > 0 ? round((($stats->hadir ?? 0) / ($stats->total ?? 1)) * 100) : 0 }}% tingkat kehadiran</p>
                </div>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="card-hover card p-5 group animate-slide-up" style="animation-delay: 0.2s">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Terlambat</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white mt-1" x-text="stats.terlambat">{{ $stats->terlambat ?? 0 }}</h3>
                    <p class="text-[10px] text-yellow-600 mt-1">Perlu tinjauan ulang</p>
                </div>
            </div>
        </div>

        <!-- Alpha/Izin -->
        <div class="card-hover card p-5 group animate-slide-up" style="animation-delay: 0.25s">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Alpha/Izin</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white mt-1" x-text="stats.alpha + stats.izin">{{ ($stats->alpha ?? 0) + ($stats->izin ?? 0) }}</h3>
                    <p class="text-[10px] text-red-500 mt-1">Ketidakhadiran guru</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table (Updated with AJAX) -->
    <div class="card overflow-hidden" x-show="!loading">
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
                    <template x-for="att in attendances" :key="att.id">
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative flex-shrink-0">
                                        <template x-if="att.photo_url">
                                            <img :src="att.photo_url" 
                                                 :alt="att.name || '?'" 
                                                 class="w-8 h-8 rounded-full object-cover border border-slate-200 dark:border-slate-700 shadow-sm"
                                                 x-on:error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='flex'">
                                        </template>
                                        <div :class="att.photo_url ? 'absolute inset-0' : 'w-8 h-8'"
                                             :style="att.photo_url ? 'display: none;' : ''"
                                             class="rounded-full bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center text-white dark:text-navy-900 font-bold text-xs flex-shrink-0 shadow-sm">
                                            <span x-text="att.name?.charAt(0)?.toUpperCase() || '?'"></span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-navy-800 dark:text-white" x-text="att.name"></p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400" x-text="att.email"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-navy-800 dark:text-white" x-text="formatDate(att.date)"></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400" x-text="formatDay(att.date)"></p>
                            </td>
                            <td class="px-6 py-4">
                                <template x-if="att.check_in">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="clock" class="w-4 h-4 text-green-500"></i>
                                        <span class="text-sm font-mono text-slate-700 dark:text-slate-300" x-text="att.check_in"></span>
                                    </div>
                                </template>
                                <template x-if="!att.check_in">
                                    <span class="text-sm text-slate-400">-</span>
                                </template>
                            </td>
                            <td class="px-6 py-4">
                                <template x-if="att.check_out">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="clock" class="w-4 h-4 text-blue-500"></i>
                                        <span class="text-sm font-mono text-slate-700 dark:text-slate-300" x-text="att.check_out"></span>
                                    </div>
                                </template>
                                <template x-if="!att.check_out">
                                    <span class="text-sm text-slate-400">-</span>
                                </template>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                                      :class="getStatusClass(att.status)"
                                      x-text="att.status"></span>
                            </td>
                        </tr>
                    </template>
                    
                    <!-- Empty State -->
                    <template x-if="attendances.length === 0 && !loading">
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
                    </template>
                </tbody>
            </table>
        </div>

        <!-- AJAX Pagination -->
        <div x-show="pagination && pagination.links && pagination.links.length > 3" class="p-4 border-t border-slate-200 dark:border-slate-700" x-cloak>
            <nav class="flex items-center justify-center gap-1">
                <!-- Previous -->
                <template x-if="pagination.prev_page_url">
                    <button @click="loadPage(pagination.prev_page_url)" 
                            class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-1">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                </template>
                
                <!-- Page Numbers -->
                <template x-for="link in pagination.links" :key="link.label">
                    <template x-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')">
                        <button @click="loadPage(link.url)"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors min-w-[40px]"
                                :class="link.active 
                                    ? 'bg-navy-800 text-white dark:bg-gold-500 dark:text-navy-900' 
                                    : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'"
                                x-text="link.label.replace('&laquo;', '').replace('&raquo;', '').trim()"></button>
                    </template>
                </template>
                
                <!-- Next -->
                <template x-if="pagination.next_page_url">
                    <button @click="loadPage(pagination.next_page_url)" 
                            class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-1">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </template>
            </nav>
        </div>
    </div>

    <!-- Weekly Recap Section -->
    <div class="card overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-white dark:from-slate-800/50 dark:to-slate-800/30">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                    <i data-lucide="calendar-clock" class="w-5 h-5 text-white dark:text-navy-900"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-navy-800 dark:text-white">Rekap Terlambat Per Minggu</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ringkasan keterlambatan guru berdasarkan periode yang dipilih</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guru</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Minggu 1</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Minggu 2</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Minggu 3</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Minggu 4</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700" id="weekly-recap-body">
                    @forelse($weeklyRecap as $teacher)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative flex-shrink-0 w-9 h-9">
                                        @if($teacher['photo_url'])
                                            <img src="{{ $teacher['photo_url'] }}" 
                                                 alt="{{ $teacher['name'] }}" 
                                                 class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-700 shadow-sm"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        @endif
                                        <div style="{{ $teacher['photo_url'] ? 'display: none;' : '' }}"
                                             class="absolute inset-0 rounded-full bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center text-white dark:text-navy-900 font-bold text-xs flex-shrink-0 shadow-sm">
                                            {{ strtoupper(substr($teacher['name'], 0, 1)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-navy-800 dark:text-white">{{ $teacher['name'] }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $teacher['email'] }}</p>
                                    </div>
                                </div>
                            </td>
                            @foreach(['week1','week2','week3','week4'] as $wk)
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold
                                        {{ $teacher[$wk] > 0 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-600' }}">
                                        {{ $teacher[$wk] }}
                                    </span>
                                </td>
                            @endforeach
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-sm font-bold shadow-sm
                                    {{ $teacher['total'] > 3 ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                    {{ $teacher['total'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <i data-lucide="check-circle-2" class="w-10 h-10 text-green-400"></i>
                                    <p class="text-slate-500 dark:text-slate-400 font-medium text-sm">Tidak ada keterlambatan pada periode ini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed top-6 right-6 z-[110] flex items-center gap-3 px-5 py-4 rounded-xl shadow-2xl"
         :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
         x-cloak>
        <i :data-lucide="toast.type === 'success' ? 'check-circle' : 'alert-circle'" class="w-5 h-5"></i>
        <p class="text-sm font-medium" x-text="toast.message"></p>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('reportApp', () => ({
            // Initial data
            attendances: {!! json_encode($attendances->items() ?? []) !!},
            stats: {!! json_encode($stats ?? ['total' => 0, 'hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'alpha' => 0]) !!},
            pagination: {!! json_encode($attendances ?? null) !!},
            teachers: {!! json_encode($teachers ?? []) !!},
            
            // Filters
            filters: {
                start_date: '{{ request("start_date") }}',
                end_date: '{{ request("end_date") }}',
                teacher_id: '{{ request("teacher_id") }}'
            },
            
            // UI State
            loading: false,
            toast: { show: false, message: '', type: 'success' },
            
            init() {
                if (window.lucide) lucide.createIcons();
            },
            
            // Get teacher name by ID
            getTeacherName() {
                if (!this.filters.teacher_id) return 'Semua Guru';
                const teacher = this.teachers.find(t => t.id == this.filters.teacher_id);
                return teacher ? teacher.name : 'Semua Guru';
            },
            
            // Select teacher
            selectTeacher(id) {
                this.filters.teacher_id = id;
            },
            
            // Get filtered teachers list
            get filteredTeachers() {
                if (!this.search) return this.teachers;
                return this.teachers.filter(t => 
                    t.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    t.email.toLowerCase().includes(this.search.toLowerCase())
                );
            },
            
            // Check if any filters are active
            get hasActiveFilters() {
                return this.filters.start_date || this.filters.end_date || this.filters.teacher_id;
            },
            
            // Reset all filters
            resetFilters() {
                this.filters = { start_date: '', end_date: '', teacher_id: null };
                this.filterData();
            },
            
            // Build query params
            buildParams() {
                const params = new URLSearchParams();
                if (this.filters.start_date) params.append('start_date', this.filters.start_date);
                if (this.filters.end_date) params.append('end_date', this.filters.end_date);
                if (this.filters.teacher_id) params.append('teacher_id', this.filters.teacher_id);
                params.append('ajax', '1');
                return params;
            },
            
            // Fetch data via AJAX
            async fetchData(url) {
                this.loading = true;
                try {
                    const response = await fetch(`${url}?${this.buildParams()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (!response.ok) throw new Error('Network error');
                    
                    const data = await response.json();
                    this.attendances = data.data || [];
                    this.stats = data.stats || this.stats;
                    this.pagination = data;
                    
                } catch (error) {
                    console.error('Fetch error:', error);
                    this.showToast('Gagal memuat data', 'error');
                } finally {
                    this.loading = false;
                    if (window.lucide) lucide.createIcons();
                }
            },
            
            // Filter data (called on filter change)
            async filterData() {
                await this.fetchData('{{ route("reports.index") }}');
            },
            
            // Load specific page
            async loadPage(url) {
                const cleanUrl = url.replace(/[?&]ajax=1/, '');
                await this.fetchData(cleanUrl);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            
            // Export to Excel
            exportReport() {
                const params = new URLSearchParams();
                if (this.filters.start_date) params.append('start_date', this.filters.start_date);
                if (this.filters.end_date) params.append('end_date', this.filters.end_date);
                if (this.filters.teacher_id) params.append('teacher_id', this.filters.teacher_id);

                const baseUrl = '{{ route("reports.export-excel") }}';
                const url = params.toString() ? `${baseUrl}?${params}` : baseUrl;
                window.open(url, '_blank');
            },
            
            // Format date for display
            formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            },
            
            // Format day name
            formatDay(dateString) {
                if (!dateString) return '';
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const date = new Date(dateString);
                return days[date.getDay()];
            },
            
            // Get status badge class
            getStatusClass(status) {
                const classes = {
                    'Hadir': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                    'Terlambat': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                    'Izin': 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                    'Alpha': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                };
                return classes[status] || 'bg-slate-100 text-slate-700';
            },
            
            // Show toast notification
            showToast(message, type = 'success') {
                this.toast = { show: true, message, type };
                setTimeout(() => {
                    this.toast.show = false;
                }, 4000);
            }
        }));
    });
    
    // Init icons
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
        opacity: 0;
    }
    
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    [x-cloak] { display: none !important; }
</style>
@endsection