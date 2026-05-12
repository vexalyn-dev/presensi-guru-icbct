@extends('layouts.app')

@section('page-title', 'Detail Pengajuan')

@section('content')
@php
    $isAdmin = Auth::user()->isAdmin();
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
<div class="max-w-3xl mx-auto space-y-6 fade-in">
    <div class="flex items-center gap-4">
        <a href="{{ $isAdmin ? route('leaves.index') : route('teacher.leaves') }}" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Detail pengajuan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                @if($isAdmin)
                    {{ $leave->user->name }}
                @else
                    Pengajuan Anda
                @endif
            </p>
        </div>
    </div>

    <div class="card p-6 md:p-8 space-y-6">
        <div class="flex flex-wrap items-center gap-2">
            @php
                $statusLabels = [
                    'Pending' => 'Menunggu',
                    'Approved' => 'Disetujui',
                    'Rejected' => 'Ditolak',
                ];
            @endphp
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                <i data-lucide="{{ $typeIcons[$leave->type] ?? 'file' }}" class="w-3 h-3"></i>
                {{ $leave->type }}
            </span>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$leave->status] ?? '' }}">
                {{ $statusLabels[$leave->status] ?? $leave->status }}
            </span>
        </div>

        @if($isAdmin)
            <div class="flex items-center gap-3 pb-6 border-b border-slate-200 dark:border-slate-700">
                @if($leave->user->photo)
                    <img src="{{ asset('storage/' . $leave->user->photo) }}" alt="" class="w-14 h-14 rounded-full object-cover">
                @else
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-gold-400 to-gold-500 flex items-center justify-center text-navy-900 font-bold text-lg">
                        {{ mb_substr($leave->user->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <p class="font-semibold text-navy-800 dark:text-white">{{ $leave->user->name }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $leave->user->email }}</p>
                </div>
            </div>
        @endif

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Mulai</dt>
                <dd class="mt-1 font-medium text-navy-800 dark:text-white">{{ $leave->start_date->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Selesai</dt>
                <dd class="mt-1 font-medium text-navy-800 dark:text-white">{{ $leave->end_date->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Durasi</dt>
                <dd class="mt-1 font-medium text-navy-800 dark:text-white">{{ $leave->duration }} hari</dd>
            </div>
        </dl>

        <div>
            <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Alasan</h3>
            <p class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-wrap">{{ $leave->reason }}</p>
        </div>

        @if($leave->attachment)
            <div>
                <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Lampiran</h3>
                <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                    <i data-lucide="paperclip" class="w-4 h-4"></i>
                    Buka lampiran
                </a>
            </div>
        @endif

        @if($leave->notes)
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-700/40 border border-slate-100 dark:border-slate-600">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Catatan</p>
                <p class="text-sm text-slate-700 dark:text-slate-200">{{ $leave->notes }}</p>
            </div>
        @endif

        @if($leave->approver)
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Diproses oleh <span class="font-medium text-slate-700 dark:text-slate-300">{{ $leave->approver->name }}</span>
                @if($leave->approved_at)
                    · {{ $leave->approved_at->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                @endif
            </p>
        @endif

        @if($isAdmin && $leave->status === 'Pending')
            <div class="space-y-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                <form action="{{ route('leaves.approve', $leave) }}" method="POST" class="flex flex-wrap items-end gap-3">
                    @csrf
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Catatan persetujuan (opsional)</label>
                        <input type="text" name="notes" maxlength="500" class="input-field w-full" placeholder="Opsional">
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-medium transition-colors shrink-0">
                        Setujui
                    </button>
                </form>
                <form action="{{ route('leaves.reject', $leave) }}" method="POST" class="space-y-2">
                    @csrf
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">Alasan penolakan</label>
                    <textarea name="notes" rows="2" required maxlength="500" class="input-field w-full resize-y" placeholder="Wajib diisi"></textarea>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-medium transition-colors">
                        Tolak pengajuan
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
