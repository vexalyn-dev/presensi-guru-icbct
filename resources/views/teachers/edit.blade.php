@extends('layouts.app')

@section('page-title', 'Edit Guru')

@section('content')
    <div class="fade-in" x-data="editPageState({{ $teacher->is_active ? 'true' : 'false' }})">

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
                            <i data-lucide="hash" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="truncate">
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">ID Guru</p>
                            @if($teacher->teacher && $teacher->teacher->employee_code)
                                <p class="text-lg font-bold font-mono text-navy-800 dark:text-white truncate">{{ $teacher->teacher->employee_code }}</p>
                            @else
                                <p class="text-lg font-bold text-slate-400 dark:text-slate-500 truncate">Belum diatur</p>
                            @endif
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
                    <div class="card">
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
                                        {{-- Avatar bulat: klik untuk upload --}}
                                        <div class="relative flex-shrink-0">
                                            <input type="file" name="photo" accept="image/*" id="photo-upload"
                                                class="hidden" onchange="previewTeacherPhoto(this)">
                                            <label for="photo-upload" class="block cursor-pointer group/avatar" title="Klik untuk ganti foto">
                                                <img id="photo-preview-main" src="{{ $teacher->photo_url }}"
                                                    class="w-20 h-20 rounded-full object-cover border-4 border-slate-200 dark:border-slate-700 shadow-lg transition-all duration-300 group-hover/avatar:brightness-75">
                                                {{-- Overlay upload icon --}}
                                                <div class="absolute inset-0 flex items-center justify-center rounded-full opacity-0 group-hover/avatar:opacity-100 transition-opacity duration-200 pointer-events-none">
                                                    <div class="flex flex-col items-center gap-0.5">
                                                        <svg width="22" height="22" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 12V4m0 0L8 8m4-4l4 4"/>
                                                        </svg>
                                                        <span style="font-size:9px;color:#fff;font-weight:700;letter-spacing:0.02em;">UPLOAD</span>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="flex-1">
                                            <div class="relative">
                                                {{-- Tombol Hapus Foto Profil --}}
                                                <button type="button" onclick="deleteTeacherPhoto()"
                                                    id="btn-delete-photo"
                                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-700 border border-red-200 dark:border-red-700 hover:bg-red-50 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl text-xs font-bold transition-all cursor-pointer shadow-sm active:scale-95">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    Hapus Foto Profil
                                                </button>
                                            </div>
                                            <p id="photo-filename" class="text-[10px] text-slate-400 mt-2 font-medium italic">Klik foto untuk ganti • Disarankan 1:1 • Maks. 2MB</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nama -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Nama
                                        Lengkap <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <i data-lucide="user"
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                        <input type="text" name="name" value="{{ old('name', $teacher->name) }}" required
                                            class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('name') ? 'border-red-500 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    </div>
                                    @error('name')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i
                                    data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Kode
                                        Guru <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <i data-lucide="hash"
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                        <input type="text" name="teacher_code" value="{{ old('teacher_code', $teacher->teacher_code) }}" required
                                            class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('teacher_code') ? 'border-red-500 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    </div>
                                    @error('teacher_code')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i
                                    data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>@enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Email <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <i data-lucide="mail"
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                        <input type="email" name="email" value="{{ old('email', $teacher->email) }}"
                                            required
                                            class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('email') ? 'border-red-500 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
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
                                        <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}" placeholder="08xxxxxxxxxx"
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
                                            class="w-full pl-11 pr-11 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('password') ? 'border-red-500 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        <button type="button" onclick="togglePassword('password')"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
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

                                <!-- Mata Pelajaran Multi-Select (Pure JS) -->
                                @php
                                    $subjectOptions = $subjects->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values()->toArray();
                                @endphp
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Mata Pelajaran</label>
                                    <div class="relative" id="mapel-wrap">
                                        {{-- Trigger --}}
                                        <div id="mapel-trigger"
                                             class="relative cursor-pointer select-none"
                                             onclick="mapelToggle()">
                                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            <div id="mapel-display"
                                                 class="w-full pl-10 pr-10 py-3 min-h-[46px] flex items-center bg-slate-50 border-2 border-slate-200 rounded-xl text-sm transition-all hover:border-blue-400">
                                                <span id="mapel-placeholder" class="text-slate-400">Pilih mata pelajaran...</span>
                                                <span id="mapel-selected-text" class="text-slate-800 font-medium truncate hidden"></span>
                                            </div>
                                            <svg id="mapel-chevron" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>

                                        {{-- Dropdown Panel --}}
                                        <div id="mapel-dropdown"
                                             class="hidden absolute left-0 right-0 bottom-full mb-1.5 z-[9999] bg-white border border-slate-200 rounded-xl shadow-2xl overflow-hidden"
                                             style="min-width:100%; transform-origin: bottom center; transition: opacity 0.18s ease, transform 0.18s cubic-bezier(0.34,1.56,0.64,1); opacity:0; transform: scaleY(0.92) translateY(6px);"
                                             >{{-- Search --}}
                                            <div class="p-2.5 border-b border-slate-100 bg-slate-50">
                                                <div class="relative">
                                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                    </svg>
                                                    <input id="mapel-search" type="text"
                                                           placeholder="Cari mata pelajaran..."
                                                           oninput="mapelSearch(this.value)"
                                                           onclick="event.stopPropagation()"
                                                           class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                </div>
                                            </div>
                                            {{-- List --}}
                                            <div id="mapel-list" class="max-h-56 overflow-y-auto p-1.5"></div>
                                            {{-- Footer --}}
                                            <div id="mapel-footer" class="hidden px-3 py-2 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                                                <span class="text-xs text-slate-500"><span id="mapel-count" class="font-bold text-slate-700">0</span> dipilih</span>
                                                <button type="button" onclick="mapelClearAll()" class="text-xs font-semibold text-red-500 hover:text-red-700">Hapus semua</button>
                                            </div>
                                        </div>

                                        {{-- Hidden inputs container --}}
                                        <div id="mapel-hidden-inputs"></div>
                                    </div>
                                    @error('subjects')<p class="mt-2 text-xs text-red-500 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>@enderror
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
                            <p class="text-xs text-red-600 dark:text-red-400 mb-4 font-medium">
                                ⚠️ Perhatian: Tindakan menghapus data tidak dapat dibatalkan.
                            </p>
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

        {{-- ── Modal Hapus Foto Profil ── --}}
        <div id="modal-hapus-foto"
             style="display:none;position:fixed;inset:0;z-index:9998;background:rgba(10,15,30,0.7);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:16px;"
             onclick="if(event.target===this) closeHapusFotoModal()">
            <div style="background:#fff;border-radius:20px;width:100%;max-width:400px;overflow:hidden;box-shadow:0 32px 64px rgba(15,23,42,0.28);animation:hapusFotoIn 0.25s cubic-bezier(0.22,1,0.36,1);">
                {{-- Header --}}
                <div style="background:linear-gradient(135deg,#FEF2F2,#FFF5F5);border-bottom:1px solid #FECACA;padding:20px 24px;display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;background:#FEE2E2;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="20" height="20" fill="none" stroke="#DC2626" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:0.95rem;font-weight:700;color:#0F172A;margin:0;">Hapus Foto Profil</p>
                        <p style="font-size:0.75rem;color:#94A3B8;margin:0;">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
                {{-- Body --}}
                <div style="padding:20px 24px;">
                    <p style="font-size:0.88rem;color:#475569;line-height:1.6;margin:0;">
                        Foto profil akan dihapus dan kembali ke foto <strong style="color:#0F172A;">default</strong>. Lanjutkan?
                    </p>
                </div>
                {{-- Footer --}}
                <div style="padding:0 24px 20px;display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" onclick="closeHapusFotoModal()"
                        style="padding:10px 20px;background:#F1F5F9;border:1.5px solid #E2E8F0;border-radius:10px;font-size:0.85rem;font-weight:600;color:#475569;cursor:pointer;transition:all 0.15s;"
                        onmouseover="this.style.background='#E2E8F0'" onmouseout="this.style.background='#F1F5F9'">
                        Batal
                    </button>
                    <button type="button" onclick="confirmHapusFoto()"
                        style="padding:10px 20px;background:#DC2626;border:none;border-radius:10px;font-size:0.85rem;font-weight:700;color:#fff;cursor:pointer;display:flex;align-items:center;gap:6px;transition:all 0.15s;"
                        onmouseover="this.style.background='#B91C1C'" onmouseout="this.style.background='#DC2626'">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Ya, Hapus Foto
                    </button>
                </div>
            </div>
        </div>
        <style>
            @keyframes hapusFotoIn {
                from { opacity:0; transform:scale(0.94) translateY(12px); }
                to   { opacity:1; transform:scale(1) translateY(0); }
            }
        </style>
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

        {{-- Blade data injection — pakai type=application/json agar VS Code tidak parse sebagai JS --}}
        <script type="application/json" id="mapel-json-options">{!! json_encode($subjects->map(fn($s)=>['id'=>$s->id,'name'=>$s->name])->values()) !!}</script>
        <script type="application/json" id="mapel-json-selected">{!! json_encode($teacherSubjectIds) !!}</script>

        <script>
            /** 
             * Define components globally for maximum reliability 
             * This ensures they are available even if Alpine initializes before the script runs.
             */
            window.subjectDropdown = (options, initialSelected) => {
                return {
                    open: false,
                    options: [],
                    selected: initialSelected ? String(initialSelected).trim() : '',

                    init() {
                        // Parse and ensure unique options by ID using Map
                        const rawOptions = Array.isArray(options) ? options : Object.values(options);
                        const uniqueMap = new Map();
                        
                        rawOptions.forEach(opt => {
                            if (opt && opt.name && opt.id) {
                                const key = String(opt.id); // Use ID as unique key
                                if (!uniqueMap.has(key)) {
                                    uniqueMap.set(key, {
                                        id: String(opt.id),
                                        name: String(opt.name).trim()
                                    });
                                }
                            }
                        });
                        
                        this.options = Array.from(uniqueMap.values());
                        this.selected = Array.isArray(initialSelected)
                            ? initialSelected.map(String)
                            : (initialSelected ? [String(initialSelected).trim()] : []);

                        // Watch dropdown open/close
                        this.$watch('open', value => {
                            if (value) {
                                // Force icon re-render
                                this.$nextTick(() => {
                                    if (window.lucide) {
                                        window.lucide.createIcons();
                                    }
                                });
                            }
                        });
                    },

                    select(name) {
                        const strName = String(name).trim();
                        
                        // Single select: replace the value
                        this.selected = strName;
                        this.open = false;
                        
                        // Re-render icons after selection
                        this.$nextTick(() => {
                            if (window.lucide) {
                                lucide.createIcons();
                            }
                        });
                    },

                    clear() {
                        this.selected = '';
                        this.$nextTick(() => {
                            if (window.lucide) {
                                lucide.createIcons();
                            }
                        });
                    },

                    isSelected(name) {
                        const strName = String(name).trim();
                        return this.selected === strName;
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
                        // Reposition mapel dropdown on scroll/resize
                        const reposition = () => {
                            document.querySelectorAll('[x-data]').forEach(el => {
                                const comp = el._x_dataStack?.[0];
                                if (comp && comp.open && comp.triggerRect !== undefined) {
                                    const ref = el.querySelector('[x-ref="trigger"]') || el;
                                    const rect = ref.getBoundingClientRect();
                                    const dd = el.querySelector('[style*="position:fixed"]');
                                    if (dd) {
                                        dd.style.top   = (rect.bottom + window.scrollY + 6) + 'px';
                                        dd.style.left  = (rect.left + window.scrollX) + 'px';
                                        dd.style.width = rect.width + 'px';
                                    }
                                }
                            });
                        };
                        window.addEventListener('scroll', reposition, true);
                        window.addEventListener('resize', reposition);
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
                    const file = input.files[0];
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        // Update preview foto
                        const preview = document.getElementById('photo-preview-main') 
                                     || document.getElementById('photo-preview');
                        if (preview) {
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                        }
                        // Tampilkan nama file terpilih
                        const filenameEl = document.getElementById('photo-filename');
                        if (filenameEl) {
                            filenameEl.textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB) — klik Simpan untuk menyimpan';
                            filenameEl.style.color = '#16a34a';
                        }
                    }
                    reader.readAsDataURL(file);
                }
            }

            // ─── Mapel Multi-Select (Pure JS) ──────────────────
            /* global MAPEL_OPTIONS, MAPEL_SELECTED */
            (function() {
                var MAPEL_OPTIONS  = JSON.parse(document.getElementById('mapel-json-options').textContent  || '[]');
                var MAPEL_SELECTED = JSON.parse(document.getElementById('mapel-json-selected').textContent || '[]');
                var isOpen = false;

                // ── Icon palette untuk setiap mata pelajaran ──
                var MAPEL_ICONS = [
                    // Bahasa & Sastra
                    { keys: ['bahasa indonesia','sastra','menulis','membaca'],
                      bg:'#EDE9FE', color:'#7C3AED',
                      svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>' },
                    // Bahasa Asing
                    { keys: ['bahasa inggris','bahasa jepang','bahasa arab','bahasa jerman','bahasa perancis','bahasa mandarin','bahasa sunda','bahasa asing'],
                      bg:'#DBEAFE', color:'#2563EB',
                      svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>' },
                    // Matematika
                    { keys: ['matematika','kalkulus','statistika','aljabar','geometri'],
                      bg:'#FEF3C7', color:'#D97706',
                      svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="5" x2="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>' },
                    // IPA / Sains
                    { keys: ['fisika','kimia','biologi','ipa','sains','astronomi'],
                      bg:'#D1FAE5', color:'#059669',
                      svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v11m0 0h11m-11 0a2 2 0 0 1-2 2H3m20-2v4a2 2 0 0 1-2 2H9"/></svg>' },
                    // IPS / Sosial
                    { keys: ['ips','geografi','sejarah','ekonomi','sosiologi','antropologi','ilmu sosial'],
                      bg:'#FEE2E2', color:'#DC2626',
                      svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>' },
                    // TIK / Komputer
                    { keys: ['tik','komputer','pemrograman','teknologi','informatika','rekayasa','jaringan','sistem','database','coding','software','hardware'],
                      bg:'#DBEAFE', color:'#1D4ED8',
                      svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#1D4ED8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>' },
                    // Seni & Budaya
                    { keys: ['seni','budaya','musik','tari','teater','gambar','lukis','desain','kriya','prakarya'],
                      bg:'#FCE7F3', color:'#DB2777',
                      svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#DB2777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8.56 2.75c4.37 6.03 6.02 9.42 8.03 17.72m2.54-15.38c-3.72 4.35-8.94 5.66-16.88 5.85m19.5 1.9c-3.5-.93-6.63-.82-8.94 0-2.58.92-5.01 2.86-7.44 6.32"/></svg>' },
                    // Olahraga
                    { keys: ['olahraga','penjaskes','penjas','jasmani','kesehatan','kebugaran','renang','atletik'],
                      bg:'#DCFCE7', color:'#16A34A',
                      svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M4.93 4.93l4.24 4.24m5.66 5.66l4.24 4.24M14.12 9.88l4.24-4.24m-9.9 9.9l-4.24 4.24"/></svg>' },
                    // Agama
                    { keys: ['agama','pendidikan agama','pai','paq','quran','aqidah','fiqh','akhlak','ibadah'],
                      bg:'#FEF9C3', color:'#CA8A04',
                      svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#CA8A04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>' },
                    // PKN / Kewarganegaraan
                    { keys: ['pkn','kewarganegaraan','pancasila','ppkn','civic'],
                      bg:'#FEE2E2', color:'#B91C1C',
                      svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#B91C1C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>' },
                    // BK / Konseling
                    { keys: ['bimbingan','konseling','bk','psikologi','bp'],
                      bg:'#EDE9FE', color:'#6D28D9',
                      svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#6D28D9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>' },
                    // Teknik / Kejuruan
                    { keys: ['teknik','otomotif','mesin','elektro','listrik','las','bangunan','konstruksi','sipil','mekatronika','manufaktur'],
                      bg:'#FEF3C7', color:'#B45309',
                      svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#B45309" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>' },
                    // Default fallback
                    { keys: [],
                      bg:'#F1F5F9', color:'#64748B',
                      svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>' },
                ];

                // Palet warna fallback — diacak berdasarkan id agar setiap mapel konsisten
                var FALLBACK_PALETTES = [
                    { bg:'#DBEAFE', svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>' },
                    { bg:'#D1FAE5', svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>' },
                    { bg:'#FEF3C7', svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>' },
                    { bg:'#FCE7F3', svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#DB2777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>' },
                    { bg:'#EDE9FE', svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>' },
                    { bg:'#FEE2E2', svg:'<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>' },
                ];

                function mapelGetIcon(id, name) {
                    var lower = (name || '').toLowerCase();
                    for (var i = 0; i < MAPEL_ICONS.length - 1; i++) {
                        var entry = MAPEL_ICONS[i];
                        for (var k = 0; k < entry.keys.length; k++) {
                            if (lower.indexOf(entry.keys[k]) >= 0) return entry;
                        }
                    }
                    // Fallback: pilih palet berdasarkan id agar konsisten
                    return FALLBACK_PALETTES[id % FALLBACK_PALETTES.length];
                }

                function mapelRender(filter) {
                    var list = document.getElementById('mapel-list');
                    if (!list) return;
                    var q = (filter || '').toLowerCase();
                    var filtered = q
                        ? MAPEL_OPTIONS.filter(function(s){ return s.name.toLowerCase().indexOf(q) >= 0; })
                        : MAPEL_OPTIONS;

                    if (filtered.length === 0) {
                        list.innerHTML = '<div style="padding:24px 0;text-align:center;color:#94a3b8;font-size:13px;">Tidak ada mapel ditemukan</div>';
                        return;
                    }

                    list.innerHTML = filtered.map(function(s) {
                        var checked = MAPEL_SELECTED.indexOf(s.id) >= 0;
                        var iconData = mapelGetIcon(s.id, s.name);
                        return '<label onclick="mapelToggleItem(' + s.id + ',event)" style="display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;cursor:pointer;transition:background 0.12s;background:' + (checked ? '#EFF6FF' : 'transparent') + ';" onmouseover="if(!this.dataset.checked)this.style.background=\'#F8FAFC\'" onmouseout="this.style.background=\'' + (checked ? '#EFF6FF' : 'transparent') + '\'"  data-id="' + s.id + '" data-checked="' + (checked ? '1' : '') + '">'
                            + '<div style="flex-shrink:0;width:18px;height:18px;border-radius:5px;border:2px solid ' + (checked ? '#3B82F6' : '#CBD5E1') + ';background:' + (checked ? '#3B82F6' : '#fff') + ';display:flex;align-items:center;justify-content:center;transition:all 0.15s;">'
                            + (checked ? '<svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>' : '')
                            + '</div>'
                            + '<div style="flex-shrink:0;width:32px;height:32px;border-radius:9px;background:' + iconData.bg + ';display:flex;align-items:center;justify-content:center;">' + iconData.svg + '</div>'
                            + '<span style="font-size:13px;font-weight:500;color:#374151;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + s.name + '</span>'
                            + '</label>';
                    }).join('');
                }

                function mapelUpdateDisplay() {
                    var placeholder = document.getElementById('mapel-placeholder');
                    var selectedText = document.getElementById('mapel-selected-text');
                    var display = document.getElementById('mapel-display');
                    var footer = document.getElementById('mapel-footer');
                    var countEl = document.getElementById('mapel-count');
                    var hiddenContainer = document.getElementById('mapel-hidden-inputs');

                    var names = MAPEL_SELECTED.map(function(id) {
                        var s = MAPEL_OPTIONS.find(function(o){ return o.id == id; });
                        return s ? s.name : null;
                    }).filter(Boolean);

                    if (names.length > 0) {
                        placeholder.classList.add('hidden');
                        selectedText.classList.remove('hidden');
                        selectedText.textContent = names.join(', ');
                        display.style.borderColor = '#93C5FD';
                        display.style.background = '#EFF6FF';
                        if (footer) { footer.classList.remove('hidden'); footer.style.display='flex'; }
                        if (countEl) countEl.textContent = names.length;
                    } else {
                        placeholder.classList.remove('hidden');
                        selectedText.classList.add('hidden');
                        display.style.borderColor = '';
                        display.style.background = '';
                        if (footer) { footer.classList.add('hidden'); footer.style.display='none'; }
                    }

                    // Sync hidden inputs
                    if (hiddenContainer) {
                        hiddenContainer.innerHTML = MAPEL_SELECTED.map(function(id) {
                            return '<input type="hidden" name="subjects[]" value="' + id + '">';
                        }).join('');
                    }
                }

                window.mapelToggle = function() {
                    var dd = document.getElementById('mapel-dropdown');
                    var chevron = document.getElementById('mapel-chevron');
                    var display = document.getElementById('mapel-display');
                    isOpen = !isOpen;
                    if (isOpen) {
                        // Show with animation
                        dd.classList.remove('hidden');
                        // Force reflow so transition fires
                        dd.getBoundingClientRect();
                        dd.style.opacity = '1';
                        dd.style.transform = 'scaleY(1) translateY(0)';
                        chevron.style.transform = 'translateY(-50%) rotate(180deg)';
                        display.style.borderColor = '#3B82F6';
                        display.style.boxShadow = '0 0 0 3px rgba(59,130,246,0.15)';
                        display.style.background = '#fff';
                        document.getElementById('mapel-search').value = '';
                        mapelRender('');
                        setTimeout(function(){ document.getElementById('mapel-search').focus(); }, 50);
                    } else {
                        // Hide with animation
                        dd.style.opacity = '0';
                        dd.style.transform = 'scaleY(0.92) translateY(6px)';
                        setTimeout(function(){ dd.classList.add('hidden'); }, 160);
                        chevron.style.transform = 'translateY(-50%) rotate(0deg)';
                        display.style.boxShadow = '';
                        if (MAPEL_SELECTED.length > 0) {
                            display.style.borderColor = '#93C5FD';
                            display.style.background = '#EFF6FF';
                        } else {
                            display.style.borderColor = '';
                            display.style.background = '';
                        }
                    }
                };

                window.mapelToggleItem = function(id, e) {
                    e.stopPropagation();
                    var idx = MAPEL_SELECTED.indexOf(id);
                    if (idx >= 0) {
                        MAPEL_SELECTED.splice(idx, 1);
                    } else {
                        MAPEL_SELECTED.push(id);
                    }
                    mapelRender(document.getElementById('mapel-search').value);
                    mapelUpdateDisplay();
                };

                window.mapelSearch = function(val) {
                    mapelRender(val);
                };

                window.mapelClearAll = function() {
                    MAPEL_SELECTED.length = 0;
                    mapelRender(document.getElementById('mapel-search') ? document.getElementById('mapel-search').value : '');
                    mapelUpdateDisplay();
                };

                // Close on outside click
                document.addEventListener('click', function(e) {
                    var wrap = document.getElementById('mapel-wrap');
                    if (wrap && !wrap.contains(e.target) && isOpen) {
                        window.mapelToggle();
                    }
                });

                // ESC key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && isOpen) window.mapelToggle();
                });

                // Init on DOM ready
                document.addEventListener('DOMContentLoaded', function() {
                    mapelUpdateDisplay();
                });
                // Also init immediately in case DOMContentLoaded already fired
                if (document.readyState !== 'loading') {
                    mapelUpdateDisplay();
                }
            })();

            // ─── Photo Preview (klik foto bulat) ───────────────
            function previewTeacherPhoto(input) {
                if (!input.files || !input.files[0]) return;
                var file = input.files[0];
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran foto melebihi 2MB. Silakan pilih foto yang lebih kecil.');
                    input.value = '';
                    return;
                }
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.getElementById('photo-preview-main');
                    if (preview) preview.src = e.target.result;
                    var info = document.getElementById('photo-filename');
                    if (info) { info.textContent = '✓ ' + file.name + ' — siap disimpan'; info.style.color = '#16a34a'; }
                    // Hapus flag DELETE kalau sebelumnya mau hapus
                    var delInput = document.getElementById('delete_photo_flag');
                    if (delInput) delInput.remove();
                };
                reader.readAsDataURL(file);
            }

            // ─── Hapus Foto Profil ──────────────────────────────
            function deleteTeacherPhoto() {
                var modal = document.getElementById('modal-hapus-foto');
                if (modal) modal.style.display = 'flex';
            }

            function closeHapusFotoModal() {
                var modal = document.getElementById('modal-hapus-foto');
                if (modal) modal.style.display = 'none';
            }

            function confirmHapusFoto() {
                closeHapusFotoModal();

                // Tambahkan hidden input flag delete
                var existing = document.getElementById('delete_photo_flag');
                if (!existing) {
                    var flag = document.createElement('input');
                    flag.type  = 'hidden';
                    flag.name  = 'delete_photo';
                    flag.id    = 'delete_photo_flag';
                    flag.value = '1';
                    document.querySelector('form').appendChild(flag);
                }

                // Reset preview ke avatar default
                var preview = document.getElementById('photo-preview-main');
                if (preview) preview.src = '{{ asset("images/default-teacher.png") }}';

                // Reset file input
                var fileInput = document.getElementById('photo-upload');
                if (fileInput) fileInput.value = '';

                // Update info text
                var info = document.getElementById('photo-filename');
                if (info) { info.textContent = 'Foto akan dihapus saat kamu klik Simpan Perubahan'; info.style.color = '#ef4444'; }
            }

            // Toggle Password Visibility
            function togglePassword(id) {
                const input = document.getElementById(id);
                if (!input) return;
                const btn = input.parentElement.querySelector('button[onclick*="togglePassword"]');
                const icon = btn ? btn.querySelector('i[data-lucide]') : null;

                if (input.type === 'password') {
                    input.type = 'text';
                    if (icon) { icon.setAttribute('data-lucide', 'eye-off'); }
                } else {
                    input.type = 'password';
                    if (icon) { icon.setAttribute('data-lucide', 'eye'); }
                }
                if (icon && window.lucide) lucide.createIcons();
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

        /* ── Custom Checkbox (login page style) ── */
        .cb-nichek {
            position: absolute !important;
            opacity: 0 !important;
            pointer-events: none !important;
            width: 1px !important;
            height: 1px !important;
        }
        .cb-box {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 22px !important;
            height: 22px !important;
            min-width: 22px !important;
            border: 2px solid #cbd5e1 !important;
            border-radius: 7px !important;
            background: #fff !important;
            cursor: pointer !important;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s, transform 0.15s !important;
            position: relative !important;
            box-shadow: 0 1px 3px rgba(15,23,42,0.06) !important;
            flex-shrink: 0 !important;
        }
        .dark .cb-box {
            border-color: #475569 !important;
            background: #1e293b !important;
        }
        .cb-box:hover {
            border-color: #0f172a !important;
            box-shadow: 0 0 0 4px rgba(15,23,42,0.08) !important;
        }
        .dark .cb-box:hover { border-color: #94a3b8 !important; }
        .cb-box .cb-check {
            opacity: 0 !important;
            transform: scale(0) rotate(-10deg) !important;
            transition: opacity 0.18s, transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        }
        .cb-box.checked {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%) !important;
            border-color: #0f172a !important;
            box-shadow: 0 4px 14px rgba(15,23,42,0.28) !important;
            animation: cbBounce 0.38s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        }
        .dark .cb-box.checked {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%) !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 4px 14px rgba(59,130,246,0.3) !important;
        }
        .cb-box.checked .cb-check {
            opacity: 1 !important;
            transform: scale(1) rotate(0deg) !important;
        }
        @keyframes cbBounce {
            0%   { transform: scale(0.8); }
            55%  { transform: scale(1.18); }
            100% { transform: scale(1); }
        }
    </style>
@endsection