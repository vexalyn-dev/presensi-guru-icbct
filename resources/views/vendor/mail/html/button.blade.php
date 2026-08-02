@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
<div class="btn-wrapper" style="text-align:{{ $align }}; margin: 28px 0;">
    <a href="{{ $url }}"
       class="btn-reset"
       target="_blank"
       rel="noopener"
       style="display:inline-block;background-color:#0F172A;color:#FFFFFF;text-decoration:none;font-size:15px;font-weight:700;padding:14px 40px;border-radius:12px;letter-spacing:0.02em;box-shadow:0 4px 16px rgba(15,23,42,0.28);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI','Inter',Helvetica,Arial,sans-serif;">
        {!! $slot !!}
    </a>
</div>
