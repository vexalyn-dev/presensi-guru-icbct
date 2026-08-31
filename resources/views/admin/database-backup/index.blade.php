@extends('layouts.app')

@section('page-title', 'Backup Database')

@section('content')
<div class="space-y-6 fade-in">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i data-lucide="database-backup" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white tracking-tight">Backup Database</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola cadangan data database aplikasi</p>
            </div>
        </div>
        @if(session('success'))
            <div class="flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
                <i data-lucide="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                <span class="text-sm font-medium text-green-700 dark:text-green-300">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="flex items-center gap-2 px-4 py-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
                <span class="text-sm font-medium text-amber-700 dark:text-amber-300">{{ session('warning') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 dark:text-red-400"></i>
                <span class="text-sm font-medium text-red-700 dark:text-red-300">{{ session('error') }}</span>
            </div>
        @endif
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="archive" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total Backup</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="hard-drive" class="w-6 h-6 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total Ukuran</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['total_size'] }}</h3>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="clock" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Terakhir Backup</p>
                    <h3 class="text-base font-bold text-navy-800 dark:text-white truncate">
                        {{ $stats['latest']['created_at'] ?? 'Belum ada' }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    {{-- CREATE BACKUP CARD --}}
    <div class="card p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-navy-100 dark:bg-navy-900/30 rounded-xl flex items-center justify-center">
                <i data-lucide="plus" class="w-5 h-5 text-navy-600 dark:text-gold-400"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-navy-800 dark:text-white">Buat Backup Baru</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Pilih tipe backup yang diinginkan</p>
            </div>
        </div>

        <form action="{{ request()->routeIs('piket.*') ? route('piket.database-backup.create') : route('admin.database-backup.create') }}" method="POST" class="space-y-4" id="backup-form">
            @csrf
            <div class="flex flex-wrap gap-4">
                <label class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all flex-1 min-w-[200px]"
                       :class="formType === 'full' ? 'border-navy-800 dark:border-gold-400 bg-navy-50 dark:bg-navy-900/20' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 hover:border-slate-300'">
                    <input type="radio" name="full" value="0" class="hidden" x-model="formType" x-init>
                    <div class="w-8 h-8 rounded-lg bg-slate-200 dark:bg-slate-600 flex items-center justify-center flex-shrink-0"
                         :class="formType === 'full' ? 'bg-navy-800 dark:bg-gold-400' : ''">
                        <i data-lucide="database-zap" class="w-4 h-4 text-white dark:text-navy-900"
                           :class="formType !== 'full' ? 'text-slate-500' : ''"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-navy-800 dark:text-white">Smart (Rekomendasi)</p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">Tabel esensial saja, tanpa log sistem</p>
                    </div>
                </label>

                <label class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 cursor-pointer transition-all flex-1 min-w-[200px]"
                       :class="formType === 'all' ? 'border-navy-800 dark:border-gold-400 bg-navy-50 dark:bg-navy-900/20' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 hover:border-slate-300'">
                    <input type="radio" name="full" value="1" class="hidden" x-model="formType">
                    <div class="w-8 h-8 rounded-lg bg-slate-200 dark:bg-slate-600 flex items-center justify-center flex-shrink-0"
                         :class="formType === 'all' ? 'bg-navy-800 dark:bg-gold-400' : ''">
                        <i data-lucide="database" class="w-4 h-4 text-white dark:text-navy-900"
                           :class="formType !== 'all' ? 'text-slate-500' : ''"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-navy-800 dark:text-white">Full (Semua)</p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">Termasuk semua tabel sistem</p>
                    </div>
                </label>
            </div>

            <button type="submit" id="btn-create"
                    class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all hover:shadow-lg hover:-translate-y-0.5 active:scale-[.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none">
                <i data-lucide="download" class="w-4 h-4"></i>
                Buat Backup Sekarang
            </button>
        </form>

        <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
            <div class="flex items-start gap-2">
                <i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-xs font-bold text-blue-800 dark:text-blue-300 mb-0.5">Catatan</p>
                    <p class="text-[11px] text-blue-700 dark:text-blue-400 leading-relaxed">
                        Backup disimpan di server lokal pada <code class="font-mono text-[10px] bg-blue-100 dark:bg-blue-900/40 px-1 py-0.5 rounded">storage/app/backups/</code>.
                        Maksimal 5 file backup akan disimpan, yang lama akan otomatis dihapus.
                        Pastikan <code class="font-mono text-[10px] bg-blue-100 dark:bg-blue-900/40 px-1 py-0.5 rounded">mysqldump</code> tersedia di server.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- BACKUP LIST --}}
    <div class="card p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center">
                <i data-lucide="folder-open" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-navy-800 dark:text-white">Daftar Backup</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ count($backups) }} file tersedia</p>
            </div>
        </div>

        @if($backups->isEmpty())
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="folder-open" class="w-8 h-8 text-slate-400 dark:text-slate-500"></i>
                </div>
                <p class="text-sm font-bold text-slate-600 dark:text-slate-400">Belum ada backup</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Buat backup pertama menggunakan tombol di atas</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach($backups as $i => $backup)
                    <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all group">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                            {{ $i === 0 ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-slate-100 dark:bg-slate-700' }}">
                            <i data-lucide="{{ $i === 0 ? 'circle-check' : 'file-archive' }}"
                               class="w-5 h-5 {{ $i === 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-navy-800 dark:text-white truncate">
                                {{ $backup['filename'] }}
                                @if($i === 0)
                                    <span class="ml-2 px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-full text-[10px] font-bold">Terbaru</span>
                                @endif
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                <i data-lucide="calendar" class="w-3 h-3 inline mr-0.5"></i>
                                {{ $backup['created_at'] }}
                                &bull;
                                <i data-lucide="hard-drive" class="w-3 h-3 inline mr-0.5"></i>
                                {{ $backup['size_human'] }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                            <a href="{{ request()->routeIs('piket.*') ? route('piket.database-backup.download', $backup['filename']) : route('admin.database-backup.download', $backup['filename']) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-2 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-lg text-xs font-semibold transition-all hover:shadow-md active:scale-95">
                                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                Download
                            </a>
                            <button onclick="confirmDelete('{{ $backup['filename'] }}', '{{ addslashes($backup['size_human']) }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-lg text-xs font-semibold transition-all hover:bg-red-100 dark:hover:bg-red-900/40 active:scale-95">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                Hapus
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4" x-cloak>
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm p-6 transform transition-all">
        <div class="flex flex-col items-center text-center">
            <div class="w-14 h-14 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="trash-2" class="w-7 h-7 text-red-600 dark:text-red-400"></i>
            </div>
            <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-1">Hapus Backup?</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">File <strong id="deleteFilename" class="text-slate-700 dark:text-slate-300"></strong></p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mb-6" id="deleteSize"></p>
            <div class="flex gap-3 w-full">
                <button onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-all hover:bg-slate-200 dark:hover:bg-slate-600">
                    Batal
                </button>
                <button id="btnConfirmDelete"
                        class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let formType = 'smart';
    let deleteTarget = null;

    function confirmDelete(filename, size) {
        deleteTarget = filename;
        document.getElementById('deleteFilename').textContent = filename;
        document.getElementById('deleteSize').textContent = 'Ukuran: ' + size;
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        deleteTarget = null;
    }

    document.getElementById('btnConfirmDelete').addEventListener('click', async function () {
        if (!deleteTarget) return;
        this.disabled = true;
        this.innerHTML = '<div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>';

        const res = await fetch("{{ request()->routeIs('piket.*') ? route('piket.database-backup.destroy', ':fn') : route('admin.database-backup.destroy', ':fn') }}".replace(':fn', deleteTarget), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        const data = await res.json();

        if (data.success) {
            window.location.reload();
        } else {
            alert('Gagal menghapus file.');
            this.disabled = false;
            this.innerHTML = '<i data-lucide="trash-2" class="w-4 h-4"></i> Ya, Hapus';
        }
    });

    document.getElementById('backup-form').addEventListener('submit', function () {
        const btn = document.getElementById('btn-create');
        btn.disabled = true;
        btn.innerHTML = '<div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div> Memproses...';
    });
</script>

<style>
    .fade-in { animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
