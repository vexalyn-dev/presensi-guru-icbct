@extends('layouts.app')

@section('page-title', 'Izin & Sakit')

@section('content')
<div class="space-y-6 fade-in">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                <i data-lucide="file-text" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-navy-800 dark:text-white tracking-tight">Izin & Sakit</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    {{ Auth::user()->isAdmin() ? 'Kelola pengajuan izin dan sakit guru' : 'Pengajuan izin dan sakit Anda' }}
                </p>
            </div>
        </div>
        @if(Auth::user()->isAdmin())
            <a href="{{ route('leaves.create') }}" class="btn-primary flex items-center justify-center gap-2 shrink-0">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Ajukan Izin
            </a>
        @else
            <a href="{{ route('teacher.dashboard') }}#izin" class="btn-primary flex items-center justify-center gap-2 shrink-0">
                <i data-lucide="file-plus" class="w-4 h-4"></i>
                Ajukan dari portal
            </a>
        @endif
    </div>
    
    @if(session('success'))
        <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Pengajuan -->
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="file-text" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total Pengajuan</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['total'] ?? 0 }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1">Seluruh periode</p>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pending</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['pending'] ?? 0 }}</h3>
                    <p class="text-[10px] text-yellow-500 mt-1">Menunggu persetujuan</p>
                </div>
            </div>
        </div>

        <!-- Disetujui -->
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Disetujui</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['approved'] ?? 0 }}</h3>
                    <p class="text-[10px] text-green-500 mt-1">Telah dikonfirmasi</p>
                </div>
            </div>
        </div>

        <!-- Ditolak -->
        <div class="card-hover card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Ditolak</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['rejected'] ?? 0 }}</h3>
                    <p class="text-[10px] text-red-500 mt-1">Tidak disetujui</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaves List -->
    <div class="space-y-4">
        @forelse($leaves as $leave)
            <div class="card p-5 hover:shadow-md transition-all">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4">
                        @if($leave->user->photo)
                            <img src="{{ asset('storage/' . $leave->user->photo) }}" class="w-12 h-12 rounded-full object-cover">
                        @else
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-gold-400 to-gold-500 flex items-center justify-center text-navy-900 font-bold">
                                {{ substr($leave->user->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-base font-bold text-navy-800 dark:text-white">{{ $leave->user->name }}</h3>
                                @php
                                    $statusColors = [
                                        'Pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        'Approved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                        'Rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    ];
                                    $typeIcons = [
                                        'Sakit' => 'pill',
                                        'Izin' => 'file-check',
                                        'Dinas' => 'briefcase',
                                        'Cuti' => 'coffee',
                                    ];
                                @endphp
                                @php
                                    $statusLabels = [
                                        'Pending' => 'Menunggu',
                                        'Approved' => 'Disetujui',
                                        'Rejected' => 'Ditolak',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$leave->status] }}">
                                    {{ $statusLabels[$leave->status] ?? $leave->status }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                    <i data-lucide="{{ $typeIcons[$leave->type] ?? 'file' }}" class="w-3 h-3"></i>
                                    {{ $leave->type }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400 mb-2">
                                <span class="flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                    {{ $leave->duration }} Hari
                                </span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $leave->reason }}</p>
                            @if($leave->notes)
                                <div class="mt-3 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        <strong>Catatan:</strong> {{ $leave->notes }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($leave->status === 'Pending' && Auth::user()->isAdmin())
                            <form action="{{ route('leaves.approve', $leave) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs font-medium transition-colors">
                                    Setujui
                                </button>
                            </form>
                            <form action="{{ route('leaves.reject', $leave) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-xs font-medium transition-colors">
                                    Tolak
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('leaves.show', $leave) }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-medium transition-colors">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-16 text-center">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center">
                        <i data-lucide="file-text" class="w-10 h-10 text-slate-400"></i>
                    </div>
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada pengajuan</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Pengajuan izin dan sakit akan muncul di sini</p>
                    </div>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('leaves.create') }}" class="btn-primary flex items-center gap-2 text-sm">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Ajukan Izin
                        </a>
                    @else
                        <a href="{{ route('teacher.dashboard') }}#izin" class="btn-primary flex items-center gap-2 text-sm">
                            <i data-lucide="file-plus" class="w-4 h-4"></i>
                            Ajukan izin / sakit
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection