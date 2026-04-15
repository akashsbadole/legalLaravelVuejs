<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Calendar</h1>
                    <p class="text-gray-600 dark:text-gray-400">Schedule and events</p>
                </div>
                <button @click="showAddModal = true"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Add Event
                </button>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex justify-between items-center mb-4 md:mb-6">
                    <button @click="previousMonth" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ currentMonthName }} {{ currentYear }}
                    </h2>
                    <button @click="nextMonth" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                <div class="hidden md:grid grid-cols-7 gap-1 mb-2">
                    <div v-for="day in weekDays" :key="day"
                        class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">
                        {{ day }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <div class="min-w-[700px] grid grid-cols-7 gap-1">
                        <div v-for="(day, index) in calendarDays" :key="index"
                            :class="[
                                'min-h-[80px] md:min-h-[100px] p-1 md:p-2 border border-gray-200 dark:border-gray-700 rounded',
                                day.isCurrentMonth ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900',
                                day.isToday ? 'ring-2 ring-primary-500' : ''
                            ]">
                            <div class="text-xs md:text-sm font-medium text-gray-900 dark:text-white mb-1">
                                {{ day.date }}
                            </div>
                            <div class="space-y-1">
                                <div v-for="event in day.events.slice(0, 2)" :key="event.id"
                                    @click="selectedEvent = event"
                                    :class="[
                                        'text-[10px] md:text-xs p-0.5 md:p-1 rounded truncate cursor-pointer',
                                        event.type === 'case' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                        event.type === 'task' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                        event.type === 'meeting' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' :
                                        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                                    ]">
                                    {{ event.title }}
                                </div>
                                <div v-if="day.events.length > 2" class="text-[10px] text-gray-500 dark:text-gray-400">
                                    +{{ day.events.length - 2 }} more
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ editingEventId ? 'Edit Event' : 'Add Event' }}
                    </h3>
                    
                    <form @submit.prevent="addEvent" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                            <input v-model="newEvent.title" type="text" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                            <select v-model="newEvent.type"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="meeting">Meeting</option>
                                <option value="case">Case Hearing</option>
                                <option value="task">Task Due</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                <input v-model="newEvent.date" type="date" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time</label>
                                <input v-model="newEvent.time" type="time"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <textarea v-model="newEvent.description" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                        </div>
                        
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="resetNewEvent(); showAddModal = false"
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                                {{ editingEventId ? 'Update Event' : 'Add Event' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div v-if="selectedEvent" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ selectedEvent.title }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        {{ formatDate(selectedEvent.date) }} at {{ selectedEvent.time || 'All day' }}
                    </p>
                    <p v-if="selectedEvent.description" class="text-gray-700 dark:text-gray-300 mb-4">
                        {{ selectedEvent.description }}
                    </p>
                    <div class="flex justify-end">
                        <button @click="openEditModal(selectedEvent)"
                            class="px-4 py-2 text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900 rounded-lg mr-2">
                            Edit
                        </button>
                        <button @click="deleteEvent(selectedEvent.id); selectedEvent = null"
                            class="px-4 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900 rounded-lg mr-2">
                            Delete
                        </button>
                        <button @click="selectedEvent = null"
                            class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import AppLayout from '../layouts/AppLayout.vue';
import axios from 'axios';

const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
const currentDate = ref(new Date());
const events = ref([]);
const showAddModal = ref(false);
const selectedEvent = ref(null);
const editingEventId = ref(null);

const newEvent = reactive({
    title: '',
    type: 'meeting',
    date: '',
    time: '',
    description: ''
});

const currentMonthName = computed(() => {
    return currentDate.value.toLocaleString('default', { month: 'long' });
});

const currentYear = computed(() => {
    return currentDate.value.getFullYear();
});

const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startPadding = firstDay.getDay();
    const totalDays = lastDay.getDate();
    
    const days = [];
    
    const prevMonth = new Date(year, month, 0);
    for (let i = startPadding - 1; i >= 0; i--) {
        const date = prevMonth.getDate() - i;
        days.push({
            date,
            isCurrentMonth: false,
            isToday: false,
            events: []
        });
    }
    
    const today = new Date();
    for (let i = 1; i <= totalDays; i++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
        days.push({
            date: i,
            isCurrentMonth: true,
            isToday: today.getDate() === i && today.getMonth() === month && today.getFullYear() === year,
            events: events.value.filter(e => e.date === dateStr)
        });
    }
    
    const remaining = 42 - days.length;
    for (let i = 1; i <= remaining; i++) {
        days.push({
            date: i,
            isCurrentMonth: false,
            isToday: false,
            events: []
        });
    }
    
    return days;
});

const previousMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1);
};

const nextMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1);
};

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('en-US', { 
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
    });
};

const addEvent = async () => {
    try {
        const payload = {
            title: newEvent.title,
            type: newEvent.type === 'case' ? 'case_hearing' : newEvent.type,
            start_date: newEvent.date,
            start_time: newEvent.time || null,
            description: newEvent.description
        };
        
        if (editingEventId.value) {
            // Update existing event
            const response = await axios.put(`/api/calendar/events/${editingEventId.value}`, payload);
            const index = events.value.findIndex(e => e.id === editingEventId.value);
            if (index !== -1) {
                events.value[index] = {
                    ...events.value[index],
                    ...response.data.event
                };
            }
        } else {
            // Create new event
            const response = await axios.post('/api/calendar/events', payload);
            events.value.push({
                id: response.data.event.id,
                title: response.data.event.title,
                type: response.data.event.type,
                start_date: response.data.event.start_date,
                start_time: response.data.event.start_time,
                description: newEvent.description
            });
        }
        
        showAddModal.value = false;
        resetNewEvent();
    } catch (error) {
        console.error('Failed to save event:', error);
        alert('Failed to save event. Please try again.');
    }
};

const openEditModal = (event) => {
    editingEventId.value = event.id;
    newEvent.title = event.title;
    // Convert type back: stored type may be 'case_hearing' but select uses 'case'
    newEvent.type = event.type === 'case_hearing' ? 'case' : event.type;
    newEvent.date = event.start_date; // the event object has start_date
    newEvent.time = event.start_time || '';
    newEvent.description = event.description || '';
    showAddModal.value = true;
    selectedEvent.value = null; // close detail modal
};

const resetNewEvent = () => {
    Object.assign(newEvent, { title: '', type: 'meeting', date: '', time: '', description: '' });
    editingEventId.value = null;
};

const deleteEvent = async (id) => {
    if (confirm('Are you sure you want to delete this event?')) {
        try {
            await axios.delete(`/api/calendar/events/${id}`);
            events.value = events.value.filter(e => e.id !== id);
            selectedEvent.value = null;
        } catch (error) {
            console.error('Failed to delete event:', error);
            alert('Failed to delete event. Please try again.');
        }
    }
};

const loadEvents = async () => {
    try {
        const year = currentDate.value.getFullYear();
        const month = currentDate.value.getMonth();
        const startDate = new Date(year, month, 1).toISOString().split('T')[0];
        const endDate = new Date(year, month + 1, 0).toISOString().split('T')[0];
        
        const response = await axios.get(`/api/calendar/events?start_date=${startDate}&end_date=${endDate}`);
        events.value = response.data.events || [];
    } catch (error) {
        console.error('Failed to load events:', error);
    }
};

onMounted(() => {
    loadEvents();
});
</script>
