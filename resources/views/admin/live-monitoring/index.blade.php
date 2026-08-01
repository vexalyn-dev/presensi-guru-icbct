@extends('layouts.app')
@section('page-title', 'Live Monitoring')

@section('content')
<div class="space-y-6 fade-in" id="live-root" x-data="liveMonitoring(@js($initialData))" x-init="init()">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="relative">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <i data-lucide="monitor" class="w-6 h-6 text-white"></i>
                </div>
                <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-white dark:border-slate-900"></span>
                </span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white tracking-tight">Live Monitoring</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Pantau aktivitas mengajar guru secara real-time</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-mono text-sm">
                <span class="text-slate-400 text-xs mr-1">Server</span>
                <span class="text-emerald-600 dark:text-emerald-400 font-bold" x-text="waktuServer"></span>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-400">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Diperbarui <span class="font-medium text-slate-600 dark:text-slate-300 ml-1" x-text="updatedAt"></span>
            </div>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="users" class="w-5 h-5 text-slate-500 dark:text-slate-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Total Guru</p>
                    <p class="text-2xl font-extrabold text-navy-800 dark:text-white" x-text="data.stats.total_guru"></p>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="log-in" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Sudah Masuk</p>
                    <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400" x-text="data.stats.sudah_masuk"></p>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="presentation" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Sedang Mengajar</p>
                    <p class="text-2xl font-extrabold text-blue-600 dark:text-blue-400" x-text="data.stats.sedang_mengajar"></p>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="user-x" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Belum Masuk</p>
                    <p class="text-2xl font-extrabold text-red-600 dark:text-red-400" x-text="data.stats.belum_masuk"></p>
                </div>
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="log-out" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Sudah Keluar</p>
                    <p class="text-2xl font-extrabold text-amber-600 dark:text-amber-400" x-text="data.stats.sudah_keluar"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- GRID UTAMA: Sedang Mengajar + Belum Scan --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Sedang Mengajar --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-emerald-50/50 dark:bg-emerald-900/10">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                        <i data-lucide="presentation" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <h2 class="text-sm font-bold text-navy-800 dark:text-white">Sedang Mengajar</h2>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 text-xs font-bold"
                      x-text="data.sedang_mengajar.length + ' guru'"></span>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-96 overflow-y-auto">
                <template x-for="item in data.sedang_mengajar" :key="item.id">
                    <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <template x-if="item.user.photo">
                            <img :src="item.user.photo" :alt="item.user.name" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                        </template>
                        <template x-if="!item.user.photo">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                 x-text="item.user.initial"></div>
                        </template>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-navy-800 dark:text-white truncate" x-text="item.user.name"></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate"
                               x-text="item.subject + ' • ' + item.classroom + ' • Jam ke-' + item.period"></p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 font-mono" x-text="formatDuration(item.timestamp_masuk)"></p>
                            <p class="text-[10px] text-slate-400">Durasi</p>
                        </div>
                    </div>
                </template>
                <div x-show="data.sedang_mengajar.length === 0" class="px-5 py-10 text-center">
                    <i data-lucide="inbox" class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-2"></i>
                    <p class="text-sm text-slate-400">Tidak ada guru yang sedang mengajar</p>
                </div>
            </div>
        </div>

        {{-- Belum Scan Masuk --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-red-50/50 dark:bg-red-900/10">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                        <i data-lucide="user-x" class="w-4 h-4 text-red-600 dark:text-red-400"></i>
                    </div>
                    <h2 class="text-sm font-bold text-navy-800 dark:text-white">Belum Scan Masuk</h2>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400 text-xs font-bold"
                      x-text="data.belum_scan_masuk.length + ' guru'"></span>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-96 overflow-y-auto">
                <template x-for="(item, i) in data.belum_scan_masuk" :key="i">
                    <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <template x-if="item.user.photo">
                            <img :src="item.user.photo" :alt="item.user.name" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                        </template>
                        <template x-if="!item.user.photo">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                                 :class="item.status === 'terlambat_parah' ? 'bg-gradient-to-br from-red-500 to-red-600' : 'bg-gradient-to-br from-amber-500 to-amber-600'"
                                 x-text="item.user.initial"></div>
                        </template>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-navy-800 dark:text-white truncate" x-text="item.user.name"></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate"
                               x-text="item.subject + ' • ' + item.classroom"></p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Jadwal: <span x-text="item.jam_mulai"></span></p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold"
                                  :class="item.status === 'terlambat_parah'
                                    ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
                                    : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'"
                                  x-text="item.terlambat_menit + ' mnt'"></span>
                            <p class="text-[10px] text-slate-400 mt-0.5"
                               x-text="item.status === 'terlambat_parah' ? 'Terlambat Parah' : 'Belum Scan'"></p>
                        </div>
                    </div>
                </template>
                <div x-show="data.belum_scan_masuk.length === 0" class="px-5 py-10 text-center">
                    <i data-lucide="check-circle" class="w-8 h-8 text-emerald-400 mx-auto mb-2"></i>
                    <p class="text-sm text-slate-400">Semua guru sudah scan masuk! 🎉</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Masih di Sekolah --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-blue-50/50 dark:bg-blue-900/10">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i data-lucide="map-pin" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                </div>
                <h2 class="text-sm font-bold text-navy-800 dark:text-white">Guru Masih di Sekolah (Belum Scan Keluar)</h2>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 text-xs font-bold"
                  x-text="data.belum_scan_keluar.length + ' guru'"></span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-left">
                        <th class="px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Guru</th>
                        <th class="px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status Presensi</th>
                        <th class="px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Durasi di Sekolah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                    <template x-for="(item, i) in data.belum_scan_keluar" :key="i">
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <template x-if="item.user.photo">
                                        <img :src="item.user.photo" :alt="item.user.name" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                    </template>
                                    <template x-if="!item.user.photo">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0"
                                             x-text="item.user.initial"></div>
                                    </template>
                                    <div>
                                        <p class="font-semibold text-navy-800 dark:text-white text-sm" x-text="item.user.name"></p>
                                        <p class="text-[10px] text-slate-400 font-mono" x-text="item.user.teacher_code"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                      :class="item.status_presensi === 'Tepat Waktu'
                                        ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                                        : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'">
                                    <span class="w-1.5 h-1.5 rounded-full"
                                          :class="item.status_presensi === 'Tepat Waktu' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                                    <span x-text="item.status_presensi"></span>
                                </span>
                            </td>
                            <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-300 text-sm" x-text="item.check_in_time"></td>
                            <td class="px-5 py-3 text-right">
                                <span class="text-blue-600 dark:text-blue-400 font-mono font-bold text-sm"
                                      x-text="formatDuration(item.timestamp_masuk)"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <div x-show="data.belum_scan_keluar.length === 0" class="px-5 py-10 text-center">
                <i data-lucide="check-circle" class="w-8 h-8 text-emerald-400 mx-auto mb-2"></i>
                <p class="text-sm text-slate-400">Semua guru sudah scan keluar.</p>
            </div>
        </div>
    </div>

    {{-- Sudah Selesai --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/30">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                </div>
                <h2 class="text-sm font-bold text-navy-800 dark:text-white">Sudah Selesai Mengajar Hari Ini</h2>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-bold"
                  x-text="data.sudah_selesai.length + ' sesi'"></span>
        </div>
        <div class="divide-y divide-slate-50 dark:divide-slate-800/50 max-h-72 overflow-y-auto">
            <template x-for="(item, i) in data.sudah_selesai" :key="i">
                <div class="flex items-center gap-3 px-5 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 font-bold text-xs flex-shrink-0"
                         x-text="item.user.initial"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-navy-800 dark:text-white truncate" x-text="item.user.name"></p>
                        <p class="text-xs text-slate-500 truncate" x-text="item.subject + ' • ' + item.classroom + ' (Jam ke-' + item.period + ')'"></p>
                    </div>
                    <div class="text-right flex-shrink-0 text-xs text-slate-400 font-mono">
                        <span x-text="item.check_in_time + ' - ' + item.check_out_time"></span>
                        <span class="ml-2 text-slate-500" x-text="'(' + item.durasi_label + ')'"></span>
                    </div>
                </div>
            </template>
            <div x-show="data.sudah_selesai.length === 0" class="px-5 py-8 text-center">
                <p class="text-sm text-slate-400">Belum ada sesi yang selesai hari ini.</p>
            </div>
        </div>
    </div>
</div>

<script>
function liveMonitoring(initialData) {
    return {
        data: initialData,
        waktuServer: initialData.waktu_server || '00:00:00',
        updatedAt: initialData.updated_at || '--:--:--',
        pollInterval: null,
        timerInterval: null,

        init() {
            // Polling setiap 15 detik
            this.pollInterval = setInterval(() => this.fetchData(), 15000);
            // Tick server time setiap 1 detik
            this.timerInterval = setInterval(() => {
                this.waktuServer = this.tickTime(this.waktuServer);
            }, 1000);
            if (window.lucide) lucide.createIcons();
        },

        async fetchData() {
            try {
                const res = await fetch('{{ route("admin.live-monitoring.refresh") }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    const json = await res.json();
                    this.data       = json;
                    this.waktuServer = json.waktu_server;
                    this.updatedAt   = json.updated_at;
                    this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
                }
            } catch (e) {
                console.warn('Live monitoring fetch failed:', e.message);
            }
        },

        formatDuration(ts) {
            const diff = Math.max(0, Math.floor(Date.now() / 1000) - ts);
            const h = Math.floor(diff / 3600);
            const m = Math.floor((diff % 3600) / 60);
            const s = diff % 60;
            if (h > 0) return `${h}j ${m}m ${s}d`;
            return `${m}m ${s}d`;
        },

        tickTime(t) {
            if (!t || t.length < 8) return t;
            const [h, m, s] = t.split(':').map(Number);
            let total = h * 3600 + m * 60 + s + 1;
            const nh = Math.floor(total / 3600) % 24;
            const nm = Math.floor((total % 3600) / 60);
            const ns = total % 60;
            return `${String(nh).padStart(2,'0')}:${String(nm).padStart(2,'0')}:${String(ns).padStart(2,'0')}`;
        }
    };
}
</script>

<style>
.fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
</style>
@endsection
