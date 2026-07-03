@extends('layouts.app')

@section('page-title', 'Ajukan Izin')

@section('content')
    <div class="fade-in" x-data="leaveForm()">

        <!-- Page Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('leaves.index') }}"
                    class="group flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600">
                    <i data-lucide="arrow-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
                    <span>Kembali</span>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Ajukan Izin / Sakit</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Isi formulir di bawah ini untuk mengajukan izin
                    </p>
                </div>
            </div>
        </div>

        <!-- Row 1: Panduan & Timeline (Side by Side) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            <!-- Panduan Pengajuan Card -->
            <div class="card p-6 animate-slide-up" style="animation-delay: 0.1s">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-400/30">
                        <i data-lucide="info" class="w-5 h-5 text-white"></i>
                    </div>
                    <h3 class="text-base font-bold text-navy-800 dark:text-white">Panduan Pengajuan</h3>
                </div>

                <div class="space-y-4">
                    <div
                        class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors group">
                        <div
                            class="w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 group-hover:scale-110 transition-transform">
                            <i data-lucide="check" class="w-3.5 h-3.5 text-green-600 dark:text-green-400"></i>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400">Ajukan minimal 1 hari sebelum tanggal izin</p>
                    </div>
                    <div
                        class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors group">
                        <div
                            class="w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 group-hover:scale-110 transition-transform">
                            <i data-lucide="check" class="w-3.5 h-3.5 text-green-600 dark:text-green-400"></i>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400">Lampirkan surat dokter untuk pengajuan sakit
                        </p>
                    </div>
                    <div
                        class="flex items-start gap-3 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors group">
                        <div
                            class="w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 group-hover:scale-110 transition-transform">
                            <i data-lucide="check" class="w-3.5 h-3.5 text-green-600 dark:text-green-400"></i>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400">Maksimal lampiran 2MB (PDF, JPG, PNG)</p>
                    </div>
                </div>
            </div>

            <!-- Proses Pengajuan Timeline Card (FUNCTIONAL) -->
            <div class="card p-6 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-800/50 animate-slide-up" style="animation-delay: 0.15s">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-gold-400 to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-gold-400/30">
                        <i data-lucide="clock" class="w-5 h-5 text-navy-900"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Proses Pengajuan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Status terkini pengajuan Anda</p>
                    </div>
                </div>

                <div class="relative pl-4 border-l-2 border-slate-200 dark:border-slate-700 space-y-5">
                    <!-- Step 1: Diajukan (Always active) -->
                    <div class="relative group">
                        <div class="absolute -left-[21px] w-4 h-4 bg-gold-400 rounded-full border-2 border-white dark:border-slate-800 shadow-lg group-hover:scale-125 transition-transform animate-pulse"></div>
                        <div class="p-3 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs font-semibold text-navy-800 dark:text-white">Diajukan</p>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ now()->format('H:i') }}</span>
                            </div>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Form dikirim</p>
                            <p class="text-[10px] text-slate-400 mt-1">{{ now()->locale('id')->isoFormat('D MMM YYYY') }}</p>
                        </div>
                    </div>

                    <!-- Step 2: Ditinjau (Active if not pending) -->
                    <div class="relative group">
                        <div class="absolute -left-[21px] w-4 h-4 bg-slate-300 dark:bg-slate-600 rounded-full border-2 border-white dark:border-slate-800 shadow-lg transition-transform"></div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Ditinjau</p>
                            </div>
                            <p class="text-[10px] text-slate-400">Admin memverifikasi</p>
                        </div>
                    </div>

                    <!-- Step 3: Disetujui/Ditolak -->
                    <div class="relative group">
                        <div class="absolute -left-[21px] w-4 h-4 bg-slate-300 dark:bg-slate-600 rounded-full border-2 border-white dark:border-slate-800 shadow-lg transition-transform"></div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Disetujui/Ditolak</p>
                            </div>
                            <p class="text-[10px] text-slate-400">Notifikasi dikirim</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Formulir Pengajuan (Split into 2 Cards) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            <!-- Card 1: Jenis & Tanggal -->
            <div class="card p-6 animate-slide-up" style="animation-delay: 0.2s">
                <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                        <i data-lucide="calendar-check" class="w-5 h-5 text-white dark:text-navy-900"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Jenis & Tanggal</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Pilih jenis dan periode izin</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <!-- Request Type - MODERN DROPDOWN -->
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                            Jenis Pengajuan <span class="text-red-500">*</span>
                        </label>

                        <!-- Modern Custom Dropdown -->
                        <div class="relative" x-data="{ open: false, selected: '' }" @click.outside="open = false">
                            <!-- Dropdown Trigger -->
                            <button type="button" 
                                    @click="open = !open"
                                    class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all hover:border-navy-300 dark:hover:border-gold-600 flex items-center justify-between group">

                                <div class="flex items-center gap-3">
                                    <i :data-lucide="getIcon(selected)" 
                                       class="w-4 h-4"
                                       :class="selected ? 'text-navy-600 dark:text-gold-400' : 'text-slate-400'"></i>
                                    <span class="text-slate-700 dark:text-slate-300" 
                                          x-text="getLabel(selected) || 'Pilih jenis pengajuan...'"></span>
                                </div>
                                <i data-lucide="chevron-down" 
                                   class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                   :class="{'rotate-180': open}"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                                 class="absolute z-50 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden"
                                 x-cloak>

                                <div class="p-2 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50">
                                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 px-2">Pilih Jenis</p>
                                </div>

                                <div class="max-h-60 overflow-y-auto p-1.5 space-y-1">
                                    <button type="button"
                                            @click="selectType('Izin'); open = false"
                                            class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group"
                                            :class="selected === 'Izin' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300'">
                                        <i data-lucide="file-check" class="w-4 h-4" :class="selected === 'Izin' ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500'"></i>
                                        <div class="flex-1">
                                            <p class="font-medium">Izin</p>
                                            <p class="text-[10px] opacity-70">Keperluan pribadi</p>
                                        </div>
                                        <i data-lucide="check" class="w-4 h-4" x-show="selected === 'Izin'"></i>
                                    </button>

                                    <button type="button"
                                            @click="selectType('Sakit'); open = false"
                                            class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group"
                                            :class="selected === 'Sakit' ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300'">
                                        <i data-lucide="pill" class="w-4 h-4" :class="selected === 'Sakit' ? 'text-green-600' : 'text-slate-400 group-hover:text-green-500'"></i>
                                        <div class="flex-1">
                                            <p class="font-medium">Sakit</p>
                                            <p class="text-[10px] opacity-70">Dengan surat dokter</p>
                                        </div>
                                        <i data-lucide="check" class="w-4 h-4" x-show="selected === 'Sakit'"></i>
                                    </button>

                                    <button type="button"
                                            @click="selectType('Dinas'); open = false"
                                            class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group"
                                            :class="selected === 'Dinas' ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300'">
                                        <i data-lucide="briefcase" class="w-4 h-4" :class="selected === 'Dinas' ? 'text-purple-600' : 'text-slate-400 group-hover:text-purple-500'"></i>
                                        <div class="flex-1">
                                            <p class="font-medium">Dinas Luar</p>
                                            <p class="text-[10px] opacity-70">Tugas kantor</p>
                                        </div>
                                        <i data-lucide="check" class="w-4 h-4" x-show="selected === 'Dinas'"></i>
                                    </button>

                                    <button type="button"
                                            @click="selectType('Cuti'); open = false"
                                            class="w-full px-3 py-2.5 rounded-lg text-left text-sm transition-all flex items-center gap-3 group"
                                            :class="selected === 'Cuti' ? 'bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300' : 'hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300'">
                                        <i data-lucide="coffee" class="w-4 h-4" :class="selected === 'Cuti' ? 'text-orange-600' : 'text-slate-400 group-hover:text-orange-500'"></i>
                                        <div class="flex-1">
                                            <p class="font-medium">Cuti</p>
                                            <p class="text-[10px] opacity-70">Cuti tahunan</p>
                                        </div>
                                        <i data-lucide="check" class="w-4 h-4" x-show="selected === 'Cuti'"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Hidden Input for Form Submission -->
                            <input type="hidden" name="type" :value="selected" required>
                        </div>

                        @error('type')
                            <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                Tanggal Mulai <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <i data-lucide="calendar" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                <input type="date" 
                                       name="start_date" 
                                       value="{{ old('start_date', now()->format('Y-m-d')) }}" 
                                       required 
                                       x-model="startDate"
                                       @change="validateDates"
                                       class="w-full pl-11 pr-4 py-3.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                            </div>
                            @error('start_date')
                                <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                Tanggal Selesai <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <i data-lucide="calendar" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                <input type="date" 
                                       name="end_date" 
                                       value="{{ old('end_date', now()->format('Y-m-d')) }}" 
                                       required 
                                       x-model="endDate"
                                       @change="validateDates"
                                       :min="startDate"
                                       class="w-full pl-11 pr-4 py-3.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                            </div>
                            @error('end_date')
                                <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                                </p>
                            @enderror
                            <!-- Duration Preview -->
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400" x-show="startDate && endDate">
                                <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                                <span x-text="getDurationText()"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Alasan & Lampiran -->
            <div class="card p-6 animate-slide-up" style="animation-delay: 0.25s">
                <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                        <i data-lucide="file-text" class="w-5 h-5 text-white dark:text-navy-900"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Detail Pengajuan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Alasan dan dokumen pendukung</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <!-- Reason -->
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                            Alasan Pengajuan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative group">
                            <i data-lucide="align-left" class="absolute left-4 top-4 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                            <textarea name="reason" 
                                      rows="3" 
                                      required 
                                      maxlength="1000"
                                      x-model="reason"
                                      class="w-full pl-11 pr-4 py-3.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all resize-none hover:border-navy-300 dark:hover:border-gold-600"
                                      placeholder="Jelaskan alasan pengajuan Anda secara detail...">{{ old('reason') }}</textarea>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-xs text-slate-500 dark:text-slate-400">Gunakan bahasa yang formal dan jelas</p>
                            <p class="text-xs" :class="reason.length > 900 ? 'text-orange-500' : 'text-slate-400'" x-text="reason.length + '/1000'"></p>
                        </div>
                        @error('reason')
                            <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                            Lampiran Dokumen
                            <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>

                        <div class="relative">
                            <input type="file" 
                                   name="attachment" 
                                   id="attachment"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="hidden"
                                   @change="handleFileUpload($event)">

                            <label for="attachment" 
                                   class="flex flex-col items-center justify-center w-full p-4 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl cursor-pointer hover:border-navy-400 dark:hover:border-gold-500 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-all duration-200 group"
                                   :class="fileName ? 'border-green-400 bg-green-50 dark:bg-green-900/10' : ''">

                                <template x-if="!fileName">
                                    <div class="flex flex-col items-center text-center">
                                        <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                            <i data-lucide="upload-cloud" class="w-6 h-6 text-slate-400 group-hover:text-navy-600 dark:group-hover:text-gold-400 transition-colors"></i>
                                        </div>
                                        <p class="text-xs font-medium text-navy-800 dark:text-white">Upload Surat</p>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400">PDF, JPG, PNG • Max 2MB</p>
                                    </div>
                                </template>

                                <template x-if="fileName">
                                    <div class="flex items-center gap-2 w-full">
                                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i data-lucide="file-check" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-navy-800 dark:text-white truncate" x-text="fileName"></p>
                                            <p class="text-[10px] text-green-600 dark:text-green-400">File terpilih</p>
                                        </div>
                                        <button type="button" 
                                                @click.stop="clearFile"
                                                class="p-1.5 text-slate-400 hover:text-red-500 transition-colors">
                                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </template>
                            </label>
                        </div>

                        @error('attachment')
                            <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Quick Tips Card -->
        <div class="mb-6">
            <div class="card p-5 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 animate-slide-up" style="animation-delay: 0.3s">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i data-lucide="lightbulb" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-blue-800 dark:text-blue-300 mb-1">Tip Pengajuan</h4>
                        <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                            Jelaskan alasan dengan jelas dan lampirkan dokumen pendukung untuk mempercepat persetujuan. Pastikan semua data yang diisi sudah benar sebelum mengirim.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 4: Confirmation & Action Buttons (Separate from Card) -->
        <div class="animate-slide-up" style="animation-delay: 0.35s">

            <!-- Confirmation Checkbox (Standalone) -->
            <div class="mb-4">
                <label class="flex items-start gap-3 p-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors shadow-sm">
                    <input type="checkbox" 
                           name="confirm" 
                           value="1" 
                           required
                           class="w-5 h-5 rounded border-slate-300 text-navy-600 focus:ring-navy-500 mt-0.5">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Saya menyatakan data yang diisi benar</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Dengan mengajukan ini, saya menyetujui kebijakan perusahaan terkait cuti dan izin</p>
                    </div>
                </label>
            </div>

            <!-- Action Buttons (Separate Bar) -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('leaves.index') }}" 
                   class="px-6 py-3.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-bold transition-all hover:-translate-y-0.5 active:translate-y-0 shadow-sm">
                    Batal
                </a>
                <button type="submit"
                        class="px-8 py-3.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:shadow-navy-800/40 dark:hover:shadow-gold-400/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex items-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    Kirim Pengajuan
                </button>
            </div>
        </div>
    </div>

    <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data" class="hidden">
        @csrf
    </form>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('leaveForm', () => ({
                startDate: '',
                endDate: '',
                reason: '',
                fileName: '',
                selectedType: '',

                getIcon(type) {
                    const icons = {
                        'Izin': 'file-check',
                        'Sakit': 'pill',
                        'Dinas': 'briefcase',
                        'Cuti': 'coffee'
                    };
                    return icons[type] || 'list';
                },

                getLabel(type) {
                    const labels = {
                        'Izin': 'Izin - Keperluan pribadi',
                        'Sakit': 'Sakit - Dengan surat dokter',
                        'Dinas': 'Dinas Luar - Tugas kantor',
                        'Cuti': 'Cuti - Cuti tahunan'
                    };
                    return labels[type] || '';
                },

                selectType(type) {
                    this.selectedType = type;
                    // Update hidden input
                    const input = document.querySelector('input[name="type"]');
                    if (input) input.value = type;
                },

                validateDates() {
                    if (this.startDate && this.endDate && new Date(this.endDate) < new Date(this.startDate)) {
                        alert('Tanggal selesai tidak boleh sebelum tanggal mulai');
                        this.endDate = this.startDate;
                    }
                },

                getDurationText() {
                    if (!this.startDate || !this.endDate) return '';
                    const start = new Date(this.startDate);
                    const end = new Date(this.endDate);
                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    return diffDays === 1 ? '1 hari' : `${diffDays} hari`;
                },

                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        if (file.size > 2 * 1024 * 1024) {
                            alert('Ukuran file maksimal 2MB');
                            event.target.value = '';
                            this.fileName = '';
                            return;
                        }
                        this.fileName = file.name;
                    }
                },

                clearFile() {
                    this.fileName = '';
                    document.getElementById('attachment').value = '';
                }
            }));
        });

        // Drag & drop support
        document.addEventListener('DOMContentLoaded', () => {
            const dropZone = document.querySelector('label[for="attachment"]');
            const fileInput = document.getElementById('attachment');

            if (dropZone && fileInput) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                    });
                });

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.classList.add('border-navy-400', 'bg-slate-100', 'dark:bg-slate-700/50');
                    });
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.classList.remove('border-navy-400', 'bg-slate-100', 'dark:bg-slate-700/50');
                    });
                });

                dropZone.addEventListener('drop', (e) => {
                    const files = e.dataTransfer.files;
                    if (files.length) {
                        fileInput.files = files;
                        const event = new Event('change');
                        fileInput.dispatchEvent(event);
                    }
                });
            }

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

        input[type="file"]::-webkit-file-upload-button {
            display: none;
        }

        input, textarea, select, button {
            transition: all 0.2s ease-in-out;
        }

        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
    </style>
@endsection