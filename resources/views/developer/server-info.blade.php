@extends('layouts.developer')
@section('content')

@php
    $diskPercentInt = (int) $diskPercent;
    $diskColor = $diskPercentInt > 90 ? '#f43f5e' : ($diskPercentInt > 70 ? '#f59e0b' : '#10b981');
    $dbStatus = $dbConnected ? 'connected' : 'disconnected';
    $dbBadge = $dbConnected ? 't-ok' : 't-bad';
@endphp

<div id="tab-server-info" class="tab-content">
    <header class="page-head">
        <h2><i data-lucide="monitor" style="width:22px;height:22px;color:var(--accent);vertical-align:middle;margin-right:6px"></i> Server Info</h2>
        <p>Informasi lengkap tentang server, aplikasi, dan status sistem secara real-time.</p>
    </header>

    <div class="grid-main">
        {{-- PHP --}}
        <div class="card">
            <div class="card-head">
                <h3><i data-lucide="cpu" style="width:18px;height:18px;color:#6366f1"></i> PHP</h3>
            </div>
            <div class="info">
                <div><p class="l">Versi PHP</p><p class="v mono">{{ $phpVersion }}</p></div>
                <div><p class="l">Zendor/OPcache</p><p class="v mono">{{ php_uname('r') }}</p></div>
                <div><p class="l">File ini</p><p class="v mono trunc" style="font-size:11px">{{ $phpIni }}</p></div>
                <div><p class="l">Upload Max</p><p class="v mono">{{ $uploadMax }}</p></div>
                <div><p class="l">Post Max</p><p class="v mono">{{ $postMax }}</p></div>
                <div><p class="l">Memory Limit</p><p class="v mono">{{ $memoryLimit }}</p></div>
                <div><p class="l">Max Execution Time</p><p class="v mono">{{ $maxExecTime }}s</p></div>
                <div><p class="l">Ekstensi</p><p class="v">{{ count($extList) }} modul aktif</p></div>
            </div>
        </div>

        {{-- Laravel --}}
        <div class="card">
            <div class="card-head">
                <h3><i data-lucide="framework" style="width:18px;height:18px;color:#ef4444"></i> Laravel</h3>
            </div>
            <div class="info">
                <div><p class="l">Versi Framework</p><p class="v mono">{{ $laravelVersion }}</p></div>
                <div><p class="l">Environment</p><p class="v mono"><span class="badge t-ok">{{ strtoupper(app()->environment()) }}</span></p></div>
                <div><p class="l">Default Timezone</p><p class="v mono">{{ $timezone }}</p></div>
                <div><p class="l">URL Aplikasi</p><p class="v mono trunc">{{ config('app.url') }}</p></div>
                <div><p class="l">Cache Status</p><p class="v">{{ $cacheStatus['config'] === 'CACHED' ? 'Config ✓' : 'Config ✗' }} / {{ $cacheStatus['route'] === 'CACHED' ? 'Route ✓' : 'Route ✗' }} / {{ $cacheStatus['view'] === 'CACHED' ? 'View ✓' : 'View ✗' }}</p></div>
            </div>
        </div>

        {{-- Database --}}
        <div class="card">
            <div class="card-head">
                <h3><i data-lucide="database" style="width:18px;height:18px;color:#10b981"></i> Database</h3>
                <span class="live"><i></i> {{ $dbConnected ? 'Terhubung' : 'Gagal' }}</span>
            </div>
            <div class="info">
                <div><p class="l">Koneksi</p><p class="v"><span class="dot {{ $dbBadge }}"></span> {{ $dbConnected ? 'Terhubung' : 'Gagal' }}</p></div>
                <div><p class="l">Driver</p><p class="v mono">{{ config('database.default') }}</p></div>
                <div><p class="l">Host</p><p class="v mono">{{ $dbHost }}</p></div>
                <div><p class="l">Database</p><p class="v mono">{{ $dbName }}</p></div>
                <div><p class="l">Username</p><p class="v mono">{{ $dbUser }}</p></div>
                <div><p class="l">DSN</p><p class="v mono trunc" style="font-size:11px">{{ $pdoDsn }}</p></div>
            </div>
        </div>

        {{-- Server --}}
        <div class="card">
            <div class="card-head">
                <h3><i data-lucide="server" style="width:18px;height:18px;color:#06b6d4"></i> Server</h3>
            </div>
            <div class="info">
                <div><p class="l">Software</p><p class="v mono">{{ $serverSoftware }}</p></div>
                <div><p class="l">Protocol</p><p class="v mono">{{ $serverProtocol }}</p></div>
                <div><p class="l">Server Port</p><p class="v mono">{{ $serverPort }}</p></div>
                <div><p class="l">Server IP</p><p class="v mono">{{ $serverAddr }}</p></div>
                <div><p class="l">Request IP</p><p class="v mono">{{ $remoteAddr }}</p></div>
                <div><p class="l">Waktu server</p><p class="v mono">{{ now()->format('d M Y H:i') }} WIB</p></div>
            </div>
        </div>

        {{-- Disk --}}
        <div class="card">
            <div class="card-head">
                <h3><i data-lucide="hard-drive" style="width:18px;height:18px;color:#f59e0b"></i> Penyimpanan</h3>
            </div>
            <div class="pad" style="padding:16px 20px">
                <div style="display:flex;justify-content:space-between;margin-bottom:8px">
                    <span class="mute" style="font-size:13px">Penggunaan disk</span>
                    <span class="mono" style="font-size:13px;color:{{ $diskColor }}">{{ $diskPercent }}%</span>
                </div>
                <div style="height:8px;background:var(--bg-hover);border-radius:999px;overflow:hidden;margin-bottom:12px">
                    <div style="height:100%;width:{{ $diskPercent }}%;background:{{ $diskColor }};border-radius:999px;transition:width .5s"></div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;text-align:center">
                    <div><p class="l" style="margin-bottom:2px">Total</p><p class="v mono" style="font-size:14px">{{ round($diskTotal / 1073741824, 1) }} GB</p></div>
                    <div><p class="l" style="margin-bottom:2px">Terpakai</p><p class="v mono" style="font-size:14px;color:{{ $diskColor }}">{{ round($diskUsed / 1073741824, 1) }} GB</p></div>
                    <div><p class="l" style="margin-bottom:2px">Sisa</p><p class="v mono" style="font-size:14px;color:#10b981">{{ round($diskFree / 1073741824, 1) }} GB</p></div>
                </div>
            </div>
        </div>

        {{-- Session --}}
        <div class="card">
            <div class="card-head">
                <h3><i data-lucide="cookie" style="width:18px;height:18px;color:#a78bfa"></i> Session</h3>
            </div>
            <div class="info">
                <div><p class="l">Driver</p><p class="v mono">{{ $sessionDriver }}</p></div>
                <div><p class="l">Lifetime</p><p class="v mono">{{ $sessionLifetime }} menit</p></div>
                <div><p class="l">Path</p><p class="v mono trunc" style="font-size:11px">{{ $sessionPath }}</p></div>
                <div><p class="l">Encrypt</p><p class="v mono">{{ config('session.encrypt') ? 'Ya' : 'Tidak' }}</p></div>
                <div><p class="l">Secure Cookie</p><p class="v mono">{{ config('session.secure') ? 'Ya' : 'Tidak' }}</p></div>
                <div><p class="l">Domain</p><p class="v mono">{{ config('session.domain') ?? 'Semua' }}</p></div>
            </div>
        </div>
    </div>
</div>

@endsection
