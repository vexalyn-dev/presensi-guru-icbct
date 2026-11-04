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
?>

<div class="relative w-full" {{ $attributes->merge(['class' => '']) }}>
    <div class="relative">
        <button type="button"
                x-data="{ open: false }"
                x-init="
                    const input = $refs.calInput;
                    const btn = $el.closest('.relative').querySelector('button[type=button]');
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        input.showPicker();
                    });
                "
                class="flex items-center gap-3 px-4 py-3 rounded-2xl border-2 transition-all duration-200 w-full text-left"
                :class="{}"
                :disabled="{{ $disabled ? 'true' : 'false' }}"
                {{ $disabled ? 'disabled' : '' }}>

            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
            </svg>

            <input x-ref="calInput"
                   type="date"
                   name="{{ $name }}"
                   id="{{ $uuid }}"
                   value="{{ $value }}"
                   min="{{ $min }}"
                   max="{{ $max }}"
                   {{ $required ? 'required' : '' }}
                   {{ $disabled ? 'disabled' : '' }}
                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />

            <span class="flex-1 text-sm font-medium text-slate-900 dark:text-white truncate"
                  x-data="{ val: '{{ $value }}' }"
                  x-text="val ? new Date(val + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : 'Pilih tanggal'"
                  class="pointer-events-none"></span>

            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
            </svg>
        </button>
    </div>
</div>
