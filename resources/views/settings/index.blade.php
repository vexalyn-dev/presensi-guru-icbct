@extends('layouts.app')

@section('page-title', 'Pengaturan')

@section('content')
<div class="space-y-6 fade-in" x-data="{ activeTab: 'general' }">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-navy-800 dark:text-white tracking-tight">Pengaturan Sistem</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Konfigurasi menyeluruh untuk performa sistem optimal.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button onclick="document.getElementById('resetModal').style.display = 'flex'" 
                    class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all duration-300">
                Reset ke Default
            </button>
        </div>
    </div>

    <!-- Alert Success/Error -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl flex items-center gap-4 animate-slide-up">
        <i data-lucide="check-circle" class="w-5 h-5"></i>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-100 text-red-600 px-6 py-4 rounded-2xl animate-slide-up">
        <div class="flex items-center gap-4 mb-2">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            <span class="text-sm font-bold">Terjadi Kesalahan:</span>
        </div>
        <ul class="list-disc list-inside text-xs font-medium space-y-1 ml-9">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Navigation Sidebar -->
        <div class="lg:col-span-4 space-y-4">
            <div class="card p-2 space-y-1">
                <button @click="activeTab = 'general'" 
                        :class="activeTab === 'general' ? 'bg-navy-800 text-white shadow-lg shadow-navy-900/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                        class="w-full flex items-center gap-4 px-5 py-4 rounded-xl transition-all duration-300 group">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                         :class="activeTab === 'general' ? 'bg-white/10 text-white' : 'bg-slate-100 dark:bg-slate-700 group-hover:bg-white text-navy-800'">
                        <i data-lucide="layout-grid" class="w-5 h-5"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-[13px] font-bold uppercase tracking-wider">Umum</p>
                        <p class="text-[10px] opacity-70 font-medium tracking-tight">Identitas & Bahasa</p>
                    </div>
                </button>

                <button @click="activeTab = 'attendance'" 
                        :class="activeTab === 'attendance' ? 'bg-navy-800 text-white shadow-lg shadow-navy-900/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                        class="w-full flex items-center gap-4 px-5 py-4 rounded-xl transition-all duration-300 group">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                         :class="activeTab === 'attendance' ? 'bg-white/10 text-white' : 'bg-slate-100 dark:bg-slate-700 group-hover:bg-white text-navy-800'">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-[13px] font-bold uppercase tracking-wider">Presensi</p>
                        <p class="text-[10px] opacity-70 font-medium tracking-tight">Aturan & Radius</p>
                    </div>
                </button>

                <button @click="activeTab = 'appearance'" 
                        :class="activeTab === 'appearance' ? 'bg-navy-800 text-white shadow-lg shadow-navy-900/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                        class="w-full flex items-center gap-4 px-5 py-4 rounded-xl transition-all duration-300 group">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                         :class="activeTab === 'appearance' ? 'bg-white/10 text-white' : 'bg-slate-100 dark:bg-slate-700 group-hover:bg-white text-navy-800'">
                        <i data-lucide="palette" class="w-5 h-5"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-[13px] font-bold uppercase tracking-wider">Tampilan</p>
                        <p class="text-[10px] opacity-70 font-medium tracking-tight">Logo & Warna</p>
                    </div>
                </button>

                <button @click="activeTab = 'notification'" 
                        :class="activeTab === 'notification' ? 'bg-navy-800 text-white shadow-lg shadow-navy-900/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                        class="w-full flex items-center gap-4 px-5 py-4 rounded-xl transition-all duration-300 group">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                         :class="activeTab === 'notification' ? 'bg-white/10 text-white' : 'bg-slate-100 dark:bg-slate-700 group-hover:bg-white text-navy-800'">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-[13px] font-bold uppercase tracking-wider">Notifikasi</p>
                        <p class="text-[10px] opacity-70 font-medium tracking-tight">Email & Alert</p>
                    </div>
                </button>
            </div>
        </div>

        <!-- Main Content area -->
        <div class="lg:col-span-8">
            
            <!-- General Settings -->
            <div x-show="activeTab === 'general'" x-transition:enter="slide-in" class="space-y-6">
                <div class="card p-8">
                    <div class="flex items-center gap-5 mb-8 pb-6 border-b border-slate-100 dark:border-slate-700/50">
                        <div class="w-12 h-12 bg-slate-50 dark:bg-navy-900/30 rounded-xl flex items-center justify-center text-navy-800 dark:text-slate-300">
                            <i data-lucide="layout-grid" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-navy-800 dark:text-white">Identitas Sekolah</h2>
                            <p class="text-xs text-slate-500 font-medium">Atur informasi dasar instansi Anda.</p>
                        </div>
                    </div>

                    <form action="{{ route('settings.general') }}" method="POST" class="space-y-8">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest pl-1">Nama Aplikasi</label>
                                <input type="text" name="app_name" value="{{ old('app_name', $settings['general']['app_name'] ?? 'ICB CT - Absensi Guru') }}" required
                                       class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold focus:bg-white dark:focus:bg-slate-700 focus:border-navy-800 transition-all outline-none">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest pl-1">Zona Waktu</label>
                                <select name="app_timezone" class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold focus:bg-white dark:focus:bg-slate-700 focus:border-navy-800 transition-all outline-none appearance-none">
                                    <option value="Asia/Jakarta" {{ old('app_timezone', $settings['general']['app_timezone'] ?? 'Asia/Jakarta') === 'Asia/Jakarta' ? 'selected' : '' }}>WIB (Jakarta)</option>
                                    <option value="Asia/Makassar" {{ old('app_timezone', $settings['general']['app_timezone'] ?? 'Asia/Jakarta') === 'Asia/Makassar' ? 'selected' : '' }}>WITA (Makassar)</option>
                                    <option value="Asia/Jayapura" {{ old('app_timezone', $settings['general']['app_timezone'] ?? 'Asia/Jakarta') === 'Asia/Jayapura' ? 'selected' : '' }}>WIT (Jayapura)</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest pl-1">Bahasa</label>
                                <select name="app_language" class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold focus:bg-white dark:focus:bg-slate-700 focus:border-navy-800 transition-all outline-none appearance-none">
                                    <option value="id" {{ old('app_language', $settings['general']['app_language'] ?? 'id') === 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                    <option value="en" {{ old('app_language', $settings['general']['app_language'] ?? 'id') === 'en' ? 'selected' : '' }}>English</option>
                                </select>
                            </div>

                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest pl-1">Email Administrator</label>
                                <input type="email" name="admin_email" value="{{ old('admin_email', $settings['general']['admin_email'] ?? '') }}"
                                       class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold focus:bg-white dark:focus:bg-slate-700 focus:border-navy-800 transition-all outline-none" placeholder="admin@sekolah.sch.id">
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="btn-ripple bg-navy-800 hover:bg-navy-900 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-lg shadow-navy-900/20 flex items-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Attendance Settings -->
            <div x-show="activeTab === 'attendance'" x-transition:enter="slide-in" class="space-y-6">
                <div class="card p-8">
                    <div class="flex items-center gap-5 mb-8 pb-6 border-b border-slate-100 dark:border-slate-700/50">
                        <div class="w-12 h-12 bg-slate-50 dark:bg-navy-900/30 rounded-xl flex items-center justify-center text-navy-800 dark:text-slate-300">
                            <i data-lucide="clock" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-navy-800 dark:text-white">Aturan Presensi</h2>
                            <p class="text-xs text-slate-500 font-medium">Kelola jam kerja dan validasi absensi.</p>
                        </div>
                    </div>

                    <form action="{{ route('settings.attendance') }}" method="POST" class="space-y-8">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest pl-1">Jam Mulai Presensi</label>
                                <input type="time" name="attendance_start_time" value="{{ old('attendance_start_time', $settings['attendance']['attendance_start_time'] ?? '07:30') }}" required
                                       class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold focus:bg-white dark:focus:bg-slate-700 focus:border-navy-800 transition-all outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest pl-1">Batas Akhir Presensi</label>
                                <input type="time" name="attendance_end_time" value="{{ old('attendance_end_time', $settings['attendance']['attendance_end_time'] ?? '08:00') }}" required
                                       class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold focus:bg-white dark:focus:bg-slate-700 focus:border-navy-800 transition-all outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest pl-1">Toleransi Terlambat (Menit)</label>
                                <input type="number" name="attendance_late_grace_period" value="{{ old('attendance_late_grace_period', $settings['attendance']['attendance_late_grace_period'] ?? 15) }}"
                                       class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold focus:bg-white dark:focus:bg-slate-700 focus:border-navy-800 transition-all outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest pl-1">Radius Lokasi (Meter)</label>
                                <input type="number" name="location_radius" value="{{ old('location_radius', $settings['attendance']['location_radius'] ?? 100) }}"
                                       class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold focus:bg-white dark:focus:bg-slate-700 focus:border-navy-800 transition-all outline-none">
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="btn-ripple bg-navy-800 hover:bg-navy-900 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-lg shadow-navy-900/20 flex items-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                Simpan Aturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Appearance Settings -->
            <div x-show="activeTab === 'appearance'" x-transition:enter="slide-in" class="space-y-6">
                <div class="card p-8">
                    <div class="flex items-center gap-5 mb-8 pb-6 border-b border-slate-100 dark:border-slate-700/50">
                        <div class="w-12 h-12 bg-slate-50 dark:bg-navy-900/30 rounded-xl flex items-center justify-center text-navy-800 dark:text-slate-300">
                            <i data-lucide="palette" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-navy-800 dark:text-white">Branding & Visual</h2>
                            <p class="text-xs text-slate-500 font-medium">Ubah logo dan tema warna aplikasi.</p>
                        </div>
                    </div>

                    <form action="{{ route('settings.appearance') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest pl-1">Logo Aplikasi</label>
                                <div class="relative group h-40 bg-slate-50 dark:bg-slate-900/30 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl flex flex-col items-center justify-center overflow-hidden hover:border-navy-800 transition-all cursor-pointer" onclick="document.getElementById('logoInput').click()">
                                    @if($appSettings->app_logo ?? null)
                                        <img src="{{ asset('storage/' . $appSettings->app_logo) }}" class="max-h-24 object-contain">
                                    @else
                                        <i data-lucide="image-plus" class="w-8 h-8 text-slate-300"></i>
                                        <span class="text-[10px] text-slate-400 font-bold mt-2">Upload Logo</span>
                                    @endif
                                    <input type="file" id="logoInput" name="app_logo" accept="image/*" class="hidden">
                                </div>
                            </div>
                            <div class="space-y-4">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest pl-1">Favicon</label>
                                <div class="relative group h-40 bg-slate-50 dark:bg-slate-900/30 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl flex flex-col items-center justify-center overflow-hidden hover:border-navy-800 transition-all cursor-pointer" onclick="document.getElementById('faviconInput').click()">
                                    @if($appSettings->app_favicon ?? null)
                                        <img src="{{ asset('storage/' . $appSettings->app_favicon) }}" class="w-12 h-12 object-contain rounded-lg">
                                    @else
                                        <i data-lucide="sparkles" class="w-8 h-8 text-slate-300"></i>
                                        <span class="text-[10px] text-slate-400 font-bold mt-2">Upload Favicon</span>
                                    @endif
                                    <input type="file" id="faviconInput" name="app_favicon" accept=".ico,.png,.jpg,.jpeg" class="hidden">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 bg-slate-50 dark:bg-slate-900/20 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center gap-4">
                                <input type="color" name="primary_color" value="{{ old('primary_color', $settings['appearance']['primary_color'] ?? '#0F172A') }}" class="w-12 h-12 rounded-lg cursor-pointer border-0 p-0 overflow-hidden shadow-sm">
                                <div class="flex-1">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Warna Utama</label>
                                    <input type="text" readonly value="{{ old('primary_color', $settings['appearance']['primary_color'] ?? '#0F172A') }}" class="w-full bg-transparent text-sm font-bold border-none p-0 focus:ring-0">
                                </div>
                            </div>
                            <div class="p-4 bg-slate-50 dark:bg-slate-900/20 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center gap-4">
                                <input type="color" name="accent_color" value="{{ old('accent_color', $settings['appearance']['accent_color'] ?? '#FACC15') }}" class="w-12 h-12 rounded-lg cursor-pointer border-0 p-0 overflow-hidden shadow-sm">
                                <div class="flex-1">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Warna Aksen</label>
                                    <input type="text" readonly value="{{ old('accent_color', $settings['appearance']['accent_color'] ?? '#FACC15') }}" class="w-full bg-transparent text-sm font-bold border-none p-0 focus:ring-0">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="btn-ripple bg-navy-800 hover:bg-navy-900 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-lg shadow-navy-900/20 flex items-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                Terapkan Branding
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notifications Settings -->
            <div x-show="activeTab === 'notification'" x-transition:enter="slide-in" class="space-y-6">
                <div class="card p-8">
                    <div class="flex items-center gap-5 mb-8 pb-6 border-b border-slate-100 dark:border-slate-700/50">
                        <div class="w-12 h-12 bg-slate-50 dark:bg-navy-900/30 rounded-xl flex items-center justify-center text-navy-800 dark:text-slate-300">
                            <i data-lucide="bell" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-navy-800 dark:text-white">Sistem Notifikasi</h2>
                            <p class="text-xs text-slate-500 font-medium">Atur notifikasi otomatis ke admin.</p>
                        </div>
                    </div>

                    <form action="{{ route('settings.notification') }}" method="POST" class="space-y-8">
                        @csrf
                        <div class="space-y-4">
                            <label class="flex items-center justify-between p-5 bg-slate-50 dark:bg-slate-900/20 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer group hover:border-navy-800 transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center shadow-sm text-slate-400 group-hover:text-navy-800 transition-colors">
                                        <i data-lucide="mail" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-navy-800 dark:text-white">Laporan Harian via Email</p>
                                        <p class="text-[10px] text-slate-500">Ringkasan presensi setiap hari.</p>
                                    </div>
                                </div>
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="email_notification" {{ old('email_notification', $settings['notification']['email_notification'] ?? true) ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-navy-800"></div>
                                </div>
                            </label>

                            <label class="flex items-center justify-between p-5 bg-slate-50 dark:bg-slate-900/20 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer group hover:border-navy-800 transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center shadow-sm text-slate-400 group-hover:text-navy-800 transition-colors">
                                        <i data-lucide="zap" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-navy-800 dark:text-white">Alert Terlambat Realtime</p>
                                        <p class="text-[10px] text-slate-500">Notifikasi instan ke administrator.</p>
                                    </div>
                                </div>
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="late_notification" {{ old('late_notification', $settings['notification']['late_notification'] ?? true) ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-navy-800"></div>
                                </div>
                            </label>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest pl-1">Email Penerima Alerts</label>
                            <input type="email" name="admin_email" value="{{ old('admin_email', $settings['notification']['admin_email'] ?? $settings['general']['admin_email'] ?? '') }}"
                                   class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold focus:bg-white dark:focus:bg-slate-700 focus:border-navy-800 transition-all outline-none" placeholder="alerts@sekolah.sch.id">
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="btn-ripple bg-navy-800 hover:bg-navy-900 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-lg shadow-navy-900/20 flex items-center gap-2">
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

<!-- Reset Modal -->
<div id="resetModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4" style="display: none;">
    <div class="card p-10 max-w-md w-full shadow-2xl animate-pop-in">
        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/20 rounded-2xl flex items-center justify-center text-red-600 mb-6 mx-auto">
            <i data-lucide="alert-octagon" class="w-8 h-8"></i>
        </div>
        <h3 class="text-xl font-bold text-navy-800 dark:text-white mb-2 text-center">Reset Konfigurasi?</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-8 text-center leading-relaxed">Tindakan ini akan mengembalikan semua pengaturan ke nilai awal pabrik. Data yang sudah diubah akan hilang permanen.</p>
        <div class="flex gap-4">
            <button onclick="document.getElementById('resetModal').style.display = 'none'" class="flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-bold transition-colors">
                Batalkan
            </button>
            <form action="{{ route('settings.reset') }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white rounded-xl text-sm font-bold shadow-xl shadow-red-500/30 transition-colors">
                    Ya, Reset
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>

<style>
    .slide-in {
        animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .animate-slide-up {
        animation: slideUp 0.5s ease-out forwards;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection