<template>
    <div>
        <router-view />
    </div>
</template>

<script setup>
import { onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from './stores/auth';
import useSeo from './composables/useSeo';

const route = useRoute();
const authStore = useAuthStore();
const { setSeo, initRouteSeo } = useSeo();

onMounted(async () => {
    if (authStore.token && !authStore.user) {
        try {
            await authStore.fetchUser();
            await authStore.fetchSettings();
        } catch (error) {
            console.log('Session expired or invalid');
        }
    } else if (authStore.token && authStore.user) {
        await authStore.fetchSettings();
    }
    
    // Initialize SEO on mount
    initRouteSeo();
});

// Watch route changes and update SEO
watch(
    () => route.name,
    () => {
        initRouteSeo();
    }
);
</script>