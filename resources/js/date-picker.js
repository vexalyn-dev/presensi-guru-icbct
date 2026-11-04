/**
 * Custom Date Picker — Alpine.js component
 * Renders a modern, navy/gold themed calendar popup
 */
window.customDatePicker = function (config) {
    const monthNames = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    const weekDays = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

    let today = new Date();
    today.setHours(0, 0, 0, 0);

    let curYear = today.getFullYear();
    let curMonth = today.getMonth();

    let selDate = null;
    if (config.initialValue) {
        const d = new Date(config.initialValue + 'T00:00:00');
        if (!isNaN(d.getTime())) {
            selDate = d;
            curYear = d.getFullYear();
            curMonth = d.getMonth();
        }
    }

    function formatDate(d) {
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${dd}`;
    }

    function generateDays() {
        const firstDay = new Date(curYear, curMonth, 1);
        const lastDay = new Date(curYear, curMonth + 1, 0);
        const startDayOfWeek = firstDay.getDay();

        const days = [];
        const prevMonthLastDay = new Date(curYear, curMonth, 0).getDate();

        for (let i = startDayOfWeek - 1; i >= 0; i--) {
            const day = prevMonthLastDay - i;
            const date = new Date(curYear, curMonth - 1, day);
            days.push({
                key: `prev_${day}`,
                label: day,
                date: formatDate(date),
                selectable: false,
                isToday: false,
                isSelected: false,
            });
        }

        for (let day = 1; day <= lastDay.getDate(); day++) {
            const date = new Date(curYear, curMonth, day);
            const isToday = date.getTime() === today.getTime();
            const isSelected = selDate && date.getTime() === selDate.getTime();
            const minDate = config.min ? new Date(config.min + 'T00:00:00') : null;
            const maxDate = config.max ? new Date(config.max + 'T00:00:00') : null;
            let selectable = true;
            if (minDate && date < minDate) selectable = false;
            if (maxDate && date > maxDate) selectable = false;

            days.push({
                key: `curr_${day}`,
                label: day,
                date: formatDate(date),
                selectable,
                isToday,
                isSelected,
            });
        }

        const remaining = 42 - days.length;
        for (let day = 1; day <= remaining; day++) {
            const date = new Date(curYear, curMonth + 1, day);
            days.push({
                key: `next_${day}`,
                label: day,
                date: formatDate(date),
                selectable: false,
                isToday: false,
                isSelected: false,
            });
        }

        return days;
    }

    return {
        name: config.name || 'date',
        id: config.id || 'date',
        placeholder: config.placeholder || 'Pilih tanggal',
        required: !!config.required,
        disabled: !!config.disabled,
        min: config.min || null,
        max: config.max || null,
        hasXModel: !!config.hasXModel,
        xModelKey: config.xModelKey || null,
        open: false,
        currentYear: curYear,
        currentMonth: curMonth,
        monthNames,
        weekDays,
        daysGrid: [],
        selectedDate: selDate ? formatDate(selDate) : '',
        displayValue: selDate
            ? selDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
            : '',

        init() {
            this.daysGrid = generateDays();

            // Sync with x-model if provided
            if (config.hasXModel && config.xModelKey) {
                const el = document.querySelector(`[x-model="${config.xModelKey}"]`);
                if (el) {
                    const observer = new MutationObserver(() => {
                        const val = el.getAttribute('value') || el.value;
                        if (val && val !== this.selectedDate) {
                            const d = new Date(val + 'T00:00:00');
                            if (!isNaN(d.getTime())) {
                                selDate = d;
                                this.selectedDate = val;
                                this.displayValue = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                                curYear = d.getFullYear();
                                curMonth = d.getMonth();
                                this.currentYear = curYear;
                                this.currentMonth = curMonth;
                                this.daysGrid = generateDays();
                            }
                        }
                    });
                    observer.observe(el, { attributes: true, attributeFilter: ['value'] });
                }
            }
        },

        prevMonth() {
            curMonth--;
            if (curMonth < 0) { curMonth = 11; curYear--; }
            this.currentMonth = curMonth;
            this.currentYear = curYear;
            this.daysGrid = generateDays();
        },

        nextMonth() {
            curMonth++;
            if (curMonth > 11) { curMonth = 0; curYear++; }
            this.currentMonth = curMonth;
            this.currentYear = curYear;
            this.daysGrid = generateDays();
        },

        selectDate(dateStr) {
            const d = new Date(dateStr + 'T00:00:00');
            selDate = d;
            this.selectedDate = dateStr;
            this.displayValue = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            this.open = false;

            if (config.hasXModel && config.xModelKey) {
                const el = document.querySelector(`[x-model="${config.xModelKey}"]`);
                if (el) el.value = dateStr;
            }

            this.$dispatch('date-selected', { name: config.name, value: dateStr });
        },

        selectToday() {
            this.selectDate(formatDate(today));
        },

        clearDate() {
            selDate = null;
            this.selectedDate = '';
            this.displayValue = '';
            this.open = false;

            if (config.hasXModel && config.xModelKey) {
                const el = document.querySelector(`[x-model="${config.xModelKey}"]`);
                if (el) el.value = '';
            }

            this.$dispatch('date-selected', { name: config.name, value: '' });
        },
    };
};
