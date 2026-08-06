@extends(activeLayout())
@section('page-title', 'Riwayat Laporan')
@php
    $user = auth()->user();
    $rp = $user->canAccessAdmin() ? 'admin.support' : ($user->isGuruPiket() ? 'piket.support' : 'teacher.support');

    $typeLabels = [
        'bug'         => ['label' => 'Laporkan Bug',    'icon' => 'bug',          'bg' => 'bg-red-100 dark:bg-red-900/30',    'text' => 'text-red-600 dark:text-red-400'],
        'feature'     => ['label' => 'Request Fitur',   'icon' => 'lightbulb',    'bg' => 'bg-amber-100 dark:bg-amber-900/30','text' => 'text-amber-600 dark:text-amber-400'],
        'maintenance' => ['label' => 'Maintenance',     'icon' => 'wrench',        'bg' => 'bg-blue-100 dark:bg-blue-900/30',  'text' => 'text-blue-600 dark:text-blue-400'],
        'question'    => ['label' => 'Pertanyaan',      'icon' => 'help-circle',   'bg' => 'bg-purple-100 dark:bg-purple-900/30','text' => 'text-purple-600 dark:text-purple-400'],
    ];
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
@endphp

@section('content')
<div class="space-y-5 fade-in" x-data="historyPage()">

    {{-- ── HEADER ──────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                <i data-lucide="clock" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Riwayat Laporan</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Semua laporan yang pernah kamu kirimkan</p>
            </div>
        </div>

        {{-- Action buttons --}}
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Tombol Pilih --}}
            <button @click="toggleSelectMode()"
                    :class="selectMode ? 'bg-navy-800 text-white dark:bg-gold-400 dark:text-navy-900' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold hover:opacity-90 transition-all shadow-sm">
                <i data-lucide="check-square" class="w-4 h-4"></i>
                <span x-text="selectMode ? 'Batalkan' : 'Pilih'"></span>
            </button>

            {{-- Hapus terpilih (muncul saat ada yang dipilih) --}}
            <button x-show="selected.length > 0"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    @click="confirmBulkDelete()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition-all shadow-sm shadow-red-600/20">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                Hapus (<span x-text="selected.length"></span>)
            </button>

            <a href="{{ route($rp) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 rounded-xl text-sm font-semibold shadow-lg hover:opacity-90 transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Buat Laporan Baru
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
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

    {{-- ── SELECT-ALL BAR (muncul saat mode pilih aktif) ─────── --}}
    <div x-show="selectMode"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="flex items-center gap-3 px-4 py-3 bg-navy-800/5 dark:bg-gold-400/10 border border-navy-800/20 dark:border-gold-400/20 rounded-xl">

        {{-- Stylish checkbox pilih semua --}}
        <button @click="toggleSelectAll()" class="flex items-center gap-2.5 flex-1 text-left group">
            <div class="relative w-5 h-5 flex-shrink-0">
                <div :class="allSelected ? 'bg-navy-800 dark:bg-gold-400 border-navy-800 dark:border-gold-400' : 'bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 group-hover:border-navy-800 dark:group-hover:border-gold-400'"
                     class="w-5 h-5 rounded-md border-2 transition-all duration-150 flex items-center justify-center">
                    <svg x-show="allSelected" class="w-3 h-3 text-white dark:text-navy-900" fill="none" viewBox="0 0 12 12" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2 6l3 3 5-5"/>
                    </svg>
                    <div x-show="someSelected && !allSelected" class="w-2 h-0.5 bg-navy-800 dark:bg-gold-400 rounded-full"></div>
                </div>
            </div>
            <span class="text-sm font-semibold text-navy-800 dark:text-white">
                <span x-show="!allSelected">Pilih semua</span>
                <span x-show="allSelected">Batal semua</span>
            </span>
        </button>

        <span class="text-xs text-slate-500 dark:text-slate-400" x-show="selected.length > 0">
            <span x-text="selected.length"></span> dipilih
        </span>
    </div>

    @if($tickets->isEmpty())
    {{-- Empty state --}}
    <div class="card p-16 text-center">
        <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-5">
            <i data-lucide="inbox" class="w-10 h-10 text-slate-300 dark:text-slate-600"></i>
        </div>
        <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-2">Belum ada laporan</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 max-w-xs mx-auto">Belum pernah ngirim laporan. Ada masalah? Laporin sekarang!</p>
        <a href="{{ route($rp) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-xl text-sm font-bold hover:opacity-90 transition-all shadow-lg">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Buat Laporan Pertama
        </a>
    </div>

    @else
    <div class="card overflow-hidden">
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @foreach($tickets as $ticket)
            @php $tCfg = $typeLabels[$ticket->type] ?? $typeLabels['question']; @endphp

            <div class="relative ticket-row group"
                 data-ticket-id="{{ $ticket->id }}"
                 data-delete-url="{{ route($rp . '.destroy', $ticket) }}"
                 data-show-url="{{ route($rp . '.show', $ticket) }}"
                 data-ticket-title="{{ $ticket->title }}">

                <div class="flex items-center gap-3 px-4 py-3.5 transition-colors"
                     :class="selectMode ? 'hover:bg-slate-50 dark:hover:bg-slate-800/40 cursor-pointer' : ''">

                    {{-- ── Stylish Checkbox (hanya muncul saat mode pilih) ── --}}
                    <div x-show="selectMode"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-75"
                         x-transition:enter-end="opacity-100 scale-100"
                         @click.stop="toggleItem({{ $ticket->id }})"
                         class="flex-shrink-0 cursor-pointer">
                        <div class="relative w-5 h-5">
                            <div :class="selected.includes({{ $ticket->id }}) ? 'bg-navy-800 dark:bg-gold-400 border-navy-800 dark:border-gold-400 scale-100' : 'bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 hover:border-navy-600 dark:hover:border-gold-400 scale-100'"
                                 class="w-5 h-5 rounded-md border-2 transition-all duration-150 flex items-center justify-center shadow-sm">
                                <svg x-show="selected.includes({{ $ticket->id }})"
                                     class="w-3 h-3 text-white dark:text-navy-900"
                                     fill="none" viewBox="0 0 12 12" stroke="currentColor" stroke-width="2.5"
                                     style="display:none">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2 6l3 3 5-5"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- ── Klik area: navigasi kalau bukan mode pilih ── --}}
                    <div class="flex items-center gap-3 flex-1 min-w-0"
                         @click="selectMode ? toggleItem({{ $ticket->id }}) : (window.location.href = '{{ route($rp . '.show', $ticket) }}')"
                         :class="selectMode ? 'cursor-pointer' : 'cursor-pointer'">

                        {{-- Icon tipe --}}
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $tCfg['bg'] }}">
                            <i data-lucide="{{ $tCfg['icon'] }}" class="w-5 h-5 {{ $tCfg['text'] }}"></i>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            {{-- Baris 1: label tipe + badge role --}}
                            @php
                                $roleMap = [
                                    'admin'      => ['label' => 'Admin',      'cls' => 'bg-navy-100 text-navy-700 dark:bg-navy-800/40 dark:text-navy-300'],
                                    'operator'   => ['label' => 'Operator',   'cls' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400'],
                                    'guru'       => ['label' => 'Guru',        'cls' => 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400'],
                                    'guru_piket' => ['label' => 'Guru Piket', 'cls' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400'],
                                ];
                                $role    = $ticket->user?->role ?? 'guru';
                                $roleCfg = $roleMap[$role] ?? ['label' => ucfirst($role), 'cls' => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400'];
                            @endphp
                            <div class="flex items-center gap-1.5 flex-wrap mb-0.5">
                                <span class="text-[10px] font-bold {{ $tCfg['text'] }} leading-none">{{ $tCfg['label'] }}</span>
                                <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-md {{ $roleCfg['cls'] }}">{{ $roleCfg['label'] }}</span>
                            </div>

                            {{-- Baris 2: judul --}}
                            <p class="text-sm font-semibold text-navy-800 dark:text-white truncate mb-1 group-hover:text-navy-600 dark:group-hover:text-gold-400 transition-colors">
                                {{ $ticket->title }}
                            </p>

                            {{-- Baris 3: status, prioritas, waktu --}}
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $statusColors[$ticket->status] ?? '' }}">
                                    {{ $statusLabels[$ticket->status]['label'] ?? ucfirst($ticket->status) }}
                                </span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $priorityColors[$ticket->priority] ?? '' }}">
                                    {{ $priorityLabels[$ticket->priority]['label'] ?? ucfirst($ticket->priority) }}
                                </span>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500">{{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Chevron (hilang saat mode pilih) --}}
                    <i x-show="!selectMode" data-lucide="chevron-right"
                       class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-slate-500 transition-colors flex-shrink-0"></i>
                </div>
            </div>
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

