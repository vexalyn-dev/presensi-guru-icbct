@extends(activeLayout())
@section('page-title', 'Detail Tiket')
@php
    $user = auth()->user();
    $rp = $user->canAccessAdmin() ? 'admin.support' : ($user->isGuruPiket() ? 'piket.support' : 'teacher.support');
@endphp
@section('content')
<div class="space-y-6 fade-in">

    <div class="flex items-center gap-4">
        <a href="{{ route($rp . '.history') }}"
           class="w-10 h-10 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center justify-center hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm group">
            <i data-lucide="arrow-left" class="w-4 h-4 text-slate-500 group-hover:-translate-x-0.5 transition-transform"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-navy-800 dark:text-white">Detail Tiket</h1>
            @if($ticket->ticket_id)
            <p class="text-xs font-mono text-slate-400 mt-0.5">{{ $ticket->ticket_id }}</p>
            @endif
        </div>
    </div>

    @php
    $statusColors = [
        'new'=>'bg-blue-100 text-blue-700','review'=>'bg-amber-100 text-amber-700',
        'in_progress'=>'bg-indigo-100 text-indigo-700','testing'=>'bg-purple-100 text-purple-700',
        'completed'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700','on_hold'=>'bg-slate-100 text-slate-600',
    ];
    $priorityColors=['low'=>'bg-green-100 text-green-700','medium'=>'bg-amber-100 text-amber-700','high'=>'bg-orange-100 text-orange-700','critical'=>'bg-red-100 text-red-700'];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Info utama --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="card p-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <h2 class="text-lg font-bold text-navy-800 dark:text-white leading-snug">{{ $ticket->title }}</h2>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusColors[$ticket->status] ?? '' }}">
                            {{ $statusLabels[$ticket->status]['label'] ?? ucfirst($ticket->status) }}
                        </span>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $priorityColors[$ticket->priority] ?? '' }}">
                            {{ $priorityLabels[$ticket->priority]['label'] ?? '' }}
                        </span>
                    </div>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $ticket->description }}</p>
            </div>

            @if($ticket->extra_fields && array_filter($ticket->extra_fields))
            <div class="card p-6 space-y-4">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Detail Tambahan</p>
                @foreach($ticket->extra_fields as $key => $val)
                @if($val)
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wide mb-1">{{ str_replace('_', ' ', $key) }}</p>
                    <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line">{{ $val }}</p>
                </div>
                @endif
                @endforeach
            </div>
            @endif

            @if($ticket->attachments && count($ticket->attachments))
            <div class="card p-6">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Lampiran</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($ticket->attachments as $att)
                    <a href="{{ $att['url'] }}" target="_blank"
                       class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors group">
                        <i data-lucide="paperclip" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-navy-800 dark:text-white truncate">{{ $att['name'] }}</p>
                            <p class="text-[10px] text-slate-400">{{ number_format($att['size']/1024, 0) }} KB</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar info --}}
        <div class="space-y-5">
            <div class="card p-5 space-y-4">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Informasi Tiket</p>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-slate-500">Jenis</span><span class="font-semibold text-navy-800 dark:text-white">{{ $typeLabels[$ticket->type]['label'] ?? '' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Kategori</span><span class="font-semibold text-navy-800 dark:text-white">{{ $ticket->category ?? '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Dibuat</span><span class="font-semibold text-navy-800 dark:text-white">{{ $ticket->created_at->format('d M Y H:i') }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Diperbarui</span><span class="font-semibold text-navy-800 dark:text-white">{{ $ticket->updated_at->diffForHumans() }}</span></div>
                    @if($ticket->vexalyn_sent_at)
                    <div class="flex justify-between"><span class="text-slate-500">Dikirim</span><span class="text-green-600 font-semibold flex items-center gap-1"><i data-lucide="check" class="w-3 h-3"></i> {{ $ticket->vexalyn_sent_at->format('d M H:i') }}</span></div>
                    @endif
                </div>
            </div>

            @if($ticket->metadata)
            <div class="card p-5 space-y-3">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Info Sistem</p>
                <div class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
                    @foreach(['browser'=>'Browser','os'=>'OS','device'=>'Device','resolution'=>'Resolusi'] as $k=>$label)
                    @if($ticket->metadata[$k] ?? null)
                    <div class="flex justify-between"><span>{{ $label }}</span><span class="font-semibold text-navy-800 dark:text-white">{{ $ticket->metadata[$k] }}</span></div>
                    @endif
                    @endforeach
                    @if($ticket->metadata['url'] ?? null)
                    <div class="pt-1 border-t border-slate-100 dark:border-slate-800">
                        <p class="text-[10px] text-slate-400 mb-1">URL</p>
                        <p class="font-mono text-[10px] text-slate-500 break-all">{{ $ticket->metadata['url'] }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- GitHub Issue link jika ada --}}
            @if($ticket->github_issue_url)
            <div class="card p-5 border-2 border-blue-200 dark:border-blue-800 bg-blue-50/50 dark:bg-blue-900/10">
                <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="github" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                    <p class="text-xs font-bold text-blue-800 dark:text-blue-400">Terdaftar di GitHub Issues</p>
                </div>
                <a href="{{ $ticket->github_issue_url }}" target="_blank"
                   class="text-xs text-blue-600 dark:text-blue-400 underline underline-offset-2 hover:text-blue-800 break-all">
                    {{ $ticket->github_issue_url }}
                </a>
            </div>
            @endif

            {{-- ClickUp Task link jika ada --}}
            @if($ticket->clickup_task_url)
            <div class="card p-5 border-2 border-purple-200 dark:border-purple-800 bg-purple-50/50 dark:bg-purple-900/10">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 12.5L8.5 18l4-4.5L17 18l4-5.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                    <p class="text-xs font-bold text-purple-800 dark:text-purple-400">Terdaftar di ClickUp</p>
                </div>
                <a href="{{ $ticket->clickup_task_url }}" target="_blank"
                   class="text-xs text-purple-600 dark:text-purple-400 underline underline-offset-2 hover:text-purple-800 break-all">
                    {{ $ticket->clickup_task_url }}
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
<script>document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });</script>
<style>.fade-in{animation:fadeIn .4s ease-out}@keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}</style>
@endsection
