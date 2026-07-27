@extends('layouts.app')

@section('page-title', 'Data Guru')

@section('content')
<div class="space-y-6 fade-in">

    <!-- Premium Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                <i data-lucide="users" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-navy-800 dark:text-white tracking-tight">Data Guru</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola data dan informasi seluruh guru</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div id="teacherSummaryCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total Guru</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['total'] ?? 0 }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1">ID otomatis</p>
                </div>
            </div>
        </div>
        
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="circle-check" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Aktif</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['active'] ?? 0 }}</h3>
                    <p class="text-[10px] text-green-500 mt-1">{{ ($stats['total'] ?? 0) > 0 ? round((($stats['active'] ?? 0)/($stats['total'] ?? 1))*100) : 0 }}% dari total</p>
                </div>
            </div>
        </div>
        
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="circle-x" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Nonaktif</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['inactive'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gold-50 dark:bg-gold-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="scan-face" class="w-6 h-6 text-gold-600 dark:text-gold-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Hadir Hari Ini</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['today_checkin'] ?? 0 }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1">{{ now()->locale('id')->format('d M') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <form action="{{ route('teachers.index') }}" method="GET" id="filterForm" class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <div class="relative flex-1 lg:w-80">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode guru, telepon..."
                       class="w-full pl-11 pr-4 py-3 bg-white dark:bg-navy-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-400 transition-all shadow-sm hover:shadow-md"
                />
            </div>

            <!-- Premium Custom Status Dropdown -->
            <div x-data="{ 
                open: false, 
                selected: '{{ request('status') }}',
                get displayText() {
                    if (this.selected === 'active') return 'Aktif';
                    if (this.selected === 'inactive') return 'Nonaktif';
                    return 'Semua Status';
                },
                get displayIcon() {
                    if (this.selected === 'active') return 'circle-check';
                    if (this.selected === 'inactive') return 'circle-x';
                    return 'filter';
                },
                get displayColor() {
                    if (this.selected === 'active') return 'text-green-600 dark:text-green-400';
                    if (this.selected === 'inactive') return 'text-slate-500 dark:text-slate-400';
                    return 'text-navy-800 dark:text-white';
                },
                selectStatus(value) {
                    this.selected = value;
                    this.open = false;
                    document.querySelector('input[name=status]').value = value;
                    document.getElementById('filterForm').submit();
                }
            }" 
            @click.away="open = false" 
            class="relative">
                
                <input type="hidden" name="status" value="{{ request('status') }}">
                
                <!-- Trigger Button -->
                <button type="button" 
                        @click="open = !open"
                        class="group relative flex items-center gap-2.5 pl-4 pr-11 py-3 bg-white dark:bg-navy-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold transition-all shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-400 min-w-[180px]"
                        :class="open && 'ring-2 ring-navy-800 dark:ring-gold-400 border-navy-800 dark:border-gold-400'">
                    <i :data-lucide="displayIcon" class="w-4 h-4 transition-colors" :class="displayColor"></i>
                    <span class="transition-colors" :class="displayColor" x-text="displayText"></span>
                    <i data-lucide="chevron-down" 
                       class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 transition-transform duration-200"
                       :class="open && 'rotate-180'"></i>
                </button>

                <!-- Dropdown Panel -->
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute top-full mt-2 w-full min-w-[220px] bg-white dark:bg-navy-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden z-50"
                     style="display: none;">
                    
                    <div class="py-1">
                        <!-- All Status Option -->
                        <button type="button"
                                @click="selectStatus('')"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/50 group"
                                :class="selected === '' && 'bg-slate-50 dark:bg-slate-700/50'">
                            <div class="w-5 h-5 flex items-center justify-center">
                                <i data-lucide="filter" class="w-4 h-4 text-navy-800 dark:text-white"></i>
                            </div>
                            <span class="flex-1 text-left font-medium text-navy-800 dark:text-white">Semua Status</span>
                            <svg x-show="selected === ''" class="w-4 h-4 text-navy-800 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>

                        <!-- Active Option -->
                        <button type="button"
                                @click="selectStatus('active')"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm transition-colors hover:bg-green-50 dark:hover:bg-green-900/20 group"
                                :class="selected === 'active' && 'bg-green-50 dark:bg-green-900/20'">
                            <div class="w-5 h-5 flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse shadow-lg shadow-green-500/50"></div>
                            </div>
                            <span class="flex-1 text-left font-medium text-green-700 dark:text-green-400">Aktif</span>
                            <svg x-show="selected === 'active'" class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>

                        <!-- Inactive Option -->
                        <button type="button"
                                @click="selectStatus('inactive')"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/50 group"
                                :class="selected === 'inactive' && 'bg-slate-50 dark:bg-slate-700/50'">
                            <div class="w-5 h-5 flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-slate-400"></div>
                            </div>
                            <span class="flex-1 text-left font-medium text-slate-600 dark:text-slate-400">Nonaktif</span>
                            <svg x-show="selected === 'inactive'" class="w-4 h-4 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="openImportModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <span>Import Excel</span>
            </button>
            <a href="{{ route('teachers.create') }}" class="btn-ripple btn-primary flex items-center gap-2 text-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Guru
            </a>
        </div>
    </div>

    <!-- Import Excel Modal -->
    <form action="{{ route('teachers.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="importModal"
             class="hidden fixed inset-0 z-[9999] items-center justify-center p-4"
             style="background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px);">
            
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
                 style="animation: modalSlideIn 0.3s ease-out;">
                
                <!-- Header Modal -->
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white dark:text-navy-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white dark:text-navy-900">Import Data Guru</h3>
                        </div>
                        <button type="button" onclick="closeImportModal()"
                                class="p-2 hover:bg-white/10 rounded-lg transition-colors group">
                            <svg class="w-5 h-5 text-white/70 group-hover:text-white dark:text-navy-900/70 dark:group-hover:text-navy-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body Modal -->
                <div class="p-6 space-y-5">
                    <!-- Info Box -->
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-xs text-blue-700 dark:text-blue-300">
                                <p class="font-bold mb-1">Format File Excel:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Kolom 1: <strong>nama</strong> (wajib)</li>
                                    <li>Kolom 2: <strong>kode_guru</strong> (wajib)</li>
                                    <li>Email akan di-generate otomatis dari kode guru</li>
                                    <li>Password default: <code class="bg-blue-100 dark:bg-blue-900/50 px-1 rounded">password123</code></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Download Template -->
                    <a href="{{ route('teachers.template') }}"
                       class="flex items-center justify-center gap-2 px-4 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-all group">
                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download Template Excel
                    </a>

                    <!-- ✅ Custom File Upload (Modern Design) -->
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                            Pilih File Excel <span class="text-red-500">*</span>
                        </label>
                        
                        <div class="relative">
                            <input type="file" id="excelFileInput" name="file" accept=".xlsx,.xls,.csv" required
                                   class="hidden" onchange="handleFileSelect(this)">
                            
                            <label for="excelFileInput" 
                                   id="fileUploadLabel"
                                   class="flex flex-col items-center justify-center w-full p-6 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl cursor-pointer hover:border-navy-400 dark:hover:border-gold-500 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-all duration-200 group">
                                
                                <!-- State: Belum ada file -->
                                <div id="fileUploadEmpty" class="flex flex-col items-center text-center">
                                    <div class="w-14 h-14 bg-gradient-to-br from-navy-100 to-navy-200 dark:from-gold-400/20 dark:to-gold-500/20 rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                        <svg class="w-7 h-7 text-navy-600 dark:text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-navy-800 dark:text-white mb-1">Klik untuk pilih file</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">atau drag & drop file di sini</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">Format: .xlsx, .xls, .csv (Max 2MB)</p>
                                </div>

                                <!-- State: File terpilih -->
                                <div id="fileUploadSelected" class="hidden flex-col items-center text-center">
                                    <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-400/20 dark:to-green-500/20 rounded-2xl flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p id="fileName" class="text-sm font-semibold text-navy-800 dark:text-white mb-1 truncate max-w-full"></p>
                                    <p id="fileSize" class="text-xs text-slate-500 dark:text-slate-400"></p>
                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-2 font-medium">Klik untuk ganti file</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeImportModal()"
                            class="px-5 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl text-sm font-semibold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Import Data
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Bulk Actions -->
    <div id="bulkActions" class="hidden card p-4 bg-gold-50 dark:bg-gold-900/20 border border-gold-200 dark:border-gold-800">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gold-800 dark:text-gold-300">
                <span id="selectedCount">0</span> guru dipilih
            </p>
            <div class="flex items-center gap-2">
                <button onclick="bulkToggleStatus(true)" class="px-3 py-1.5 bg-green-500 text-white text-xs font-medium rounded-lg hover:bg-green-600 transition-colors">Aktifkan</button>
                <button onclick="bulkToggleStatus(false)" class="px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded-lg hover:bg-red-600 transition-colors">Nonaktifkan</button>
                <button onclick="triggerBulkDelete()" class="px-3 py-1.5 bg-slate-700 text-white text-xs font-medium rounded-lg hover:bg-slate-800 transition-colors">Hapus</button>
                <button onclick="clearSelection()" class="px-3 py-1.5 bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-lg hover:bg-slate-300 transition-colors">Batal</button>
            </div>
        </div>
    </div>

    <!-- Teachers Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="teachersTable">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-3 py-3 w-10">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 rounded border-slate-300 text-navy-800 focus:ring-navy-800">
                        </th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">ID Guru</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guru</th>
                        <th class="px-4 py-3 text-center w-28 text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kode Guru</th>
                        <th class="px-4 py-3 text-center w-64 text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Mapel</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
    @forelse($teachers as $teacher)
        @php
            $idGuru = $teacher->id_guru ?: 'GURU-' . str_pad($teacher->id, 5, '0', STR_PAD_LEFT);
            $numericId = '00000';

            if (preg_match('/GURU-(\d{5})/', $idGuru, $matches)) {
                $numericId = $matches[1];
            } else {
                $numericId = str_pad(preg_replace('/\D/', '', $idGuru), 5, '0', STR_PAD_LEFT);
            }

            $displayCode = 'SMKICBCT-' . $numericId;
            $mapel = optional($teacher->teacher)->major_specialty ?? $teacher->subject;
        @endphp
        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
            <td class="px-4 py-3">
                <input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}" 
                       class="teacher-checkbox rounded border-slate-300 text-navy-600 focus:ring-navy-500"
                       onchange="updateBulkActions()">
            </td>
            
            <!-- 1. ID GURU (Format: #12345) -->
            <td class="px-4 py-3 align-middle">
                <span class="text-[11px] font-mono text-slate-500 dark:text-slate-400">#{{ $numericId }}</span>
            </td>

            <!-- 2. GURU (Nama + Kode Guru di bawahnya) -->
            <td class="px-4 py-3 align-middle">
                <div class="flex items-center gap-3">
                    <img src="{{ $teacher->photo_url }}"
                         alt="{{ $teacher->name }}"
                         class="w-10 h-10 rounded-full object-cover border-2 border-slate-200 dark:border-slate-700">
                    <div class="flex flex-col gap-0.5">
                        <p class="text-[13px] font-bold text-navy-800 dark:text-white leading-tight">{{ $teacher->name }}</p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono leading-none">
                            {{ $displayCode }}
                        </p>
                    </div>
                </div>
            </td>

            <!-- 3. KODE GURU -->
            <td class="px-4 py-3 text-center align-middle w-28">
                <span class="text-[13px] text-slate-700 dark:text-slate-300 inline-block">
                    {{ $teacher->teacher_code ?? '-' }}
                </span>
            </td>

            <!-- Email -->
            <td class="px-4 py-3 text-center align-middle w-64">
                <span class="text-[13px] text-slate-700 dark:text-slate-300 font-medium truncate max-w-[260px] inline-block">
                    {{ $teacher->email ?? '-' }}
                </span>
            </td>

            <!-- Status -->
            <td class="px-4 py-3 text-center align-middle">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold
                    {{ $teacher->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $teacher->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                    {{ $teacher->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </td>

            <!-- Mapel -->
            <td class="px-4 py-3 text-center align-middle">
                @if($mapel)
                    <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg text-[11px] font-bold uppercase">
                        {{ $mapel }}
                    </span>
                @else
                    <span class="text-[11px] text-slate-400 italic">-</span>
                @endif
            </td>

            <!-- Aksi -->
            <td class="px-4 py-3 align-middle">
                <div class="flex items-center justify-center gap-2">
                    <a href="{{ route('teachers.show', $teacher) }}" class="p-2 w-9 h-9 flex items-center justify-center bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-all" title="Lihat Detail">
                        <i data-lucide="eye" class="w-4 h-4 text-slate-600 dark:text-slate-400"></i>
                    </a>
                    <a href="{{ route('teachers.edit', $teacher) }}" class="p-2 w-9 h-9 flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 rounded-lg transition-all" title="Edit">
                        <i data-lucide="pencil" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                    </a>
                    <button type="button"
                            class="p-2 w-9 h-9 flex items-center justify-center bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 rounded-lg transition-all delete-btn"
                            data-delete-url="{{ route('teachers.destroy', $teacher) }}"
                            data-delete-label="{{ $teacher->name }}"
                            title="Hapus">
                        <i data-lucide="trash-2" class="w-4 h-4 text-red-600 dark:text-red-400"></i>
                    </button>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="px-4 py-12 text-center">
                <i data-lucide="users" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada data guru yang ditemukan</p>
                @if(request('search') || request('status'))
                    <a href="{{ route('teachers.index') }}" class="text-xs text-navy-600 dark:text-gold-400 hover:underline mt-2 inline-block">Reset Filter</a>
                @endif
            </td>
        </tr>
    @endforelse
</tbody>
            </table>
        </div>

        @if($teachers->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                {{ $teachers->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ====================================================== --}}
{{-- PREMIUM DELETE MODAL - Pure HTML/CSS/JS (No Alpine.js) --}}
{{-- ====================================================== --}}
<div id="deleteModal"
     style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:1rem;">
    
    {{-- Backdrop --}}
    <div id="deleteModalBackdrop"
         style="position:absolute; inset:0; background:rgba(15,23,42,0.65); backdrop-filter:blur(4px);">
    </div>

    {{-- Modal Card --}}
    <div id="deleteModalBox"
         style="position:relative; z-index:1; background:white; border-radius:1.5rem; box-shadow:0 25px 60px rgba(0,0,0,0.25); border:1px solid #e2e8f0; width:100%; max-width:440px; transition:transform 0.25s ease, opacity 0.25s ease; transform:scale(0.9); opacity:0;">
        
        {{-- Body --}}
        <div style="padding:2.5rem 2rem 1.5rem; display:flex; flex-direction:column; align-items:center; text-align:center;">
            <div style="width:80px; height:80px; background:#FEF2F2; border-radius:1.25rem; display:flex; align-items:center; justify-content:center; margin-bottom:1.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                    <path d="M12 9v4"/><path d="M12 17h.01"/>
                </svg>
            </div>
            <h3 style="font-size:1.5rem; font-weight:800; color:#0F172A; margin:0 0 0.75rem;">Konfirmasi Hapus</h3>
            <p style="font-size:0.9rem; color:#64748B; line-height:1.6; margin:0;">
                Apakah Anda yakin ingin menghapus<br>
                <strong id="deleteTargetLabel" style="color:#DC2626;"></strong>?<br>
                Tindakan ini <strong>permanen</strong> dan tidak dapat dibatalkan.
            </p>
        </div>

        {{-- Actions --}}
        <div style="padding:1rem 2rem 2rem; display:flex; flex-direction:column; gap:0.75rem;">
            <form id="deleteForm" method="POST" action="" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" id="deleteSubmitBtn"
                        style="width:100%; padding:0.85rem 1.5rem; background:#DC2626; color:white; font-size:0.9rem; font-weight:700; border:none; border-radius:0.875rem; cursor:pointer; box-shadow:0 8px 20px rgba(220,38,38,0.3); transition:all 0.2s;">
                    Ya, Hapus Data
                </button>
            </form>
            <button type="button" id="deleteCancelBtn"
                    style="width:100%; padding:0.85rem 1.5rem; background:white; color:#374151; font-size:0.9rem; font-weight:700; border:2px solid #E5E7EB; border-radius:0.875rem; cursor:pointer; transition:all 0.2s;">
                Batalkan
            </button>
        </div>
    </div>
</div>

{{-- ====================================================== --}}
{{-- STATUS TOGGLE MODAL (Activate = Green / Deactivate = Orange) --}}
{{-- ====================================================== --}}
<div id="statusModal"
     style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:1rem;">

    {{-- Backdrop --}}
    <div id="statusModalBackdrop"
         style="position:absolute; inset:0; background:rgba(15,23,42,0.65); backdrop-filter:blur(4px);">
    </div>

    {{-- Modal Card --}}
    <div id="statusModalBox"
         style="position:relative; z-index:1; background:white; border-radius:1.5rem; box-shadow:0 25px 60px rgba(0,0,0,0.25); border:1px solid #e2e8f0; width:100%; max-width:440px; transition:transform 0.25s ease, opacity 0.25s ease; transform:scale(0.9); opacity:0;">

        {{-- Body --}}
        <div style="padding:2.5rem 2rem 1.5rem; display:flex; flex-direction:column; align-items:center; text-align:center;">
            <div id="statusModalIcon"
                 style="width:80px; height:80px; border-radius:1.25rem; display:flex; align-items:center; justify-content:center; margin-bottom:1.5rem;">
            </div>
            <h3 id="statusModalTitle" style="font-size:1.5rem; font-weight:800; color:#0F172A; margin:0 0 0.75rem;"></h3>
            <p style="font-size:0.9rem; color:#64748B; line-height:1.6; margin:0;">
                <span id="statusModalBody"></span>
            </p>
        </div>

        {{-- Actions --}}
        <div style="padding:1rem 2rem 2rem; display:flex; flex-direction:column; gap:0.75rem;">
            <button type="button" id="statusConfirmBtn"
                    style="width:100%; padding:0.85rem 1.5rem; color:white; font-size:0.9rem; font-weight:700; border:none; border-radius:0.875rem; cursor:pointer; transition:all 0.2s;">
            </button>
            <button type="button" id="statusCancelBtn"
                    style="width:100%; padding:0.85rem 1.5rem; background:white; color:#374151; font-size:0.9rem; font-weight:700; border:2px solid #E5E7EB; border-radius:0.875rem; cursor:pointer; transition:all 0.2s;">
                Batalkan
            </button>
        </div>
    </div>
</div>

{{-- Dark mode styles for modals --}}
<style>
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Custom scrollbar untuk modal */
    #fileUploadLabel:hover {
        border-style: solid;
    }

    /* Delete Modal */
    .dark #deleteModalBox { background: #1E293B !important; border-color: #334155 !important; }
    .dark #deleteModalBox h3 { color: #F8FAFC !important; }
    .dark #deleteModalBox p { color: #94A3B8 !important; }
    .dark #deleteCancelBtn { background: #1E293B !important; color: #CBD5E1 !important; border-color: #475569 !important; }
    .dark #deleteModalBox > div:first-child > div:first-child { background: rgba(220,38,38,0.15) !important; }
    #deleteSubmitBtn:hover { background: #B91C1C !important; transform: scale(1.02); }
    #deleteCancelBtn:hover { background: #F8FAFC !important; }
    /* Status Modal */
    .dark #statusModalBox { background: #1E293B !important; border-color: #334155 !important; }
    .dark #statusModalTitle { color: #F8FAFC !important; }
    .dark #statusModalBox p { color: #94A3B8 !important; }
    .dark #statusCancelBtn { background: #1E293B !important; color: #CBD5E1 !important; border-color: #475569 !important; }
    #statusCancelBtn:hover { background: #F8FAFC !important; }
</style>

<script>
    // ============================================
    // Modal State
    // ============================================
    let _isBulkDelete = false;
    let _statusToggleValue = null; // null | true (activate) | false (deactivate)

    // ============================================
    // Open / Close Modal
    // ============================================
    function showDeleteModal(url, label, isBulk) {
        _isBulkDelete = !!isBulk;
        document.getElementById('deleteTargetLabel').textContent = label;

        if (!isBulk) {
            document.getElementById('deleteForm').action = url;
        }

        const modal = document.getElementById('deleteModal');
        const box   = document.getElementById('deleteModalBox');

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => requestAnimationFrame(() => {
            box.style.transform = 'scale(1)';
            box.style.opacity   = '1';
        }));
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        const box   = document.getElementById('deleteModalBox');

        box.style.transform = 'scale(0.9)';
        box.style.opacity   = '0';

        setTimeout(() => {
            modal.style.display          = 'none';
            document.body.style.overflow = '';
            _isBulkDelete                = false;
        }, 250);
    }

    // ============================================
    // Wire up buttons (after DOM is ready)
    // ============================================
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();

        // Delete - Cancel button & Backdrop
        document.getElementById('deleteCancelBtn').addEventListener('click', closeDeleteModal);
        document.getElementById('deleteModalBackdrop').addEventListener('click', closeDeleteModal);

        // Delete submit
        document.getElementById('deleteForm').addEventListener('submit', function (e) {
            if (_isBulkDelete) {
                e.preventDefault();
                closeDeleteModal();
                submitBulkDeleteForm();
            }
        });

        // Per-row delete buttons
        document.querySelectorAll('.delete-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const url   = this.getAttribute('data-delete-url');
                const label = this.getAttribute('data-delete-label');
                showDeleteModal(url, label, false);
            });
        });

        // Status Modal - Cancel & Backdrop
        document.getElementById('statusCancelBtn').addEventListener('click', closeStatusModal);
        document.getElementById('statusModalBackdrop').addEventListener('click', closeStatusModal);

        // Status Modal - Confirm
        document.getElementById('statusConfirmBtn').addEventListener('click', function () {
            closeStatusModal();
            submitBulkToggleForm(_statusToggleValue);
        });

        // Auto-hide flash notifications
        const notification = document.querySelector('.slide-up.bg-green-50, .slide-up.bg-red-50');
        if (notification) {
            setTimeout(() => {
                notification.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                notification.style.opacity    = '0';
                notification.style.transform  = 'translateY(-10px)';
                setTimeout(() => notification.remove(), 500);
            }, 2000);
        }
    });

    // ============================================
    // Bulk Delete
    // ============================================
    function triggerBulkDelete() {
        const count = document.querySelectorAll('.teacher-checkbox:checked').length;
        if (count === 0) { alert('Pilih guru terlebih dahulu'); return; }
        showDeleteModal('', count + ' guru', true);
    }

    function submitBulkDeleteForm() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('teachers.bulk-delete') }}";

        const csrf = document.createElement('input');
        csrf.type  = 'hidden';
        csrf.name  = '_token';
        csrf.value = "{{ csrf_token() }}";
        form.appendChild(csrf);

        const method = document.createElement('input');
        method.type  = 'hidden';
        method.name  = '_method';
        method.value = 'DELETE';
        form.appendChild(method);

        document.querySelectorAll('.teacher-checkbox:checked').forEach(cb => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'teacher_ids[]';
            input.value = cb.value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }

    // ============================================
    // Bulk Select
    // ============================================
    let selectedTeachers = [];

    function toggleSelectAll() {
        const all = document.getElementById('selectAll').checked;
        document.querySelectorAll('.teacher-checkbox').forEach(cb => cb.checked = all);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checked = document.querySelectorAll('.teacher-checkbox:checked');
        selectedTeachers = Array.from(checked).map(cb => cb.value);

        const bar   = document.getElementById('bulkActions');
        const count = document.getElementById('selectedCount');
        if (selectedTeachers.length > 0) {
            bar.classList.remove('hidden');
            count.textContent = selectedTeachers.length;
        } else {
            bar.classList.add('hidden');
        }
    }

    function clearSelection() {
        document.getElementById('selectAll').checked = false;
        document.querySelectorAll('.teacher-checkbox').forEach(cb => cb.checked = false);
        updateBulkActions();
    }

    // ============================================
    // Status Modal (Activate / Deactivate)
    // ============================================
    function showStatusModal(status) {
        const count = selectedTeachers.length;
        if (count === 0) { alert('Pilih guru terlebih dahulu'); return; }

        _statusToggleValue = status;

        const isActivate = status === true;

        // Colors
        const iconBg     = isActivate ? '#F0FDF4' : '#FFF7ED';
        const iconStroke = isActivate ? '#16A34A' : '#EA580C';
        const titleColor = isActivate ? '#15803D' : '#C2410C';
        const btnBg      = isActivate ? '#16A34A' : '#EA580C';
        const btnShadow  = isActivate ? 'rgba(22,163,74,0.3)' : 'rgba(234,88,12,0.3)';
        const btnHover   = isActivate ? '#15803D' : '#C2410C';

        // Icon SVG
        const iconSVG = isActivate
            ? `<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="${iconStroke}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>`
            : `<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="${iconStroke}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="10" x2="10" y1="15" y2="9"/><line x1="14" x2="14" y1="15" y2="9"/></svg>`;

        const title  = isActivate ? 'Konfirmasi Aktifkan' : 'Konfirmasi Nonaktifkan';
        const action = isActivate ? 'mengaktifkan' : 'menonaktifkan';
        const btnText = isActivate ? `Ya, Aktifkan ${count} Guru` : `Ya, Nonaktifkan ${count} Guru`;

        // Apply
        const iconEl = document.getElementById('statusModalIcon');
        iconEl.style.background = iconBg;
        iconEl.innerHTML = iconSVG;

        const titleEl = document.getElementById('statusModalTitle');
        titleEl.style.color = titleColor;
        titleEl.textContent = title;

        document.getElementById('statusModalBody').innerHTML =
            `Apakah Anda yakin ingin <strong>${action}</strong><br>` +
            `<strong style="color:${titleColor}">${count} guru</strong> yang dipilih?`;

        const confirmBtn = document.getElementById('statusConfirmBtn');
        confirmBtn.textContent = btnText;
        confirmBtn.style.background = btnBg;
        confirmBtn.style.boxShadow  = `0 8px 20px ${btnShadow}`;
        confirmBtn.onmouseover = () => confirmBtn.style.background = btnHover;
        confirmBtn.onmouseleave = () => confirmBtn.style.background = btnBg;

        // Show
        const modal = document.getElementById('statusModal');
        const box   = document.getElementById('statusModalBox');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => requestAnimationFrame(() => {
            box.style.transform = 'scale(1)';
            box.style.opacity   = '1';
        }));
    }

    // ============================================
    // Import Modal File Select
    // ============================================
    function handleFileSelect(input) {
        const emptyState = document.getElementById('fileUploadEmpty');
        const selectedState = document.getElementById('fileUploadSelected');
        const fileNameEl = document.getElementById('fileName');
        const fileSizeEl = document.getElementById('fileSize');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Validasi ukuran (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2MB!');
                input.value = '';
                return;
            }
            
            const fileName = file.name.toLowerCase();
            const isValidType = fileName.endsWith('.xlsx') || 
                               fileName.endsWith('.xls') || 
                               fileName.endsWith('.csv');
            
            if (!isValidType) {
                alert('Format file harus .xlsx, .xls, atau .csv!');
                input.value = '';
                return;
            }
            
            emptyState.classList.add('hidden');
            selectedState.classList.remove('hidden');
            selectedState.classList.add('flex');
            
            fileNameEl.textContent = file.name;
            fileSizeEl.textContent = formatFileSize(file.size);
        } else {
            emptyState.classList.remove('hidden');
            selectedState.classList.add('hidden');
            selectedState.classList.remove('flex');
        }
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
    
    document.addEventListener('click', function(e) {
        if (e.target.id === 'importModal') {
            const input = document.getElementById('excelFileInput');
            if (input) {
                input.value = '';
                document.getElementById('fileUploadEmpty').classList.remove('hidden');
                document.getElementById('fileUploadSelected').classList.add('hidden');
                document.getElementById('fileUploadSelected').classList.remove('flex');
            }
        }
    });

    function closeStatusModal() {
        const modal = document.getElementById('statusModal');
        const box   = document.getElementById('statusModalBox');
        box.style.transform = 'scale(0.9)';
        box.style.opacity   = '0';
        setTimeout(() => {
            modal.style.display          = 'none';
            document.body.style.overflow = '';
            _statusToggleValue           = null;
        }, 250);
    }

    function bulkToggleStatus(status) {
        if (selectedTeachers.length === 0) { alert('Pilih guru terlebih dahulu'); return; }
        showStatusModal(status);
    }

    function submitBulkToggleForm(status) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('teachers.bulk-toggle') }}";

        [
            { name: '_token',  value: "{{ csrf_token() }}" },
            { name: 'status',  value: status ? '1' : '0'  }
        ].forEach(({ name, value }) => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = name;
            inp.value = value;
            form.appendChild(inp);
        });

        selectedTeachers.forEach(id => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'teacher_ids[]';
            inp.value = id;
            form.appendChild(inp);
        });

        document.body.appendChild(form);
        form.submit();
    }

    function openImportModal() {
        const modal = document.getElementById('importModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeImportModal() {
        const modal = document.getElementById('importModal');
        if (!modal) return;
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
</script>
@endsection