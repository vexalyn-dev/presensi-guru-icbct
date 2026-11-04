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

<style>
    /* Custom calendar styling — only for inputs inside this component */
    #{{ $uuid }} {
        color-scheme: light;
        accent-color: #0F172A;
    }
    .dark #{{ $uuid }} {
        color-scheme: dark;
        accent-color: #FACC15;
    }
    /* Hide default calendar icon for Chrome/Safari, show custom one */
    #{{ $uuid }}::-webkit-calendar-picker-indicator {
        opacity: 0;
        cursor: pointer;
    }
    #{{ $uuid }}::-moz-focus-inner {
        border: 0;
        padding: 0;
    }
</style>

<div class="relative w-full" {{ $attributes->merge(['class' => $class]) }}>
    <div class="relative">
        <input type="date"
               id="{{ $uuid }}"
               name="{{ $name }}"
               value="{{ $value }}"
               min="{{ $min }}"
               max="{{ $max }}"
               {{ $required ? 'required' : '' }}
               {{ $disabled ? 'disabled' : '' }}
               class="w-full px-4 py-3 rounded-2xl border-2 transition-all duration-200 appearance-none text-sm font-medium
                      {{ $value ? 'border-navy-800/20 dark:border-slate-600 text-slate-900 dark:text-white' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-500 text-slate-400 dark:text-slate-500' }}
                      {{ $disabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer' }}"
               style="cursor:pointer; color: {{ $value ? '#0f172a' : '#94a3b8' }};"
               onfocus="this.classList.add('border-navy-800','dark:border-gold-400','shadow-lg','shadow-navy-900/10','dark:shadow-gold-400/10'); this.classList.remove('border-slate-200','dark:border-slate-700','hover:border-slate-300','dark:hover:border-slate-500');"
               onblur="this.classList.remove('border-navy-800','dark:border-gold-400','shadow-lg','shadow-navy-900/10','dark:shadow-gold-400/10'); if(!this.value){ this.classList.add('border-slate-200','dark:border-slate-700','hover:border-slate-300','dark:hover:border-slate-500'); }"
               onchange="var el=this; var lbl=el.closest('.relative').querySelector('.date-label');
                         if(el.value){
                             var d=new Date(el.value+'T00:00:00');
                             var m=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                             lbl.textContent=d.getDate()+' '+m[d.getMonth()]+' '+d.getFullYear();
                             lbl.style.color='#0f172a';
                             el.classList.remove('text-slate-400','dark:text-slate-500');
                             el.classList.add('text-slate-900','dark:text-white');
                         } else {
                             lbl.textContent='{{ $placeholder }}';
                             lbl.style.color='#94a3b8';
                             el.classList.remove('text-slate-900','dark:text-white');
                             el.classList.add('text-slate-400','dark:text-slate-500');
                         }
                         el.style.color=el.value?'#0f172a':'#94a3b8';"
        >
        <!-- Custom label overlay -->
        <span class="date-label absolute inset-0 flex items-center gap-3 px-4 pointer-events-none text-sm font-medium"
              style="color: {{ $value ? '#0f172a' : '#94a3b8' }};">
            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
            </svg>
            <span>{{ $displayValue ?: $placeholder }}</span>
            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 ml-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
            </svg>
        </span>
    </div>
</div>
