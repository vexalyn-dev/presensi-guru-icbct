@extends('layouts.teacher')

@section('page-title', 'Notifikasi')

@section('content')
<div class="fade-in space-y-4 sm:space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg shadow-navy-800/30 dark:shadow-gold-400/30 flex-shrink-0 relative">
                <i data-lucide="bell" class="w-5 h-5 sm:w-6 sm:h-6 text-white dark:text-navy-900"></i>
                @if(auth()->user()->unreadCount() > 0)
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">
                    {{ auth()->user()->unreadCount() > 9 ? '9+' : auth()->user()->unreadCount() }}
                </span>
                @endif
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-navy-800 dark:text-white">Notifikasi</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                    @if(auth()->user()->unreadCount() > 0)
                        {{ auth()->user()->unreadCount() }} belum dibaca
                    @else
                        Semua sudah dibaca
                    @endif
                </p>
            </div>
        </div>
        @if(auth()->user()->unreadCount() > 0)
        <form action="{{ route('teacher.notifications.read-all') }}" method="POST" class="w-full sm:w-auto">
            @csrf
            <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-navy-800 dark:bg-gold-400 text-white dark:text-navy-900 rounded-xl text-sm font-semibold transition-all hover:shadow-lg hover:-translate-y-0.5 flex items-center justify-center gap-2">
                <i data-lucide="check-check" class="w-4 h-4"></i>
                Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>

    <!-- Stats Bar -->
    @php
        $totalNotif  = $notifications->total();
        $unreadCount = auth()->user()->unreadCount();
        $readCount   = $totalNotif - $unreadCount;
    @endphp
    <div class="grid grid-cols-3 gap-3">
        <div class="card p-3 sm:p-4 flex items-center gap-2 sm:gap-3">
            <div class="w-8 h-8 sm:w-9 sm:h-9 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="bell" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">Total</p>
                <p class="text-base sm:text-lg font-bold text-navy-800 dark:text-white">{{ $totalNotif }}</p>
            </div>
        </div>
        <div class="card p-3 sm:p-4 flex items-center gap-2 sm:gap-3">
            <div class="w-8 h-8 sm:w-9 sm:h-9 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="bell-ring" class="w-4 h-4 text-red-500 dark:text-red-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">Belum Baca</p>
                <p class="text-base sm:text-lg font-bold text-navy-800 dark:text-white">{{ $unreadCount }}</p>
            </div>
        </div>
        <div class="card p-3 sm:p-4 flex items-center gap-2 sm:gap-3">
            <div class="w-8 h-8 sm:w-9 sm:h-9 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 truncate">Dibaca</p>
                <p class="text-base sm:text-lg font-bold text-navy-800 dark:text-white">{{ $readCount }}</p>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="space-y-2 sm:space-y-3">
        @forelse($notifications as $notif)
        <a href="{{ $notif->action_url ?? '#' }}" 
           onclick="markAsRead('{{ $notif->id }}')"
           class="block card p-4 sm:p-5 hover:shadow-lg transition-all {{ !$notif->is_read ? 'border-l-4 border-l-blue-500 bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
            <div class="flex items-start gap-3 sm:gap-4">
                <!-- Icon -->
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl {{ $notif->color }} flex items-center justify-center flex-shrink-0 relative">
                    <i data-lucide="{{ $notif->icon }}" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                    @if(!$notif->is_read)
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-blue-500 rounded-full border-2 border-white dark:border-slate-800"></span>
                    @endif
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-1">
                        <h3 class="text-sm sm:text-base font-bold text-navy-800 dark:text-white leading-snug">{{ $notif->title }}</h3>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            @if(!$notif->is_read)
                            <span class="px-1.5 py-0.5 bg-blue-500 text-white rounded-full text-[9px] sm:text-[10px] font-bold whitespace-nowrap">Baru</span>
                            @endif
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mb-1.5 line-clamp-2">{{ $notif->message }}</p>
                    <p class="text-[10px] sm:text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                        <i data-lucide="clock" class="w-3 h-3"></i>
                        {{ $notif->created_at->locale('id')->diffForHumans() }}
                    </p>
                </div>
            </div>
        </a>
        @empty
        <div class="card p-10 sm:p-12 text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                <i data-lucide="bell-off" class="w-8 h-8 sm:w-10 sm:h-10 text-slate-400 dark:text-slate-500"></i>
            </div>
            <h3 class="text-base sm:text-lg font-bold text-navy-800 dark:text-white mb-2">Tidak Ada Notifikasi</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada notifikasi untuk ditampilkan</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
    <div class="card p-3 sm:p-4">
        {{ $notifications->links() }}
    </div>
    @endif

</div>

<script>
    function markAsRead(notificationId) {
        fetch(`/teacher/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => {
            if (response.ok) {
                setTimeout(() => window.location.reload(), 500);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
    });
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
@endsection
