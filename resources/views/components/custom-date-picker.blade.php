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
        <!-- Trigger Label -->
        <label for="{{ $uuid }}"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl border-2 cursor-pointer transition-all duration-200 w-full"
               {{ $disabled ? 'style="pointer-events:none;opacity:0.6"' : '' }}>

            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-colors duration-200 group-hover:text-navy-800 dark:group-hover:text-gold-400"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
            </svg>

            <span id="lbl_{{ $uuid }}"
                  class="flex-1 text-sm font-medium truncate transition-colors duration-200"
                  style="color: {{ $value ? '#0f172a' : '#94a3b8' }};">
                {{ $display ?: $placeholder }}
            </span>

            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform duration-200"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
            </svg>
        </label>

        <!-- Hidden Native Input -->
        <input type="date"
               id="{{ $uuid }}"
               name="{{ $name }}"
               value="{{ $value }}"
               min="{{ $min }}"
               max="{{ $max }}"
               {{ $required ? 'required' : '' }}
               {{ $disabled ? 'disabled' : '' }}
               onchange="var s=document.getElementById('lbl_{{ $uuid }}');
                         if(this.value){
                             var d=new Date(this.value+'T00:00:00');
                             var months=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                             s.textContent=d.getDate()+' '+months[d.getMonth()]+' '+d.getFullYear();
                             s.style.color='#0f172a';
                             this.closest('.relative').querySelector('label').classList.add('border-navy-800/20','dark:border-slate-600');
                             this.closest('.relative').querySelector('label').classList.remove('border-slate-200','dark:border-slate-700');
                         } else {
                             s.textContent='{{ $placeholder }}';
                             s.style.color='#94a3b8';
                             this.closest('.relative').querySelector('label').classList.remove('border-navy-800/20','dark:border-slate-600');
                             this.closest('.relative').querySelector('label').classList.add('border-slate-200','dark:border-slate-700');
                         }"
               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
               style="cursor:pointer;" />
    </div>
</div>
