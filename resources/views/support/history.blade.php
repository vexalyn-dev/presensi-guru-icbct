@extends(auth()->user()->isAdmin() ? 'layouts.app' : 'layouts.teacher')
@section('page-title', 'Riwayat Laporan')
@section('content')
<div class="space-y-6 fade-in">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i data-lucide="clock" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Riwayat Laporan</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Semua tiket yang pernah Anda kirimkan</p>
            </div>
        </div>
        <a href="{{ route('support.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-semibold shadow-lg hover:opacity-90 transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Buat Laporan Baru
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 flex-shrink-0"></i>
        <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('warning'))
    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 flex items-center gap-3">
        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0"></i>
        <p class="text-sm font-medium text-amber-800 dark:text-amber-300">{{ session('warning') }}</p>
    </div>
    @endif

    @php
    $statusColors = [
        'new'         => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        'review'      => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'in_progress' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400',
        'testing'     => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
        'completed'   => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        'rejected'    => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        'on_hold'     => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
    ];
    $priorityColors = [
        'low'      => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        'medium'   => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'high'     => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
        'critical' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    ];
    $typeIcons = ['bug'=>'bug','feature'=>'lightbulb','maintenance'=>'wrench','question'=>'help-circle'];
    @endphp

    @if($tickets->isEmpty())
    {{-- Empty state --}}
    <div class="card p-16 text-center">
        <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-5">
            <i data-lucide="inbox" class="w-10 h-10 text-slate-300 dark:text-slate-600"></i>
        </div>
        <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-2">Belum ada laporan</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 max-w-xs mx-auto">Anda belum pernah mengirim laporan. Temui masalah? Laporkan sekarang!</p>
        <a href="{{ route('support.index') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-xl text-sm font-bold hover:opacity-90 transition-all shadow-lg">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Buat Laporan Pertama
        </a>
    </div>
    @else
    <div class="card overflow-hidden">
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @foreach($tickets as $ticket)
            <a href="{{ route('support.show', $ticket) }}"
               class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors group">

                {{-- Icon tipe --}}
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                    @if($ticket->type==='bug') bg-red-100 dark:bg-red-900/30
                    @elseif($ticket->type==='feature') bg-amber-100 dark:bg-amber-900/30
                    @elseif($ticket->type==='maintenance') bg-blue-100 dark:bg-blue-900/30
                    @else bg-purple-100 dark:bg-purple-900/30 @endif">
                    <i data-lucide="{{ $typeIcons[$ticket->type] ?? 'help-circle' }}" class="w-5 h-5
                        @if($ticket->type==='bug') text-red-600 dark:text-red-400
                        @elseif($ticket->type==='feature') text-amber-600 dark:text-amber-400
                        @elseif($ticket->type==='maintenance') text-blue-600 dark:text-blue-400
                        @else text-purple-600 dark:text-purple-400 @endif"></i>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <p class="text-sm font-semibold text-navy-800 dark:text-white truncate">{{ $ticket->title }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($ticket->ticket_id)
                        <span class="text-[10px] font-mono text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md">{{ $ticket->ticket_id }}</span>
                        @endif
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $statusColors[$ticket->status] ?? '' }}">
                            {{ $statusLabels[$ticket->status]['label'] ?? ucfirst($ticket->status) }}
                        </span>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $priorityColors[$ticket->priority] ?? '' }}">
                            {{ $priorityLabels[$ticket->priority]['label'] ?? ucfirst($ticket->priority) }}
                        </span>
                        <span class="text-[10px] text-slate-400">{{ $ticket->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-slate-500 transition-colors flex-shrink-0"></i>
            </a>
            @endforeach
        </div>
        @if($tickets->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
    @endif
</div>
<script>document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });</script>
<style>.fade-in{animation:fadeIn .4s ease-out}@keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}</style>
@endsection
