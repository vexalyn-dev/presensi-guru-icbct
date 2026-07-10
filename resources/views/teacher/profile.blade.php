@extends('layouts.teacher')

@section('page-title', 'Profil Saya')

@section('content')
<div class="fade-in space-y-6">
    
    <!-- Alerts -->
    @if(session('success'))
    <div class="card p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800">
        <div class="flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
            <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Info Card -->
        <div class="lg:col-span-1">
            <div class="card p-6 text-center sticky top-24">
                <div class="relative inline-block">
                    <img src="{{ $teacher && $teacher->photo ? asset('storage/' . $teacher->photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0F172A&color=fff&size=128' }}" 
                         class="w-32 h-32 rounded-2xl object-cover border-4 border-white dark:border-slate-700 shadow-lg mx-auto">
                    <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-gradient-to-br from-gold-400 to-gold-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i data-lucide="shield" class="w-5 h-5 text-navy-900"></i>
                    </div>
                </div>
                
                <h2 class="text-xl font-bold text-navy-800 dark:text-white mt-4">{{ auth()->user()->name }}</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ auth()->user()->email }}</p>
                
                @if($teacher && $teacher->major_specialty)
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400 rounded-full text-xs font-bold mt-3">
                    <i data-lucide="book-open" class="w-3 h-3"></i>
                    {{ $teacher->major_specialty }}
                </span>
                @endif

                <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700 space-y-3 text-left">
                    @if($teacher)
                    @if($teacher->nip)
                    <div class="flex items-center gap-3">
                        <i data-lucide="id-card" class="w-4 h-4 text-slate-400"></i>
                        <div class="flex-1">
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">NIP</p>
                            <p class="text-sm font-semibold text-navy-800 dark:text-white">{{ $teacher->nip }}</p>
                        </div>
                    </div>
                    @endif
                    @if($teacher->education)
                    <div class="flex items-center gap-3">
                        <i data-lucide="graduation-cap" class="w-4 h-4 text-slate-400"></i>
                        <div class="flex-1">
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Pendidikan</p>
                            <p class="text-sm font-semibold text-navy-800 dark:text-white">{{ $teacher->education }}</p>
                        </div>
                    </div>
                    @endif
                    @if($teacher->join_date)
                    <div class="flex items-center gap-3">
                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                        <div class="flex-1">
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Bergabung Sejak</p>
                            <p class="text-sm font-semibold text-navy-800 dark:text-white">{{ \Carbon\Carbon::parse($teacher->join_date)->format('d M Y') }}</p>
                        </div>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Forms -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Edit Profile Form -->
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                        <i data-lucide="user" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-navy-800 dark:text-white">Edit Profil</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Perbarui informasi pribadi Anda</p>
                    </div>
                </div>

                <form action="{{ route('teacher.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" required value="{{ old('name', auth()->user()->name) }}"
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('name') ? 'border-red-400 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                        @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Email</label>
                        <input type="email" value="{{ auth()->user()->email }}" readonly
                               class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-500 cursor-not-allowed">
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Email tidak dapat diubah</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nomor Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $teacher->phone ?? '') }}"
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('phone') ? 'border-red-400 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                        @error('phone')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Alamat</label>
                        <textarea name="address" rows="3"
                                  class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('address') ? 'border-red-400 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">{{ old('address', $teacher->address ?? '') }}</textarea>
                        @error('address')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Foto Profil</label>
                        <input type="file" name="photo" accept="image/*"
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('photo') ? 'border-red-400 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Maksimal 2MB (JPG, PNG, GIF)</p>
                        @error('photo')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Form -->
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                        <i data-lucide="lock" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-navy-800 dark:text-white">Ganti Password</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Perbarui password akun Anda</p>
                    </div>
                </div>

                <form action="{{ route('teacher.profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Password Lama</label>
                        <input type="password" name="current_password" required
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('current_password') ? 'border-red-400 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                        @error('current_password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Password Baru</label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 {{ $errors->has('password') ? 'border-red-400 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                        @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" required minlength="8"
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                            Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
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
</style>
@endsection