<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4">
        <div class="max-w-md w-full">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">LegalFirm</h1>
                <p class="mt-2 text-gray-600">Sign in to your account</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div v-if="error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm">
                    {{ error }}
                </div>

                <form @submit.prevent="handleLogin" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input v-model="form.email" type="email" required 
                               class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="admin@legalfirm.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input v-model="form.password" type="password" required 
                               class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" v-model="form.remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-500">Forgot password?</a>
                    </div>

                    <button type="submit" :disabled="loading"
                            class="w-full py-3 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <span v-if="loading">Signing in...</span>
                        <span v-else>Sign in</span>
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account? 
                        <router-link to="/register" class="text-blue-600 hover:text-blue-500 font-medium">Register</router-link>
                    </p>
                </div>

                <!-- Demo Credentials -->
                <div class="mt-6 pt-6 border-t">
                    <p class="text-xs text-gray-500 mb-3">Demo Credentials (click to fill):</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button @click="fillCredentials('admin@legalfirm.com', 'password')" 
                                class="px-3 py-2 text-xs bg-blue-50 hover:bg-blue-100 rounded-lg text-left">
                            <span class="block font-medium text-blue-700">Admin</span>
                            <span class="text-blue-500">admin@legalfirm.com</span>
                        </button>
                        <button @click="fillCredentials('jane@legalfirm.com', 'password')" 
                                class="px-3 py-2 text-xs bg-green-50 hover:bg-green-100 rounded-lg text-left">
                            <span class="block font-medium text-green-700">Attorney</span>
                            <span class="text-green-500">jane@legalfirm.com</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import useSeo from '../composables/useSeo';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const { setSeo } = useSeo();

const form = ref({
    email: '',
    password: '',
    remember: false
});

const loading = ref(false);
const error = ref('');

function fillCredentials(email, password) {
    form.value.email = email;
    form.value.password = password;
}

async function handleLogin() {
    loading.value = true;
    error.value = '';

    try {
        await authStore.login({
            email: form.value.email,
            password: form.value.password
        });

        const redirect = route.query.redirect || '/dashboard';
        window.location.hash = redirect;
        window.location.reload();
    } catch (err) {
        error.value = err.response?.data?.message || 'Invalid credentials. Please try again.';
    } finally {
        loading.value = false;
    }
}
</script>
