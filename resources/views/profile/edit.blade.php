@extends('layouts.app')

@section('page-title', 'Profil Saya')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in" x-data="profileApp()">
    
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-center md:items-end justify-between bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-sm border border-slate-200 dark:border-slate-700 gap-6">
        <div class="flex items-center flex-col md:flex-row text-center md:text-left gap-6">
            <div class="relative group">
                <div class="w-24 h-24 rounded-[1.5rem] p-1 bg-gradient-to-br from-indigo-500 to-purple-600 shadow-xl shadow-indigo-500/20">
                    <img src="{{ $user->photo_url }}" id="photo-preview" class="w-full h-full rounded-[1.2rem] object-cover" alt="Profile">
                </div>
                <label for="photo-upload" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 rounded-[1.5rem] flex items-center justify-center cursor-pointer transition-all duration-300">
                    <i data-lucide="camera" class="w-6 h-6 text-white"></i>
                    <input type="file" name="photo" id="photo-upload" class="hidden" accept="image/*" @change="previewImage($event)" form="profile-form">
                </label>
            </div>
            <div>
                <div class="flex items-center justify-center md:justify-start gap-3 mb-1">
                    <h1 class="text-3xl font-black text-navy-800 dark:text-white tracking-tight">{{ $user->name }}</h1>
                    <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full text-[10px] font-black uppercase tracking-widest mt-1">
                        {{ $user->role_name }}
                    </span>
                </div>
                <p class="text-slate-500 font-medium">{{ $user->email }}</p>
            </div>
        </div>
        
        <div class="hidden md:block">
            <div class="flex flex-col items-end">
                <span class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Status Akun</span>
                <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-xl">
                    <span class="relative flex h-2.5 w-2.5">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-xs font-bold tracking-wide">Aktif & Aman</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Forms Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Col: Personal Details -->
        <div class="lg:col-span-7 space-y-8">
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 xl:p-10 shadow-sm border border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center text-indigo-600">
                        <i data-lucide="user" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-navy-800 dark:text-white">Informasi Pribadi</h2>
                        <p class="text-xs text-slate-500 font-medium">Perbarui profil dan biodata Anda.</p>
                    </div>
                </div>

                <form id="profile-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-transparent rounded-[1.2rem] text-sm font-bold text-navy-800 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:border-indigo-500 transition-all outline-none shadow-inner">
                            @error('name')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1">Email Saat Ini</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-transparent rounded-[1.2rem] text-sm font-bold text-navy-800 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:border-indigo-500 transition-all outline-none shadow-inner">
                            @error('email')<p class="text-[10px] text-red-500 font-bold mt-1 pl-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1">Nomor Telepon</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-transparent rounded-[1.2rem] text-sm font-bold text-navy-800 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:border-indigo-500 transition-all outline-none shadow-inner" placeholder="08xxxxxxxx">
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1">Alamat Domisili</label>
                            <textarea name="address" rows="2"
                                class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-transparent rounded-[1.2rem] text-sm font-bold text-navy-800 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:border-indigo-500 transition-all outline-none resize-none shadow-inner">{{ old('address', $user->address) }}</textarea>
                        </div>
                        
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1">Bio Singkat</label>
                            <textarea name="bio" rows="3"
                                class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-transparent rounded-[1.2rem] text-sm font-bold text-navy-800 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:border-indigo-500 transition-all outline-none resize-none shadow-inner" placeholder="Ceritakan sedikit tentang Anda...">{{ old('bio', $user->bio) }}</textarea>
                        </div>
                    </div>

                    <div class="pt-6 flex justify-end">
                        <button type="submit" class="px-8 py-4 bg-navy-800 hover:bg-navy-900 text-white rounded-[1.2rem] text-sm font-black shadow-xl shadow-navy-800/20 active:scale-95 transition-all flex items-center gap-3">
                            <i data-lucide="save" class="w-5 h-5"></i> Simpan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Right Col: Security & Danger Zone -->
        <div class="lg:col-span-5 space-y-8" x-data="passwordForm()">
            <!-- Password Update -->
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 xl:p-10 shadow-sm border border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-2xl flex items-center justify-center text-orange-600">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-navy-800 dark:text-white">Keamanan Akun</h2>
                        <p class="text-xs text-slate-500 font-medium">Ubah password secara berkala.</p>
                    </div>
                </div>

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-5">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1">Password Saat Ini</label>
                            <div class="relative">
                                <input :type="currentVisible ? 'text' : 'password'" name="current_password" required
                                    class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-transparent rounded-[1.2rem] text-sm font-bold text-navy-800 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:border-orange-500 transition-all outline-none shadow-inner">
                                <button type="button" @click="currentVisible = !currentVisible" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-orange-500 transition-colors">
                                    <i :data-lucide="currentVisible ? 'eye-off' : 'eye'" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1">Password Baru</label>
                            <div class="relative">
                                <input :type="newVisible ? 'text' : 'password'" name="password" required x-model="newPassword" @input="checkStrength"
                                    class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-transparent rounded-[1.2rem] text-sm font-bold text-navy-800 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:border-orange-500 transition-all outline-none shadow-inner">
                                <button type="button" @click="newVisible = !newVisible" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-orange-500 transition-colors">
                                    <i :data-lucide="newVisible ? 'eye-off' : 'eye'" class="w-5 h-5"></i>
                                </button>
                            </div>
                            
                            <div class="mt-3 flex items-center gap-3" x-show="newPassword.length > 0">
                                <div class="flex-1 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300" :class="strengthClass" :style="`width: ${strengthPercent}%`"></div>
                                </div>
                                <span class="text-[10px] font-black" :class="strengthTextColor" x-text="strengthText"></span>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest pl-1">Ulangi Password Baru</label>
                            <div class="relative">
                                <input :type="confirmVisible ? 'text' : 'password'" name="password_confirmation" required x-model="confirmPassword"
                                    class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-transparent rounded-[1.2rem] text-sm font-bold text-navy-800 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:border-orange-500 transition-all outline-none shadow-inner">
                                <button type="button" @click="confirmVisible = !confirmVisible" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-orange-500 transition-colors">
                                    <i :data-lucide="confirmVisible ? 'eye-off' : 'eye'" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 flex justify-end">
                        <button type="submit" class="px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white rounded-[1.2rem] text-sm font-black shadow-xl shadow-orange-500/20 active:scale-95 transition-all w-full md:w-auto text-center justify-center">
                            Perbarui Keamanan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 dark:bg-red-900/10 rounded-[2.5rem] p-8 border-2 border-red-200/50 dark:border-red-900/30">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/40 rounded-2xl flex items-center justify-center text-red-600">
                        <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-red-800 dark:text-red-400">Danger Zone</h2>
                        <p class="text-[10px] font-black text-red-600/70 uppercase tracking-widest mt-1">Penghapusan Bersifat Permanen</p>
                    </div>
                </div>
                <p class="text-xs font-medium text-red-700/80 dark:text-red-300/80 mb-6 leading-relaxed">
                    Setelah akun Anda dihapus, semua data profil dan riwayat Anda sistem tidak akan bisa dipulihkan kembali. Mohon pastikan keputusan ini dengan bijak.
                </p>
                <form action="{{ route('profile.destroy') }}" method="POST" onsubmit="return confirm('Apakah Anda sangat yakin ingin menghapus akun ini secara permanen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-4 bg-red-500 hover:bg-red-600 text-white rounded-[1.2rem] text-sm font-black shadow-lg shadow-red-500/30 active:scale-95 transition-all flex items-center justify-center gap-3">
                        <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus Akun Permanen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('profileApp', () => ({
            previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        document.getElementById('photo-preview').src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }
        }));

        Alpine.data('passwordForm', () => ({
            newPassword: '',
            confirmPassword: '',
            currentVisible: false,
            newVisible: false,
            confirmVisible: false,
            strength: 0,
            
            get strengthClass() { return ['bg-slate-300', 'bg-red-500', 'bg-orange-500', 'bg-blue-500', 'bg-emerald-500'][this.strength] || 'bg-slate-300'; },
            get strengthTextColor() { return ['text-slate-400', 'text-red-500', 'text-orange-500', 'text-blue-500', 'text-emerald-500'][this.strength] || 'text-slate-400'; },
            get strengthText() { return ['-', 'Sangat Lemah', 'Lemah', 'Sedang', 'Kuat'][this.strength] || '-'; },
            get strengthPercent() { return this.strength * 25; },
            
            checkStrength() {
                let score = 0;
                const p = this.newPassword;
                if(p.length > 0) score = 1;
                if(p.length >= 8) score = 2;
                if(p.length >= 8 && /[A-Z]/.test(p) && /[0-9]/.test(p)) score = 3;
                if(p.length >= 8 && /[A-Z]/.test(p) && /[0-9]/.test(p) && /[^A-Za-z0-9]/.test(p)) score = 4;
                this.strength = score;
            }
        }));
    });
</script>

<style>
    .animate-fade-in {
        animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection