@extends('layouts.teacher')
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
                </div>
                <div class="px-6 pb-6">
                    @php
                        $profilePhoto = auth()->user()->photo
                            ? asset('storage/' . auth()->user()->photo)
                            : ($teacher && $teacher->photo ? asset('storage/' . $teacher->photo) : null);
                    @endphp
                    <div class="flex justify-between items-end -mt-10 mb-4">
                        <div class="relative group cursor-pointer" onclick="document.getElementById('photo-upload-trigger').click()">
                            <img id="profile-avatar"
                                 src="{{ $profilePhoto ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=1E3A5F&color=fff&size=200&bold=true' }}"
                                 alt="{{ auth()->user()->name }}"
                                 class="w-20 h-20 rounded-2xl object-cover border-4 border-white dark:border-slate-900 shadow-xl transition-all duration-300">
                            <div class="absolute inset-0 rounded-2xl bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <i data-lucide="camera" class="w-6 h-6 text-white"></i>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-gradient-to-br from-gold-400 to-gold-500 rounded-lg flex items-center justify-center shadow-md border-2 border-white dark:border-slate-900">
                                <i data-lucide="camera" class="w-3 h-3 text-navy-900"></i>
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
                    <p class="text-[10px] text-slate-400 mt-3 flex items-center gap-1">
                        <i data-lucide="info" class="w-3 h-3"></i> Klik foto untuk mengganti
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

                    {{-- Photo Upload Card --}}
                    <div id="photo-drop-zone"
                         class="p-4 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30 transition-all duration-300">
                        <div class="flex items-center gap-4">
                            <div class="relative flex-shrink-0 cursor-pointer group" onclick="document.getElementById('photo-upload-trigger').click()">
                                <img id="photo-preview"
                                     src="{{ $profilePhoto ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=1E3A5F&color=fff&size=200&bold=true' }}"
                                     alt="Preview" class="w-16 h-16 rounded-xl object-cover shadow-md border-2 border-white dark:border-slate-700 group-hover:scale-105 transition-transform">
                                <div class="absolute inset-0 rounded-xl bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <i data-lucide="camera" class="w-5 h-5 text-white"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-navy-800 dark:text-white mb-0.5">Foto Profil</p>
                                <p id="photo-info" class="text-[11px] text-slate-500 dark:text-slate-400 mb-3">JPG, PNG • Maks. 2MB • Disarankan 1:1</p>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="document.getElementById('photo-upload-trigger').click()"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-navy-800 dark:bg-gold-400 hover:opacity-90 text-white dark:text-navy-900 rounded-lg text-xs font-bold transition-all shadow-sm active:scale-95">
                                        <i data-lucide="upload" class="w-3 h-3"></i> Pilih Foto
                                    </button>
                                    <button type="button" id="photo-cancel-btn" onclick="cancelPhoto()"
                                            class="hidden items-center gap-1.5 px-3 py-1.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-lg text-xs font-bold transition-all active:scale-95">
                                        <i data-lucide="x" class="w-3 h-3"></i> Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                        @error('photo')<p class="mt-2 text-xs text-red-500 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>@enderror
                    </div>

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
                        <p class="mt-1 text-xs text-slate-400">Email tidak dapat diubah</p>
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
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file terlalu besar. Maksimal 2MB.');
        input.value = '';
        return;
    }
    var reader = new FileReader();
    reader.onload = function(e) {
        var src = e.target.result;
        var preview = document.getElementById('photo-preview');
        var avatar  = document.getElementById('profile-avatar');
        if (preview) preview.src = src;
        if (avatar)  avatar.src  = src;

        var info = document.getElementById('photo-info');
        if (info) { info.textContent = '✓ ' + file.name + ' (' + (file.size/1024).toFixed(0) + ' KB) — klik Simpan'; info.style.color = '#16a34a'; }

        var btn = document.getElementById('photo-cancel-btn');
        if (btn) btn.classList.replace('hidden', 'inline-flex');

        var zone = document.getElementById('photo-drop-zone');
        if (zone) { zone.style.borderStyle = 'solid'; zone.style.borderColor = '#1e3a5f'; }
    };
    reader.readAsDataURL(file);
}

function cancelPhoto() {
    var input = document.getElementById('photo-upload-trigger');
    if (input) input.value = '';
    var preview = document.getElementById('photo-preview');
    var avatar  = document.getElementById('profile-avatar');
    if (preview) preview.src = originalPhotoSrc;
    if (avatar)  avatar.src  = originalPhotoSrc;
    var info = document.getElementById('photo-info');
    if (info) { info.textContent = 'JPG, PNG • Maks. 2MB • Disarankan 1:1'; info.style.color = ''; }
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
@endsection
