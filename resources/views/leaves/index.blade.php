@extends(activeLayout())

@section('page-title', 'Izin & Sakit')

@section('content')
<div class="fade-in space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30">
                <i data-lucide="file-text" class="w-6 h-6 text-white dark:text-navy-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Izin & Sakit</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Kelola pengajuan izin dan sakit guru</p>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2000)" x-show="show"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="card p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800">
        <div class="flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
            <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="card p-4 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800">
        <div class="flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
            <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card p-4 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="file-text" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Total Pengajuan</p>
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white" data-leave-stat="total">{{ $stats['total'] }}</h3>
                    <p class="text-[10px] text-blue-500">Seluruh periode</p>
                </div>
            </div>
        </div>

        <div class="card p-4 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="clock" class="w-5 h-5 text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Pending</p>
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white" data-leave-stat="pending">{{ $stats['pending'] }}</h3>
                    <p class="text-[10px] text-yellow-600">Menunggu persetujuan</p>
                </div>
            </div>
        </div>

        <div class="card p-4 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Disetujui</p>
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white" data-leave-stat="approved">{{ $stats['approved'] }}</h3>
                    <p class="text-[10px] text-green-500">Telah dikonfirmasi</p>
                </div>
            </div>
        </div>

        <div class="card p-4 group hover:shadow-lg transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="x-circle" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Ditolak</p>
                    <h3 class="text-xl font-bold text-navy-800 dark:text-white" data-leave-stat="rejected">{{ $stats['rejected'] }}</h3>
                    <p class="text-[10px] text-red-500">Tidak disetujui</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Requests List -->
    <div class="space-y-4" id="leave-requests-list" data-latest-url="{{ route($routeLatest) }}">
        @forelse($leaveRequests as $leave)
        <div class="card p-5 hover:shadow-lg transition-all cursor-context-menu"
             data-leave-id="{{ $leave->id }}"
             data-delete-url="{{ route('teacher.leave.destroy', $leave) }}"
             oncontextmenu="showLeaveContextMenu(event, this)">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4 flex-1">
                        <img src="{{ $leave->user->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($leave->user->name) }}"
                             class="w-12 h-12 rounded-xl object-cover border-2 border-slate-200 dark:border-slate-700 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <h3 class="text-base font-bold text-navy-800 dark:text-white">{{ $leave->user->name }}</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                {{ $leave->status === 'pending'  ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                {{ $leave->status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                {{ $leave->status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                {{ ucfirst($leave->status) }}
                            </span>
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-full text-[10px] font-bold">
                                {{ ucfirst($leave->type) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400 mb-2 flex-wrap">
                            <div class="flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                <span>{{ $leave->start_date->format('d M Y') }} - {{ $leave->end_date->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                <span>{{ $leave->duration }} Hari</span>
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $leave->reason }}</p>
                        @if($leave->admin_notes)
                        <div class="mt-2 p-2 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                            @php
                                $approverRole2 = $leave->approvedBy?->role ?? '';
                                $labelCatatan2 = match(true) {
                                    in_array($approverRole2, ['admin','operator']) => 'Catatan Operator',
                                    $approverRole2 === 'guru_piket' => 'Catatan Guru Piket',
                                    default => 'Catatan Peninjau',
                                };
                            @endphp
                            <p class="text-xs text-slate-500 dark:text-slate-400"><strong>{{ $labelCatatan2 }}:</strong> {{ $leave->admin_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-center gap-2 flex-shrink-0">
                    @if($leave->status === 'pending')
                    <form action="{{ route($routeApprove, $leave) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="admin_notes" value="Disetujui">
                        <button type="submit" class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            Setujui
                        </button>
                    </form>
                    <form action="{{ route($routeReject, $leave) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="admin_notes" value="Ditolak">
                        <button type="submit" onclick="return confirm('Yakin ingin menolak pengajuan ini?')" class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            Tolak
                        </button>
                    </form>
                    @endif
                    <a href="{{ route($routeShow, $leave) }}" class="px-3 py-2 bg-navy-800 dark:bg-gold-400 hover:bg-navy-900 dark:hover:bg-gold-500 text-white dark:text-navy-900 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                        Detail
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="card p-12 text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-lucide="file-text" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
            </div>
            <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-2">Belum Ada Pengajuan</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada pengajuan izin atau sakit</p>
        </div>
        @endforelse
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
    });

    // ── Context Menu Klik Kanan ──
    let _ctxLeaveTarget = null;

    function showLeaveContextMenu(e, el) {
        e.preventDefault();
        _ctxLeaveTarget = el;
        const ctxMenu = document.getElementById('leave-context-menu');
        if (!ctxMenu) return;
        ctxMenu.style.display = 'block';
        let x = e.pageX, y = e.pageY;
        const menuW = ctxMenu.offsetWidth || 180;
        const menuH = ctxMenu.offsetHeight || 100;
        if (x + menuW > window.innerWidth)  x = window.innerWidth - menuW - 8;
        if (y + menuH > window.innerHeight) y = window.innerHeight - menuH - 8;
        ctxMenu.style.left = x + 'px';
        ctxMenu.style.top  = y + 'px';
    }

    document.addEventListener('click', () => {
        const ctxMenu = document.getElementById('leave-context-menu');
        if (ctxMenu) ctxMenu.style.display = 'none';
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            const ctxMenu = document.getElementById('leave-context-menu');
            if (ctxMenu) ctxMenu.style.display = 'none';
        }
    });

    function ctxDeleteLeave() {
        if (!_ctxLeaveTarget) return;
        const url = _ctxLeaveTarget.dataset.deleteUrl;
        if (!url) return;
        if (!confirm('Hapus pengajuan ini?')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        const token = document.querySelector('meta[name=csrf-token]')?.content || '';
        form.innerHTML = '<input type="hidden" name="_token" value="' + token + '">'
                       + '<input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(form);
        form.submit();
    }

    (() => {
        const list = document.getElementById('leave-requests-list');
        if (!list) return;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const esc = value => String(value ?? '').replace(/[&<>"']/g, char => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        }[char]));

        const statusClass = status => ({
            pending: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
            approved: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
            rejected: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        }[status] || 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400');

        const actionButtons = leave => leave.status === 'pending' ? `
            <form action="${esc(leave.approve_url)}" method="POST" class="inline">
                <input type="hidden" name="_token" value="${esc(csrf)}">
                <input type="hidden" name="admin_notes" value="Disetujui oleh admin">
                <button type="submit" class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    Setujui
                </button>
            </form>
            <form action="${esc(leave.reject_url)}" method="POST" class="inline">
                <input type="hidden" name="_token" value="${esc(csrf)}">
                <input type="hidden" name="admin_notes" value="Ditolak oleh admin">
                <button type="submit" onclick="return confirm('Yakin ingin menolak pengajuan ini?')" class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    Tolak
                </button>
            </form>
        ` : '';

        const renderLeave = leave => `
            <div class="card p-5 hover:shadow-lg transition-all">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-4 flex-1">
                        <img src="${esc(leave.teacher_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(leave.teacher_name)}`)}"
                             class="w-12 h-12 rounded-xl object-cover border-2 border-slate-200 dark:border-slate-700 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <h3 class="text-base font-bold text-navy-800 dark:text-white">${esc(leave.teacher_name)}</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${statusClass(leave.status)}">${esc(leave.status_text)}</span>
                                <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-full text-[10px] font-bold">${esc(leave.type_text)}</span>
                            </div>
                            <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400 mb-2 flex-wrap">
                                <div class="flex items-center gap-1">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                    <span>${esc(leave.start_date)} - ${esc(leave.end_date)}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                    <span>${esc(leave.duration)} Hari</span>
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-400">${esc(leave.reason)}</p>
                            ${leave.admin_notes ? `<div class="mt-2 p-2 bg-slate-50 dark:bg-slate-700/50 rounded-lg"><p class="text-xs text-slate-500 dark:text-slate-400"><strong>${leave.approver_role_label || 'Catatan Peninjau'}:</strong> ${esc(leave.admin_notes)}</p></div>` : ''}
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        ${actionButtons(leave)}
                        <a href="${esc(leave.show_url)}" class="px-3 py-2 bg-navy-800 dark:bg-gold-400 hover:bg-navy-900 dark:hover:bg-gold-500 text-white dark:text-navy-900 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        `;

        const renderEmpty = () => `
            <div class="card p-12 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="file-text" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
                </div>
                <h3 class="text-lg font-bold text-navy-800 dark:text-white mb-2">Belum Ada Pengajuan</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada pengajuan izin atau sakit</p>
            </div>
        `;

        async function refreshLeaves() {
            try {
                const response = await fetch(list.dataset.latestUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await response.json();
                if (!data.success) return;

                Object.entries(data.stats || {}).forEach(([key, value]) => {
                    const stat = document.querySelector(`[data-leave-stat="${key}"]`);
                    if (stat) stat.textContent = value;
                });

                list.innerHTML = data.leaves?.length ? data.leaves.map(renderLeave).join('') : renderEmpty();
                if (window.lucide) lucide.createIcons();
            } catch (error) {
                console.error('Error refreshing leave requests:', error);
            }
        }

        window.refreshLeaveRequests = refreshLeaves;
        refreshLeaves();
        setInterval(refreshLeaves, 3000);
        window.addEventListener('focus', refreshLeaves);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) refreshLeaves();
        });
    })();
</script>

<style>
    .fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

{{-- Context Menu Klik Kanan --}}
<div id="leave-context-menu"
     style="display:none;position:fixed;z-index:9999;min-width:160px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 8px 24px rgba(15,23,42,0.15);overflow:hidden;"
     class="dark:!bg-slate-800 dark:!border-slate-700">
    <button onclick="ctxDeleteLeave()"
            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors font-medium">
        <i data-lucide="trash-2" class="w-4 h-4"></i>
        Hapus Pengajuan
    </button>
</div>

@endsection
