@php $isPiket = auth()->user()?->isGuruPiket(); @endphp
@extends(activeLayout())
@section('page-title', 'Approval Izin & Sakit')

@section('content')
<div class="fade-in space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i data-lucide="check-circle" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Approval Izin & Sakit</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Review dan setujui pengajuan izin guru</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="card p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0"></i>
        <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="card p-4">
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Menunggu</p>
            <h3 class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingCount }}</h3>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Disetujui</p>
            <h3 class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $approvedCount }}</h3>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Ditolak</p>
            <h3 class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $rejectedCount }}</h3>
        </div>
    </div>

    {{-- List --}}
    <div class="space-y-4">
        @forelse($leaves as $leave)
        <div class="card p-5 hover:shadow-lg transition-all">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4 flex-1 min-w-0">
                    <img src="{{ $leave->user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($leave->user->name ?? 'G').'&background=0F172A&color=fff' }}"
                         class="w-11 h-11 rounded-xl object-cover border-2 border-slate-200 dark:border-slate-700 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h3 class="text-sm font-bold text-navy-800 dark:text-white">{{ $leave->user->name ?? '-' }}</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                @if($leave->status === 'pending') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                @elseif($leave->status === 'approved') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                @else bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 @endif">
                                {{ ucfirst($leave->status) }}
                            </span>
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-full text-[10px] font-bold">
                                {{ ucfirst($leave->type ?? '-') }}
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 dark:text-slate-400 mb-1.5">
                            <span class="flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3 h-3"></i>
                                {{ optional($leave->start_date)->format('d M Y') }} – {{ optional($leave->end_date)->format('d M Y') }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                {{ $leave->duration ?? '-' }} Hari
                            </span>
                        </div>
                        <p class="text-sm text-slate-600 dark:text-slate-400 truncate">{{ $leave->reason }}</p>
                        @if($leave->admin_notes)
                        @php
                            $approverRolePiket = $leave->approvedBy?->role ?? '';
                            $labelPiket = match(true) {
                                in_array($approverRolePiket, ['admin','operator']) => 'Catatan Operator',
                                $approverRolePiket === 'guru_piket' => 'Catatan Guru Piket',
                                default => 'Catatan Peninjau',
                            };
                        @endphp
                        <p class="text-xs text-slate-500 dark:text-slate-500 mt-1 italic">{{ $labelPiket }}: {{ $leave->admin_notes }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-end sm:items-center gap-2 flex-shrink-0">
                    @if($leave->status === 'pending')
                    <form action="{{ $isPiket ? route('piket.leave-approval.approve', $leave) : route('leave-approval.approve', $leave) }}" method="POST">
                        @csrf
                        <input type="hidden" name="admin_notes" value="Disetujui oleh piket">
                        <button type="submit" class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i> Setujui
                        </button>
                    </form>
                    <form action="{{ $isPiket ? route('piket.leave-approval.reject', $leave) : route('leave-approval.reject', $leave) }}" method="POST">
                        @csrf
                        <input type="hidden" name="admin_notes" value="Ditolak oleh piket">
                        <button type="submit" onclick="return confirm('Yakin ingin menolak pengajuan ini?')"
                                class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i> Tolak
                        </button>
                    </form>
                    @endif
                    <a href="{{ $isPiket ? route('piket.leaves.show', $leave) : route('leaves.show', $leave) }}"
                       class="px-3 py-2 bg-navy-800 hover:bg-navy-900 text-white rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 whitespace-nowrap">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="card p-12 text-center">
            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="check-circle" class="w-8 h-8 text-slate-400"></i>
            </div>
            <h3 class="text-base font-bold text-navy-800 dark:text-white mb-1">Tidak Ada Pengajuan</h3>
            <p class="text-sm text-slate-500">Semua pengajuan sudah ditangani</p>
        </div>
        @endforelse

        {{ $leaves->links() }}
    </div>
</div>
<script>document.addEventListener('DOMContentLoaded', () => { if(window.lucide) lucide.createIcons(); });</script>
<style>.fade-in{animation:fadeIn .4s ease-out}@keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}</style>
@endsection
