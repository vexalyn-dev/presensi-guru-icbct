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
    <div class="relative">
        <!-- Custom styled wrapper -->
        <div class="flex items-center gap-3 px-4 py-3 rounded-2xl border-2 transition-all duration-200 w-full"
             id="wrap_{{ $uuid }}"
             {{ $disabled ? 'style="pointer-events:none;opacity:0.6"' : 'onclick="document.getElementById(\'inp_'.$uuid.'\').showPicker()"' }}>

            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
            </svg>

            <span id="lbl_{{ $uuid }}"
                  class="flex-1 text-sm font-medium transition-colors duration-200"
                  style="color: {{ $value ? '#0f172a' : '#94a3b8' }};">
                {{ $displayValue ?: $placeholder }}
            </span>

            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
            </svg>
        </div>

        <!-- Native hidden input — showPicker() called by wrapping div -->
        <input type="date"
               id="inp_{{ $uuid }}"
               name="{{ $name }}"
               value="{{ $value }}"
               min="{{ $min }}"
               max="{{ $max }}"
               {{ $required ? 'required' : '' }}
               {{ $disabled ? 'disabled' : '' }}
               style="position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%;"
               onchange="var d=new Date(this.value+'T00:00:00');
                        var m=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                        var lbl=document.getElementById('lbl_{{ $uuid }}');
                        if(this.value){
                            lbl.textContent=d.getDate()+' '+m[d.getMonth()]+' '+d.getFullYear();
                            lbl.style.color='#0f172a';
                            document.getElementById('wrap_{{ $uuid }}').classList.add('has-value');
                        } else {
                            lbl.textContent='{{ $placeholder }}';
                            lbl.style.color='#94a3b8';
                            document.getElementById('wrap_{{ $uuid }}').classList.remove('has-value');
                        }"
        />
    </div>
</div>

<style>
    #wrap_{{ $uuid }}.has-value {
        border-color: rgba(15, 23, 42, 0.2) !important;
    }
    .dark #wrap_{{ $uuid }}.has-value {
        border-color: rgb(71, 85, 105) !important;
    }
</style>
