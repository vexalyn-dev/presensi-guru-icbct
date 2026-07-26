@extends('layouts.app')

@section('page-title', 'Pengaturan')

@section('content')
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="fade-in" x-data="settingsApp()">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                <i data-lucide="settings-2" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-navy-800 dark:text-white tracking-tight">Pengaturan Sistem</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Konfigurasi menyeluruh untuk performa sistem optimal</p>
            </div>
        </div>

        <button @click="showResetModal = true"
                class="group flex items-center gap-2 px-4 py-2.5 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 rounded-xl text-sm font-semibold transition-all hover:-translate-y-0.5">
            <i data-lucide="rotate-ccw" class="w-4 h-4 group-hover:rotate-180 transition-transform duration-300"></i>
            Reset Default
        </button>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="mb-6 card p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 animate-slide-up">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                </div>
                <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- HORIZONTAL TAB NAVIGATION -->
    <div class="card p-2 mb-6 overflow-x-auto">
        <div class="flex gap-1 min-w-max">
            <button @click="activeTab = 'general'"
                    :class="activeTab === 'general' ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow-lg' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap flex-shrink-0">
                <i data-lucide="layout-grid" class="w-4 h-4"></i>
                <span>Umum</span>
            </button>
            
            <button @click="activeTab = 'attendance'"
                    :class="activeTab === 'attendance' ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow-lg' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap flex-shrink-0">
                <i data-lucide="clock" class="w-4 h-4"></i>
                <span>Presensi</span>
            </button>
            
            <button @click="activeTab = 'appearance'"
                    :class="activeTab === 'appearance' ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow-lg' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap flex-shrink-0">
                <i data-lucide="palette" class="w-4 h-4"></i>
                <span>Tampilan</span>
            </button>

            <button @click="activeTab = 'notification'"
                    :class="activeTab === 'notification' ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow-lg' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap flex-shrink-0">
                <i data-lucide="bell" class="w-4 h-4"></i>
                <span>Notifikasi</span>
            </button>

            <button @click="activeTab = 'maps'; setTimeout(initLeafletMap, 100)"
                    :class="activeTab === 'maps' ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow-lg' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap flex-shrink-0">
                <i data-lucide="map-pin" class="w-4 h-4"></i>
                <span>Peta & GPS</span>
            </button>
        </div>
    </div>

    <!-- Main Content Area dengan ANIMASI -->
    <div class="space-y-6 relative min-h-[400px]">

        <!-- 1. General Settings -->
        <div x-show="activeTab === 'general'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             style="display: none;">
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i data-lucide="layout-grid" class="w-5 h-5 text-white dark:text-navy-900"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Identitas Sekolah</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Atur informasi dasar instansi Anda</p>
                    </div>
                </div>

                <form action="{{ route('settings.general') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Nama Aplikasi <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <i data-lucide="app-window" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                <input type="text" name="app_name" value="{{ old('app_name', $settings['general']['app_name'] ?? 'ICB CT - Absensi Guru') }}" required
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                            </div>
                        </div>

                        <!-- Zona Waktu (custom dropdown) -->
                        <div class="relative" @click.outside="timezoneDropdownOpen = false">
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Zona Waktu</label>

                            <button type="button" @click="timezoneDropdownOpen = !timezoneDropdownOpen"
                                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 flex items-center justify-between hover:border-navy-300 dark:hover:border-gold-600 transition-all">
                                <div class="flex items-center gap-3">
                                    <img :src="getFlagUrl(selectedTimezone.badge)"
                                         :alt="selectedTimezone.badge"
                                         class="w-10 h-6 rounded-md object-cover border border-slate-200 dark:border-slate-700" />
                                    <span class="text-slate-700 dark:text-slate-300 font-medium" x-text="selectedTimezone.name"></span>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': timezoneDropdownOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="timezoneDropdownOpen"
                                 x-transition:enter="transition ease-out duration-220"
                                 x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-180"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-3 scale-95"
                                 class="absolute z-[60] left-0 right-0 bottom-full mb-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-[0_20px_60px_-15px_rgba(15,23,42,0.35)] overflow-hidden"
                                 style="max-height: 320px; min-width: 100%;">

                                <div class="p-3 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                                    <div class="relative">
                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <input type="text" x-model="timezoneSearch" placeholder="Cari zona waktu..."
                                               class="w-full pl-10 pr-4 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                                    </div>
                                </div>

                                <div class="overflow-y-auto" style="max-height: 240px;">
                                    <template x-for="tz in filteredTimezones" :key="tz.value">
                                        <button type="button" @click="selectTimezone(tz)"
                                                class="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-3 border-b border-slate-100 dark:border-slate-700 last:border-0"
                                                :class="selectedTimezone.value === tz.value ? 'bg-navy-50 dark:bg-navy-900/30' : ''">
                                            <img :src="getFlagUrl(tz.badge)"
                                                 :alt="tz.badge"
                                                 class="w-11 h-7 rounded-md object-cover border border-slate-200 dark:border-slate-700" />
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300 truncate" x-text="tz.name"></p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate" x-text="tz.value"></p>
                                            </div>
                                            <svg x-show="selectedTimezone.value === tz.value" class="w-5 h-5 text-navy-800 dark:text-gold-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </template>

                                    <div x-show="filteredTimezones.length === 0" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                                        <svg class="w-12 h-12 mx-auto mb-2 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-sm">Tidak ditemukan</p>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="app_timezone" :value="selectedTimezone.value">
                        </div>

                        <!-- Bahasa (custom dropdown) -->
                        <div class="relative" @click.outside="languageDropdownOpen = false">
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Bahasa</label>
                            <button type="button" @click="languageDropdownOpen = !languageDropdownOpen"
                                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 flex items-center justify-between hover:border-navy-300 dark:hover:border-gold-600 transition-all">
                                <div class="flex items-center gap-3">
                                    <img :src="getFlagUrl(selectedLanguage.badge)"
                                         :alt="selectedLanguage.badge"
                                         class="w-10 h-6 rounded-md object-cover border border-slate-200 dark:border-slate-700" />
                                    <span class="text-slate-700 dark:text-slate-300 font-medium" x-text="selectedLanguage.name"></span>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': languageDropdownOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="languageDropdownOpen"
                                 x-transition:enter="transition ease-out duration-220"
                                 x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-180"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-3 scale-95"
                                 class="absolute z-[60] left-0 right-0 bottom-full mb-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-[0_20px_60px_-15px_rgba(15,23,42,0.35)] overflow-hidden"
                                 style="max-height: 320px; min-width: 100%;">

                                <div class="p-3 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                                    <div class="relative">
                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <input type="text" x-model="languageSearch" placeholder="Cari bahasa..."
                                               class="w-full pl-10 pr-4 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                                    </div>
                                </div>

                                <div class="overflow-y-auto" style="max-height: 240px;">
                                    <template x-for="lang in filteredLanguages" :key="lang.code">
                                        <button type="button" @click="selectLanguage(lang)"
                                                class="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-3 border-b border-slate-100 dark:border-slate-700 last:border-0"
                                                :class="selectedLanguage.code === lang.code ? 'bg-navy-50 dark:bg-navy-900/30' : ''">
                                            <img :src="getFlagUrl(lang.badge)"
                                                 :alt="lang.badge"
                                                 class="w-11 h-7 rounded-md object-cover border border-slate-200 dark:border-slate-700" />
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300 truncate" x-text="lang.name"></p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase" x-text="lang.code"></p>
                                            </div>
                                            <svg x-show="selectedLanguage.code === lang.code" class="w-5 h-5 text-navy-800 dark:text-gold-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </template>

                                    <div x-show="filteredLanguages.length === 0" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                                        <svg class="w-12 h-12 mx-auto mb-2 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-sm">Tidak ditemukan</p>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="app_language" :value="selectedLanguage.code">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Email Operator</label>
                            <div class="relative group">
                                <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                <input type="email" name="admin_email" value="{{ old('admin_email', $settings['general']['admin_email'] ?? '') }}"
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500" placeholder="admin@sekolah.sch.id">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end pt-5 border-t border-slate-200 dark:border-slate-700">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white rounded-xl text-sm font-semibold transition-all shadow-lg hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. Attendance Settings -->
        <div x-show="activeTab === 'attendance'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             style="display: none;">
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i data-lucide="clock" class="w-5 h-5 text-white dark:text-navy-900"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Aturan Presensi</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Kelola jam kerja dan validasi absensi</p>
                    </div>
                </div>

                <form action="{{ route('settings.attendance') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Jam Mulai Presensi -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Jam Mulai Presensi</label>
                            <div class="relative group">
                                <i data-lucide="sunrise" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                <input type="time" name="attendance_start_time" 
                                       value="{{ old('attendance_start_time', $settings['attendance']['attendance_start_time'] ?? '06:30') }}" required
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                            </div>
                        </div>

                        <!-- Batas Akhir Presensi -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Batas Akhir Presensi</label>
                            <div class="relative group">
                                <i data-lucide="sunset" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                <input type="time" name="attendance_end_time" 
                                       value="{{ old('attendance_end_time', $settings['attendance']['attendance_end_time'] ?? '16:00') }}" required
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                            </div>
                        </div>

                        <!-- Toleransi Terlambat -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Toleransi Terlambat (Menit)</label>
                            <div class="relative group">
                                <i data-lucide="timer" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                <input type="number" name="attendance_late_grace_period" 
                                       value="{{ old('attendance_late_grace_period', $settings['attendance']['attendance_late_grace_period'] ?? 5) }}" min="0" max="60"
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                            </div>
                        </div>

                        <!-- ✅ Validasi GPS (DIPINDAH KE SINI) -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Validasi GPS</label>
                            <div class="relative" x-data="{ openGps: false }" @click.outside="openGps = false">
                                <button type="button" @click="openGps = !openGps"
                                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 flex items-center justify-between hover:border-navy-300 dark:hover:border-gold-600 transition-all">
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 rounded-lg flex items-center justify-center" 
                                             :class="selectedGpsValidation === 'on' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30'">
                                            <i data-lucide="map-pin" :class="selectedGpsValidation === 'on' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'" class="w-4 h-4"></i>
                                        </div>
                                        <span class="text-slate-700 dark:text-slate-300 font-medium" x-text="selectedGpsValidation === 'on' ? 'Aktif' : 'Nonaktif'"></span>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': openGps}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="openGps"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                                     class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl overflow-hidden"
                                     x-cloak>
                                    <div class="overflow-y-auto" style="max-height: 240px;">
                                        <template x-for="option in gpsValidationOptions" :key="option.value">
                                            <button type="button" @click="selectedGpsValidation = option.value; openGps = false"
                                                    class="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-3 border-b border-slate-100 dark:border-slate-700 last:border-0"
                                                    :class="selectedGpsValidation === option.value ? 'bg-navy-50 dark:bg-navy-900/30' : ''">
                                                <div class="w-6 h-6 rounded-lg flex items-center justify-center" 
                                                     :class="option.value === 'on' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30'">
                                                    <i data-lucide="map-pin" :class="option.value === 'on' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'" class="w-4 h-4"></i>
                                                </div>
                                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300" x-text="option.name"></span>
                                                <svg x-show="selectedGpsValidation === option.value" class="w-5 h-5 text-navy-800 dark:text-gold-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <input type="hidden" name="gps_validation_status" :value="selectedGpsValidation">
                            </div>
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Aktifkan validasi lokasi saat presensi</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- QR Code Expiration -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">QR Code Berlaku (Detik)</label>
                            <div class="relative" x-data="{ openQr: false }" @click.outside="openQr = false">
                                <button type="button" @click="openQr = !openQr"
                                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 flex items-center justify-between hover:border-navy-300 dark:hover:border-gold-600 transition-all">
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 rounded-lg flex items-center justify-center bg-blue-100 dark:bg-blue-900/30">
                                            <i data-lucide="clock" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <span class="text-slate-700 dark:text-slate-300 font-medium" x-text="selectedQrExpiration + ' detik'"></span>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': openQr}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="openQr"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                                     class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl overflow-hidden"
                                     x-cloak>
                                    <div class="overflow-y-auto" style="max-height: 240px;">
                                        <template x-for="option in qrExpirationOptions" :key="option.value">
                                            <button type="button" @click="selectedQrExpiration = option.value; openQr = false"
                                                    class="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-3 border-b border-slate-100 dark:border-slate-700 last:border-0"
                                                    :class="selectedQrExpiration === option.value ? 'bg-navy-50 dark:bg-navy-900/30' : ''">
                                                <div class="w-6 h-6 rounded-lg flex items-center justify-center bg-blue-100 dark:bg-blue-900/30">
                                                    <i data-lucide="clock" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                                                </div>
                                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300" x-text="option.name"></span>
                                                <svg x-show="selectedQrExpiration === option.value" class="w-5 h-5 text-navy-800 dark:text-gold-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <input type="hidden" name="qr_expiration" :value="selectedQrExpiration">
                            </div>
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Durasi QR Code tetap valid</p>
                        </div>

                        <!-- ✅ Auto Logout -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Auto Logout</label>
                            <div class="relative" x-data="{ openLogout: false }" @click.outside="openLogout = false">
                                <button type="button" @click="openLogout = !openLogout"
                                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 flex items-center justify-between hover:border-navy-300 dark:hover:border-gold-600 transition-all">
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 rounded-lg flex items-center justify-center" 
                                             :class="selectedAutoLogout === 'off' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-purple-100 dark:bg-purple-900/30'">
                                            <i data-lucide="log-out" :class="selectedAutoLogout === 'off' ? 'text-red-600 dark:text-red-400' : 'text-purple-600 dark:text-purple-400'" class="w-4 h-4"></i>
                                        </div>
                                        <span class="text-slate-700 dark:text-slate-300 font-medium" 
                                              x-text="selectedAutoLogout === 'off' ? 'Nonaktif' : selectedAutoLogout + ' menit'"></span>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': openLogout}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="openLogout"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                                     class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl overflow-hidden"
                                     x-cloak>
                                    <div class="overflow-y-auto" style="max-height: 240px;">
                                        <template x-for="option in autoLogoutOptions" :key="option.value">
                                            <button type="button" @click="selectedAutoLogout = option.value; openLogout = false"
                                                    class="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-3 border-b border-slate-100 dark:border-slate-700 last:border-0"
                                                    :class="selectedAutoLogout === option.value ? 'bg-navy-50 dark:bg-navy-900/30' : ''">
                                                <div class="w-6 h-6 rounded-lg flex items-center justify-center" 
                                                     :class="option.value === 'off' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-purple-100 dark:bg-purple-900/30'">
                                                    <i data-lucide="log-out" :class="option.value === 'off' ? 'text-red-600 dark:text-red-400' : 'text-purple-600 dark:text-purple-400'" class="w-4 h-4"></i>
                                                </div>
                                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300" x-text="option.name"></span>
                                                <svg x-show="selectedAutoLogout === option.value" class="w-5 h-5 text-navy-800 dark:text-gold-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <input type="hidden" name="auto_logout" :value="selectedAutoLogout">
                            </div>
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Auto logout jika tidak aktif</p>
                        </div>
                    </div>

                    <div class="flex justify-end pt-5 border-t border-slate-200 dark:border-slate-700">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white rounded-xl text-sm font-semibold transition-all shadow-lg hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i> Simpan Aturan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 3. Appearance Settings -->
        <div x-show="activeTab === 'appearance'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             style="display: none;">
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i data-lucide="palette" class="w-5 h-5 text-white dark:text-navy-900"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Branding & Visual</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Ubah logo dan tema warna aplikasi</p>
                    </div>
                </div>

                <form action="{{ route('settings.appearance') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Logo Aplikasi</label>
                            <div class="relative">
                                <input type="file" id="logoInput" name="app_logo" accept="image/*" class="hidden" @change="previewLogo($event)">
                                <label for="logoInput" class="flex flex-col items-center justify-center w-full p-6 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl cursor-pointer hover:border-navy-400 dark:hover:border-gold-500 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-all">
                                    <template x-if="!logoPreview">
                                        <div class="flex flex-col items-center text-center">
                                            <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center mb-3">
                                                <i data-lucide="image" class="w-7 h-7 text-slate-400"></i>
                                            </div>
                                            <p class="text-sm font-medium text-navy-800 dark:text-white">Upload Logo</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">PNG, JPG • Max 2MB</p>
                                        </div>
                                    </template>
                                    <template x-if="logoPreview">
                                        <div class="flex flex-col items-center">
                                            <img :src="logoPreview" class="max-h-24 object-contain mb-3 rounded-lg">
                                            <p class="text-xs text-green-600 dark:text-green-400 font-medium">Logo terpilih</p>
                                        </div>
                                    </template>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Favicon</label>
                            <div class="relative">
                                <input type="file" id="faviconInput" name="app_favicon" accept=".ico,.png,.jpg,.jpeg" class="hidden" @change="previewFavicon($event)">
                                <label for="faviconInput" class="flex flex-col items-center justify-center w-full p-6 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl cursor-pointer hover:border-navy-400 dark:hover:border-gold-500 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-all">
                                    <template x-if="!faviconPreview">
                                        <div class="flex flex-col items-center text-center">
                                            <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center mb-3">
                                                <i data-lucide="sparkles" class="w-7 h-7 text-slate-400"></i>
                                            </div>
                                            <p class="text-sm font-medium text-navy-800 dark:text-white">Upload Favicon</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">ICO, PNG • 32x32px</p>
                                        </div>
                                    </template>
                                    <template x-if="faviconPreview">
                                        <div class="flex flex-col items-center">
                                            <img :src="faviconPreview" class="w-12 h-12 object-contain mb-3 rounded-lg">
                                            <p class="text-xs text-green-600 dark:text-green-400 font-medium">Favicon terpilih</p>
                                        </div>
                                    </template>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Warna Utama</label>
                            <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600">
                                <input type="color" name="primary_color" x-model="primaryColor" @input="updateColorPreview('primary', $event.target.value)"
                                       value="{{ old('primary_color', $settings['appearance']['primary_color'] ?? '#0F172A') }}" class="w-12 h-12 rounded-lg cursor-pointer border-2 border-slate-200 dark:border-slate-600 p-0 overflow-hidden">
                                <div class="flex-1">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Kode HEX</p>
                                    <input type="text" readonly x-model="primaryColor" class="w-full bg-transparent text-sm font-mono font-semibold text-navy-800 dark:text-white border-none p-0 focus:ring-0">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Warna Aksen</label>
                            <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600">
                                <input type="color" name="accent_color" x-model="accentColor" @input="updateColorPreview('accent', $event.target.value)"
                                       value="{{ old('accent_color', $settings['appearance']['accent_color'] ?? '#FACC15') }}" class="w-12 h-12 rounded-lg cursor-pointer border-2 border-slate-200 dark:border-slate-600 p-0 overflow-hidden">
                                <div class="flex-1">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Kode HEX</p>
                                    <input type="text" readonly x-model="accentColor" class="w-full bg-transparent text-sm font-mono font-semibold text-navy-800 dark:text-white border-none p-0 focus:ring-0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-5 border-t border-slate-200 dark:border-slate-700">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white rounded-xl text-sm font-semibold transition-all shadow-lg hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i> Terapkan Branding
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 4. Notification Settings -->
        <div x-show="activeTab === 'notification'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             style="display: none;">
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i data-lucide="bell" class="w-5 h-5 text-white dark:text-navy-900"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Sistem Notifikasi</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Atur notifikasi otomatis ke Operator</p>
                    </div>
                </div>

                <form action="{{ route('settings.notification') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600 hover:border-navy-300 dark:hover:border-gold-600 transition-colors">
                        <label class="flex items-start gap-4 cursor-pointer">
                            <div class="relative inline-flex items-center cursor-pointer mt-1">
                                <input type="checkbox" name="email_notification" {{ old('email_notification', $settings['notification']['email_notification'] ?? true) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-navy-800 dark:peer-checked:bg-gold-500"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-navy-800 dark:text-white">Laporan Harian via Email</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kirim ringkasan presensi setiap hari ke Operator</p>
                            </div>
                        </label>
                    </div>

                    <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600 hover:border-navy-300 dark:hover:border-gold-600 transition-colors">
                        <label class="flex items-start gap-4 cursor-pointer">
                            <div class="relative inline-flex items-center cursor-pointer mt-1">
                                <input type="checkbox" name="late_notification" {{ old('late_notification', $settings['notification']['late_notification'] ?? true) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-navy-800 dark:peer-checked:bg-gold-500"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-navy-800 dark:text-white">Alert Terlambat Realtime</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Notifikasi instan ketika guru terlambat absen</p>
                            </div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Email Penerima Alerts</label>
                        <div class="relative group">
                            <i data-lucide="bell-ring" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                            <input type="email" name="alert_email" value="{{ old('alert_email', $settings['notification']['alert_email'] ?? $settings['general']['admin_email'] ?? '') }}"
                                   class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500" placeholder="alerts@sekolah.sch.id">
                        </div>
                    </div>

                    <div class="flex justify-end pt-5 border-t border-slate-200 dark:border-slate-700">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white rounded-xl text-sm font-semibold transition-all shadow-lg hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i> Simpan Notifikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 5. Maps & GPS Settings dengan Leaflet -->
        <div x-show="activeTab === 'maps'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             style="display: none;">
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i data-lucide="map-pin" class="w-5 h-5 text-white dark:text-navy-900"></i>
                        </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Lokasi Sekolah (GPS)</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Atur koordinat untuk validasi radius presensi</p>
                    </div>
                </div>

                <form action="{{ route('settings.maps') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Latitude</label>
                            <input type="text" id="school_lat" name="school_latitude" 
                                   value="{{ old('school_latitude', $settings['maps']['school_latitude'] ?? '-6.9142402999999995') }}" required
                                   class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Longitude</label>
                            <input type="text" id="school_lng" name="school_longitude" 
                                   value="{{ old('school_longitude', $settings['maps']['school_longitude'] ?? '107.64586179999999') }}" required
                                   class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Radius (Meter)</label>
                            <input type="number" id="school_radius" name="location_radius" 
                                   value="{{ old('location_radius', $settings['attendance']['location_radius'] ?? 50) }}" 
                                   min="10" max="1000" step="10"
                                   class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-mono">
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Ubah untuk update lingkaran di peta</p>
                        </div>
                    </div>

                    <!-- Map Container -->
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Peta Interaktif</label>
                        <div class="relative h-[500px] w-full rounded-xl overflow-hidden border-2 border-slate-200 dark:border-slate-700">
                            <div id="leaflet-map" class="w-full h-full"></div>

                            <!-- Legend -->
                            <div class="absolute top-4 right-4 bg-white dark:bg-slate-800 rounded-lg shadow-lg p-3 z-[1000] border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-4 h-4 rounded-full bg-blue-500/30 border-2 border-blue-500"></div>
                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Area Presensi</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded-full bg-red-600"></div>
                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Lokasi Sekolah</span>
                                </div>
                                <div class="mt-2 pt-2 border-t border-slate-200 dark:border-slate-700">
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Radius: <span id="radius-display" class="font-bold text-blue-600 dark:text-blue-400">50</span> meter</p>
                                </div>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
                            <i data-lucide="info" class="w-3 h-3"></i>
                            Klik peta atau geser marker untuk update koordinat. Ubah radius untuk update lingkaran area.
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <button type="button" @click="getCurrentLocation()" 
                                class="px-4 py-2 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-xl text-sm font-semibold flex items-center gap-2">
                            <i data-lucide="crosshair" class="w-4 h-4"></i>
                            Gunakan Lokasi Saya
                        </button>
                        <button type="button" @click="resetMapToDefault()" 
                                class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold flex items-center gap-2">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            Reset
                        </button>
                    </div>

                    <div class="flex justify-end pt-5 border-t border-slate-200 dark:border-slate-700">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white rounded-xl text-sm font-semibold flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Simpan Lokasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reset Confirmation Modal -->
        <div x-show="showResetModal" x-cloak @keydown.escape.window="showResetModal = false"
             @click.self="showResetModal = false"
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             role="dialog" aria-modal="true">

            <div x-show="showResetModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative z-20 bg-white dark:bg-navy-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-lg w-full border border-slate-200 dark:border-slate-700">
                <div class="p-6 sm:p-8">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-1">Reset Konfigurasi?</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Tindakan ini akan mengembalikan semua pengaturan ke nilai awal. Data yang sudah diubah akan hilang permanen.</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <button @click="showResetModal = false" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-colors">Batal</button>
                        <form action="{{ route('settings.reset') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Ya, Reset
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Global map variables
    let leafletMap;
    let leafletMarker;
    let radiusCircle;
    let mapInitialized = false;

    document.addEventListener('alpine:init', () => {
        Alpine.data('settingsApp', () => ({
            activeTab: 'general',
            showResetModal: false,
            primaryColor: '{{ old("primary_color", $settings["appearance"]["primary_color"] ?? "#0F172A") }}',
            accentColor: '{{ old("accent_color", $settings["appearance"]["accent_color"] ?? "#FACC15") }}',
            logoPreview: null,
            faviconPreview: null,

            // Languages data with badges
            languages: [
                { code: 'id', name: 'Bahasa Indonesia', badge: 'ID' },
                { code: 'en', name: 'English', badge: 'GB' },
                { code: 'ar', name: 'العربية (Arabic)', badge: 'SA' },
                { code: 'zh', name: '中文 (Chinese)', badge: 'CN' },
                { code: 'ja', name: '日本語 (Japanese)', badge: 'JP' },
                { code: 'ko', name: '한국어 (Korean)', badge: 'KR' },
                { code: 'ms', name: 'Bahasa Melayu', badge: 'MY' },
                { code: 'hi', name: 'हिन्दी (Hindi)', badge: 'IN' },
                { code: 'th', name: 'ไทย (Thai)', badge: 'TH' },
                { code: 'vi', name: 'Tiếng Việt (Vietnamese)', badge: 'VN' },
                { code: 'fr', name: 'Français (French)', badge: 'FR' },
                { code: 'de', name: 'Deutsch (German)', badge: 'DE' },
                { code: 'es', name: 'Español (Spanish)', badge: 'ES' },
                { code: 'pt', name: 'Português (Portuguese)', badge: 'PT' },
                { code: 'ru', name: 'Русский (Russian)', badge: 'RU' },
                { code: 'tr', name: 'Türkçe (Turkish)', badge: 'TR' },
                { code: 'nl', name: 'Nederlands (Dutch)', badge: 'NL' },
                { code: 'it', name: 'Italiano (Italian)', badge: 'IT' },
                { code: 'pl', name: 'Polski (Polish)', badge: 'PL' },
                { code: 'sv', name: 'Svenska (Swedish)', badge: 'SE' },
            ],

            // Timezones data
            timezones: [
                { value: 'Asia/Jakarta', name: 'WIB - Jakarta (GMT+7)', badge: 'ID' },
                { value: 'Asia/Makassar', name: 'WITA - Makassar (GMT+8)', badge: 'ID' },
                { value: 'Asia/Jayapura', name: 'WIT - Jayapura (GMT+9)', badge: 'ID' },
                { value: 'Asia/Singapore', name: 'Singapore (GMT+8)', badge: 'SG' },
                { value: 'Asia/Kuala_Lumpur', name: 'Malaysia (GMT+8)', badge: 'MY' },
                { value: 'Asia/Bangkok', name: 'Thailand (GMT+7)', badge: 'TH' },
                { value: 'Asia/Ho_Chi_Minh', name: 'Vietnam (GMT+7)', badge: 'VN' },
                { value: 'Asia/Manila', name: 'Philippines (GMT+8)', badge: 'PH' },
                { value: 'Asia/Tokyo', name: 'Japan (GMT+9)', badge: 'JP' },
                { value: 'Asia/Seoul', name: 'South Korea (GMT+9)', badge: 'KR' },
                { value: 'Asia/Shanghai', name: 'China (GMT+8)', badge: 'CN' },
                { value: 'Asia/Hong_Kong', name: 'Hong Kong (GMT+8)', badge: 'HK' },
                { value: 'Asia/Taipei', name: 'Taiwan (GMT+8)', badge: 'TW' },
                { value: 'Asia/Dubai', name: 'UAE (GMT+4)', badge: 'AE' },
                { value: 'Asia/Kolkata', name: 'India (GMT+5:30)', badge: 'IN' },
                { value: 'Asia/Karachi', name: 'Pakistan (GMT+5)', badge: 'PK' },
                { value: 'Asia/Dhaka', name: 'Bangladesh (GMT+6)', badge: 'BD' },
                { value: 'Asia/Riyadh', name: 'Saudi Arabia (GMT+3)', badge: 'SA' },
                { value: 'Asia/Tehran', name: 'Iran (GMT+3:30)', badge: 'IR' },
                { value: 'Asia/Istanbul', name: 'Turkey (GMT+3)', badge: 'TR' },
                { value: 'Europe/London', name: 'UK (GMT+0)', badge: 'GB' },
                { value: 'Europe/Paris', name: 'France (GMT+1)', badge: 'FR' },
                { value: 'Europe/Berlin', name: 'Germany (GMT+1)', badge: 'DE' },
                { value: 'Europe/Rome', name: 'Italy (GMT+1)', badge: 'IT' },
                { value: 'Europe/Madrid', name: 'Spain (GMT+1)', badge: 'ES' },
                { value: 'Europe/Amsterdam', name: 'Netherlands (GMT+1)', badge: 'NL' },
                { value: 'Europe/Moscow', name: 'Russia Moscow (GMT+3)', badge: 'RU' },
                { value: 'America/New_York', name: 'USA Eastern (GMT-5)', badge: 'US' },
                { value: 'America/Chicago', name: 'USA Central (GMT-6)', badge: 'US' },
                { value: 'America/Denver', name: 'USA Mountain (GMT-7)', badge: 'US' },
                { value: 'America/Los_Angeles', name: 'USA Pacific (GMT-8)', badge: 'US' },
                { value: 'America/Toronto', name: 'Canada Eastern (GMT-5)', badge: 'CA' },
                { value: 'America/Vancouver', name: 'Canada Pacific (GMT-8)', badge: 'CA' },
                { value: 'America/Mexico_City', name: 'Mexico (GMT-6)', badge: 'MX' },
                { value: 'America/Sao_Paulo', name: 'Brazil (GMT-3)', badge: 'BR' },
                { value: 'America/Argentina/Buenos_Aires', name: 'Argentina (GMT-3)', badge: 'AR' },
                { value: 'Australia/Sydney', name: 'Australia Sydney (GMT+11)', badge: 'AU' },
                { value: 'Australia/Melbourne', name: 'Australia Melbourne (GMT+11)', badge: 'AU' },
                { value: 'Pacific/Auckland', name: 'New Zealand (GMT+13)', badge: 'NZ' },
                { value: 'Africa/Cairo', name: 'Egypt (GMT+2)', badge: 'EG' },
                { value: 'Africa/Lagos', name: 'Nigeria (GMT+1)', badge: 'NG' },
                { value: 'Africa/Johannesburg', name: 'South Africa (GMT+2)', badge: 'ZA' },
            ],

            gpsValidationOptions: [
                { value: 'on', name: 'Aktif' },
                { value: 'off', name: 'Nonaktif' },
            ],

            qrExpirationOptions: [
                { value: '15', name: '15 detik' },
                { value: '30', name: '30 detik' },
                { value: '45', name: '45 detik' },
                { value: '60', name: '60 detik' },
            ],

            autoLogoutOptions: [
                { value: 'off', name: 'Nonaktif' },
                { value: '5', name: '5 menit' },
                { value: '10', name: '10 menit' },
                { value: '15', name: '15 menit' },
                { value: '30', name: '30 menit' },
                { value: '60', name: '60 menit' },
                { value: '120', name: '120 menit' },
            ],

            // Dropdown state
            languageDropdownOpen: false,
            languageSearch: '',
            timezoneDropdownOpen: false,
            timezoneSearch: '',
            gpsDropdownOpen: false,
            qrDropdownOpen: false,
            autoLogoutDropdownOpen: false,

            // Selected values
            selectedLanguage: null,
            selectedTimezone: null,
            selectedGpsValidation: '{{ old("gps_validation_status", $settings["attendance"]["gps_validation_status"] ?? "on") }}',
            selectedQrExpiration: '{{ old("qr_expiration", $settings["attendance"]["qr_expiration"] ?? "30") }}',
            selectedAutoLogout: '{{ old("auto_logout", $settings["attendance"]["auto_logout"] ?? "off") }}',

            // Computed properties for filtering
            get filteredLanguages() {
                if (!this.languageSearch) return this.languages;
                return this.languages.filter(lang => 
                    lang.name.toLowerCase().includes(this.languageSearch.toLowerCase()) ||
                    lang.code.toLowerCase().includes(this.languageSearch.toLowerCase())
                );
            },

            get filteredTimezones() {
                if (!this.timezoneSearch) return this.timezones;
                return this.timezones.filter(tz => 
                    tz.name.toLowerCase().includes(this.timezoneSearch.toLowerCase()) ||
                    tz.value.toLowerCase().includes(this.timezoneSearch.toLowerCase())
                );
            },

            // Methods
            selectLanguage(lang) {
                this.selectedLanguage = lang;
                this.languageDropdownOpen = false;
                this.languageSearch = '';
            },

            selectTimezone(tz) {
                this.selectedTimezone = tz;
                this.timezoneDropdownOpen = false;
                this.timezoneSearch = '';
            },

            selectGpsValidation(option) {
                this.selectedGpsValidation = option;
                this.gpsDropdownOpen = false;
            },

            selectQrExpiration(option) {
                this.selectedQrExpiration = option;
                this.qrDropdownOpen = false;
            },

            selectAutoLogout(option) {
                this.selectedAutoLogout = option;
                this.autoLogoutDropdownOpen = false;
            },

            getFlagUrl(code) {
                const lowerCode = code.toLowerCase();
                return `https://flagcdn.com/w40/${lowerCode}.png`;
            },

            updateColorPreview(type, value) {
                if (type === 'primary') this.primaryColor = value;
                if (type === 'accent') this.accentColor = value;
            },

            previewLogo(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => this.logoPreview = e.target.result;
                    reader.readAsDataURL(file);
                }
            },

            previewFavicon(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => this.faviconPreview = e.target.result;
                    reader.readAsDataURL(file);
                }
            },

            getCurrentLocation() {
                window.getCurrentLocation();
            },

            resetMapToDefault() {
                window.resetMapToDefault();
            },

            // Init
            init() {
                // Set initial values from settings
                const currentLang = '{{ old("app_language", $settings["general"]["app_language"] ?? "id") }}';
                const currentTimezone = '{{ old("app_timezone", $settings["general"]["app_timezone"] ?? "Asia/Jakarta") }}';
                const currentGpsValidation = '{{ old("gps_validation_status", $settings["attendance"]["gps_validation_status"] ?? "on") }}';
                const currentQrExpiration = '{{ old("qr_expiration", $settings["attendance"]["qr_expiration"] ?? "30") }}';
                const currentAutoLogout = '{{ old("auto_logout", $settings["attendance"]["auto_logout"] ?? "off") }}';
                
                this.selectedLanguage = this.languages.find(l => l.code === currentLang) || this.languages[0];
                this.selectedTimezone = this.timezones.find(t => t.value === currentTimezone) || this.timezones[0];
                this.selectedGpsValidation = this.gpsValidationOptions.some(opt => opt.value === currentGpsValidation) ? currentGpsValidation : 'on';
                this.selectedQrExpiration = this.qrExpirationOptions.some(opt => String(opt.value) === String(currentQrExpiration)) ? currentQrExpiration : '30';
                this.selectedAutoLogout = this.autoLogoutOptions.some(opt => String(opt.value) === String(currentAutoLogout)) ? currentAutoLogout : 'off';
            }
        }));
    });

    function initLeafletMap() {
        if (mapInitialized) return;

        const defaultLat = parseFloat(document.getElementById('school_lat').value) || -6.9142402999999995;
        const defaultLng = parseFloat(document.getElementById('school_lng').value) || 107.64586179999999;
        const defaultRadius = parseInt(document.getElementById('school_radius').value) || 50;

        leafletMap = L.map('leaflet-map').setView([defaultLat, defaultLng], 17);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(leafletMap);

        const customIcon = L.divIcon({
            className: 'custom-marker',
            html: `<div style="
                width: 32px;
                height: 32px;
                background: linear-gradient(135deg, #DC2626 0%, #991B1B 100%);
                border: 3px solid #FFFFFF;
                border-radius: 50% 50% 50% 0;
                transform: rotate(-45deg);
                box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
                display: flex;
                align-items: center;
                justify-content: center;
            "><div style="
                width: 10px;
                height: 10px;
                background: #FFFFFF;
                border-radius: 50%;
                transform: rotate(45deg);
            "></div></div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });

        leafletMarker = L.marker([defaultLat, defaultLng], { 
            draggable: true,
            icon: customIcon
        }).addTo(leafletMap);

        radiusCircle = L.circle([defaultLat, defaultLng], {
            color: '#3B82F6',
            fillColor: '#3B82F6',
            fillOpacity: 0.20,
            weight: 3,
            radius: defaultRadius
        }).addTo(leafletMap);

        updateMarkerPopup(defaultLat, defaultLng, defaultRadius);

        leafletMarker.on('dragend', function() {
            const pos = leafletMarker.getLatLng();
            const radius = parseInt(document.getElementById('school_radius').value) || 50;

            document.getElementById('school_lat').value = pos.lat.toFixed(7);
            document.getElementById('school_lng').value = pos.lng.toFixed(7);
            radiusCircle.setLatLng(pos);
            updateMarkerPopup(pos.lat, pos.lng, radius);
        });

        leafletMap.on('click', function(e) {
            const radius = parseInt(document.getElementById('school_radius').value) || 50;

            leafletMarker.setLatLng([e.latlng.lat, e.latlng.lng]);
            radiusCircle.setLatLng([e.latlng.lat, e.latlng.lng]);

            document.getElementById('school_lat').value = e.latlng.lat.toFixed(7);
            document.getElementById('school_lng').value = e.latlng.lng.toFixed(7);
            updateMarkerPopup(e.latlng.lat, e.latlng.lng, radius);
        });

        const radiusInput = document.getElementById('school_radius');
        radiusInput.addEventListener('input', function() {
            const newRadius = parseInt(this.value) || 50;
            radiusCircle.setRadius(newRadius);
            document.getElementById('radius-display').textContent = newRadius;

            const pos = leafletMarker.getLatLng();
            updateMarkerPopup(pos.lat, pos.lng, newRadius);
        });

        document.getElementById('radius-display').textContent = defaultRadius;
        mapInitialized = true;

        setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 100);
    }

    function updateMarkerPopup(lat, lng, radius) {
        leafletMarker.setPopupContent(`
            <div style="text-align: center; font-family: 'Inter', sans-serif; min-width: 150px;">
                <h3 style="margin: 0 0 8px 0; color: #0F172A; font-size: 14px; font-weight: 700;">📍 Lokasi Sekolah</h3>
                <p style="margin: 0; font-size: 11px; color: #64748B; font-family: monospace;">
                    Lat: ${lat.toFixed(6)}<br>
                    Lng: ${lng.toFixed(6)}
                </p>
                <p style="margin: 8px 0 0 0; font-size: 11px; color: #F59E0B; font-weight: 600;">
                    Radius: ${radius}m
                </p>
            </div>
        `);
    }

    function getCurrentLocation() {
        if (!navigator.geolocation) {
            alert('Perangkat Anda tidak mendukung GPS.');
            return;
        }

        const requestLocation = (highAccuracy, timeout) => {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    const radius = parseInt(document.getElementById('school_radius').value) || 50;

                    document.getElementById('school_lat').value = lat.toFixed(7);
                    document.getElementById('school_lng').value = lng.toFixed(7);

                    if (!leafletMap) {
                        initLeafletMap();
                    }

                    if (leafletMap) {
                        leafletMap.setView([lat, lng], 18);
                        leafletMarker.setLatLng([lat, lng]);
                        radiusCircle.setLatLng([lat, lng]);
                        updateMarkerPopup(lat, lng, radius);
                    }
                },
                (error) => {
                    if (error.code === error.TIMEOUT && highAccuracy) {
                        requestLocation(false, 20000);
                        return;
                    }

                    let message = 'Gagal mendapatkan lokasi. ';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            message += 'Izin GPS ditolak.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message += 'Lokasi tidak tersedia.';
                            break;
                        case error.TIMEOUT:
                            message += 'Timeout. Coba lagi atau periksa koneksi GPS.';
                            break;
                        default:
                            message += 'Terjadi kesalahan saat mengambil lokasi.';
                    }
                    alert(message);
                },
                { enableHighAccuracy: highAccuracy, timeout, maximumAge: 60000 }
            );
        };

        requestLocation(true, 10000);
    }

    function resetMapToDefault() {
        const defaultLat = -6.9142402999999995;
        const defaultLng = 107.64586179999999;
        const defaultRadius = 50;

        document.getElementById('school_lat').value = defaultLat;
        document.getElementById('school_lng').value = defaultLng;
        document.getElementById('school_radius').value = defaultRadius;
        document.getElementById('radius-display').textContent = defaultRadius;

        if (leafletMap) {
            leafletMap.setView([defaultLat, defaultLng], 17);
            leafletMarker.setLatLng([defaultLat, defaultLng]);
            radiusCircle.setLatLng([defaultLat, defaultLng]);
            radiusCircle.setRadius(defaultRadius);
            updateMarkerPopup(defaultLat, defaultLng, defaultRadius);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
    });
</script>

<style>
    .fade-in { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-slide-up { animation: slideUp 0.5s ease-out forwards; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    [x-cloak] { display: none !important; }
    select { -webkit-appearance: none; -moz-appearance: none; appearance: none; }
    input[type="color"] { -webkit-appearance: none; -moz-appearance: none; appearance: none; border: none; padding: 0; }
    input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
    input[type="color"]::-webkit-color-swatch { border: none; border-radius: 0.5rem; }
    input, select, button { transition: all 0.2s ease-in-out; }

    /* Fix map container */
    #leaflet-map {
        min-height: 384px;
    }

    #search-results {
        z-index: 50;
    }

    #search-results div {
        z-index: 50;
    }

    /* Custom Marker Styling */
    .custom-marker {
        background: transparent !important;
        border: none !important;
    }

    /* Radius Circle Animation */
    .leaflet-interactive {
        transition: all 0.3s ease;
    }

    /* Popup Custom Styling */
    .leaflet-popup-content-wrapper {
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.2);
        border: 1px solid rgba(250, 204, 21, 0.3);
    }

    .leaflet-popup-content {
        margin: 12px 16px;
        font-family: 'Inter', sans-serif;
    }

    .leaflet-popup-tip {
        background: white;
        border: 1px solid rgba(250, 204, 21, 0.3);
    }

    /* Legend Styling */
    .legend-container {
        backdrop-filter: blur(10px);
    }

    /* Dark mode adjustments */
    .dark .leaflet-popup-content-wrapper {
        background: #1E293B;
        color: #F1F5F9;
        border-color: rgba(250, 204, 21, 0.3);
    }

    .dark .leaflet-popup-tip {
        background: #1E293B;
        border-color: rgba(250, 204, 21, 0.3);
    }

    .dark .leaflet-control-zoom a {
        background: #1E293B;
        color: #FACC15;
        border-color: #334155;
    }

    .dark .leaflet-control-zoom a:hover {
        background: #334155;
    }

    .dark .leaflet-control-attribution {
        background: rgba(30, 41, 59, 0.8);
        color: #94A3B8;
    }

    .dark .leaflet-control-attribution a {
        color: #FACC15;
    }

    /* Custom Scrollbar for Dropdowns */
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: transparent;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 3px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }

    .dark .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #475569;
    }

    .dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #64748B;
    }

    /* Smooth button transitions */
    button { transition: all 0.2s ease; }
    button:focus { outline: none; }
    button:hover { transform: translateY(-1px); }
    button:active { transform: translateY(0); }
</style>
@endsection