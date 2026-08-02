@php
    $appName = config('app.name', 'ICB CT');
@endphp
<div class="email-footer">
    <p class="footer-text">
        © {{ date('Y') }} <strong style="color:#475569;">{{ $appName }}</strong> &nbsp;·&nbsp; Sistem Presensi Digital
        <br>
        <span style="font-size:11px;color:#CBD5E1;">Email ini dikirim otomatis, mohon jangan dibalas.</span>
    </p>
</div>
