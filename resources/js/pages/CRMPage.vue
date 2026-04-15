<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">CRM Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage client interactions and relationships</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Leads</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.totalLeads }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Active Interactions</p>
                    <p class="text-2xl font-bold text-blue-600">{{ stats.activeInteractions }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending Follow-ups</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ stats.pendingFollowUps }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Conversion Rate</p>
                    <p class="text-2xl font-bold text-green-600">{{ stats.conversionRate }}%</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Interactions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Interactions</h2>
                    <div class="space-y-4">
                        <div v-for="interaction in interactions" :key="interaction.id"
                            class="flex items-start space-x-4 pb-4 border-b border-gray-100 dark:border-gray-700 last:border-0">
                            <div :class="getTypeColor(interaction.type)" class="p-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getTypeIcon(interaction.type)"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ interaction.title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ interaction.client }} - {{ formatDate(interaction.date) }}</p>
                            </div>
                            <span :class="getStatusClass(interaction.status)" class="px-2 py-1 text-xs rounded-full">
                                {{ interaction.status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Follow-ups -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Upcoming Follow-ups</h2>
                    <div class="space-y-3">
                        <div v-for="followUp in followUps" :key="followUp.id"
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 mr-3">
                                    {{ followUp.client.charAt(0) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ followUp.title }}</p>
                                    <p class="text-xs text-gray-500">{{ followUp.client }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatDate(followUp.dueDate) }}</p>
                                <p class="text-xs text-gray-500">{{ followUp.time }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leads Pipeline -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mt-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Lead Pipeline</h2>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div v-for="stage in pipeline" :key="stage.name"
                        class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-medium text-gray-900 dark:text-white">{{ stage.name }}</h3>
                            <span class="bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded-full text-xs">{{ stage.count }}</span>
                        </div>
                        <div class="space-y-2">
                            <div v-for="lead in stage.leads.slice(0, 3)" :key="lead.id"
                                class="bg-white dark:bg-gray-800 rounded p-2 text-sm">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ lead.name }}</p>
                                <p class="text-xs text-gray-500">{{ lead.value }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '../layouts/AppLayout.vue';
import axios from 'axios';

const stats = ref({
    totalLeads: 0,
    activeInteractions: 0,
    pendingFollowUps: 0,
    conversionRate: 0
});

const interactions = ref([]);
const followUps = ref([]);
const pipeline = ref([]);

const loadCRMData = async () => {
    try {
        const [leadsRes, interactionsRes, followUpsRes] = await Promise.all([
            axios.get('/api/leads'),
            axios.get('/api/crm/interactions'),
            axios.get('/api/crm/follow-ups')
        ]);

        const leads = leadsRes.data.data || leadsRes.data || [];
        stats.value.totalLeads = leads.length;
        
        const now = new Date();
        stats.value.activeInteractions = (interactionsRes.data.data || interactionsRes.data || [])
            .filter(i => i.status !== 'completed').length;
        
        const upcomingFollowUps = (followUpsRes.data.data || followUpsRes.data || [])
            .filter(f => new Date(f.due_date) >= now);
        stats.value.pendingFollowUps = upcomingFollowUps.length;
        
        stats.value.conversionRate = leads.length > 0 
            ? Math.round((leads.filter(l => l.status === 'converted' || l.status === 'won').length / leads.length) * 100) 
            : 0;

        interactions.value = (interactionsRes.data.data || interactionsRes.data || []).slice(0, 5).map(i => ({
            id: i.id,
            title: i.title || 'Interaction',
            client: i.client?.name || 'Unknown',
            type: i.type || 'call',
            status: i.status || 'pending',
            date: i.created_at
        }));

        followUps.value = upcomingFollowUps.slice(0, 5).map(f => ({
            id: f.id,
            title: f.title || 'Follow-up',
            client: f.client?.name || 'Unknown',
            dueDate: f.due_date,
            time: f.due_time || ''
        }));

        const pipelineStages = ['new', 'contacted', 'qualified', 'proposal', 'negotiation'];
        pipeline.value = pipelineStages.map(stage => {
            const stageLeads = leads.filter(l => (l.status || '').toLowerCase() === stage);
            return {
                name: stage.charAt(0).toUpperCase() + stage.slice(1),
                count: stageLeads.length,
                leads: stageLeads.slice(0, 3).map(l => ({
                    id: l.id,
                    name: l.name || l.client?.name || 'Unknown',
                    value: `$${(l.value || l.estimated_value || 0).toLocaleString()}`
                }))
            };
        });
    } catch (error) {
        console.error('Failed to load CRM data:', error);
    }
};

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function getTypeColor(type) {
    const colors = {
        call: 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300',
        email: 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300',
        meeting: 'bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300'
    };
    return colors[type] || colors.call;
}

function getTypeIcon(type) {
    const icons = {
        call: 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
        email: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        meeting: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
    };
    return icons[type] || icons.call;
}

function getStatusClass(status) {
    const classes = {
        completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        scheduled: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
    };
    return classes[status] || classes.pending;
}

onMounted(() => {
    loadCRMData();
});
</script>
