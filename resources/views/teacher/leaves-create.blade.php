@extends('layouts.app')

@section('page-title', 'Ajukan Izin / Sakit')

@section('content')
<div class="max-w-2xl mx-auto fade-in">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('teacher.leaves') }}"
            class="group flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600">
            <i data-lucide="arrow-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
            <span>Kembali</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Form Pengajuan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Silakan isi form di bawah ini dengan lengkap</p>
        </div>
    </div>

    <!-- Form Ajukan Izin -->
    <div class="card p-6">
        <form action="{{ route('teacher.leave-request.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mulai Tanggal</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-gold-500 focus:border-gold-500" required>
                    @error('start_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-gold-500 focus:border-gold-500" required>
                    @error('end_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jenis</label>
                <select name="type" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-gold-500 focus:border-gold-500" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="Izin" {{ old('type') == 'Izin' ? 'selected' : '' }}>Izin</option>
                    <option value="Sakit" {{ old('type') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                </select>
                @error('type') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Alasan</label>
                <textarea name="reason" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-slate-900 dark:text-white px-4 py-2.5 focus:ring-2 focus:ring-gold-500 focus:border-gold-500" required>{{ old('reason') }}</textarea>
                @error('reason') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Lampiran (Opsional, PDF/Image max 2MB)</label>
                <input type="file" name="attachment" accept=".pdf,image/*" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-slate-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
                @error('attachment') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="px-6 py-2.5 bg-navy-800 text-white font-medium rounded-xl hover:bg-navy-900 transition-all icon-click">
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
