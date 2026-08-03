@extends('layouts.piket')
@section('page-title', 'Dashboard Piket')
@section('content')
<div class="space-y-6 fade-in">

    {{-- ── HEADER SELAMAT DATANG ── --}}
    <div class="rounded-2xl bg-gradient-to-r from-navy-800 via-navy-900 to-slate-900 p-6 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-gold-400/10 rounded-full -translate-y-1/2 translate-x-1/3 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gold-400/20 border border-gold-400/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="shield" class="w-6 h-6 text-gold-400"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold">Selamat bertugas, {{ auth()->user()->name }}!</h1>
                    <p class="text-sm text-slate-300 mt-0.5">
                        {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} &bull; Pos Piket
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('piket.attendance') }}"
                   class="flex items-center gap-2 px-4 py-2.5 bg-gold-400 hover:bg-gold-500 text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg">
                    <i data-lucide="scan-line" class="w-4 h-4"></i>
                    Mulai Scan
                </a>
            </div>
        </div>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-5">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="circle-check" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Hadir</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $hadirHariIni }}</h3>
                    <p class="text-[10px] text-green-600 dark:text-green-400 font-medium">Hari ini</p>
                </div>
            </div>
        </div>

        <div class="card p-5">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="clock" class="w-5 h-5 text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Terlambat</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $terlambatHariIni }}</h3>
                    <p class="text-[10px] text-yellow-600 dark:text-yellow-400 font-medium">Hari ini</p>
                </div>
            </div>
        </div>

        <div class="card p-5">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="user-x" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Belum Absen</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $belumAbsen }}</h3>
                    <p class="text-[10px] text-red-600 dark:text-red-400 font-medium">dari {{ $totalGuru }} guru</p>
                </div>
            </div>
        </div>

        <a href="{{ route('piket.leave-approval') }}" class="card p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <i data-lucide="bell" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Izin Pending</p>
                    <h3 class="text-2xl font-bold text-navy-800 dark:text-white">{{ $pendingCount }}</h3>
                    <p class="text-[10px] text-amber-600 dark:text-amber-400 font-medium">Perlu ditinjau</p>
                </div>
            </div>
        </a>
    </div>

    {{-- ── QUICK ACTIONS ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('piket.attendance') }}"
           class="card p-5 flex items-center gap-4 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform">
                <i data-lucide="scan-line" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-navy-800 dark:text-white">Presensi Harian</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Konfirmasi scan guru masuk/keluar</p>
            </div>
            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 ml-auto group-hover:translate-x-0.5 transition-transform"></i>
        </a>

        <a href="{{ route('piket.leave-approval') }}"
           class="card p-5 flex items-center gap-4 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
            <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-amber-400/30 group-hover:scale-110 transition-transform">
                <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <p class="text-sm font-bold text-navy-800 dark:text-white">Approval Izin</p>
                    @if($pendingCount > 0)
                    <span class="px-1.5 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full">{{ $pendingCount }}</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Terima atau tolak izin & sakit</p>
            </div>
            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 ml-auto group-hover:translate-x-0.5 transition-transform"></i>
        </a>

        <a href="{{ route('piket.class-attendance.manual') }}"
           class="card p-5 flex items-center gap-4 hover:shadow-lg hover:-translate-y-0.5 transition-all group">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                <i data-lucide="clipboard-edit" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-navy-800 dark:text-white">Manual Presensi</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Input presensi kelas manual</p>
            </div>
            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 ml-auto group-hover:translate-x-0.5 transition-transform"></i>
        </a>
    </div>

    {{-- ── 2 KOLOM: Scan Terbaru + Izin Pending ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Presensi Terbaru --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i data-lucide="scan-line" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                    </div>
                    <h3 class="text-sm font-bold text-navy-800 dark:text-white">Scan Terbaru Hari Ini</h3>
                </div>
                <a href="{{ route('piket.attendance') }}" class="text-xs font-semibold text-navy-700 dark:text-gold-400 hover:underline">
                    Lihat semua →
                </a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($recentAttendances as $att)
                <div class="px-5 py-3.5 flex items-center gap-3">
                    <img src="{{ $att->user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($att->user->name ?? 'G').'&background=0F172A&color=fff&size=64' }}"
                         class="w-9 h-9 rounded-lg object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-navy-800 dark:text-white truncate">{{ $att->user->name ?? '-' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Masuk: {{ optional($att->check_in)->format('H:i') ?? '-' }}
                            @if($att->check_out) · Keluar: {{ optional($att->check_out)->format('H:i') }} @endif
                        </p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold flex-shrink-0
                        @if($att->status === 'Hadir') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                        @elseif($att->status === 'Terlambat') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                        @else bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400 @endif">
                        {{ $att->status }}
                    </span>
                </div>
                @empty
                <div class="px-5 py-10 text-center">
                    <i data-lucide="scan-line" class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                    <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada presensi hari ini</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Izin Pending --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                        <i data-lucide="clock" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <h3 class="text-sm font-bold text-navy-800 dark:text-white">Izin Menunggu Konfirmasi</h3>
                </div>
                <a href="{{ route('piket.leave-approval') }}" class="text-xs font-semibold text-navy-700 dark:text-gold-400 hover:underline">
                    Lihat semua →
                </a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($pendingLeaves as $leave)
                <div class="px-5 py-3.5 flex items-center gap-3">
                    <img src="{{ $leave->user->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($leave->user->name ?? 'G').'&background=0F172A&color=fff&size=64' }}"
                         class="w-9 h-9 rounded-lg object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-navy-800 dark:text-white truncate">{{ $leave->user->name ?? '-' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ ucfirst($leave->type) }} ·
                            {{ optional($leave->start_date)->format('d M') }}–{{ optional($leave->end_date)->format('d M Y') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <form action="{{ route('piket.leave-approval.approve', $leave) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="admin_notes" value="Disetujui oleh piket">
                            <button type="submit" class="p-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-all" title="Setujui">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                        <form action="{{ route('piket.leave-approval.reject', $leave) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="admin_notes" value="Ditolak oleh piket">
                            <button type="submit" onclick="return confirm('Tolak pengajuan ini?')"
                                    class="p-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all" title="Tolak">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center">
                    <i data-lucide="check-circle" class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
                    <p class="text-sm text-slate-400 dark:text-slate-500">Tidak ada pengajuan yang menunggu</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

<script>document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });</script>
<style>
.fade-in { animation: fadeIn .4s ease-out; }
@keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
</style>

@if(session('show_welcome'))
<script>sessionStorage.setItem('show_welcome_piket_{{ auth()->id() }}', '1');</script>
@endif

{{-- Welcome Modal Piket --}}
<div id="piket-welcome-overlay" style="display:none;position:fixed;inset:0;z-index:9998;background:rgba(15,23,42,0.55);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:16px;">
    <div id="piket-welcome-box" style="background:#fff;border-radius:24px;padding:32px 28px 28px;max-width:380px;width:100%;text-align:center;box-shadow:0 24px 56px rgba(15,23,42,0.2);transform:translateY(20px) scale(0.97);opacity:0;transition:all 0.35s cubic-bezier(0.22,1,0.36,1);">
        <div style="width:64px;height:64px;background:linear-gradient(135deg,#0F172A,#1E293B);border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;box-shadow:0 8px 20px rgba(15,23,42,0.2);">
            <svg width="28" height="28" fill="none" stroke="#FACC15" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <h2 style="font-size:1.3rem;font-weight:800;color:#0F172A;margin:0 0 6px;letter-spacing:-0.3px;">Selamat Bertugas!</h2>
        <p style="font-size:0.9rem;font-weight:600;color:#0F172A;margin:0 0 16px;">Halo, {{ auth()->user()->name }} 👋</p>
        <p style="font-size:0.83rem;color:#64748B;line-height:1.5;margin:0 0 16px;">Selamat datang di portal Guru Piket.<br>Tugasmu hari ini:</p>
        <div style="text-align:left;background:#F8FAFC;border-radius:12px;padding:14px 16px;margin:0 0 20px;border:1px solid #E2E8F0;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                <div style="width:22px;height:22px;background:#22C55E;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="13" height="13" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span style="font-size:0.82rem;color:#1E293B;font-weight:500;">Konfirmasi scan masuk &amp; keluar guru</span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                <div style="width:22px;height:22px;background:#F59E0B;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="13" height="13" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span style="font-size:0.82rem;color:#1E293B;font-weight:500;">Tinjau &amp; proses pengajuan izin/sakit</span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:22px;height:22px;background:#3B82F6;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="13" height="13" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <span style="font-size:0.82rem;color:#1E293B;font-weight:500;">Input manual jika ada kendala scan</span>
            </div>
        </div>
        <button onclick="closePiketWelcome()" style="width:100%;padding:13px;background:#0F172A;color:#fff;border:none;border-radius:14px;font-size:0.95rem;font-weight:700;cursor:pointer;box-shadow:0 6px 18px rgba(15,23,42,0.22);">
            Siap Bertugas →
        </button>
    </div>
</div>
<script>
(function() {
    var KEY = 'show_welcome_piket_{{ auth()->id() }}';
    if (sessionStorage.getItem(KEY) !== '1') return;
    sessionStorage.removeItem(KEY);
    var overlay = document.getElementById('piket-welcome-overlay');
    var box     = document.getElementById('piket-welcome-box');
    if (!overlay || !box) return;
    overlay.style.display = 'flex';
    requestAnimationFrame(function() { requestAnimationFrame(function() {
        box.style.transform = 'translateY(0) scale(1)';
        box.style.opacity   = '1';
    }); });
})();
function closePiketWelcome() {
    var overlay = document.getElementById('piket-welcome-overlay');
    var box     = document.getElementById('piket-welcome-box');
    box.style.transform = 'translateY(10px) scale(0.97)';
    box.style.opacity   = '0';
    setTimeout(function() { overlay.style.display = 'none'; }, 320);
}
document.getElementById('piket-welcome-overlay').addEventListener('click', function(e) {
    if (e.target === this) closePiketWelcome();
});
</script>
@endsection
