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
    'icon' => true,
    'xModel' => null,
])

<?php
$uuid = $id ?? 'cal_' . uniqid();
$hasXModel = $xModel !== null;
$defaultValue = $value ?? ($hasXModel ? '' : null);
$initValueJs = $defaultValue ? json_encode($defaultValue) : 'null';
?>

<div x-data="customDatePicker({
    name: '{{ $name }}',
    id: '{{ $uuid }}',
    initialValue: {!! $initValueJs !!},
    placeholder: '{{ $placeholder }}',
    required: {{ $required ? 'true' : 'false' }},
    disabled: {{ $disabled ? 'true' : 'false' }},
    min: {!! $min ? json_encode($min) : 'null' !!},
    max: {!! $max ? json_encode($max) : 'null' !!},
    hasXModel: {{ $hasXModel ? 'true' : 'false' }},
    xModelKey: '{{ $xModel }}',
})"
     x-init="init()"
     @click.outside="open = false"
     class="relative inline-block w-full"
     {{ $attributes->merge(['class' => '']) }}>

    <!-- Trigger Input -->
    <div class="relative" :class="disabled ? 'pointer-events-none opacity-60' : ''">
        <button type="button"
                tabindex="-1"
                class="absolute inset-0 w-full h-full opacity-0 cursor-text"
                @click="$el.closest('.relative').querySelector('div.flex').click()"></button>

        <div class="flex items-center gap-3 px-4 py-3 rounded-2xl border-2 transition-all duration-200"
             :class="open
                 ? 'border-navy-800 dark:border-gold-400 shadow-lg shadow-navy-900/10 dark:shadow-gold-400/10'
                 : selectedDate
                     ? 'border-navy-800/20 dark:border-slate-600'
                     : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-500'"
             @click="!disabled && (open = !open)">

            @if ($icon)
            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-colors duration-200"
                 :class="open ? 'text-navy-800 dark:text-gold-400' : ''"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
            </svg>
            @endif

            <span class="flex-1 text-sm font-medium truncate"
                  :class="selectedDate ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500'"
                  x-text="displayValue || placeholder"></span>

            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform duration-200"
                 :class="open ? 'rotate-180 text-navy-800 dark:text-gold-400' : ''"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
            </svg>
        </div>

        <!-- Hidden Input -->
        <input type="hidden"
               :name="name"
               :id="id"
               :value="selectedDate"
               :required="required"
               x-model.trim="{{ $hasXModel ? $xModel : 'selectedDate' }}"/>
    </div>

    <!-- Calendar Popup -->
    <template x-if="open">
        <div x-show="open"
             @click.outside="open = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
             class="absolute z-50 top-full mt-2 left-0 w-[320px] sm:w-[340px] bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl shadow-slate-900/15 dark:shadow-black/40 overflow-hidden">

            <!-- Header -->
            <div class="flex items-center justify-between px-4 pt-4 pb-3">
                <button type="button"
                        @click="prevMonth()"
                        class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-navy-800 dark:hover:bg-gold-400 text-slate-500 hover:text-white dark:hover:text-navy-900 flex items-center justify-center transition-all duration-200 active:scale-90">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 19.5-7.5-7.5 7.5-7.5"/>
                    </svg>
                </button>

                <div class="text-center">
                    <span class="text-sm font-bold text-slate-900 dark:text-white" x-text="monthNames[currentMonth] + ' ' + currentYear"></span>
                </div>

                <button type="button"
                        @click="nextMonth()"
                        class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-navy-800 dark:hover:bg-gold-400 text-slate-500 hover:text-white dark:hover:text-navy-900 flex items-center justify-center transition-all duration-200 active:scale-90">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                    </svg>
                </button>
            </div>

            <!-- Weekday Headers -->
            <div class="grid grid-cols-7 px-3 pb-2">
                <template x-for="(day, i) in weekDays" :key="day">
                    <div class="text-center text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 py-1"
                         x-text="day"></div>
                </template>
            </div>

            <!-- Days Grid -->
            <div class="grid grid-cols-7 px-3 pb-3 gap-1">
                <template x-for="day in daysGrid" :key="day.key">
                    <button type="button"
                            @click="day.selectable && selectDate(day.date)"
                            :disabled="!day.selectable"
                            class="relative h-9 rounded-xl text-sm font-semibold transition-all duration-150 flex items-center justify-center disabled:cursor-not-allowed"
                            :class="[
                                day.isToday && !day.isSelected
                                    ? 'bg-navy-800/10 dark:bg-gold-400/20 text-navy-800 dark:text-gold-400 ring-1 ring-navy-800/30 dark:ring-gold-400/40'
                                    : day.isSelected
                                        ? 'bg-gradient-to-br from-navy-800 to-navy-900 dark:from-gold-400 dark:to-gold-500 text-white dark:text-navy-900 shadow-md shadow-navy-900/20 dark:shadow-gold-400/20 scale-105'
                                        : day.selectable
                                            ? 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60'
                                            : 'text-slate-300 dark:text-slate-600'
                            ]"
                            x-text="day.label"></template>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between px-4 pb-4">
                <button type="button"
                        @click="selectToday()"
                        class="text-[11px] font-bold text-navy-800 dark:text-gold-400 hover:text-navy-900 dark:hover:text-gold-500 transition-colors px-3 py-1.5 rounded-lg hover:bg-navy-800/5 dark:hover:bg-gold-400/10">
                    Hari Ini
                </button>
                <button type="button"
                        x-show="selectedDate"
                        @click="clearDate()"
                        class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors px-3 py-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50">
                    Hapus
                </button>
            </div>
        </div>
    </template>
</div>

