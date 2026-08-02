@props(['url'])
@php
    $appSettings = null;
    try { $appSettings = \App\Models\AppSetting::getInstance(); } catch (\Throwable $e) {}
    $logoUrl = ($appSettings && $appSettings->app_logo)
        ? asset('storage/' . $appSettings->app_logo)
        : null;
    $appName = $appSettings->app_name ?? config('app.name', 'ICB CINTA TEKNIKA');
@endphp
<div class="email-header">
    <a href="{{ $url }}" style="text-decoration:none;">
        <div class="header-logo-box">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $appName }}" style="width:76%;height:76%;object-fit:contain;">
            @else
                <svg width="36" height="36" fill="none" stroke="#FACC15" stroke-width="2" viewBox="0 0 24 24"
                     style="display:inline-block;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            @endif
        </div>
        <div class="header-app-name">{{ $appName }}</div>
        <div class="header-tagline">Sistem Presensi Digital Guru</div>
    </a>
</div>
