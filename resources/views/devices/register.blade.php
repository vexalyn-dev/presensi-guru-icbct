@extends('layouts.teacher')
@section('page-title', 'Daftarkan Perangkat')
@section('content')
<div class="min-h-[70vh] flex items-center justify-center p-4">
    <div class="w-full max-w-md">

        @if(session('warning'))
        <div class="mb-4 p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 flex items-start gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5"></i>
            <p class="text-sm text-amber-800 dark:text-amber-300">{{ session('warning') }}</p>
        </div>
        @endif
        @if(session('error'))
        <div class="mb-4 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 flex items-start gap-3">
            <i data-lucide="x-circle" class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5"></i>
            <p class="text-sm text-red-800 dark:text-red-300">{{ session('error') }}</p>
        </div>
        @endif

        <div class="card overflow-hidden">
            <div class="px-6 py-8 text-center border-b border-slate-100 dark:border-slate-800 bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500">
                <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="smartphone" class="w-8 h-8 text-white dark:text-navy-900"></i>
                </div>
                <h2 class="text-xl font-extrabold text-white dark:text-navy-900">Daftarkan Perangkat</h2>
                <p class="text-sm text-white/70 dark:text-navy-900/70 mt-1">
                    Perangkat ini belum terdaftar.<br>
                    Maks. {{ \App\Services\DeviceService::MAX_DEVICES }} perangkat per akun.
                    <span class="font-bold">({{ $deviceCount }}/{{ \App\Services\DeviceService::MAX_DEVICES }} terdaftar)</span>
                </p>
            </div>

            <div class="p-6">
                <form action="{{ route('devices.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="device_token" id="device_token">

                    <div>
                        <label class="block text-sm font-semibold text-navy-800 dark:text-white mb-2">
                            Nama Perangkat <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i data-lucide="smartphone" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                            <input type="text" name="device_name" required maxlength="50"
                                   value="{{ old('device_name') }}"
                                   placeholder="Contoh: HP Samsung A51 Saya"
                                   class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-2 border-slate-200 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy-800 dark:focus:ring-gold-500 transition-all">
                        </div>
                        @error('device_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div id="token-status" class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 text-xs text-slate-400 flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        Mengambil identitas perangkat...
                    </div>

                    <button type="submit" id="submit-btn" disabled
                            class="w-full py-3.5 bg-gradient-to-r from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 hover:opacity-90 text-white dark:text-navy-900 rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        Daftarkan Perangkat Ini
                    </button>
                </form>

                <p class="text-xs text-slate-400 text-center mt-4">
                    <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                    ID perangkat disimpan di browser ini. Ganti browser = daftarkan ulang.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tokenInput  = document.getElementById('device_token');
    var statusEl    = document.getElementById('token-status');
    var submitBtn   = document.getElementById('submit-btn');

    var token = localStorage.getItem('icb_device_token');
    if (!token) {
        token = 'dev_' + ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, function(c) {
            return (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16);
        });
        localStorage.setItem('icb_device_token', token);
    }

    tokenInput.value = token;
    statusEl.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i><span class="text-emerald-600 dark:text-emerald-400">ID perangkat siap: <span class="font-mono">' + token.substring(0, 16) + '...</span></span>';
    submitBtn.disabled = false;
    if (window.lucide) lucide.createIcons();
});
</script>
<script>document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });</script>
@endsection
