@extends('layouts.app')

@section('page-title', 'Tambah Kelas')

@section('content')
    <div class="fade-in max-w-2xl mx-auto space-y-6">

        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('classrooms.index') }}"
                class="p-2.5 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-xl transition-all hover:-translate-x-1 group">
                <i data-lucide="arrow-left"
                    class="w-5 h-5 text-slate-600 dark:text-slate-400 group-hover:text-navy-800 dark:group-hover:text-gold-400 transition-colors"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Tambah Kelas Baru</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Isi data kelas untuk generate QR Code</p>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('classrooms.store') }}" method="POST" class="card p-6">
            @csrf

            <div class="space-y-5">
                <!-- Nama Kelas -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Nama Kelas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required placeholder="Contoh: X-A, XI-IPA-1, XII-IPS-2"
                        value="{{ old('name') }}"
                        class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Nama kelas yang akan ditampilkan</p>
                </div>

                <!-- Kode Kelas -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Kode Kelas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" required placeholder="Contoh: XA001, XIIPA1, XIIIPS2"
                        value="{{ old('code') }}"
                        class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 @error('code') border-red-500 @enderror uppercase">
                    @error('code')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Kode unik untuk kelas (tanpa spasi)</p>
                </div>

                <!-- Gedung -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Gedung / Blok
                    </label>
                    <input type="text" name="building" placeholder="Contoh: Gedung A, Blok C" value="{{ old('building') }}"
                        class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Opsional - Lokasi gedung kelas</p>
                </div>

                <!-- Lantai -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Lantai
                    </label>
                    <select name="floor"
                        class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                        <option value="">Pilih Lantai</option>
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ old('floor') == $i ? 'selected' : '' }}>Lantai {{ $i }}</option>
                        @endfor
                    </select>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Opsional - Lantai gedung</p>
                </div>

                <!-- Status -->
                <div>
                    <label
                        class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-700/30 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-slate-300 text-navy-600 focus:ring-navy-500">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Kelas Aktif</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Kelas yang tidak aktif tidak akan muncul
                                dalam jadwal</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('classrooms.index') }}"
                    class="flex-1 px-6 py-3.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-all hover:-translate-y-0.5 text-center">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 px-6 py-3.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:from-navy-900 hover:to-slate-900 dark:hover:from-gold-500 dark:hover:to-gold-600 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan & Generate QR</span>
                </button>
            </div>
        </form>
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
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection