import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
        tenantId: null,
        loading: false,
        settings: {
            currency: 'USD',
            currency_symbol: '$',
            site_name: 'LegalFirm'
        },
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        isAdmin: (state) => state.user?.role === 'admin',
        isAttorney: (state) => ['admin', 'attorney'].includes(state.user?.role),
        currentUser: (state) => state.user,
        currencySymbol: (state) => state.settings.currency_symbol || '$',
        tenantId: (state) => state.tenantId,
    },

    actions: {
        async login(credentials) {
            this.loading = true;
            try {
                const response = await axios.post('/login', credentials);
                this.token = response.data.token;
                this.user = response.data.user;
                this.tenantId = response.data.user.tenant_id;
                localStorage.setItem('token', this.token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                await this.fetchSettings();
                return response.data;
            } catch (error) {
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            try {
                await axios.post('/logout');
            } catch (error) {
                console.error('Logout error:', error);
            } finally {
                this.token = null;
                this.user = null;
                this.tenantId = null;
                localStorage.removeItem('token');
                delete axios.defaults.headers.common['Authorization'];
            }
        },

        async fetchUser() {
            if (!this.token) return null;

            try {
                axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                const response = await axios.get('/user');
                this.user = response.data;
                this.tenantId = response.data.tenant_id;
                return this.user;
            } catch (error) {
                this.logout();
                throw error;
            }
        },

        async fetchSettings() {
            try {
                const response = await axios.get('/settings');
                this.settings = { ...this.settings, ...response.data };
            } catch (error) {
                console.error('Failed to fetch settings:', error);
            }
        },

        async updateSettings(newSettings) {
            try {
                await axios.put('/settings', newSettings);
                this.settings = { ...this.settings, ...newSettings };
                return true;
            } catch (error) {
                console.error('Failed to update settings:', error);
                return false;
            }
        },

        async register(data) {
            this.loading = true;
            try {
                const response = await axios.post('/register', data);
                this.token = response.data.token;
                this.user = response.data.user;
                this.tenantId = response.data.user.tenant_id;
                localStorage.setItem('token', this.token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                return response.data;
            } catch (error) {
                throw error;
            } finally {
                this.loading = false;
            }
        }
    }
});
