import { defineStore } from 'pinia';
import axios from 'axios';

export const useTrustAccountsStore = defineStore('trustAccounts', {
    state: () => ({
        accounts: [],
        stats: {
            totalBalance: 0,
            pendingWithdrawals: 0,
            activeAccounts: 0,
            thisMonth: 0,
        },
        clients: [],
        loading: false,
        error: null,
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 20,
            total: 0,
        }
    }),

    getters: {
        getAccountById: (state) => (id) => {
            return state.accounts.find(account => account.id === id);
        },

        getClientAccounts: (state) => (clientId) => {
            return state.accounts.filter(account => account.client_id === clientId);
        },

        isLoading: (state) => state.loading,

        hasError: (state) => !!state.error,

        getStats: (state) => state.stats,
    },

    actions: {
        async fetchAccounts(params = {}) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get('/trust-accounts', { params });

                // Handle different response formats
                const accountsData = response.data?.data || response.data || [];
                
                // Guard against empty or invalid response
                if (!Array.isArray(accountsData)) {
                    this.accounts = [];
                    return [];
                }
                
                // Transform data to match frontend expectations
                this.accounts = accountsData.map(account => ({
                    id: account.id,
                    client: account.client?.name || 'Unknown Client',
                    client_id: account.client_id,
                    accountNumber: account.reference_number,
                    balance: parseFloat(account.balance),
                    status: account.status,
                    lastActivity: account.transaction_date,
                    type: account.transaction_type,
                    amount: parseFloat(account.amount),
                    description: account.description,
                    transaction_date: account.transaction_date,
                    current_balance: parseFloat(account.current_balance || account.balance),
                }));

                this.pagination = {
                    current_page: response.data?.current_page || 1,
                    last_page: response.data?.last_page || 1,
                    per_page: response.data?.per_page || 20,
                    total: response.data?.total || 0,
                };

                return this.accounts;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch trust accounts';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchStats() {
            try {
                const response = await axios.get('/trust-accounts/stats');
                this.stats = {
                    totalBalance: parseFloat(response.data.totalBalance),
                    pendingWithdrawals: parseFloat(response.data.pendingWithdrawals),
                    activeAccounts: response.data.activeAccounts,
                    thisMonth: parseFloat(response.data.thisMonth),
                };
                return this.stats;
            } catch (error) {
                console.error('Failed to fetch trust accounts stats:', error);
                // Return default stats on error
                return this.stats;
            }
        },

        async createTransaction(transactionData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post('/trust-accounts', transactionData);

                // Refresh accounts after creating transaction
                await this.fetchAccounts();
                await this.fetchStats();

                return response.data.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to create transaction';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateTransaction(id, updateData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.put(`/trust-accounts/${id}`, updateData);

                // Update local state
                const index = this.accounts.findIndex(account => account.id === id);
                if (index !== -1) {
                    this.accounts[index] = {
                        ...this.accounts[index],
                        ...response.data.data,
                        balance: parseFloat(response.data.data.balance),
                        amount: parseFloat(response.data.data.amount),
                    };
                }

                return response.data.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to update transaction';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteTransaction(id) {
            this.loading = true;
            this.error = null;

            try {
                await axios.delete(`/trust-accounts/${id}`);

                // Remove from local state
                this.accounts = this.accounts.filter(account => account.id !== id);

                // Refresh stats
                await this.fetchStats();

                return true;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to delete transaction';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchClientBalance(clientId) {
            try {
                const response = await axios.get(`/clients/${clientId}/trust-balance`);
                return response.data;
            } catch (error) {
                console.error('Failed to fetch client balance:', error);
                throw error;
            }
        },

        async fetchClients() {
            try {
                const response = await axios.get('/clients/all');
                this.clients = response.data.data || response.data || [];
                return this.clients;
            } catch (error) {
                console.error('Failed to fetch clients:', error);
                return [];
            }
        },

        clearError() {
            this.error = null;
        },

        resetState() {
            this.accounts = [];
            this.stats = {
                totalBalance: 0,
                pendingWithdrawals: 0,
                activeAccounts: 0,
                thisMonth: 0,
            };
            this.loading = false;
            this.error = null;
            this.pagination = {
                current_page: 1,
                last_page: 1,
                per_page: 20,
                total: 0,
            };
        }
    }
});