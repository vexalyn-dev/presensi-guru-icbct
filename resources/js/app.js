import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('customDatePicker', (config) => ({
    open: false,
    id: config.id,
    name: config.name,
    placeholder: config.placeholder,
    selectedDate: config.initialValue || '',
    disabled: config.disabled,
    required: config.required,
    min: config.min,
    max: config.max,
    currentMonth: 0,
    currentYear: 0,
    monthNames: [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ],
    weekDays: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],

    init() {
        const selected = this.parseDate(this.selectedDate) || new Date();
        this.currentMonth = selected.getMonth();
        this.currentYear = selected.getFullYear();
    },

    parseDate(value) {
        if (!value) return null;
        const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(value);
        if (!match) return null;

        const date = new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3]));
        return date.getFullYear() === Number(match[1])
            && date.getMonth() === Number(match[2]) - 1
            && date.getDate() === Number(match[3])
            ? date
            : null;
    },

    formatDate(date) {
        return [
            date.getFullYear(),
            String(date.getMonth() + 1).padStart(2, '0'),
            String(date.getDate()).padStart(2, '0'),
        ].join('-');
    },

    get displayValue() {
        const date = this.parseDate(this.selectedDate);
        return date
            ? date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
            : '';
    },

    get todaySelectable() {
        const today = this.formatDate(new Date());
        return (!this.min || today >= this.min) && (!this.max || today <= this.max);
    },

    get daysGrid() {
        const firstDay = new Date(this.currentYear, this.currentMonth, 1);
        const offset = (firstDay.getDay() + 6) % 7;
        const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
        const today = this.formatDate(new Date());
        const days = Array.from({ length: 42 }, (_, index) => {
            const dayNumber = index - offset + 1;
            if (dayNumber < 1 || dayNumber > daysInMonth) {
                return { key: `blank-${index}`, label: '', selectable: false };
            }

            const date = this.formatDate(new Date(this.currentYear, this.currentMonth, dayNumber));
            const selectable = (!this.min || date >= this.min) && (!this.max || date <= this.max);

            return {
                key: date,
                date,
                label: dayNumber,
                selectable,
                isToday: date === today,
                isSelected: date === this.selectedDate,
            };
        });

        return days;
    },

    prevMonth() {
        if (this.currentMonth === 0) {
            this.currentMonth = 11;
            this.currentYear--;
        } else {
            this.currentMonth--;
        }
    },

    nextMonth() {
        if (this.currentMonth === 11) {
            this.currentMonth = 0;
            this.currentYear++;
        } else {
            this.currentMonth++;
        }
    },

    selectDate(value) {
        const date = this.parseDate(value);
        if (!date) return;

        const formatted = this.formatDate(date);
        if ((this.min && formatted < this.min) || (this.max && formatted > this.max)) return;

        this.selectedDate = formatted;
        this.open = false;
        const input = this.$root.querySelector('input[type="hidden"]');
        input.value = formatted;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    },

    selectToday() {
        this.selectDate(this.formatDate(new Date()));
    },

    clearDate() {
        if (this.required) return;
        this.selectedDate = '';
        this.open = false;
        const input = this.$root.querySelector('input[type="hidden"]');
        input.value = '';
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    },
}));

Alpine.start();
