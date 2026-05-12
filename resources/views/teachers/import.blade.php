@extends('layouts.app')

@section('page-title', 'Import Guru')

@section('content')
<div class="max-w-2xl mx-auto fade-in">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('teachers.index') }}"
            class="group flex items-center gap-2.5 px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600">
            <i data-lucide="arrow-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
            <span>Kembali</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Import Guru</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Tambah guru secara massal</p>
        </div>
    </div>

    <div class="card p-6">
        <div class="mb-6 pb-4 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gold-100 dark:bg-gold-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="upload" class="w-5 h-5 text-gold-600 dark:text-gold-400"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-navy-800 dark:text-white">Import Data Guru</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Upload file CSV untuk import massal</p>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
            <div class="flex items-start gap-3">
                <i data-lucide="info" class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5"></i>
                <div class="text-sm text-blue-800 dark:text-blue-300">
                    <p class="font-medium mb-2">Format File CSV:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        <li>Kolom 1: Nama Lengkap</li>
                        <li>Kolom 2: Email</li>
                        <li>Kolom 3: Password</li>
                        <li>Kolom 4: No. Telepon (Opsional)</li>
                    </ul>
                </div>
            </div>
        </div>

        <form action="{{ route('teachers.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- File Upload -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    File CSV
                </label>
                <div class="relative">
                    <input type="file" name="file" id="fileInput" accept=".csv,.xlsx,.xls" required
                           class="hidden" onchange="updateFileName(this)">
                    <label for="fileInput" 
                           class="flex items-center justify-center gap-3 w-full py-8 px-4 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl cursor-pointer hover:border-gold-400 dark:hover:border-gold-400 transition-all hover:bg-slate-50 dark:hover:bg-slate-700/30">
                        <i data-lucide="file-spreadsheet" class="w-8 h-8 text-slate-400"></i>
                        <div class="text-center">
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Klik untuk upload file</p>
                            <p class="text-xs text-slate-500 mt-1" id="fileName">CSV, Excel (Max 10MB)</p>
                        </div>
                    </label>
                </div>
                @error('file')
                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between gap-3 pt-5 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('teachers.template') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gold-600 dark:text-gold-400 hover:bg-gold-50 dark:hover:bg-gold-900/20 rounded-xl transition-colors">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Download Template
                </a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('teachers.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="btn-ripple btn-primary">
                        Import Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function updateFileName(input) {
    const fileName = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        fileName.textContent = input.files[0].name;
        fileName.classList.add('text-gold-600', 'dark:text-gold-400');
    }
}
</script>
@endsection