{{-- ── Context Menu (klik kanan) ───────────────────────────── --}}
<div id="ctx-menu"
     class="fixed z-[9999] hidden min-w-[172px] bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl overflow-hidden py-1.5"
     style="top:0;left:0">
    <button id="ctx-view"
            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60 transition-colors">
        <i data-lucide="eye" class="w-4 h-4 text-slate-400"></i>
        Lihat Detail
    </button>
    <div class="h-px bg-slate-100 dark:bg-slate-700 mx-3 my-1"></div>
    <button id="ctx-delete"
            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
        <i data-lucide="trash-2" class="w-4 h-4"></i>
        Hapus Laporan
    </button>
</div>

{{-- Hidden forms --}}
<form id="delete-form" method="POST" class="hidden">
    @csrf @method('DELETE')
</form>
<form id="bulk-delete-form" method="POST" action="" class="hidden">
    @csrf @method('DELETE')
    <div id="bulk-ids-container"></div>
</form>

<script>
function historyPage() {
    return {
        selectMode: false,
        selected: [],

        get allSelected()  { return this.totalCount > 0 && this.selected.length === this.totalCount; },
        get someSelected() { return this.selected.length > 0 && !this.allSelected; },
        get totalCount()   { return document.querySelectorAll('.ticket-row').length; },

        toggleSelectMode() {
            this.selectMode = !this.selectMode;
            if (!this.selectMode) this.selected = [];
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },

        toggleItem(id) {
            const idx = this.selected.indexOf(id);
            if (idx === -1) this.selected.push(id);
            else            this.selected.splice(idx, 1);
        },

        toggleSelectAll() {
            if (this.allSelected) {
                this.selected = [];
            } else {
                this.selected = Array.from(document.querySelectorAll('.ticket-row'))
                    .map(r => parseInt(r.dataset.ticketId));
            }
        },

        confirmBulkDelete() {
            if (this.selected.length === 0) return;
            if (!confirm(`Hapus ${this.selected.length} laporan yang dipilih?\n\nTindakan ini tidak bisa dibatalkan.`)) return;

            const form      = document.getElementById('bulk-delete-form');
            const container = document.getElementById('bulk-ids-container');

            form.action = '{{ route($rp . ".bulk-destroy") }}';
            container.innerHTML = '';
            this.selected.forEach(id => {
                const inp = document.createElement('input');
                inp.type  = 'hidden';
                inp.name  = 'ids[]';
                inp.value = id;
                container.appendChild(inp);
            });
            form.submit();
        },
    };
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();

    const menu      = document.getElementById('ctx-menu');
    const btnView   = document.getElementById('ctx-view');
    const btnDelete = document.getElementById('ctx-delete');
    const form      = document.getElementById('delete-form');
    let   activeRow = null;

    function hideMenu() { menu.classList.add('hidden'); activeRow = null; }
    document.addEventListener('click', hideMenu);
    document.addEventListener('scroll', hideMenu, true);
    window.addEventListener('resize', hideMenu);

    document.querySelectorAll('.ticket-row').forEach(row => {
        row.addEventListener('contextmenu', e => {
            // Jangan tampilkan context menu saat mode pilih
            const alpineEl = document.querySelector('[x-data]');
            if (alpineEl && alpineEl._x_dataStack && alpineEl._x_dataStack[0].selectMode) return;

            e.preventDefault();
            e.stopPropagation();
            activeRow = row;

            const menuW = 188, menuH = 100;
            let x = e.clientX, y = e.clientY;
            if (x + menuW > window.innerWidth)  x = window.innerWidth  - menuW - 8;
            if (y + menuH > window.innerHeight) y = window.innerHeight - menuH - 8;

            menu.style.left = x + 'px';
            menu.style.top  = y + 'px';
            menu.classList.remove('hidden');
            if (window.lucide) lucide.createIcons();
        });
    });

    btnView.addEventListener('click', e => {
        e.stopPropagation();
        if (!activeRow) return;
        hideMenu();
        window.location.href = activeRow.dataset.showUrl;
    });

    btnDelete.addEventListener('click', e => {
        e.stopPropagation();
        if (!activeRow) return;
        const title     = activeRow.dataset.ticketTitle;
        const deleteUrl = activeRow.dataset.deleteUrl;
        hideMenu();
        if (!confirm(`Hapus laporan "${title}"?\n\nTindakan ini tidak bisa dibatalkan.`)) return;
        form.action = deleteUrl;
        form.submit();
    });
});
</script>

<style>
.fade-in { animation: fadeIn .4s ease-out; }
@keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
#ctx-menu { animation: ctxIn .12s ease-out; }
@keyframes ctxIn { from { opacity:0; transform:scale(.95) translateY(-4px); } to { opacity:1; transform:scale(1) translateY(0); } }
</style>
@endsection
