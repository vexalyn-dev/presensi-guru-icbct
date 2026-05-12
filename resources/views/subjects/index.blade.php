@extends('layouts.app')

@section('page-title', 'Mata Pelajaran')

@section('content')
    <div class="fade-in space-y-6">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Mata Pelajaran</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Kelola data mata pelajaran</p>
            </div>
            <a href="{{ route('subjects.create') }}" class="btn-primary flex items-center gap-2 w-fit">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Mapel
            </a>
        </div>

        <!-- Filters -->
        <div class="card p-5">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[250px]">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau kode mapel..."
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800">
                </div>
                <div class="w-full sm:w-48">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Status</label>
                    <select name="status"
                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800">
                        <option value="">Semua</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button type="submit"
                        class="flex-1 sm:flex-none px-5 py-2.5 bg-navy-800 hover:bg-navy-900 text-white rounded-xl text-sm font-semibold transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('subjects.index') }}"
                        class="flex-1 sm:flex-none px-5 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold transition-colors text-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Subjects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($subjects as $subject)
                <div class="card p-5 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-xl flex items-center justify-center">
                            <i data-lucide="book-open" class="w-6 h-6 text-white dark:text-navy-900"></i>
                        </div>
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $subject->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-slate-100 text-slate-700' }}">
                            {{ $subject->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-1">{{ $subject->name }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-3 font-mono bg-slate-50 dark:bg-slate-700/50 px-3 py-1.5 rounded-lg inline-block">
                        {{ $subject->code }}
                    </p>

                    @if($subject->description)
                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mb-4 h-8">{{ $subject->description }}</p>
                    @else
                        <div class="mb-4 h-8 text-xs text-slate-400 italic">Tidak ada deskripsi</div>
                    @endif

                    <div class="flex items-center gap-2 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <a href="{{ route('subjects.edit', $subject) }}"
                            class="flex-1 px-3 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold transition-colors text-center flex items-center justify-center gap-2 icon-click">
                            <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                            Edit
                        </a>
                        <form action="{{ route('subjects.destroy', $subject) }}" method="POST" class="flex-1"
                            onsubmit="return confirm('Yakin ingin menghapus?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full px-3 py-2.5 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl text-xs font-semibold transition-colors flex items-center justify-center gap-2 icon-click">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="card p-16 text-center">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center">
                                <i data-lucide="book-open" class="w-10 h-10 text-slate-400"></i>
                            </div>
                            <div>
                                <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada mata pelajaran</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Mulai dengan menambahkan mapel
                                    pertama</p>
                            </div>
                            <a href="{{ route('subjects.create') }}" class="btn-primary flex items-center gap-2 text-sm">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Tambah Mapel
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($subjects->hasPages())
            <div class="flex justify-center">
                {{ $subjects->links() }}
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons();
        });
    </script>
@endsection