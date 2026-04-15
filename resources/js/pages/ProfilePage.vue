<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Profile</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage your account settings</p>
            </div>

            <div v-if="loading" class="flex justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
            </div>

            <div v-else class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h2>
                    
                    <form @submit.prevent="updateProfile" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                <input v-model="form.name" type="text" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input v-model="form.email" type="email" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                <input v-model="form.phone" type="text" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                                <input :value="user?.role" type="text" disabled
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-gray-500 dark:text-gray-400 shadow-sm">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                            <textarea v-model="form.address" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" :disabled="saving"
                                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50">
                                {{ saving ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Change Password</h2>
                    
                    <form @submit.prevent="changePassword" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password</label>
                            <input v-model="passwordForm.current_password" type="password"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                                <input v-model="passwordForm.password" type="password"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                                <input v-model="passwordForm.password_confirmation" type="password"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" :disabled="changingPassword"
                                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50">
                                {{ changingPassword ? 'Changing...' : 'Change Password' }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Preferences</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Dark Mode</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Toggle dark theme</p>
                            </div>
                            <button @click="toggleDarkMode" 
                                :class="darkMode ? 'bg-primary-600' : 'bg-gray-300 dark:bg-gray-600'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                <span :class="darkMode ? 'translate-x-6' : 'translate-x-1'" 
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import AppLayout from '../layouts/AppLayout.vue';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';

const authStore = useAuthStore();
const user = computed(() => authStore.user);

const loading = ref(true);
const saving = ref(false);
const changingPassword = ref(false);
const darkMode = ref(localStorage.getItem('darkMode') === 'true');

const form = reactive({
    name: '',
    email: '',
    phone: '',
    address: ''
});

const passwordForm = reactive({
    current_password: '',
    password: '',
    password_confirmation: ''
});

const loadProfile = async () => {
    try {
        const response = await axios.get('/user');
        Object.assign(form, {
            name: response.data.name,
            email: response.data.email,
            phone: response.data.phone || '',
            address: response.data.address || ''
        });
    } catch (error) {
        console.error('Failed to load profile:', error);
    } finally {
        loading.value = false;
    }
};

const updateProfile = async () => {
    saving.value = true;
    try {
        const response = await axios.put('/user', form);
        authStore.user = response.data;
        alert('Profile updated successfully!');
    } catch (error) {
        alert('Failed to update profile: ' + (error.response?.data?.message || error.message));
    } finally {
        saving.value = false;
    }
};

const changePassword = async () => {
    if (passwordForm.password !== passwordForm.password_confirmation) {
        alert('Passwords do not match!');
        return;
    }
    
    changingPassword.value = true;
    try {
        await axios.put('/user/password', passwordForm);
        alert('Password changed successfully!');
        passwordForm.current_password = '';
        passwordForm.password = '';
        passwordForm.password_confirmation = '';
    } catch (error) {
        alert('Failed to change password: ' + (error.response?.data?.message || error.message));
    } finally {
        changingPassword.value = false;
    }
};

const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;
    localStorage.setItem('darkMode', darkMode.value);
    document.documentElement.classList.toggle('dark', darkMode.value);
};

onMounted(() => {
    loadProfile();
    document.documentElement.classList.toggle('dark', darkMode.value);
});
</script>
