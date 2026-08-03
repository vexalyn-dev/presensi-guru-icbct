@extends(activeLayout())
@section('page-title', 'Profil Saya')
@section('content')
<div class="fade-in space-y-6">

    @if(session('success'))
    <div class="card p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0"></i>
        <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- PROFILE CARD --}}
        <div class="lg:col-span-1">
            <div class="card overflow-hidden sticky top-24">
                <div class="h-24 bg-gradient-to-br from-navy-800 via-navy-900 to-slate-900 dark:from-gold-400 dark:via-gold-500 dark:to-amber-500 relative">
                    <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 25% 50%,white 1px,transparent 1px),radial-gradient(circle at 75% 20%,white 1px,transparent 1px);background-size:20px 20px;"></div>
                    {{-- 3-dot menu pojok kanan atas card --}}
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
                            <button type="button" onclick="openPhotoViewer(document.getElementById('profile-avatar').src);togglePhotoMenu(event);"
                                    style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 14px;border:none;background:none;cursor:pointer;font-size:0.83rem;font-weight:500;color:#1E293B;text-align:left;"
                                    onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='none'">
                                <svg width="15" height="15" fill="none" stroke="#64748B" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Lihat Foto
                            </button>
                            <button type="button" onclick="document.getElementById('photo-upload-trigger').click();togglePhotoMenu(event);"
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
                <div class="px-6 pb-6">
                    @php
                        $profilePhoto = auth()->user()->photo
                            ? asset('storage/' . auth()->user()->photo)
                            : ($teacher && $teacher->photo ? asset('storage/' . $teacher->photo) : null);
                    @endphp
                    <div class="flex justify-between items-end -mt-10 mb-4">
                        {{-- Foto: klik = viewer, hover = overlay --}}
                        <div class="relative group cursor-pointer" onclick="openPhotoViewer(this.querySelector('img').src)">
                            <img id="profile-avatar"
                                 src="{{ $profilePhoto ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=1E3A5F&color=fff&size=200&bold=true' }}"
                                 alt="{{ auth()->user()->name }}"
                                 class="w-20 h-20 rounded-2xl object-cover border-4 border-white dark:border-slate-900 shadow-xl transition-all duration-300">
                            <div class="absolute inset-0 rounded-2xl bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                </svg>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400 rounded-full text-[10px] font-bold">
                            <i data-lucide="shield" class="w-3 h-3"></i> Guru
                        </span>
                    </div>
                    <h2 class="text-lg font-extrabold text-navy-800 dark:text-white leading-tight">{{ auth()->user()->name }}</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ auth()->user()->email }}</p>
                    @if($teacher && $teacher->major_specialty)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg text-[10px] font-semibold mt-3">
                        <i data-lucide="book-open" class="w-3 h-3"></i> {{ $teacher->major_specialty }}
                    </span>
                    @endif
                    <p class="text-[10px] text-slate-400 mt-2 flex items-center gap-1">
                        <i data-lucide="more-vertical" class="w-3 h-3"></i> Gunakan menu ⋮ di atas untuk mengelola foto
                    </p>
                    @if($teacher)
                    <div class="mt-5 pt-5 border-t border-slate-100 dark:border-slate-800 space-y-3">
                        @if($teacher->education)
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="graduation-cap" class="w-3.5 h-3.5 text-slate-400"></i>
                            </div>
                            <div><p class="text-[10px] text-slate-400">Pendidikan</p><p class="text-xs font-semibold text-navy-800 dark:text-white">{{ $teacher->education }}</p></div>
                        </div>
                        @endif
                        @if($teacher->join_date)
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                            </div>
                            <div><p class="text-[10px] text-slate-400">Bergabung</p><p class="text-xs font-semibold text-navy-800 dark:text-white">{{ \Carbon\Carbon::parse($teacher->join_date)->format('d M Y') }}</p></div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- FORMS --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Edit Profile --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3 bg-slate-50/50 dark:bg-slate-800/30">
                    <div class="w-9 h-9 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                        <i data-lucide="user" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Edit Profil</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Perbarui informasi pribadi Anda</p>
                    </div>
                </div>
                <form action="{{ route('teacher.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')
                    {{-- Hidden file input --}}
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg" id="photo-upload-trigger"
                           class="hidden" onchange="handlePhotoChange(this)">
                    {{-- Hidden base64 cropped data --}}
                    <input type="hidden" name="cropped_photo_data" id="cropped_photo_data">

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                            <input type="text" name="name" required value="{{ old('name', auth()->user()->name) }}"
                                   class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('name') ? 'border-red-400' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
                        </div>
                        @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Email</label>
                        <div class="relative">
                            <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                            <input type="email" value="{{ auth()->user()->email }}" readonly
                                   class="w-full pl-11 pr-4 py-3 bg-slate-100 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-400 cursor-not-allowed">
                        </div>
                        <p class="mt-1 text-xs text-slate-400 flex items-center gap-1">
                            <i data-lucide="info" class="w-3 h-3"></i>
                            Untuk ganti email, gunakan form <a href="#email-section" class="text-navy-700 dark:text-gold-400 font-semibold underline">Ganti Email</a> di bawah
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nomor Telepon</label>
                            <div class="relative">
                                <i data-lucide="phone" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                <input type="text" name="phone" value="{{ old('phone', $teacher->phone ?? '') }}"
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('phone') ? 'border-red-400' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
                            </div>
                            @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Alamat</label>
                            <div class="relative">
                                <i data-lucide="map-pin" class="absolute left-4 top-3.5 w-4 h-4 text-slate-400"></i>
                                <textarea name="address" rows="1" class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('address') ? 'border-red-400' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all resize-none">{{ old('address', $teacher->address ?? '') }}</textarea>
                            </div>
                            @error('address')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Change Password --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3 bg-slate-50/50 dark:bg-slate-800/30">
                    <div class="w-9 h-9 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                        <i data-lucide="lock" class="w-4 h-4 text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Ganti Password</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Perbarui keamanan akun Anda</p>
                    </div>
                </div>
                <form action="{{ route('teacher.profile.password') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Password Lama</label>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                            <input type="password" name="current_password" required
                                   class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('current_password') ? 'border-red-400' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
                        </div>
                        @error('current_password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Password Baru</label>
                            <div class="relative">
                                <i data-lucide="key" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                <input type="password" name="password" required minlength="8"
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('password') ? 'border-red-400' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
                            </div>
                            @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Konfirmasi Password</label>
                            <div class="relative">
                                <i data-lucide="key" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                <input type="password" name="password_confirmation" required minlength="8"
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
                            <i data-lucide="lock" class="w-4 h-4"></i> Ganti Password
                        </button>
                    </div>
                </form>
            </div>

            {{-- ─── GANTI EMAIL ─── --}}
            <div class="card overflow-hidden" id="email-section">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3 bg-slate-50/50 dark:bg-slate-800/30">
                    <div class="w-9 h-9 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                        <i data-lucide="mail" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white">Ganti Email</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Perbarui alamat email login Anda</p>
                    </div>
                </div>
                <form action="{{ route('teacher.profile.email') }}" method="POST"
                      class="p-6 space-y-5"
                      id="email-change-form">
                    @csrf
                    @method('PUT')

                    {{-- Email sekarang (info) --}}
                    <div class="flex items-center gap-3 p-3.5 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200 dark:border-slate-700">
                        <i data-lucide="mail-check" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                        <div>
                            <p class="text-[10px] text-slate-400 leading-none mb-0.5">Email saat ini</p>
                            <p class="text-sm font-semibold text-navy-800 dark:text-white">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    {{-- Email baru --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Email Baru <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                            <input type="email" name="email" required
                                   value="{{ old('email') }}"
                                   placeholder="email@contoh.com"
                                   autocomplete="off"
                                   class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('email') ? 'border-red-400' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
                        </div>
                        @error('email')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>@enderror
                    </div>

                    {{-- Konfirmasi password --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                            <input type="password" name="email_password" required
                                   placeholder="Masukkan password Anda untuk konfirmasi"
                                   autocomplete="current-password"
                                   class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('email_password') ? 'border-red-400' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
                        </div>
                        @error('email_password')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>@enderror
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit"
                                class="px-6 py-3 bg-navy-800 dark:bg-gold-400 hover:opacity-90 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-95 flex items-center gap-2">
                            <i data-lucide="mail-check" class="w-4 h-4"></i> Simpan Email Baru
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
var originalPhotoSrc = '';
document.addEventListener('DOMContentLoaded', function() {
    var p = document.getElementById('photo-preview');
    if (p) originalPhotoSrc = p.src;
    if (window.lucide) lucide.createIcons();
});

function handlePhotoChange(input) {
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];
    if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran file terlalu besar. Maksimal 5MB.');
        input.value = '';
        return;
    }
    // Buka crop modal
    openCropModal(file, input, ['photo-preview', 'profile-avatar']);
}

function cancelPhoto() {
    var input = document.getElementById('photo-upload-trigger');
    if (input) input.value = '';
    var hidden = document.getElementById('cropped_photo_data');
    if (hidden) hidden.value = '';
    var preview = document.getElementById('photo-preview');
    var avatar  = document.getElementById('profile-avatar');
    if (preview) preview.src = originalPhotoSrc;
    if (avatar)  avatar.src  = originalPhotoSrc;
    var info = document.getElementById('photo-info');
    if (info) { info.textContent = 'JPG, PNG • Maks. 5MB • Disarankan 1:1'; info.style.color = ''; }
    var btn = document.getElementById('photo-cancel-btn');
    if (btn) btn.classList.replace('inline-flex', 'hidden');
    var zone = document.getElementById('photo-drop-zone');
    if (zone) { zone.style.borderStyle = 'dashed'; zone.style.borderColor = ''; }
}
</script>

<style>
.fade-in { animation: fadeIn 0.4s ease-out forwards; }
@keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
</style>

<script>
window.profileDefaultPhoto = 'https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=1E3A5F&color=fff&size=200&bold=true';
</script>

@include('partials.photo-cropper')

@endsection
