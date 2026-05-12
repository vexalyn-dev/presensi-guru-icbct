@extends('layouts.app')

@section('page-title', 'Ajukan Izin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 fade-in">
    <div class="flex items-center gap-4">
        <a href="{{ route('leaves.index') }}" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Ajukan Izin / Sakit</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Isi formulir di bawah ini</p>
        </div>
    </div>

    <div class="card p-6 md:p-8">
        <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Status</label>
                <select name="type" required class="input-field w-full">
                    <option value="Izin" @selected(old('type') === 'Izin')>Izin</option>
                    <option value="Sakit" @selected(old('type') === 'Sakit')>Sakit</option>
                    <option value="Dinas" @selected(old('type') === 'Dinas')>Dinas</option>
                    <option value="Cuti" @selected(old('type') === 'Cuti')>Cuti</option>
                </select>
                @error('type')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Tanggal mulai</label>
                    <input type="date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required class="input-field w-full">
                    @error('start_date')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Tanggal selesai</label>
                    <input type="date" name="end_date" value="{{ old('end_date', now()->format('Y-m-d')) }}" required class="input-field w-full">
                    @error('end_date')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Alasan</label>
                <textarea name="reason" rows="4" required maxlength="1000" class="input-field w-full resize-y" placeholder="Jelaskan alasan pengajuan…">{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Lampiran (PDF/JPG/PNG, max 2MB)</label>
                <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm text-slate-600 dark:text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-slate-100 dark:file:bg-slate-700">
                @error('attachment')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="btn-primary inline-flex items-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    Kirim pengajuan
                </button>
                <a href="{{ route('leaves.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
