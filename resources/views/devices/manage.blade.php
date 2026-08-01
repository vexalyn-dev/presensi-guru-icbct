@extends('layouts.teacher')
@section('page-title', 'Kelola Perangkat')
@section('content')
<div class="space-y-6 fade-in">

    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 rounded-2xl flex items-center justify-center shadow-lg">
            <i data-lucide="devices" class="w-6 h-6 text-white dark:text-navy-900"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-navy-800 dark:text-white">Kelola Perangkat</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Perangkat terdaftar di akun Anda (maks. {{ $maxDevices }})</p>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-green-600 flex-shrink-0"></i>
        <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 flex items-center gap-3">
        <i data-lucide="x-circle" class="w-5 h-5 text-red-600 flex-shrink-0"></i>
        <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
    </div>
    @endif

    <div class="card overflow-hidden">
        @if($devices->isEmpty())
        <div class="p-12 text-center">
            <i data-lucide="smartphone" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3"></i>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Belum ada perangkat terdaftar.</p>
        </div>
        @else
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @foreach($devices as $device)
            <div class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                {{-- Icon --}}
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                    {{ $device->is_active ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-slate-100 dark:bg-slate-800' }}">
                    @php
                        $iconName = str_contains(strtolower($device->os ?? ''), 'android') || str_contains(strtolower($device->os ?? ''), 'ios')
                            ? 'smartphone' : 'monitor';
                    @endphp
                    <i data-lucide="{{ $iconName }}" class="w-5 h-5 {{ $device->is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}"></i>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <p class="text-sm font-semibold text-navy-800 dark:text-white truncate">{{ $device->device_name }}</p>
                        @if($device->is_active)
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] font-semibold">Nonaktif</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400 truncate">{{ $device->os }} • {{ $device->browser }}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">
                        Terakhir digunakan: {{ $device->last_used_at ? $device->last_used_at->diffForHumans() : 'Belum pernah' }}
                    </p>
                </div>

                {{-- Action --}}
                @if($device->is_active)
                <form method="POST" action="{{ route('devices.destroy', $device) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Nonaktifkan perangkat ini?')"
                            class="px-3 py-1.5 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 rounded-lg text-xs font-semibold transition-colors">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5 inline mr-1"></i>
                        Hapus
                    </button>
                </form>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="card p-5 border border-amber-200 dark:border-amber-800/40 bg-amber-50/50 dark:bg-amber-900/10">
        <div class="flex items-start gap-3">
            <i data-lucide="info" class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5"></i>
            <div class="text-sm text-amber-800 dark:text-amber-300">
                <p class="font-semibold mb-1">Info Perangkat</p>
                <ul class="text-xs space-y-1 text-amber-700 dark:text-amber-400 list-disc list-inside">
                    <li>Maksimal <strong>{{ $maxDevices }}</strong> perangkat per akun.</li>
                    <li>Menghapus perangkat tidak menghapus data presensi.</li>
                    <li>Jika ganti HP, daftarkan perangkat baru dan hapus yang lama.</li>
                    <li>Ganti browser di HP yang sama = perlu daftar ulang.</li>
                </ul>
            </div>
        </div>
    </div>

</div>
<script>document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });</script>
<style>.fade-in{animation:fadeIn .4s ease-out}@keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}</style>
@endsection
