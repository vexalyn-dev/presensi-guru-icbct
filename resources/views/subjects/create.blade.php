@extends('layouts.app')

@section('page-title', 'Tambah Mata Pelajaran')

@section('content')
    <div class="max-w-3xl mx-auto fade-in">

        <!-- Header -->
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('subjects.index') }}"
                class="group flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600">
                <i data-lucide="arrow-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
                <span>Kembali</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Tambah Mata Pelajaran</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Isi form di bawah untuk menambahkan mapel baru</p>
            </div>
        </div>

        <!-- Form -->
        <div class="card p-6">
            <form action="{{ route('subjects.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Mapel -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                            Nama Mata Pelajaran
                        </label>
                        <div class="relative">
                            <i data-lucide="book-open"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 @error('name') border-red-500 @enderror"
                                placeholder="Contoh: Matematika Wajib">
                        </div>
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Guru Pengampu -->
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                            Guru Pengampu
                        </label>
                        <div class="relative">
                            <i data-lucide="user"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                            <select name="teacher_id"
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 @error('teacher_id') border-red-500 @enderror">
                                <option value="">Pilih Guru Pengampu (Opsional)</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('teacher_id')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Status Aktif -->
                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                            Status
                        </label>
                        <label
                            class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                class="w-5 h-5 rounded border-slate-300 text-gold-500 focus:ring-gold-500">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Aktif</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Mapel dapat digunakan dalam
                                    penjadwalan</p>
                            </div>
                        </label>
                    </div>

                    <!-- Deskripsi -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                            Deskripsi
                        </label>
                        <div class="relative">
                            <i data-lucide="align-left" class="absolute left-4 top-4 w-4 h-4 text-slate-400"></i>
                            <textarea name="description" rows="3"
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 @error('description') border-red-500 @enderror resize-none"
                                placeholder="Deskripsi mata pelajaran (opsional)">{{ old('description') }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-3 mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <button type="submit" class="btn-primary flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Mapel
                    </button>
                    <a href="{{ route('subjects.index') }}"
                        class="px-5 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();
        });
    </script>
@endsection