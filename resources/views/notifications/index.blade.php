@extends('layouts.app')

@section('page-title', 'Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 fade-in">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Notifikasi</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Pemberitahuan terbaru untuk Anda</p>
        </div>
        @if($notifications->count() > 0)
            <form action="{{ route('notifications.clear') }}" method="POST" onsubmit="return confirm('Hapus semua notifikasi?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-500 hover:text-red-600 font-medium flex items-center gap-2">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Hapus Semua
                </button>
            </form>
        @endif
    </div>

    <div class="space-y-3">
        @forelse($notifications as $notification)
            <div class="card p-4 hover:shadow-md transition-all {{ $notification->read_at ? 'opacity-75' : 'border-l-4 border-l-blue-500' }}">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 
                        {{ $notification->data['type'] === 'success' ? 'bg-green-100 text-green-600' : 
                           ($notification->data['type'] === 'error' ? 'bg-red-100 text-red-600' : 
                           'bg-blue-100 text-blue-600') }}">
                        <i data-lucide="{{ $notification->data['type'] === 'success' ? 'check-circle' : 
                                         ($notification->data['type'] === 'error' ? 'alert-circle' : 'bell') }}" class="w-5 h-5"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-navy-800 dark:text-white truncate">
                                {{ $notification->data['message'] }}
                            </p>
                            <span class="text-[10px] text-slate-400 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            @if(isset($notification->data['url']))
                                <a href="{{ $notification->data['url'] }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                    Lihat Detail
                                </a>
                            @else
                                <span></span>
                            @endif
                            
                            @if(!$notification->read_at)
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[10px] text-slate-400 hover:text-navy-800 dark:hover:text-white transition-colors">
                                        Tandai sudah baca
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-16 text-center">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-400">
                        <i data-lucide="bell-off" class="w-10 h-10"></i>
                    </div>
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada notifikasi</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Pemberitahuan baru akan muncul di sini</p>
                    </div>
                </div>
            </div>
        @endforelse

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection
