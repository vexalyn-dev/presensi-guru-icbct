@extends('layouts.app')

@section('page-title', 'Profil Saya')

@section('content')
<div class="fade-in space-y-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Profil Saya</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Kelola informasi akun dan preferensi Anda</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-sm font-medium">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            Akun Aktif
        </span>
    </div>

    <!-- Success Toast Notification -->
    @if(session('status') === 'profile-updated')
    <div class="card p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 animate-slide-up">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <i data-lucide="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
            </div>
            <p class="text-sm font-medium text-green-800 dark:text-green-300">Profil berhasil diperbarui!</p>
        </div>
    </div>
    @endif

    @if(session('status') === 'password-updated')
    <div class="card p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 animate-slide-up">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <i data-lucide="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
            </div>
            <p class="text-sm font-medium text-green-800 dark:text-green-300">Password berhasil diperbarui!</p>
        </div>
    </div>
    @endif

    <!-- Row 1: Profile Card (Full Width) -->
    <div class="card overflow-hidden">
        {{-- Gradient header dengan titik 3 di pojok kanan atas --}}
        <div class="h-24 bg-gradient-to-br from-navy-800 via-navy-900 to-slate-900 dark:from-gold-400 dark:via-gold-500 dark:to-amber-500 relative">
            <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 25% 50%,white 1px,transparent 1px),radial-gradient(circle at 75% 20%,white 1px,transparent 1px);background-size:20px 20px;"></div>

            {{-- 3-dot menu pojok kanan atas --}}
            <div class="absolute top-3 right-3" id="photo-menu-wrap" style="z-index:20;">
                <button type="button" onclick="togglePhotoMenu(event)"
                        style="width:32px;height:32px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px);transition:background 0.15s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'"
                        title="Opsi foto">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="white">
                        <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/>
                    </svg>
                </button>
                <div id="photo-3dot-menu"
                     style="display:none;position:absolute;top:calc(100% + 6px);right:0;
                            background:#fff;border:1px solid #E2E8F0;border-radius:12px;
                            box-shadow:0 8px 24px rgba(15,23,42,0.14);
                            min-width:165px;overflow:hidden;z-index:100;">
                    <button type="button" onclick="openPhotoViewer(document.getElementById('photo-preview').src);togglePhotoMenu(event);"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 14px;border:none;background:none;cursor:pointer;font-size:0.83rem;font-weight:500;color:#1E293B;text-align:left;"
                            onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='none'">
                        <svg width="15" height="15" fill="none" stroke="#64748B" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Lihat Foto
                    </button>
                    <button type="button" onclick="document.getElementById('photo-upload').click();togglePhotoMenu(event);"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 14px;border:none;background:none;cursor:pointer;font-size:0.83rem;font-weight:500;color:#1E293B;text-align:left;"
                            onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='none'">
                        <svg width="15" height="15" fill="none" stroke="#64748B" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Ganti / Crop Foto
                    </button>
                    <div style="height:1px;background:#F1F5F9;margin:2px 0;"></div>
                    <button type="button" onclick="deletePhoto();togglePhotoMenu(event);"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 14px;border:none;background:none;cursor:pointer;font-size:0.83rem;font-weight:500;color:#EF4444;text-align:left;"
                            onmouseover="this.style.background='#FFF5F5'" onmouseout="this.style.background='none'">
                        <svg width="15" height="15" fill="none" stroke="#EF4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus Foto
                    </button>
                </div>
            </div>
        </div>

        {{-- Hidden inputs dipindah ke dalam form --}}
        <input type="file" id="photo-upload" name="photo" class="hidden" accept="image/*" onchange="handleAdminPhotoChange(this)">

        <div class="p-6 flex flex-col md:flex-row items-center gap-6 -mt-10">
            {{-- Avatar: klik = viewer --}}
            <div class="flex-shrink-0 relative group cursor-pointer" onclick="openPhotoViewer(this.querySelector('img').src)">
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 p-1 shadow-xl">
                    <img src="{{ $user->photo_url }}" id="photo-preview"
                         class="w-full h-full rounded-xl object-cover" alt="Profile">
                </div>
                <div class="absolute inset-0 rounded-2xl bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                </div>
            </div>
            <div class="flex-1 text-center md:text-left">
                <h2 class="text-2xl font-bold text-navy-800 dark:text-white mb-1">{{ $user->name }}</h2>
                <p class="text-slate-500 dark:text-slate-400 mb-2">{{ $user->email }}</p>
                <span class="inline-flex items-center px-3 py-1 bg-navy-100 dark:bg-navy-900/30 text-navy-700 dark:text-navy-300 rounded-full text-sm font-semibold">
                    {{ $user->role_name }}
                </span>
            </div>

            <!-- Contact Info -->
            @if($user->phone)
            <div class="flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i data-lucide="phone" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Telepon</p>
                    <p class="text-sm font-semibold text-navy-800 dark:text-white">{{ $user->phone }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    <!-- Row 2: Informasi Pribadi (Full Width) -->
    <div class="card p-6">
        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
            <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                <i data-lucide="user" class="w-5 h-5 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-navy-800 dark:text-white">Informasi Pribadi</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Perbarui profil dan biodata Anda</p>
            </div>
        </div>

        <form id="profile-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            {{-- cropped_photo_data harus di dalam form agar ikut submit --}}
            <input type="hidden" name="cropped_photo_data" id="cropped_photo_data">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                        <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
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
                        <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                        Nomor Telepon
                    </label>
                    <div class="relative group">
                        <i data-lucide="phone" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600" 
                            placeholder="08xxxxxxxx">
                    </div>
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                        Alamat Domisili
                    </label>
                    <div class="relative group">
                        <i data-lucide="map-pin" class="absolute left-4 top-3.5 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                        <textarea name="address" rows="2"
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all resize-none hover:border-navy-300 dark:hover:border-gold-600">{{ old('address', $user->address) }}</textarea>
                    </div>
                </div>

                <!-- Bio -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                        Bio Singkat <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <div class="relative group">
                        <i data-lucide="align-left" class="absolute left-4 top-3.5 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                        <textarea name="bio" rows="3"
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all resize-none hover:border-navy-300 dark:hover:border-gold-600" 
                            placeholder="Ceritakan sedikit tentang Anda...">{{ old('bio', $user->bio) }}</textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Row 3: Keamanan Akun (Full Width) -->
    <div class="card p-6" x-data="passwordForm()">
        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-200 dark:border-slate-700">
            <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                <i data-lucide="lock" class="w-5 h-5 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-navy-800 dark:text-white">Keamanan Akun</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Ubah password secara berkala</p>
            </div>
        </div>

        <form action="{{ route('profile.password') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Current Password -->
                <div>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Password Saat Ini</label>
                    <div class="relative group">
                        <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                        <input :type="currentVisible ? 'text' : 'password'" name="current_password" required
                            class="w-full pl-11 pr-11 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                        <button type="button" @click="currentVisible = !currentVisible" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                            <svg x-show="!currentVisible" class="w-4 h-4 text-slate-400 hover:text-navy-600 dark:hover:text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg x-show="currentVisible" class="w-4 h-4 text-navy-600 dark:text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Password Baru</label>
                    <div class="relative group">
                        <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                        <input :type="newVisible ? 'text' : 'password'" name="password" required x-model="newPassword" @input="checkStrength"
                            class="w-full pl-11 pr-11 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600">
                        <button type="button" @click="newVisible = !newVisible" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                            <svg x-show="!newVisible" class="w-4 h-4 text-slate-400 hover:text-navy-600 dark:hover:text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg x-show="newVisible" class="w-4 h-4 text-navy-600 dark:text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                    <div class="mt-2 flex items-center gap-2" x-show="newPassword.length > 0">
                        <div class="flex-1 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300" :class="strengthClass" :style="`width: ${strengthPercent}%`"></div>
                        </div>
                        <span class="text-[10px] font-semibold" :class="strengthTextColor" x-text="strengthText"></span>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Konfirmasi Password</label>
                    <div class="relative group">
                        <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-navy-600 dark:group-focus-within:text-gold-400 transition-colors"></i>
                        <input :type="confirmVisible ? 'text' : 'password'" name="password_confirmation" required x-model="confirmPassword"
                            class="w-full pl-11 pr-11 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 focus:border-transparent transition-all hover:border-navy-300 dark:hover:border-gold-600"
                            :class="confirmPassword && confirmPassword !== newPassword ? 'border-red-500 ring-red-500' : ''">
                        <button type="button" @click="confirmVisible = !confirmVisible" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                            <svg x-show="!confirmVisible" class="w-4 h-4 text-slate-400 hover:text-navy-600 dark:hover:text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg x-show="confirmVisible" class="w-4 h-4 text-navy-600 dark:text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                    <p class="mt-2 text-xs" x-show="confirmPassword.length > 0"
                       :class="confirmPassword === newPassword ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                        <i :data-lucide="confirmPassword === newPassword ? 'check-circle' : 'x-circle'" class="w-3 h-3 inline mr-1"></i>
                        <span x-text="confirmPassword === newPassword ? 'Password cocok' : 'Password tidak cocok'"></span>
                    </p>
                </div>
            </div>

            <div class="flex justify-end pt-5 border-t border-slate-200 dark:border-slate-700">
                <button type="submit" 
                        class="px-8 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:shadow-navy-800/40 dark:hover:shadow-gold-400/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    Perbarui Keamanan
                </button>
            </div>
        </form>
    </div>

    <!-- Row 4: Action Buttons (Separate from cards) -->
    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
        <!-- Save Profile Button -->
        <button type="button" form="profile-form" onclick="document.getElementById('profile-form').submit();"
            class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:shadow-navy-800/40 dark:hover:shadow-gold-400/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex items-center justify-center gap-2">
            <i data-lucide="save" class="w-4 h-4"></i>
            Simpan Profil
        </button>

        <!-- Delete Account Button -->
        <button type="button" onclick="openDeleteModal()"
                class="w-full sm:w-auto px-6 py-3 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-xl text-sm font-semibold transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
            Hapus Akun Permanen
        </button>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div x-data="{ show: false }" 
     x-show="show"
     @open-delete-modal.window="show = true"
     @close-delete-modal.window="show = false"
     @keydown.escape.window="show = false"
     x-cloak
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    
    <!-- Backdrop -->
    <div x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm" 
         @click="show = false"></div>
    
    <!-- Modal Content -->
    <div x-show="show"
         @click.outside="show = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-700">
        
        <!-- Header -->
        <div class="flex items-start gap-4 mb-6">
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-1">Konfirmasi Hapus Akun</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Masukkan password Anda untuk konfirmasi penghapusan permanen.
                </p>
            </div>
        </div>
        
        <!-- Form -->
        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('delete')

            <div>
                <label for="password" class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Password</label>
                <input type="password" name="password" id="password" 
                       class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                       placeholder="••••••••" required>
                @error('password', 'userDeletion')
                    <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <button type="button" @click="show = false"
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
    // Password Form Alpine Component
    document.addEventListener('alpine:init', () => {
        Alpine.data('passwordForm', () => ({
            newPassword: '',
            confirmPassword: '',
            currentVisible: false,
            newVisible: false,
            confirmVisible: false,
            strength: 0,
            
            get strengthClass() { 
                return ['bg-slate-300', 'bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'][this.strength] || 'bg-slate-300'; 
            },
            get strengthTextColor() { 
                return ['text-slate-400', 'text-red-600', 'text-orange-600', 'text-yellow-600', 'text-blue-600', 'text-green-600'][this.strength] || 'text-slate-400'; 
            },
            get strengthText() { 
                return ['-', 'Lemah', 'Cukup', 'Bagus', 'Kuat', 'Sangat Kuat'][this.strength] || '-'; 
            },
            get strengthPercent() { 
                return this.strength * 20; 
            },
            
            checkStrength() {
                let score = 0;
                const p = this.newPassword;
                if (p.length >= 8) score++;
                if (p.length >= 12) score++;
                if (/[a-z]/.test(p) && /[A-Z]/.test(p)) score++;
                if (/[0-9]/.test(p)) score++;
                if (/[^a-zA-Z0-9]/.test(p)) score++;
                this.strength = Math.min(score, 5);
            }
        }));
    });
    
    // Photo function — buka crop modal
    function handleAdminPhotoChange(input) {
        if (!input.files || !input.files[0]) return;
        var file = input.files[0];
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 5MB.');
            input.value = '';
            return;
        }
        openCropModal(file, input, ['photo-preview']);
    }

    // Alias lama agar tidak error jika masih ada referensi
    function previewImage(input) { handleAdminPhotoChange(input); }
    
    // Open Delete Modal Function
    function openDeleteModal() {
        window.dispatchEvent(new CustomEvent('open-delete-modal', { bubbles: true }));
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
    
    /* Hide browser password icons */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear,
    input[type="password"]::-webkit-credentials-auto-fill-button {
        display: none !important;
    }
    
    /* Smooth transitions */
    input, textarea, button {
        transition: all 0.2s ease-in-out;
    }
</style>

<script>
@php
    $defaultPhotoUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=1E3A5F&color=fff&size=200&bold=true';
@endphp
window.profileDefaultPhoto = '{{ $defaultPhotoUrl }}';
</script>

@include('partials.photo-cropper')

@endsection