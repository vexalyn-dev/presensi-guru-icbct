@extends('layouts.app')

@section('page-title', 'Edit Guru')

@section('content')
    <div class="fade-in" x-data="editPageState({{ $teacher->is_active ? 'true' : 'false' }})">

        <!-- Toast Notification -->
        @if (session('success'))
            <div id="toast-success" class="fixed top-6 right-6 z-50 animate-slide-in-right">
                <div class="flex items-center gap-3 px-5 py-4 bg-green-500 text-white rounded-xl shadow-2xl">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div id="toast-error" class="fixed top-6 right-6 z-50 animate-slide-in-right">
                <div class="flex items-center gap-3 px-5 py-4 bg-red-500 text-white rounded-xl shadow-2xl">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif
        <!-- Page Header with Modern Back Button -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <!-- Modern Back Button -->
                <a href="{{ route('teachers.show', $teacher) }}"
                    class="group flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600">
                    <i data-lucide="arrow-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
                    <span>Kembali</span>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Edit Guru</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Perbarui data guru</p>
                </div>
            </div>
        </div>

        <form action="{{ route('teachers.update', $teacher) }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pb-6">

                <div class="group card p-5 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 h-[88px] flex items-center">
                    <div class="flex items-center gap-4 w-full">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-400/30 group-hover:scale-110 transition-transform flex-shrink-0">
                            <i data-lucide="user" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="truncate">
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">ID Guru</p>
                            <p class="text-lg font-bold text-navy-800 dark:text-white truncate">{{ $teacher->formatted_id }}</p>
                        </div>
                    </div>
                </div>

                <div class="group card p-5 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-500 rounded-xl flex items-center justify-center shadow-lg shadow-green-400/30 group-hover:scale-110 transition-transform">
                            <i data-lucide="calendar" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Bergabung</p>
                            <p class="text-lg font-bold text-navy-800 dark:text-white">
                                {{ $teacher->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>


                <div class="group card p-5 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-500"
                            :class="isActive ? 'bg-gradient-to-br from-green-400 to-green-500 shadow-green-400/30' : 'bg-gradient-to-br from-slate-400 to-slate-500 shadow-slate-400/30'">
                            <i data-lucide="activity" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium tracking-tight">Status Guru</p>
                            <p class="text-lg font-bold transition-colors duration-500"
                                :class="isActive ? 'text-green-600' : 'text-slate-500 dark:text-slate-400'"
                                x-text="isActive ? 'Aktif' : 'Nonaktif'"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Main Content (2/3) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Personal Info Card -->
                    <div class="card lg:mt-6">
                        <div
                            class="p-6 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-blue-50 to-white dark:from-slate-800/50 dark:to-slate-800/30">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-400/30">
                                    <i data-lucide="user-check" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-navy-800 dark:text-white">Profil & Akademik</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Data personal dan pengaturan
                                        akademik guru</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                                <!-- Photo Upload -->
                                <div class="md:col-span-2 pb-2 border-b border-slate-100 dark:border-slate-800 mb-2">
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-3">Foto
                                        Profile</label>
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <img id="photo-preview-main" src="{{ $teacher->photo_url }}"
                                                class="w-20 h-20 rounded-full object-cover border-4 border-slate-200 dark:border-slate-700 shadow-lg transition-transform hover:scale-105 duration-300">
                                            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-blue-500 border-2 border-white dark:border-slate-800 rounded-full flex items-center justify-center shadow-sm">
                                                <i data-lucide="camera" class="w-3 h-3 text-white"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="relative">
                                                <input type="file" name="photo" accept="image/*" id="photo-upload"
                                                    class="hidden" onchange="previewImage(this)">
                                                <label for="photo-upload"
                                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all cursor-pointer shadow-sm active:scale-95">
                                                    <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                                                    Ubah Foto Profil
                                                </label>
                                            </div>
                                            <p class="text-[10px] text-slate-400 mt-2 font-medium italic">Disarankan ukuran 1:1, Maksimal 2MB</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nama -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Nama
                                        Lengkap</label>
                                    <div class="relative">
                                        <i data-lucide="user"
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                        <input type="text" name="name" value="{{ old('name', $teacher->name) }}" required
                                            class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('name') border-red-500 @enderror">
                                    </div>
                                    @error('name')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i
                                    data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>@enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Email</label>
                                    <div class="relative">
                                        <i data-lucide="mail"
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                        <input type="email" name="email" value="{{ old('email', $teacher->email) }}"
                                            required
                                            class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror">
                                    </div>
                                    @error('email')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i
                                    data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>@enderror
                                </div>

                                <!-- No. Telepon -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">No.
                                        Telepon</label>
                                    <div class="relative">
                                        <i data-lucide="phone"
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                        <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}"
                                            class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    </div>
                                </div>

                                <!-- Password -->
                                <div>
                                    <label
                                        class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Password</label>
                                    <div class="relative">
                                        <i data-lucide="lock"
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                        <input type="password" name="password" id="password"
                                            class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('password') border-red-500 @enderror">
                                        <button type="button" onclick="togglePassword('password')"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">Kosongkan jika tidak ingin mengubah</p>
                                    @error('password')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i
                                    data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>@enderror
                                </div>

                                <!-- Alamat -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Alamat</label>
                                    <div class="relative">
                                        <i data-lucide="map-pin" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                        <input type="text" name="address" value="{{ old('address', $teacher->address) }}"
                                            class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all dark:text-white"
                                            placeholder="Masukkan alamat lengkap...">
                                    </div>
                                </div>

                                <!-- Jadwal Kerja -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <!-- Jam Masuk -->
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                            Jam Masuk <span class="text-slate-400 font-normal">(Default 07:30)</span>
                                        </label>
                                        <div class="relative group">
                                            <i data-lucide="sunrise" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                            <input type="time" name="start_time" 
                                                   value="{{ old('start_time', $teacher->start_time ?? '07:30') }}" 
                                                   class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        </div>
                                    </div>

                                    <!-- Jam Pulang -->
                                    <div>
                                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                                            Jam Pulang <span class="text-slate-400 font-normal">(Default 16:00)</span>
                                        </label>
                                        <div class="relative group">
                                            <i data-lucide="sunset" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                            <input type="time" name="end_time" 
                                                   value="{{ old('end_time', $teacher->end_time ?? '16:00') }}" 
                                                   class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        </div>
                                    </div>
                                </div>

                                <!-- Mata Pelajaran -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Mata Pelajaran</label>
                                    <div x-data="subjectDropdown({{ Js::from($subjects) }}, {{ Js::from($teacherSubject) }})" class="relative">
                                        {{-- Hidden input for single subject --}}
                                        <input type="hidden" name="subject" :value="selected.length > 0 ? selected[0] : ''">

                                        {{-- The Standard-looking Trigger --}}
                                        <div class="relative">
                                            <i data-lucide="book-open" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 z-10 pointer-events-none"></i>
                                            <div @click="open = !open" 
                                                 class="w-full pl-11 pr-10 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm cursor-pointer flex flex-wrap items-center gap-2 hover:border-blue-400 transition-all shadow-sm">
                                                
                                                <template x-if="selected.length === 0">
                                                    <span class="text-slate-400">Pilih mata pelajaran...</span>
                                                </template>

                                                <template x-if="selected.length > 0">
                                                    <div class="flex items-center gap-2 group/item animate-scale-in">
                                                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-xl text-xs font-bold border border-blue-200 dark:border-blue-800"
                                                              x-text="getSubject(selected[0]).name"></span>
                                                        <button type="button" @click.stop="clear()" class="text-slate-400 hover:text-red-500 transition-colors">
                                                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                                                        </button>
                                                    </div>
                                                </template>

                                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                                         class="text-slate-400 transition-all duration-300 transform" :class="open ? 'rotate-180' : ''">
                                                        <path d="m6 9 6 6 6-6"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Floating Overlay Panel (Standard dropdown approach) --}}
                                        <div x-show="open" 
                                             @click.outside="open = false"
                                             class="absolute left-0 z-[100] w-full min-w-[300px] max-w-sm p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none origin-bottom"
                                             style="bottom: 100%; top: auto; margin-bottom: 0.5rem;"
                                             x-cloak>
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
                                                            <i data-lucide="check" class="w-4 h-4 text-blue-600 animate-scale-in"></i>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar (1/3) -->
                <div class="lg:col-span-1 flex flex-col gap-6">

                    <!-- Status Card -->
                    <div class="card h-fit">
                        <div
                            class="p-5 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-white dark:from-slate-800/50 dark:to-slate-800/30">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-slate-400 to-slate-500 rounded-xl flex items-center justify-center shadow-lg shadow-slate-400/30">
                                    <i data-lucide="toggle-left" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-navy-800 dark:text-white">Status Akun</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Kontrol akses guru</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5">
                            <label
                                class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" x-model="isActive"
                                    class="w-5 h-5 rounded border-slate-300 text-gold-500 focus:ring-gold-500">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Aktif</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Guru dapat login dan absen</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card p-6">
                        <button type="submit"
                            class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-blue-600/30 hover:shadow-xl hover:shadow-blue-600/40 hover:-translate-y-0.5 mb-3 flex items-center justify-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('teachers.show', $teacher) }}"
                            class="block w-full py-3.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-all text-center">
                            Batal
                        </a>
                    </div>

                    <!-- Danger Zone -->
                    <div class="card h-fit border-2 border-red-100 dark:border-red-900/30 overflow-hidden">
                        <div
                            class="p-5 bg-gradient-to-r from-red-50 to-white dark:from-red-900/20 dark:to-slate-800/30 border-b border-red-200 dark:border-red-800">
                            <div class="flex items-center gap-3 text-red-800 dark:text-red-300">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-red-400 to-red-500 rounded-xl flex items-center justify-center shadow-lg shadow-red-400/30">
                                    <i data-lucide="triangle-alert" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold">Zone Bahaya</h3>
                                    <p class="text-xs opacity-70">Tindakan permanen</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5">
                            <p class="text-xs text-red-600 dark:text-red-400 mb-4 font-medium italic">Tindakan menghapus
                                data tidak dapat dikembalikan.</p>
                            <button type="button"
                                @click="openDeleteModal('{{ route('teachers.destroy', $teacher) }}', '{{ $teacher->name }}')"
                                class="w-full py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-red-500/30 hover:shadow-xl hover:shadow-red-500/40 flex items-center justify-center gap-2">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                Hapus Guru
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- Premium Delete Modal implementation --}}
        <div x-show="deleteModalOpen" x-cloak @keydown.escape.window="closeDeleteModal()"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0"
            :class="!deleteModalOpen && 'pointer-events-none'" role="dialog" aria-modal="true">

            <!-- Background overlay with transition -->
            <div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="closeDeleteModal()"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>

            <!-- Modal panel with transition -->
            <div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative bg-white dark:bg-navy-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-700 z-[110]">

                <div class="bg-white dark:bg-navy-800 px-6 pt-10 pb-6 sm:px-10 sm:pb-8">
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="flex-shrink-0 flex items-center justify-center h-20 w-20 rounded-3xl bg-red-50 dark:bg-red-900/20 mb-6 group animate-pulse">
                            <i data-lucide="alert-triangle" class="h-10 w-10 text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-2xl leading-6 font-bold text-navy-800 dark:text-white mb-4">
                            Konfirmasi Hapus
                        </h3>
                        <div class="mt-2">
                            <p class="text-base text-slate-500 dark:text-slate-400 leading-relaxed">
                                Apakah Anda yakin ingin menghapus <span class="font-bold text-red-600 dark:text-red-400"
                                    x-text="deleteLabel"></span>? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-slate-50 dark:bg-navy-900/50 px-6 py-8 sm:px-10 flex flex-col sm:flex-row-reverse justify-center gap-3">
                    <form id="delete-teacher-form-modal" action="{{ route('teachers.destroy', $teacher) }}" method="POST"
                        class="w-full sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg shadow-red-500/20 px-8 py-3 bg-red-600 text-base font-bold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition-all hover:scale-105 active:scale-95">
                            Ya, Hapus Data
                        </button>
                    </form>
                    <button type="button" @click="closeDeleteModal()"
                        class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-600 shadow-sm px-8 py-3 bg-white dark:bg-navy-800 text-base font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-navy-500 sm:text-sm transition-all">
                        Batalkan
                    </button>
                </div>
            </div>
        </div>
    </div>

        <script>
            /** 
             * Define components globally for maximum reliability 
             * This ensures they are available even if Alpine initializes before the script runs.
             */
            window.subjectDropdown = (options, initialSelected) => {
                return {
                    open: false,
                    options: Array.isArray(options) ? options : Object.values(options),
                    selected: initialSelected ? [String(initialSelected)] : [],

                    init() {
                        // Ensure unique options
                        const uniqueNames = new Set();
                        this.options = this.options.filter(opt => {
                            const nameKey = String(opt.name).trim().toUpperCase();
                            if (uniqueNames.has(nameKey)) return false;
                            uniqueNames.add(nameKey);
                            return true;
                        });

                        this.$watch('open', value => {
                            if (value) {
                                this.$nextTick(() => {
                                    if (window.lucide) window.lucide.createIcons();
                                });
                            }
                        });
                        return; // Ensure u is not a function crash is avoided
                    },

                    select(name) {
                        this.selected = [String(name)];
                        this.open = false;
                        this.$nextTick(() => {
                            if (window.lucide) lucide.createIcons();
                        });
                    },

                    clear() {
                        this.selected = [];
                        this.$nextTick(() => {
                            if (window.lucide) lucide.createIcons();
                        });
                    },

                    isSelected(name) {
                        return this.selected.includes(String(name));
                    },

                    getSubject(name) {
                        const strName = String(name);
                        return this.options.find(opt => String(opt.name) === strName) || { name: strName || 'Unknown' };
                    }
                };
            };

            window.editPageState = (initialActive) => {
                return {
                    isActive: initialActive,
                    deleteModalOpen: false,
                    deleteLabel: '',
                    openDeleteModal(url, label) {
                        this.deleteLabel = label;
                        this.deleteModalOpen = true;
                        document.body.style.overflow = 'hidden';
                    },
                    closeDeleteModal() {
                        this.deleteModalOpen = false;
                        document.body.style.overflow = 'auto';
                    },
                    init() {
                        return; // Ensure no cleanup function is returned
                    }
                };
            };

            // Initialize Icons when Lucide is ready
            document.addEventListener('DOMContentLoaded', () => {
                if (window.lucide) lucide.createIcons();
            });

            // Preview Image
            function previewImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const preview = document.getElementById('photo-preview');
                        if (preview) {
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                        }
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Toggle Password Visibility
            function togglePassword(id) {
                const input = document.getElementById(id);
                if (!input) return;
                
                if (input.type === 'password') {
                    input.type = 'text';
                } else {
                    input.type = 'password';
                }
                if (window.lucide) lucide.createIcons();
            }

            // Auto-hide Toast
            setTimeout(() => {
                const toastSuccess = document.getElementById('toast-success');
                const toastError = document.getElementById('toast-error');

                if (toastSuccess) {
                    toastSuccess.style.opacity = '0';
                    toastSuccess.style.transform = 'translateX(100%)';
                    setTimeout(() => toastSuccess.remove(), 300);
                }

                if (toastError) {
                    toastError.style.opacity = '0';
                    toastError.style.transform = 'translateX(100%)';
                    setTimeout(() => toastError.remove(), 300);
                }
            }, 3000);
        </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Hide Native Password Eye Icon in Edge and WebKit */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }
        input[type="password"]::-webkit-contacts-auto-fill-button,
        input[type="password"]::-webkit-credentials-auto-fill-button {
            visibility: hidden;
            pointer-events: none;
            position: absolute;
            right: 0;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-slide-in-right {
            animation: slideInRight 0.4s ease-out forwards;
        }

        .animate-scale-in {
            animation: scaleIn 0.2s ease-out forwards;
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

        /* Custom Scrollbar for dropdown */
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }

        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .dark .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #334155;
        }
    </style>
@endsection