@extends('layouts.app')
@section('page-title', 'Log Aktivitas')
@section('content')
<div class="space-y-6 fade-in">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i data-lucide="scroll-text" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-navy-800 dark:text-white tracking-tight">Log Aktivitas</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Audit trail & riwayat semua aktivitas sistem</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('activity-logs.export', request()->query()) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition-all shadow-lg hover:shadow-xl">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Export Excel</span>
            </a>
            <button type="button" onclick="document.getElementById('cleanupModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition-all shadow-lg">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                <span>Bersihkan</span>
            </button>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="card p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
            <p class="text-sm text-green-800 dark:text-green-300 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="activity" class="w-6 h-6 text-slate-600 dark:text-slate-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total Log</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ number_format($stats['total']) }}</h3>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="calendar" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Hari Ini</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['today'] }}</h3>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="scan-line" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Presensi Hari Ini</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['attendance'] }}</h3>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="log-in" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Login Hari Ini</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $stats['auth'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card p-4">
        <form method="GET" action="{{ route('activity-logs.index') }}" id="filter-form" class="flex flex-wrap gap-3">
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px]">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari deskripsi atau IP address..."
                       class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
            </div>

            {{-- Dropdown Kategori --}}
            <div x-data="{
                    open: false,
                    selected: '{{ request('category') }}',
                    selectedLabel: '{{ request('category') ? ($categories[request('category')]['label'] ?? 'Semua Kategori') : 'Semua Kategori' }}',
                    choose(val, label) { this.selected = val; this.selectedLabel = label; this.open = false; document.getElementById('cat-input').value = val; }
                 }" class="relative">
                <input type="hidden" name="category" id="cat-input" value="{{ request('category') }}">
                <button type="button" @click="open = !open"
                        class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all min-w-[170px] justify-between">
                    <span x-text="selectedLabel"></span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute top-full left-0 mt-1 w-52 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-xl z-50 overflow-hidden">
                    <button type="button" @click="choose('', 'Semua Kategori')"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-left hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                            :class="selected === '' ? 'font-semibold text-navy-800 dark:text-gold-400' : 'text-slate-600 dark:text-slate-300'">
                        <i data-lucide="layers" class="w-4 h-4 opacity-60"></i> Semua Kategori
                    </button>
                    @foreach($categories as $key => $cat)
                    <button type="button" @click="choose('{{ $key }}', '{{ $cat['label'] }}')"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-left hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                            :class="selected === '{{ $key }}' ? 'font-semibold text-navy-800 dark:text-gold-400' : 'text-slate-600 dark:text-slate-300'">
                        <i data-lucide="{{ $cat['icon'] }}" class="w-4 h-4 opacity-60"></i> {{ $cat['label'] }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Dropdown User --}}
            <div x-data="{
                    open: false,
                    selected: '{{ request('user_id') }}',
                    selectedLabel: '{{ request('user_id') ? ($teachers->firstWhere('id', request('user_id'))?->name ?? 'Semua Guru') : 'Semua Guru' }}',
                    choose(val, label) { this.selected = val; this.selectedLabel = label; this.open = false; document.getElementById('user-input').value = val; }
                 }" class="relative">
                <input type="hidden" name="user_id" id="user-input" value="{{ request('user_id') }}">
                <button type="button" @click="open = !open"
                        class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all min-w-[170px] justify-between">
                    <span x-text="selectedLabel" class="truncate max-w-[120px]"></span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute top-full left-0 mt-1 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-xl z-50 overflow-hidden max-h-60 overflow-y-auto">
                    <button type="button" @click="choose('', 'Semua Guru')"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-left hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                            :class="selected === '' ? 'font-semibold text-navy-800 dark:text-gold-400' : 'text-slate-600 dark:text-slate-300'">
                        <i data-lucide="users" class="w-4 h-4 opacity-60"></i> Semua Guru
                    </button>
                    @foreach($teachers as $teacher)
                    <button type="button" @click="choose('{{ $teacher->id }}', '{{ addslashes($teacher->name) }}')"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-left hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                            :class="selected === '{{ $teacher->id }}' ? 'font-semibold text-navy-800 dark:text-gold-400' : 'text-slate-600 dark:text-slate-300'">
                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center text-white dark:text-navy-900 font-bold text-[10px] flex-shrink-0">
                            {{ strtoupper(substr($teacher->name, 0, 1)) }}
                        </div>
                        <span class="truncate">{{ $teacher->name }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Tanggal --}}
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="pl-9 pr-3 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                </div>
                <span class="text-slate-400 text-sm">â€”</span>
                <div class="relative">
                    <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="pl-9 pr-3 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                </div>
            </div>

            <button type="submit" class="px-5 py-2.5 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-xl text-sm font-semibold hover:opacity-90 transition-opacity flex items-center gap-2">
                <i data-lucide="search" class="w-4 h-4"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'category', 'user_id', 'date_from', 'date_to']))
                <a href="{{ route('activity-logs.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors flex items-center gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i> Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Category Pills --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('activity-logs.index') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ !request('category') ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow-lg' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
            Semua ({{ number_format($stats['total']) }})
        </a>
        @foreach($categories as $key => $cat)
            @php $count = \App\Models\ActivityLog::where('category', $key)->whereDate('created_at', today())->count(); @endphp
            <a href="{{ route('activity-logs.index', ['category' => $key]) }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all flex items-center gap-2 {{ request('category') === $key ? 'bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 shadow-lg' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                <i data-lucide="{{ $cat['icon'] }}" class="w-4 h-4"></i>
                {{ $cat['label'] }}
                <span class="text-xs opacity-75">({{ $count }})</span>
            </a>
        @endforeach
    </div>

    {{-- Activity Log List --}}
    <div class="card overflow-hidden">
        <div class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($logs as $log)
                @php
                    // Badge config berdasarkan type
                    $badgeConfig = match($log->type) {
                        'scan_in_daily','scan_in'    => ['label' => 'Masuk',    'bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'dot' => 'bg-emerald-500'],
                        'scan_out_daily','scan_out'  => ['label' => 'Keluar',   'bg' => 'bg-blue-100 dark:bg-blue-900/30',     'text' => 'text-blue-700 dark:text-blue-400',     'dot' => 'bg-blue-500'],
                        'login'                     => ['label' => 'Login',    'bg' => 'bg-violet-100 dark:bg-violet-900/30', 'text' => 'text-violet-700 dark:text-violet-400', 'dot' => 'bg-violet-500'],
                        'logout'                    => ['label' => 'Logout',   'bg' => 'bg-slate-100 dark:bg-slate-800',      'text' => 'text-slate-600 dark:text-slate-400',   'dot' => 'bg-slate-400'],
                        'teacher_created'           => ['label' => 'Tambah Guru',  'bg' => 'bg-amber-100 dark:bg-amber-900/30',  'text' => 'text-amber-700 dark:text-amber-400',  'dot' => 'bg-amber-500'],
                        'teacher_updated'           => ['label' => 'Ubah Guru',    'bg' => 'bg-orange-100 dark:bg-orange-900/30','text' => 'text-orange-700 dark:text-orange-400','dot' => 'bg-orange-500'],
                        'teacher_deleted'           => ['label' => 'Hapus Guru',   'bg' => 'bg-red-100 dark:bg-red-900/30',     'text' => 'text-red-700 dark:text-red-400',      'dot' => 'bg-red-500'],
                        'settings_change'           => ['label' => 'Pengaturan',   'bg' => 'bg-purple-100 dark:bg-purple-900/30','text' => 'text-purple-700 dark:text-purple-400','dot' => 'bg-purple-500'],
                        default                     => ['label' => ucfirst(str_replace('_',' ',$log->type)), 'bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-600 dark:text-slate-400', 'dot' => 'bg-slate-400'],
                    };
                    $photoUrl = $log->user?->photo ? asset('storage/' . $log->user->photo) : null;
                @endphp
                <div class="px-4 py-3.5 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors cursor-pointer group"
                     onclick="showLogDetail({{ $log->id }})">
                    <div class="flex items-center gap-3">

                        {{-- Avatar --}}
                        @if($log->user)
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}" alt="{{ $log->user->name }}"
                                     class="w-9 h-9 rounded-full object-cover flex-shrink-0"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 items-center justify-center text-white dark:text-navy-900 font-bold text-sm flex-shrink-0 hidden">
                                    {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                </div>
                            @else
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center text-white dark:text-navy-900 font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                </div>
                            @endif
                        @else
                            <div class="w-9 h-9 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                            </div>
                        @endif

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                {{-- Badge tipe --}}
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $badgeConfig['bg'] }} {{ $badgeConfig['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badgeConfig['dot'] }} flex-shrink-0"></span>
                                    {{ $badgeConfig['label'] }}
                                </span>
                                {{-- Nama user --}}
                                @if($log->user)
                                <span class="text-xs font-semibold text-navy-800 dark:text-slate-200 truncate">{{ $log->user->name }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-snug truncate">{{ $log->description }}</p>
                            <div class="flex flex-wrap items-center gap-2.5 mt-1.5 text-[11px] text-slate-400 dark:text-slate-500">
                                <span class="inline-flex items-center gap-1">
                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                    @php
                                        $diff = $log->created_at->diff(\Carbon\Carbon::now());
                                        if ($diff->days > 0) echo $diff->days . ' hari lalu';
                                        elseif ($diff->h > 0) echo $diff->h . ' jam ' . $diff->i . ' mnt lalu';
                                        elseif ($diff->i > 0) echo $diff->i . ' menit lalu';
                                        else echo 'Baru saja';
                                    @endphp
                                </span>
                                @if($log->ip_address)
                                <span class="inline-flex items-center gap-1">
                                    <i data-lucide="globe" class="w-3 h-3"></i>{{ $log->ip_address }}
                                </span>
                                @endif
                                @if($log->device['browser'] ?? null)
                                <span class="inline-flex items-center gap-1">
                                    <i data-lucide="compass" class="w-3 h-3"></i>{{ $log->device['browser'] }}
                                </span>
                                @endif
                            </div>
                        </div>

                        {{-- Kanan: icon kategori + chevron --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                {{ $log->color === 'green'  ? 'bg-green-100 dark:bg-green-900/30' : '' }}
                                {{ $log->color === 'blue'   ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}
                                {{ $log->color === 'purple' ? 'bg-purple-100 dark:bg-purple-900/30' : '' }}
                                {{ $log->color === 'indigo' ? 'bg-indigo-100 dark:bg-indigo-900/30' : '' }}
                                {{ $log->color === 'amber'  ? 'bg-amber-100 dark:bg-amber-900/30' : '' }}
                                {{ $log->color === 'slate'  ? 'bg-slate-100 dark:bg-slate-800' : '' }}">
                                <i data-lucide="{{ $log->icon }}" class="w-4 h-4
                                    {{ $log->color === 'green'  ? 'text-green-600 dark:text-green-400' : '' }}
                                    {{ $log->color === 'blue'   ? 'text-blue-600 dark:text-blue-400' : '' }}
                                    {{ $log->color === 'purple' ? 'text-purple-600 dark:text-purple-400' : '' }}
                                    {{ $log->color === 'indigo' ? 'text-indigo-600 dark:text-indigo-400' : '' }}
                                    {{ $log->color === 'amber'  ? 'text-amber-600 dark:text-amber-400' : '' }}
                                    {{ $log->color === 'slate'  ? 'text-slate-600 dark:text-slate-400' : '' }}"></i>
                            </div>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 group-hover:text-slate-500 transition-colors"></i>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-12 text-center">
                    <i data-lucide="inbox" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada log aktivitas</p>
                </div>
            @endforelse
        </div>
        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>


{{-- Detail Modal — dengan backdrop --}}
<div id="logDetailModal" class="fixed inset-0 z-50 hidden" style="background:rgba(15,23,42,0.55);backdrop-filter:blur(6px);">
    <div class="min-h-screen flex items-end sm:items-center justify-center sm:p-4">
        <div id="logDetailBox"
             class="bg-white dark:bg-slate-900 w-full sm:max-w-lg sm:rounded-2xl shadow-[0_25px_60px_-10px_rgba(0,0,0,0.22)] dark:shadow-[0_25px_60px_-10px_rgba(0,0,0,0.6)] overflow-hidden border border-slate-200/60 dark:border-slate-700/60"
             style="transform:translateY(40px) scale(0.97);opacity:0;transition:all 0.35s cubic-bezier(0.34,1.56,0.64,1);">

            {{-- Header --}}
            <div class="px-5 pt-5 pb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div id="modalIconWrap" class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <i data-lucide="scan-line" id="modalIcon" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-800 dark:text-white leading-tight">Detail Aktivitas</h3>
                        <p id="modalCategoryBadge" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">—</p>
                    </div>
                </div>
                <button onclick="closeLogDetail()"
                        class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-all hover:rotate-90 duration-300">
                    <i data-lucide="x" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                </button>
            </div>

            {{-- Content --}}
            <div id="logDetailContent" class="px-5 pb-5">
                <div class="flex items-center justify-center py-10 gap-3">
                    <div class="w-5 h-5 border-2 border-navy-800/20 border-t-navy-800 dark:border-gold-400/20 dark:border-t-gold-400 rounded-full animate-spin"></div>
                    <span class="text-sm text-slate-400">Memuat data...</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Cleanup Modal --}}
<div id="cleanupModal" class="fixed inset-0 z-50 hidden" style="background:rgba(15,23,42,0.6);backdrop-filter:blur(8px);">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <i data-lucide="trash-2" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-navy-800 dark:text-white">Bersihkan Log Lama</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Hapus log yang sudah berumur tertentu</p>
                </div>
            </div>
            <form method="POST" action="{{ route('activity-logs.cleanup') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">Hapus log lebih dari (hari)</label>
                    <select name="days" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                        <option value="30">30 hari</option>
                        <option value="60">60 hari</option>
                        <option value="90" selected>90 hari</option>
                        <option value="180">180 hari</option>
                        <option value="365">365 hari</option>
                    </select>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">⚠️ Tindakan ini tidak dapat dibatalkan</p>
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" onclick="document.getElementById('cleanupModal').classList.add('hidden')"
                            class="px-5 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold">
                        Hapus Log
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
#logDetailBox.modal-open {
    transform: translateY(0) scale(1) !important;
    opacity: 1 !important;
}
</style>

<script>
function openLogDetail() {
    const modal = document.getElementById('logDetailModal');
    const box   = document.getElementById('logDetailBox');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => requestAnimationFrame(() => box.classList.add('modal-open')));
}

function closeLogDetail() {
    const box = document.getElementById('logDetailBox');
    box.classList.remove('modal-open');
    setTimeout(() => document.getElementById('logDetailModal').classList.add('hidden'), 350);
}

document.getElementById('logDetailModal').addEventListener('click', function(e) {
    if (e.target === this) closeLogDetail();
});
document.getElementById('cleanupModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});

async function showLogDetail(id) {
    const content   = document.getElementById('logDetailContent');
    const catBadge  = document.getElementById('modalCategoryBadge');
    catBadge.textContent = '—';
    content.innerHTML = `<div class="flex items-center justify-center py-10 gap-3">
        <div class="w-5 h-5 border-2 border-navy-800/20 border-t-navy-800 dark:border-gold-400/20 dark:border-t-gold-400 rounded-full animate-spin"></div>
        <span class="text-sm text-slate-400">Memuat data...</span></div>`;
    openLogDetail();

    try {
        const res  = await fetch(`/activity-logs/${id}`, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (!data.success) throw new Error();

        const d      = data.data;
        const device = d.device_info || {};
        const props  = d.properties  || {};
        const loc    = props.location || {};

        catBadge.textContent = d.category;

        // Parse tanggal & jam dari "31 Jul 2026 13:37:52"
        const parts    = (d.created_at || '').split(' ');
        const timePart = (parts[3] || '').substring(0, 5);          // "13:37"
        const datePart = parts.slice(0, 3).join(' ');                // "31 Jul 2026"

        const userBlock = d.user ? `
        <div class="grid grid-cols-3 gap-3">
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/40 flex flex-col items-center justify-center gap-1.5">
                ${d.user.photo_url && !d.user.photo_url.includes('default-teacher')
                    ? `<img src="${d.user.photo_url}" class="w-11 h-11 rounded-full object-cover ring-2 ring-white dark:ring-slate-800 shadow-md">`
                    : `<div class="w-11 h-11 rounded-full bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center text-white dark:text-navy-900 font-extrabold text-base ring-2 ring-white dark:ring-slate-800 shadow-md">${d.user.name.charAt(0).toUpperCase()}</div>`
                }
                <p class="text-xs font-bold text-navy-800 dark:text-white text-center leading-tight">${d.user.name}</p>
                <p class="text-[10px] text-slate-400 text-center truncate w-full px-1">${d.user.teacher_code || d.user.email}</p>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/40 flex flex-col justify-center">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Tanggal</p>
                <p class="text-sm font-bold text-navy-800 dark:text-white">${datePart}</p>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/40 flex flex-col justify-center">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Pukul</p>
                <p class="text-2xl font-extrabold text-navy-800 dark:text-white tabular-nums tracking-tight">${timePart}</p>
            </div>
        </div>` : `
        <div class="grid grid-cols-2 gap-3">
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/40">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Tanggal</p>
                <p class="text-sm font-bold text-navy-800 dark:text-white">${datePart}</p>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/40">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Pukul</p>
                <p class="text-2xl font-extrabold text-navy-800 dark:text-white tabular-nums">${timePart}</p>
            </div>
        </div>`;

        const gpsBlock = loc.map_url ? `
        <a href="${loc.map_url}" target="_blank"
           class="flex items-center gap-3 p-3.5 rounded-xl bg-green-50 dark:bg-green-950/30 border border-green-100 dark:border-green-900/40 hover:bg-green-100 dark:hover:bg-green-950/50 transition-colors group">
            <div class="w-9 h-9 rounded-xl bg-green-500 flex items-center justify-center flex-shrink-0 shadow-sm group-hover:scale-110 transition-transform">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-xs font-bold text-green-700 dark:text-green-400">Lokasi GPS Terdeteksi</p>
                <p class="text-[10px] text-green-600 dark:text-green-500 font-mono">${loc.latitude}, ${loc.longitude}</p>
            </div>
            <svg class="w-4 h-4 text-green-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>` : '';

        const classBlock = props.classroom_name ? `
        <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-100 dark:border-amber-900/40">
            <p class="text-[10px] font-bold uppercase tracking-widest text-amber-500 mb-2.5">Detail Kelas</p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div><span class="text-slate-500">Kelas:</span> <span class="font-bold text-navy-800 dark:text-white ml-1">${props.classroom_name}</span></div>
                <div><span class="text-slate-500">Kode:</span> <span class="font-mono font-bold text-navy-800 dark:text-white ml-1">${props.classroom_code||'-'}</span></div>
                <div><span class="text-slate-500">Jam ke:</span> <span class="font-bold text-navy-800 dark:text-white ml-1">${props.period||'-'}</span></div>
                <div><span class="text-slate-500">Mapel:</span> <span class="font-bold text-navy-800 dark:text-white ml-1">${props.subject||'-'}</span></div>
            </div>
        </div>` : '';

        content.innerHTML = `
        <div class="space-y-3">
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/40">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Keterangan</p>
                <p class="text-sm font-semibold text-navy-800 dark:text-white leading-snug">${d.description}</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="p-3.5 rounded-xl bg-blue-50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/40">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-1">Kategori</p>
                    <p class="text-sm font-bold text-blue-700 dark:text-blue-300">${d.category}</p>
                </div>
                <div class="p-3.5 rounded-xl bg-violet-50 dark:bg-violet-950/30 border border-violet-100 dark:border-violet-900/40">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-violet-400 mb-1">Jenis Aktivitas</p>
                    <p class="text-sm font-bold text-violet-700 dark:text-violet-300">${d.type}</p>
                </div>
            </div>
            ${userBlock}
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/40">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2.5">Informasi Perangkat</p>
                <div class="grid grid-cols-2 gap-y-3 gap-x-4 text-xs">
                    <div><p class="text-[10px] text-slate-400 mb-0.5">IP Address</p><p class="font-bold text-navy-800 dark:text-white font-mono">${d.ip_address||'-'}</p></div>
                    <div><p class="text-[10px] text-slate-400 mb-0.5">Perangkat</p><p class="font-bold text-navy-800 dark:text-white">${device.device||'-'}</p></div>
                    <div><p class="text-[10px] text-slate-400 mb-0.5">Browser</p><p class="font-bold text-navy-800 dark:text-white">${device.browser||'-'}</p></div>
                    <div><p class="text-[10px] text-slate-400 mb-0.5">Sistem Operasi</p><p class="font-bold text-navy-800 dark:text-white">${device.os||'-'}</p></div>
                </div>
            </div>
            ${gpsBlock}
            ${classBlock}
        </div>`;

        if (window.lucide) lucide.createIcons();
    } catch(e) {
        document.getElementById('logDetailContent').innerHTML =
            '<p class="text-center text-sm text-red-500 py-8">Gagal memuat detail aktivitas</p>';
    }
}

document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });
</script>
@endsection
