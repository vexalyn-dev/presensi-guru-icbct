@props([
    'name' => 'date',
    'id' => null,
    'value' => null,
    'placeholder' => 'Pilih tanggal',
    'required' => false,
    'disabled' => false,
    'min' => null,
    'max' => null,
    'class' => '',
])

<?php
$uuid = $id ?? 'cal_' . uniqid();
$display = $value ? date('d F Y', strtotime($value)) : '';
?>

<div class="relative inline-block w-full" {{ $attributes->merge(['class' => $class]) }}>
    <div class="relative group">
        <!-- Trigger -->
        <label for="{{ $uuid }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl border-2 cursor-pointer transition-all duration-200 block w-full"
               :class="{{ $disabled ? "'opacity-60 pointer-events-none'" : '' }}"
               {{ $disabled ? 'style="pointer-events:none;opacity:0.6"' : '' }}>

            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-colors duration-200 group-hover:text-navy-800 dark:group-hover:text-gold-400"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
            </svg>

            <span class="flex-1 text-sm font-medium truncate"
                  style="color: {{ $value ? 'var(--tw-text-slate-900, #0f172a)' : 'rgb(148 163 184)' }});"
                  x-data {{-- just to use Alpine for the display if needed --}}
                  {{ $value ? '' : "x-text='{{ $placeholder }}'" }}>
                @if($value)
                    {{ $display }}
                @else
                    {{ $placeholder }}
                @endif
            </span>

            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform duration-200"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
            </svg>
        </label>

        <!-- Native Date Input (hidden but functional) -->
        <input type="date"
               id="{{ $uuid }}"
               name="{{ $name }}"
               value="{{ $value }}"
               min="{{ $min }}"
               max="{{ $max }}"
               {{ $required ? 'required' : '' }}
               {{ $disabled ? 'disabled' : '' }}
               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
               style="cursor:pointer;"
               onchange="this.closest('.relative').querySelector('span').textContent = this.value ? new Date(this.value + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '{{ $placeholder }}';
                       this.closest('.relative').querySelector('label').classList{{ $value ? '' : '.' }}add('{{ $value ? 'border-navy-800/20 dark:border-slate-600' : 'border-slate-200 dark:border-slate-700']');
                       this.closest('.relative').querySelector('span').style.color = this.value ? '#0f172a' : '#94a3b8';"
        />
    </div>
</div>
