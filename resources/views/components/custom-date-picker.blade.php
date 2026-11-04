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
$displayValue = $value ? date('d F Y', strtotime($value)) : '';
?>

<div class="relative w-full" {{ $attributes->merge(['class' => $class]) }}>
    <!-- Hidden native input -->
    <input type="date"
           id="{{ $uuid }}"
           name="{{ $name }}"
           value="{{ $value }}"
           min="{{ $min }}"
           max="{{ $max }}"
           {{ $required ? 'required' : '' }}
           {{ $disabled ? 'disabled' : '' }}
           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
           style="cursor:pointer;"
           onchange="var el=this,lbl=document.getElementById('lbl_{{ $uuid }}');
                   if(el.value){
                       var d=new Date(el.value+'T00:00:00');
                       var m=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                       lbl.textContent=d.getDate()+' '+m[d.getMonth()]+' '+d.getFullYear();
                       lbl.style.color=getComputedStyle(document.documentElement).getPropertyValue('--tw-text-slate-900')||'#0f172a';
                   } else {
                       lbl.textContent='{{ $placeholder }}';
                       lbl.style.color='#94a3b8';
                   }"
    />

    <!-- Visible trigger -->
    <div class="flex items-center gap-3 px-4 py-3 rounded-2xl border transition-all duration-200 bg-white dark:bg-slate-800
                {{ $value ? 'border-slate-200 dark:border-slate-700' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600' }}
                {{ $disabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer' }}"
         onclick="document.getElementById('{{ $uuid }}').showPicker()"
         {{ $disabled ? 'style="pointer-events:none"' : '' }}>

        <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
        </svg>

        <span id="lbl_{{ $uuid }}"
              class="flex-1 text-sm font-medium select-none"
              style="color: {{ $value ? '#0f172a' : '#94a3b8' }};">
            {{ $displayValue ?: $placeholder }}
        </span>

        <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
        </svg>
    </div>
</div>
