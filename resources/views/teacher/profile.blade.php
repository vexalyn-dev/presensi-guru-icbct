@extends('layouts.app')

@section('page-title', 'Profil Saya')

@section('content')
<div class="max-w-2xl mx-auto fade-in">
    <div class="card p-6">
        <h3 class="text-xl font-bold text-navy-800 dark:text-white mb-4">Edit Profil</h3>
        <form action="{{ route('teacher.profile.update') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-gold-500 focus:border-gold-500" required>
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email (Tidak dapat diubah)</label>
                    <input type="email" value="{{ $user->email }}" disabled class="w-full rounded-lg border-slate-300 bg-slate-100 dark:border-slate-600 dark:bg-slate-800 text-slate-500 px-4 py-2.5 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">No. Handphone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
                    @error('phone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Alamat</label>
                    <textarea name="address" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-gold-500 focus:border-gold-500">{{ old('address', $user->address) }}</textarea>
                    @error('address') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                    <h4 class="text-sm font-semibold text-navy-800 dark:text-white mb-3">Ubah Password (Opsional)</h4>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password Baru</label>
                            <input type="password" name="password" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-gold-500 focus:border-gold-500" placeholder="Kosongkan jika tidak ingin mengubah">
                            @error('password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-gold-500 focus:border-gold-500" placeholder="Ulangi password baru">
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-navy-800 text-white font-medium rounded-xl hover:bg-navy-900 focus:ring-4 focus:ring-navy-200 transition-all icon-click">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('teachers.qr.download', $user->id) }}" class="ml-4 px-6 py-2.5 bg-navy-800 text-white font-medium rounded-xl hover:bg-navy-900 focus:ring-4 focus:ring-navy-200 transition-all">Download QR</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
