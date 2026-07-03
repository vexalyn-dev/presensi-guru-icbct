@extends('layouts.app')

@section('page-title', 'Tambah Guru')

@section('content')
    <div class="fade-in" x-data="addTeacher()">

        <!-- Page Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('teachers.index') }}"
                    class="group flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600">
                    <i data-lucide="arrow-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
                    <span>Kembali</span>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Tambah Guru</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Daftarkan guru baru ke sistem</p>
                </div>
            </div>
        </div>

        <!-- Form Wrapper (FIX: Added form tag with id) -->
        <form action="{{ route('teachers.store') }}" method="POST" enctype="multipart/form-data" id="teacher-form">
            @csrf

            <!-- Card 1: Profile (Full Width - Top) -->
            <div class="mb-6">
                <div class="card p-6">
                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <!-- Photo Upload -->
                        <div class="flex-shrink-0">
                            <div class="relative group">
                                <div id="photoPreview"
                                    class="w-32 h-32 rounded-2xl bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center border-4 border-white dark:border-slate-800 shadow-xl cursor-pointer overflow-hidden transform transition-all duration-300 group-hover:scale-105"
                                    onclick="document.getElementById('photo').click()">
                                    <img id="previewImg" src="{{ asset('images/default-teacher.png') }}"
                                        class="w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 flex flex-col items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <i data-lucide="camera" class="w-7 h-7 text-white mb-1"></i>
                                        <span class="text-[10px] text-white font-semibold uppercase">Ubah Foto</span>
                                    </div>
                                </div>
                                <input type="file" name="photo" id="photo" accept="image/*" class="hidden"
                                    onchange="previewPhoto(this)">
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-2 text-center">Max 2MB • JPG,
                                    PNG</p>
                            </div>
                        </div>

                        <!-- Status & Info -->
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                            <!-- Status -->
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                                <div class="flex items-center gap-3 mb-2">
                                    <div
                                        class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                        <i data-lucide="check-circle"
                                            class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                            Status</p>
                                        <p class="text-sm font-bold text-green-600 dark:text-green-400">Aktif Otomatis</p>
                                    </div>
                                </div>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 pl-11">Guru dapat langsung login
                                    setelah dibuat</p>
                            </div>

                            <!-- Password Info -->
                            <div
                                class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center gap-3 mb-2">
                                    <div
                                        class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-blue-600 dark:text-blue-400 uppercase tracking-wide">
                                            Password</p>
                                        <p class="text-sm font-bold text-blue-700 dark:text-blue-300">Keamanan</p>
                                    </div>
                                </div>
                                <p class="text-[10px] text-blue-600 dark:text-blue-300 pl-11 leading-relaxed">Minimal 8
                                    karakter dengan kombinasi huruf dan angka</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4 Cards Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Card 2: Data Diri (Nama & Email) -->
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                            <i data-lucide="user" class="w-5 h-5 text-white dark:text-navy-900"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-navy-800 dark:text-white">Data Diri</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Informasi identitas guru</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <i data-lucide="user"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600"
                                    placeholder="Contoh: Budi Santoso, S.Pd">
                            </div>
                            @error('name')
                                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <i data-lucide="mail"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600"
                                    placeholder="guru@sekolah.sch.id">
                            </div>
                            @error('email')
                                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Card 3: Kontak (Telepon & Tanggal) -->
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                            <i data-lucide="contact" class="w-5 h-5 text-white dark:text-navy-900"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-navy-800 dark:text-white">Kontak</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Informasi kontak dan bergabung</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                No. Telepon
                            </label>
                            <div class="relative group">
                                <i data-lucide="phone"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                    class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600"
                                    placeholder="08123456789">
                            </div>
                        </div>

                        <!-- Join Date -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                Tanggal Bergabung
                            </label>
                            <div class="relative group">
                                <i data-lucide="calendar"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                <input type="date" name="join_date" value="{{ old('join_date') }}"
                                    class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3.5: Jadwal Kerja -->
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                            <i data-lucide="clock" class="w-5 h-5 text-white dark:text-navy-900"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-navy-800 dark:text-white">Jadwal Kerja</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Jam masuk dan pulang kerja</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Jam Masuk -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                Jam Masuk <span class="text-slate-400 font-normal">(Default 07:30)</span>
                            </label>
                            <div class="relative group">
                                <i data-lucide="sunrise" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                <input type="time" name="start_time" 
                                       value="{{ old('start_time', '07:30') }}" 
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                            </div>
                        </div>

                        <!-- Jam Pulang -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                Jam Pulang <span class="text-slate-400 font-normal">(Default 16:00)</span>
                            </label>
                            <div class="relative group">
                                <i data-lucide="sunset" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                <input type="time" name="end_time" 
                                       value="{{ old('end_time', '16:00') }}" 
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Alamat & Biodata -->
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                            <i data-lucide="map-pin" class="w-5 h-5 text-white dark:text-navy-900"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-navy-800 dark:text-white">Alamat & Biodata</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Lokasi dan deskripsi guru</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <!-- Address -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                Alamat
                            </label>
                            <div class="relative group">
                                <i data-lucide="map-pin"
                                    class="absolute left-4 top-3.5 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                <textarea name="address" rows="3"
                                    class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all resize-none hover:border-navy-300 dark:hover:border-gold-600"
                                    placeholder="Alamat lengkap guru">{{ old('address') }}</textarea>
                            </div>
                            @error('address')
                                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Bio -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                Biodata <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <div class="relative group">
                                <i data-lucide="align-left"
                                    class="absolute left-4 top-3.5 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                <textarea name="bio" rows="3"
                                    class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all resize-none hover:border-navy-300 dark:hover:border-gold-600"
                                    placeholder="Deskripsi singkat guru">{{ old('bio') }}</textarea>
                            </div>
                            @error('bio')
                                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Card 5: Mata Pelajaran & Keamanan -->
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                            <i data-lucide="lock" class="w-5 h-5 text-white dark:text-navy-900"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-navy-800 dark:text-white">Mata Pelajaran & Keamanan</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Assign mapel dan atur password</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <!-- Subject Dropdown (Original Design) -->
                        <div>
                            <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                Mata Pelajaran <span class="text-xs font-normal text-slate-500">(Pilih satu mata
                                    pelajaran)</span>
                            </label>
                            <div x-data="subjectDropdown({{ Js::from($subjects) }}, {{ Js::from(old('subject')) }})"
                                class="relative">
                                <input type="hidden" name="subject" :value="selected.length > 0 ? selected[0] : ''">

                                <div class="relative">
                                    <i data-lucide="book-open"
                                        class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 z-10 pointer-events-none"></i>
                                    <div @click="open = !open"
                                        class="w-full pl-11 pr-10 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm cursor-pointer flex flex-wrap items-center gap-2 hover:border-blue-400 transition-all shadow-sm">

                                        <template x-if="selected.length === 0">
                                            <span class="text-slate-400">Pilih mata pelajaran...</span>
                                        </template>

                                        <template x-if="selected.length > 0">
                                            <div class="flex items-center gap-2 group/item animate-scale-in">
                                                <span
                                                    class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-xl text-xs font-bold border border-blue-200 dark:border-blue-800"
                                                    x-text="getSubject(selected[0]).name"></span>
                                                <button type="button" @click.stop="clear()"
                                                    class="text-slate-400 hover:text-red-500 transition-colors">
                                                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        </template>

                                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="text-slate-400 transition-all duration-300 transform"
                                                :class="open ? 'rotate-180' : ''">
                                                <path d="m6 9 6 6 6-6" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Floating Overlay Panel -->
                                <div x-show="open" @click.outside="open = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                                    class="absolute left-0 z-[100] w-full min-w-[300px] max-w-sm p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none origin-bottom"
                                    style="bottom: 100%; top: auto; margin-bottom: 0.5rem;" x-cloak>
                                    <div class="max-h-60 overflow-y-auto space-y-1 pr-1 scrollbar-thin">
                                        <template x-for="subject in options" :key="subject.id">
                                            <div @click="select(subject.name)"
                                                class="flex items-center justify-between px-4 py-2.5 rounded-xl cursor-pointer transition-all border border-transparent"
                                                :class="isSelected(subject.name) ? 'bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300'">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-all bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700"
                                                        :class="isSelected(subject.name) ? 'text-blue-600 border-blue-300' : 'text-slate-400'">
                                                        <i data-lucide="book" class="w-3.5 h-3.5"></i>
                                                    </div>
                                                    <span class="text-sm tracking-tight" x-text="subject.name"></span>
                                                </div>
                                                <template x-if="isSelected(subject.name)">
                                                    <i data-lucide="check"
                                                        class="w-4 h-4 text-blue-600 animate-scale-in"></i>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Fields -->
                        <div class="pt-5 border-t border-slate-200 dark:border-slate-700">
                            <div class="space-y-5">
                                <!-- Password -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                        Password <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative group">
                                        <i data-lucide="lock"
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                        <input type="password" name="password" id="password" required x-model="password"
                                            :type="showPassword ? 'text' : 'password'" autocomplete="new-password"
                                            class="w-full pl-11 pr-11 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600"
                                            placeholder="••••••••">

                                        <!-- Custom Eye Toggle -->
                                        <button type="button" @click="showPassword = !showPassword"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-all duration-200 focus:outline-none z-10">
                                            <svg x-show="!showPassword"
                                                class="w-5 h-5 text-slate-400 hover:text-navy-600 dark:hover:text-gold-400 transition-colors"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                            <svg x-show="showPassword"
                                                class="w-5 h-5 text-navy-600 dark:text-gold-400 transition-colors"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path
                                                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                                </path>
                                                <line x1="1" y1="1" x2="23" y2="23"></line>
                                            </svg>
                                        </button>
                                    </div>
                                    <!-- Password Strength Indicator (VISUAL ONLY - Doesn't block submit) -->
                                    <div class="mt-2 flex items-center gap-2" x-show="password.length > 0">
                                        <div
                                            class="flex-1 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-300"
                                                :class="passwordStrength.color"
                                                :style="`width: ${passwordStrength.percent}%`"></div>
                                        </div>
                                        <span class="text-[10px] font-semibold" :class="passwordStrength.text"
                                            x-text="passwordStrength.label"></span>
                                    </div>
                                    <p class="mt-1 text-[10px] text-slate-400 dark:text-slate-500">
                                        <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                                        Password bisa disimpan meskipun kekuatan rendah
                                    </p>
                                    @error('password')
                                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                            <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                        Konfirmasi Password <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative group">
                                        <i data-lucide="lock"
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                                        <input type="password" name="password_confirmation" id="passwordConfirmation"
                                            required x-model="passwordConfirm"
                                            :type="showConfirmPassword ? 'text' : 'password'" autocomplete="new-password"
                                            class="w-full pl-11 pr-11 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600"
                                            placeholder="••••••••">

                                        <!-- Custom Eye Toggle -->
                                        <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-all duration-200 focus:outline-none z-10">
                                            <svg x-show="!showConfirmPassword"
                                                class="w-5 h-5 text-slate-400 hover:text-navy-600 dark:hover:text-gold-400 transition-colors"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                            <svg x-show="showConfirmPassword"
                                                class="w-5 h-5 text-navy-600 dark:text-gold-400 transition-colors"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path
                                                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                                </path>
                                                <line x1="1" y1="1" x2="23" y2="23"></line>
                                            </svg>
                                        </button>
                                    </div>
                                    <!-- Match Indicator (Visual only) -->
                                    <p class="mt-2 text-xs" x-show="passwordConfirm.length > 0"
                                        :class="passwordConfirm === password ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                                        <i :data-lucide="passwordConfirm === password ? 'check-circle' : 'x-circle'"
                                            class="w-3 h-3 inline mr-1"></i>
                                        <span
                                            x-text="passwordConfirm === password ? 'Password cocok' : 'Password tidak cocok'"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons (Separate Bar - Bottom) -->
            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('teachers.index') }}"
                    class="px-8 py-3.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-bold transition-all hover:-translate-y-0.5 active:translate-y-0 shadow-sm">
                    Batal
                </a>
                <button type="submit"
                    class="px-10 py-3.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white rounded-xl text-sm font-bold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:shadow-navy-800/40 dark:hover:shadow-gold-400/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Simpan Guru
                </button>
            </div>
        </form>
    </div>

    <script>
        // Alpine.js Components
        document.addEventListener('alpine:init', () => {
            // Subject Dropdown
            Alpine.data('subjectDropdown', (options, initialSelected) => ({
                open: false,
                options: Array.isArray(options) ? options : Object.values(options),
                selected: initialSelected ? [String(initialSelected)] : [],

                init() {
                    const uniqueNames = new Set();
                    this.options = this.options.filter(opt => {
                        const nameKey = String(opt.name).trim().toUpperCase();
                        if (uniqueNames.has(nameKey)) return false;
                        uniqueNames.add(nameKey);
                        return true;
                    });
                },

                select(name) {
                    this.selected = [String(name)];
                    this.open = false;
                    this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                },

                clear() {
                    this.selected = [];
                    this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                },

                isSelected(name) {
                    return this.selected.includes(String(name));
                },

                getSubject(name) {
                    const strName = String(name);
                    return this.options.find(opt => String(opt.name) === strName) || { name: strName || 'Unknown' };
                }
            }));

            // Add Teacher
            Alpine.data('addTeacher', () => ({
                password: '',
                passwordConfirm: '',
                showPassword: false,
                showConfirmPassword: false,

                get passwordStrength() {
                    const pwd = this.password;
                    let score = 0;
                    if (pwd.length >= 8) score++;
                    if (pwd.length >= 12) score++;
                    if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) score++;
                    if (/[0-9]/.test(pwd)) score++;
                    if (/[^a-zA-Z0-9]/.test(pwd)) score++;

                    const colors = ['bg-slate-300', 'bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'];
                    const texts = ['text-slate-400', 'text-red-600', 'text-orange-600', 'text-yellow-600', 'text-blue-600', 'text-green-600'];
                    const labels = ['-', 'Lemah', 'Cukup', 'Bagus', 'Kuat', 'Sangat Kuat'];

                    return {
                        color: colors[Math.min(score, 5)],
                        text: texts[Math.min(score, 5)],
                        label: labels[Math.min(score, 5)],
                        percent: Math.min(score, 5) * 20
                    };
                }
            }));
        });

        // Preview Photo
        function previewPhoto(input) {
            const preview = document.getElementById('photoPreview');
            const img = document.getElementById('previewImg');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.style.opacity = '0.5';
                    setTimeout(() => {
                        img.src = e.target.result;
                        preview.style.opacity = '1';
                    }, 150);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Init Icons
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>

    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-scale-in {
            animation: scaleIn 0.2s ease-out forwards;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Custom Scrollbar */
        .scrollbar-thin::-webkit-scrollbar {
            width: 4px;
        }

        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .dark .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #475569;
        }

        /* Hide Browser Password Eye Icon */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear,
        input[type="password"]::-webkit-credentials-auto-fill-button {
            display: none !important;
        }

        input[type="password"]::-webkit-inner-spin-button,
        input[type="password"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Smooth transitions */
        input,
        textarea,
        select,
        button {
            transition: all 0.2s ease-in-out;
        }
    </style>
@endsection