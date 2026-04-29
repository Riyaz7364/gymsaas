import './bootstrap';

import ApexCharts from 'apexcharts';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

// Reuse an existing Alpine instance when another script has already loaded it.
// Otherwise, load Alpine for plain Blade pages that still depend on x-data.
if (!window.Alpine) {
    import('alpinejs').then(({ default: Alpine }) => {
        if (!window.Alpine) {
            window.Alpine = Alpine;
            window.Alpine.start();
        }
    });
}

// ApexCharts
window.ApexCharts = ApexCharts;

// FullCalendar factory helper used by the events page
window.GymHubCalendar = function (el, events, options = {}) {
    const cal = new Calendar(el, {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
        },
        events,
        ...options,
    });

    cal.render();

    return cal;
};

// Progress bar on Livewire navigations
document.addEventListener('livewire:navigating', () => {
    document.getElementById('nprogress-bar')?.classList.remove('hidden');
});

document.addEventListener('livewire:navigated', () => {
    document.getElementById('nprogress-bar')?.classList.add('hidden');
});
