@extends('layouts.app')

@section('page-title', 'Pengaturan')

@section('content')
    <div class="fade-in" x-data="settingsApp()">

        <!-- Page Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Pengaturan Sistem</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Konfigurasi menyeluruh untuk performa sistem optimal</p>
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

        @if($errors->any())
            <div class="mb-6 card p-4 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800 animate-slide-up">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-red-800 dark:text-red-300 mb-1">Terjadi Kesalahan:</p>
                        <ul class="text-xs text-red-700 dark:text-red-400 space-y-1 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Sidebar: Tab Navigation -->
            <div class="lg:col-span-3">
                <div class="card p-3 sticky top-6">
                    <nav class="space-y-1">
                        <!-- General Tab -->
                        <button @click="activeTab = 'general'"
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left transition-all duration-200 group"
                                :class="activeTab === 'general' 
                                    ? 'bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white shadow-lg' 
                                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all"
                                 :class="activeTab === 'general' 
                                    ? 'bg-white/10' 
                                    : 'bg-slate-100 dark:bg-slate-700 group-hover:bg-navy-100 dark:group-hover:bg-navy-900/30'">
                                <i data-lucide="layout-grid" class="w-4 h-4" :class="activeTab === 'general' ? 'text-white' : 'text-slate-500 dark:text-slate-400'"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold truncate" :class="activeTab === 'general' ? 'text-white' : ''">Umum</p>
                                <p class="text-[10px] opacity-70 truncate" :class="activeTab === 'general' ? 'text-white/70' : ''">Identitas & Bahasa</p>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" :class="activeTab === 'general' ? 'text-white/70' : 'text-slate-400'"></i>
                        </button>

                        <!-- Attendance Tab -->
                        <button @click="activeTab = 'attendance'"
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left transition-all duration-200 group"
                                :class="activeTab === 'attendance' 
                                    ? 'bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white shadow-lg' 
                                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all"
                                 :class="activeTab === 'attendance' 
                                    ? 'bg-white/10' 
                                    : 'bg-slate-100 dark:bg-slate-700 group-hover:bg-navy-100 dark:group-hover:bg-navy-900/30'">
                                <i data-lucide="clock" class="w-4 h-4" :class="activeTab === 'attendance' ? 'text-white' : 'text-slate-500 dark:text-slate-400'"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold truncate" :class="activeTab === 'attendance' ? 'text-white' : ''">Presensi</p>
                                <p class="text-[10px] opacity-70 truncate" :class="activeTab === 'attendance' ? 'text-white/70' : ''">Aturan & Radius</p>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" :class="activeTab === 'attendance' ? 'text-white/70' : 'text-slate-400'"></i>
                        </button>

                        <!-- Appearance Tab -->
                        <button @click="activeTab = 'appearance'"
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left transition-all duration-200 group"
                                :class="activeTab === 'appearance' 
                                    ? 'bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white shadow-lg' 
                                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all"
                                 :class="activeTab === 'appearance' 
                                    ? 'bg-white/10' 
                                    : 'bg-slate-100 dark:bg-slate-700 group-hover:bg-navy-100 dark:group-hover:bg-navy-900/30'">
                                <i data-lucide="palette" class="w-4 h-4" :class="activeTab === 'appearance' ? 'text-white' : 'text-slate-500 dark:text-slate-400'"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold truncate" :class="activeTab === 'appearance' ? 'text-white' : ''">Tampilan</p>
                                <p class="text-[10px] opacity-70 truncate" :class="activeTab === 'appearance' ? 'text-white/70' : ''">Logo & Warna</p>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" :class="activeTab === 'appearance' ? 'text-white/70' : 'text-slate-400'"></i>
                        </button>

                        <!-- Notification Tab -->
                        <button @click="activeTab = 'notification'"
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left transition-all duration-200 group"
                                :class="activeTab === 'notification' 
                                    ? 'bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white shadow-lg' 
                                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-all"
                                 :class="activeTab === 'notification' 
                                    ? 'bg-white/10' 
                                    : 'bg-slate-100 dark:bg-slate-700 group-hover:bg-navy-100 dark:group-hover:bg-navy-900/30'">
                                <i data-lucide="bell" class="w-4 h-4" :class="activeTab === 'notification' ? 'text-white' : 'text-slate-500 dark:text-slate-400'"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold truncate" :class="activeTab === 'notification' ? 'text-white' : ''">Notifikasi</p>
                                <p class="text-[10px] opacity-70 truncate" :class="activeTab === 'notification' ? 'text-white/70' : ''">Email & Alert</p>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" :class="activeTab === 'notification' ? 'text-white/70' : 'text-slate-400'"></i>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="lg:col-span-9 space-y-6">

                <!-- General Settings -->
                <div x-show="activeTab === 'general'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="space-y-6">

                    <div class="card p-6">
                        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                            <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
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
                                <!-- App Name -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                        Nama Aplikasi <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative group">
                                        <i data-lucide="app-window" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                        <input type="text" name="app_name" 
                                               value="{{ old('app_name', $settings['general']['app_name'] ?? 'ICB CT - Absensi Guru') }}" required
                                               class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                                    </div>
                                </div>

                                <!-- Timezone -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                        Zona Waktu
                                    </label>
                                    <div class="relative group">
                                        <i data-lucide="globe" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                        <select name="app_timezone" 
                                                class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all appearance-none cursor-pointer">
                                            <option value="Asia/Jakarta" {{ old('app_timezone', $settings['general']['app_timezone'] ?? 'Asia/Jakarta') === 'Asia/Jakarta' ? 'selected' : '' }}>WIB (Jakarta)</option>
                                            <option value="Asia/Makassar" {{ old('app_timezone', $settings['general']['app_timezone'] ?? 'Asia/Jakarta') === 'Asia/Makassar' ? 'selected' : '' }}>WITA (Makassar)</option>
                                            <option value="Asia/Jayapura" {{ old('app_timezone', $settings['general']['app_timezone'] ?? 'Asia/Jakarta') === 'Asia/Jayapura' ? 'selected' : '' }}>WIT (Jayapura)</option>
                                        </select>
                                        <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
                                    </div>
                                </div>

                                <!-- Language -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                        Bahasa
                                    </label>
                                    <div class="relative group">
                                        <i data-lucide="languages" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                        <select name="app_language" 
                                                class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all appearance-none cursor-pointer">
                                            <option value="id" {{ old('app_language', $settings['general']['app_language'] ?? 'id') === 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                            <option value="en" {{ old('app_language', $settings['general']['app_language'] ?? 'id') === 'en' ? 'selected' : '' }}>English</option>
                                        </select>
                                        <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
                                    </div>
                                </div>

                                <!-- Admin Email -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                        Email Operator
                                    </label>
                                    <div class="relative group">
                                        <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                        <input type="email" name="admin_email" 
                                               value="{{ old('admin_email', $settings['general']['admin_email'] ?? '') }}"
                                               class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600" 
                                               placeholder="admin@sekolah.sch.id">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-5 border-t border-slate-200 dark:border-slate-700">
                                <button type="submit" 
                                        class="px-8 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:shadow-navy-800/40 dark:hover:shadow-gold-400/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex items-center gap-2">
                                    <i data-lucide="save" class="w-4 h-4"></i>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Attendance Settings -->
                <div x-show="activeTab === 'attendance'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="space-y-6">

                    <div class="card p-6">
                        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                            <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
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
                                <!-- Start Time -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                        Jam Mulai Presensi
                                    </label>
                                    <div class="relative group">
                                        <i data-lucide="sunrise" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                        <input type="time" name="attendance_start_time" 
                                               value="{{ old('attendance_start_time', $settings['attendance']['attendance_start_time'] ?? '07:30') }}" required
                                               class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                                    </div>
                                </div>

                                <!-- End Time -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                        Batas Akhir Presensi
                                    </label>
                                    <div class="relative group">
                                        <i data-lucide="sunset" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                        <input type="time" name="attendance_end_time" 
                                               value="{{ old('attendance_end_time', $settings['attendance']['attendance_end_time'] ?? '08:00') }}" required
                                               class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                                    </div>
                                </div>

                                <!-- Late Grace Period -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                        Toleransi Terlambat (Menit)
                                    </label>
                                    <div class="relative group">
                                        <i data-lucide="timer" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                        <input type="number" name="attendance_late_grace_period" 
                                               value="{{ old('attendance_late_grace_period', $settings['attendance']['attendance_late_grace_period'] ?? 15) }}" min="0" max="60"
                                               class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                                    </div>
                                </div>

                                <!-- Location Radius -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                        Radius Lokasi (Meter)
                                    </label>
                                    <div class="relative group">
                                        <i data-lucide="map-pin" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                        <input type="number" name="location_radius" 
                                               value="{{ old('location_radius', $settings['attendance']['location_radius'] ?? 100) }}" min="10" max="1000"
                                               class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-5 border-t border-slate-200 dark:border-slate-700">
                                <button type="submit" 
                                        class="px-8 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:shadow-navy-800/40 dark:hover:shadow-gold-400/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex items-center gap-2">
                                    <i data-lucide="save" class="w-4 h-4"></i>
                                    Simpan Aturan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Appearance Settings -->
                <div x-show="activeTab === 'appearance'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="space-y-6">

                    <div class="card p-6">
                        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                            <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                                <i data-lucide="palette" class="w-5 h-5 text-white dark:text-navy-900"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-navy-800 dark:text-white">Branding & Visual</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Ubah logo dan tema warna aplikasi</p>
                            </div>
                        </div>

                        <form action="{{ route('settings.appearance') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            <!-- Logo Upload -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                        Logo Aplikasi
                                    </label>
                                    <div class="relative">
                                        <input type="file" id="logoInput" name="app_logo" accept="image/*" class="hidden" @change="previewLogo($event)">
                                        <label for="logoInput" 
                                               class="flex flex-col items-center justify-center w-full p-6 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl cursor-pointer hover:border-navy-400 dark:hover:border-gold-500 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-all duration-200 group">
                                            <template x-if="!logoPreview">
                                                <div class="flex flex-col items-center text-center">
                                                    <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                                        <i data-lucide="image" class="w-7 h-7 text-slate-400 group-hover:text-navy-600 dark:group-hover:text-gold-400 transition-colors"></i>
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

                                <!-- Favicon Upload -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                        Favicon
                                    </label>
                                    <div class="relative">
                                        <input type="file" id="faviconInput" name="app_favicon" accept=".ico,.png,.jpg,.jpeg" class="hidden" @change="previewFavicon($event)">
                                        <label for="faviconInput" 
                                               class="flex flex-col items-center justify-center w-full p-6 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl cursor-pointer hover:border-navy-400 dark:hover:border-gold-500 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-all duration-200 group">
                                            <template x-if="!faviconPreview">
                                                <div class="flex flex-col items-center text-center">
                                                    <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                                        <i data-lucide="sparkles" class="w-7 h-7 text-slate-400 group-hover:text-navy-600 dark:group-hover:text-gold-400 transition-colors"></i>
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

                            <!-- Color Pickers -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Primary Color -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                        Warna Utama
                                    </label>
                                    <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600">
                                        <input type="color" name="primary_color" 
                                               x-model="primaryColor"
                                               @input="updateColorPreview('primary', $event.target.value)"
                                               value="{{ old('primary_color', $settings['appearance']['primary_color'] ?? '#0F172A') }}" 
                                               class="w-12 h-12 rounded-lg cursor-pointer border-2 border-slate-200 dark:border-slate-600 p-0 overflow-hidden shadow-sm hover:scale-105 transition-transform">
                                        <div class="flex-1">
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Kode HEX</p>
                                            <input type="text" readonly 
                                                   x-model="primaryColor"
                                                   class="w-full bg-transparent text-sm font-mono font-semibold text-navy-800 dark:text-white border-none p-0 focus:ring-0">
                                        </div>
                                    </div>
                                </div>

                                <!-- Accent Color -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                        Warna Aksen
                                    </label>
                                    <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600">
                                        <input type="color" name="accent_color" 
                                               x-model="accentColor"
                                               @input="updateColorPreview('accent', $event.target.value)"
                                               value="{{ old('accent_color', $settings['appearance']['accent_color'] ?? '#FACC15') }}" 
                                               class="w-12 h-12 rounded-lg cursor-pointer border-2 border-slate-200 dark:border-slate-600 p-0 overflow-hidden shadow-sm hover:scale-105 transition-transform">
                                        <div class="flex-1">
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Kode HEX</p>
                                            <input type="text" readonly 
                                                   x-model="accentColor"
                                                   class="w-full bg-transparent text-sm font-mono font-semibold text-navy-800 dark:text-white border-none p-0 focus:ring-0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Color Preview -->
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600">
                                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-3">Preview Tema</p>
                                <div class="flex items-center gap-4">
                                    <div class="flex-1 h-10 rounded-lg" :style="`background: ${primaryColor}`"></div>
                                    <div class="flex-1 h-10 rounded-lg" :style="`background: ${accentColor}`"></div>
                                    <div class="flex-1 h-10 rounded-lg bg-gradient-to-r" :style="`background: linear-gradient(to right, ${primaryColor}, ${accentColor})`"></div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-5 border-t border-slate-200 dark:border-slate-700">
                                <button type="submit" 
                                        class="px-8 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:shadow-navy-800/40 dark:hover:shadow-gold-400/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex items-center gap-2">
                                    <i data-lucide="save" class="w-4 h-4"></i>
                                    Terapkan Branding
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div x-show="activeTab === 'notification'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="space-y-6">

                    <div class="card p-6">
                        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                            <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                                <i data-lucide="bell" class="w-5 h-5 text-white dark:text-navy-900"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-navy-800 dark:text-white">Sistem Notifikasi</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Atur notifikasi otomatis ke Operator</p>
                            </div>
                        </div>

                        <form action="{{ route('settings.notification') }}" method="POST" class="space-y-5">
                            @csrf

                            <!-- Email Notification Toggle -->
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600 hover:border-navy-300 dark:hover:border-gold-600 transition-colors">
                                <label class="flex items-start gap-4 cursor-pointer">
                                    <div class="relative inline-flex items-center cursor-pointer mt-1">
                                        <input type="checkbox" name="email_notification" 
                                               {{ old('email_notification', $settings['notification']['email_notification'] ?? true) ? 'checked' : '' }} 
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-navy-800 dark:peer-checked:bg-gold-500"></div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-navy-800 dark:text-white">Laporan Harian via Email</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kirim ringkasan presensi setiap hari ke Operator</p>
                                    </div>
                                </label>
                            </div>

                            <!-- Late Alert Toggle -->
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600 hover:border-navy-300 dark:hover:border-gold-600 transition-colors">
                                <label class="flex items-start gap-4 cursor-pointer">
                                    <div class="relative inline-flex items-center cursor-pointer mt-1">
                                        <input type="checkbox" name="late_notification" 
                                               {{ old('late_notification', $settings['notification']['late_notification'] ?? true) ? 'checked' : '' }} 
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-navy-800 dark:peer-checked:bg-gold-500"></div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-navy-800 dark:text-white">Alert Terlambat Realtime</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Notifikasi instan ketika guru terlambat absen</p>
                                    </div>
                                </label>
                            </div>

                            <!-- Alert Email -->
                            <div>
                                <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                    Email Penerima Alerts
                                </label>
                                <div class="relative group">
                                    <i data-lucide="bell-ring" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                    <input type="email" name="alert_email" 
                                           value="{{ old('alert_email', $settings['notification']['alert_email'] ?? $settings['general']['admin_email'] ?? '') }}"
                                           class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600" 
                                           placeholder="alerts@sekolah.sch.id">
                                </div>
                            </div>

                            <div class="flex justify-end pt-5 border-t border-slate-200 dark:border-slate-700">
                                <button type="submit" 
                                        class="px-8 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:shadow-navy-800/40 dark:hover:shadow-gold-400/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex items-center gap-2">
                                    <i data-lucide="save" class="w-4 h-4"></i>
                                    Simpan Notifikasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Confirmation Modal -->
    <div x-show="showResetModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         x-cloak>
        <div @click.outside="showResetModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="card p-6 max-w-md w-full">

            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-1">Reset Konfigurasi?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Tindakan ini akan mengembalikan semua pengaturan ke nilai awal. Data yang sudah diubah akan hilang permanen.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button @click="showResetModal = false"
                        class="px-5 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-colors">
                    Batal
                </button>
                <form action="{{ route('settings.reset') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        Ya, Reset
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('settingsApp', () => ({
                activeTab: 'general',
                showResetModal: false,
                primaryColor: '{{ old("primary_color", $settings["appearance"]["primary_color"] ?? "#0F172A") }}',
                accentColor: '{{ old("accent_color", $settings["appearance"]["accent_color"] ?? "#FACC15") }}',
                logoPreview: null,
                faviconPreview: null,

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
                }
            }));
        });

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

        [x-cloak] { display: none !important; }

        /* Custom scrollbar for selects */
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        /* Color picker styling */
        input[type="color"] {
            -webkit-appearance: none;
            border: none;
            padding: 0;
        }
        input[type="color"]::-webkit-color-swatch-wrapper {
            padding: 0;
        }
        input[type="color"]::-webkit-color-swatch {
            border: none;
            border-radius: 0.5rem;
        }

        /* Smooth transitions */
        input, select, button {
            transition: all 0.2s ease-in-out;
        }
    </style>
@endsection