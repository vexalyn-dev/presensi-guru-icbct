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
                <span>Export CSV</span>
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
        <form method="GET" action="{{ route('activity-logs.index') }}" class="flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari deskripsi atau IP address..."
                       class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
            </div>
            <select name="category" class="px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $key => $cat)
                    <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                @endforeach
            </select>
            <select name="user_id" class="px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
                <option value="">Semua User</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ request('user_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="px-4 py-2.5 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500">
            <button type="submit" class="px-5 py-2.5 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-xl text-sm font-semibold hover:opacity-90 transition-opacity">
                Filter
            </button>
            @if(request()->hasAny(['search', 'category', 'user_id', 'date_from', 'date_to']))
                <a href="{{ route('activity-logs.index') }}" class="px-4 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                    Reset
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
                <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer group"
                     onclick="showLogDetail({{ $log->id }})">
                    <div class="flex items-start gap-4">
                        {{-- Icon --}}
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                            {{ $log->color === 'green'  ? 'bg-green-100 dark:bg-green-900/30' : '' }}
                            {{ $log->color === 'blue'   ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}
                            {{ $log->color === 'purple' ? 'bg-purple-100 dark:bg-purple-900/30' : '' }}
                            {{ $log->color === 'indigo' ? 'bg-indigo-100 dark:bg-indigo-900/30' : '' }}
                            {{ $log->color === 'amber'  ? 'bg-amber-100 dark:bg-amber-900/30' : '' }}
                            {{ $log->color === 'slate'  ? 'bg-slate-100 dark:bg-slate-800' : '' }}">
                            <i data-lucide="{{ $log->icon }}" class="w-5 h-5
                                {{ $log->color === 'green'  ? 'text-green-600 dark:text-green-400' : '' }}
                                {{ $log->color === 'blue'   ? 'text-blue-600 dark:text-blue-400' : '' }}
                                {{ $log->color === 'purple' ? 'text-purple-600 dark:text-purple-400' : '' }}
                                {{ $log->color === 'indigo' ? 'text-indigo-600 dark:text-indigo-400' : '' }}
                                {{ $log->color === 'amber'  ? 'text-amber-600 dark:text-amber-400' : '' }}
                                {{ $log->color === 'slate'  ? 'text-slate-600 dark:text-slate-400' : '' }}">
                            </i>
                        </div>
                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-700 dark:text-slate-300 leading-snug">{{ $log->description }}</p>
                                    <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-slate-500 dark:text-slate-400">
                                        <span class="inline-flex items-center gap-1">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            {{ $log->created_at->diffForHumans() }}
                                        </span>
                                        @if($log->ip_address)
                                        <span class="inline-flex items-center gap-1">
                                            <i data-lucide="globe" class="w-3 h-3"></i>
                                            {{ $log->ip_address }}
                                        </span>
                                        @endif
                                        @if($log->device['device'] ?? null)
                                        <span class="inline-flex items-center gap-1">
                                            <i data-lucide="smartphone" class="w-3 h-3"></i>
                                            {{ $log->device['device'] }}
                                        </span>
                                        @endif
                                        @if($log->device['browser'] ?? null)
                                        <span class="inline-flex items-center gap-1">
                                            <i data-lucide="compass" class="w-3 h-3"></i>
                                            {{ $log->device['browser'] }}
                                        </span>
                                        @endif
                                        @if(isset(($log->properties ?? [])['location']['latitude']))
                                        <span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400">
                                            <i data-lucide="map-pin" class="w-3 h-3"></i>
                                            {{ $log->properties['location']['latitude'] }}, {{ $log->properties['location']['longitude'] }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                @if($log->user)
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 flex items-center justify-center text-white dark:text-navy-900 font-bold text-xs">
                                        {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                    </div>
                                    <div class="hidden sm:block">
                                        <p class="text-xs font-semibold text-navy-800 dark:text-white">{{ $log->user->name }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $log->user->teacher_code ?? $log->user->email }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <button class="p-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity hover:bg-slate-200 dark:hover:bg-slate-700 flex-shrink-0">
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                        </button>
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

{{-- Detail Modal --}}
<div id="logDetailModal" class="fixed inset-0 z-50 hidden" style="background: rgba(15,23,42,0.6); backdrop-filter: blur(8px);">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden" style="animation: modalSlideIn 0.3s ease-out;">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white dark:text-navy-900">Detail Aktivitas</h3>
                    <button onclick="document.getElementById('logDetailModal').classList.add('hidden')" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
                        <i data-lucide="x" class="w-5 h-5 text-white dark:text-navy-900"></i>
                    </button>
                </div>
            </div>
            <div id="logDetailContent" class="p-6">
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-navy-800 dark:border-gold-400"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Cleanup Modal --}}
<div id="cleanupModal" class="fixed inset-0 z-50 hidden" style="background: rgba(15,23,42,0.6); backdrop-filter: blur(8px);">
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
@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-20px) scale(0.95); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
</style>

<script>
async function showLogDetail(id) {
    const modal   = document.getElementById('logDetailModal');
    const content = document.getElementById('logDetailContent');
    modal.classList.remove('hidden');
    content.innerHTML = '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-navy-800 dark:border-gold-400"></div></div>';

    try {
        const res  = await fetch(`/activity-logs/${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await res.json();

        if (data.success) {
            const d       = data.data;
            const device  = d.device_info || {};
            const props   = d.properties  || {};
            const location = props.location || {};

            content.innerHTML = `
                <div class="space-y-4">
                    <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Deskripsi</p>
                        <p class="text-sm font-semibold text-navy-800 dark:text-white">${d.description}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                            <p class="text-[10px] text-blue-600 dark:text-blue-400 font-bold uppercase">Kategori</p>
                            <p class="text-sm font-semibold text-navy-800 dark:text-white capitalize">${d.category}</p>
                        </div>
                        <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-xl">
                            <p class="text-[10px] text-purple-600 dark:text-purple-400 font-bold uppercase">Tipe</p>
                            <p class="text-sm font-semibold text-navy-800 dark:text-white">${d.type}</p>
                        </div>
                    </div>
                    ${d.user ? `
                    <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">User</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-navy-800 to-navy-900 flex items-center justify-center text-white font-bold text-sm">
                                ${d.user.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-navy-800 dark:text-white">${d.user.name}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">${d.user.email}</p>
                                ${d.user.teacher_code ? `<p class="text-xs text-slate-400 font-mono">${d.user.teacher_code}</p>` : ''}
                            </div>
                        </div>
                    </div>` : ''}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase">IP Address</p>
                            <p class="text-sm font-mono font-semibold text-navy-800 dark:text-white">${d.ip_address || '-'}</p>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase">Waktu</p>
                            <p class="text-sm font-semibold text-navy-800 dark:text-white">${d.created_at}</p>
                        </div>
                    </div>
                    <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Device Info</p>
                        <div class="grid grid-cols-3 gap-2">
                            <div><p class="text-[10px] text-slate-400">OS</p><p class="text-xs font-semibold text-navy-800 dark:text-white">${device.os || '-'}</p></div>
                            <div><p class="text-[10px] text-slate-400">Browser</p><p class="text-xs font-semibold text-navy-800 dark:text-white">${device.browser || '-'}</p></div>
                            <div><p class="text-[10px] text-slate-400">Device</p><p class="text-xs font-semibold text-navy-800 dark:text-white">${device.device || '-'}</p></div>
                        </div>
                    </div>
                    ${location.map_url ? `
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-xl">
                        <p class="text-xs text-green-600 dark:text-green-400 font-bold mb-2">📍 Lokasi GPS</p>
                        <p class="text-xs font-mono text-slate-700 dark:text-slate-300 mb-2">Lat: ${location.latitude} | Lng: ${location.longitude}</p>
                        <a href="${location.map_url}" target="_blank" class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 dark:text-green-400 hover:underline">
                            🗺️ Buka di Google Maps
                        </a>
                    </div>` : ''}
                    ${props.classroom_name ? `
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                        <p class="text-xs text-amber-600 dark:text-amber-400 font-bold mb-2">🏫 Detail Kelas</p>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div><span class="text-slate-500">Kelas:</span> <span class="font-semibold">${props.classroom_name}</span></div>
                            <div><span class="text-slate-500">Kode:</span> <span class="font-mono">${props.classroom_code || '-'}</span></div>
                            <div><span class="text-slate-500">Jam ke:</span> <span class="font-semibold">${props.period || '-'}</span></div>
                            <div><span class="text-slate-500">Mapel:</span> <span class="font-semibold">${props.subject || '-'}</span></div>
                        </div>
                    </div>` : ''}
                </div>`;

            setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 100);
        }
    } catch (e) {
        content.innerHTML = '<p class="text-center text-sm text-red-500 py-4">Gagal memuat detail</p>';
    }
}

document.getElementById('logDetailModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
document.getElementById('cleanupModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});

document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();
});
</script>
@endsection
