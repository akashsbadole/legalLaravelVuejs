import { createRouter, createWebHashHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import('../pages/HomePage.vue'),
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../pages/LoginPage.vue'),
        meta: { guest: true }
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../pages/RegisterPage.vue'),
        meta: { guest: true }
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('../pages/DashboardPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/cases',
        name: 'cases',
        component: () => import('../pages/CasesPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/cases/:id',
        name: 'case.show',
        component: () => import('../pages/CaseShowPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/clients',
        name: 'clients',
        component: () => import('../pages/ClientsPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/clients/:id',
        name: 'client.show',
        component: () => import('../pages/ClientShowPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/billing',
        name: 'billing',
        component: () => import('../pages/BillingPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/documents',
        name: 'documents',
        component: () => import('../pages/DocumentsPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/tasks',
        name: 'tasks',
        component: () => import('../pages/TasksPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/calendar',
        name: 'calendar',
        component: () => import('../pages/CalendarPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/leads',
        name: 'leads',
        component: () => import('../pages/LeadsPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/reports',
        name: 'reports',
        component: () => import('../pages/ReportsPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/settings',
        name: 'settings',
        component: () => import('../pages/SettingsPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/profile',
        name: 'profile',
        component: () => import('../pages/ProfilePage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/trust-accounts',
        name: 'trust-accounts',
        component: () => import('../pages/TrustAccountsPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/crm',
        name: 'crm',
        component: () => import('../pages/CRMPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/search',
        name: 'search',
        component: () => import('../pages/SearchPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/tenants',
        name: 'tenants',
        component: () => import('../pages/TenantsPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/audit-logs',
        name: 'audit-logs',
        component: () => import('../pages/AuditLogsPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/notifications',
        name: 'notifications',
        component: () => import('../pages/NotificationsPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/portal',
        name: 'client-portal',
        component: () => import('../pages/client/ClientPortalPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/legal',
        name: 'legal',
        component: () => import('../pages/LegalSearchPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/legal/chatbot',
        name: 'legal-chatbot',
        component: () => import('../pages/LegalChatbotPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/legal/ipc',
        name: 'legal-ipc',
        component: () => import('../pages/IpcPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/documents',
        name: 'documents',
        component: () => import('../pages/DocumentManagementPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/chatbot/documents',
        name: 'chatbot-documents',
        component: () => import('../pages/ChatbotDocumentsPage.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('../pages/NotFoundPage.vue'),
    }
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
    scrollBehavior() {
        return { top: 0 };
    }
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // Check if we have a token from session (set by web login)
    if (!authStore.token) {
        try {
            // Check if Laravel set an API token in session
            const response = await fetch('/api/session-token', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
            if (response.ok) {
                const data = await response.json();
                if (data.token) {
                    authStore.token = data.token;
                    localStorage.setItem('token', data.token);
                    axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`;
                }
            }
        } catch (e) {
            // Ignore errors - user might not be logged in
        }
    }

    if (authStore.token && !authStore.user) {
        try {
            await authStore.fetchUser();
            await authStore.fetchSettings();
        } catch (e) {
            localStorage.removeItem('token');
            authStore.token = null;
            authStore.user = null;
            window.location.reload();
        }
    }

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next({ name: 'login', query: { redirect: to.fullPath } });
    } else if (to.meta.guest && authStore.isAuthenticated) {
        next({ name: 'dashboard' });
    } else {
        next();
    }
});

export default router;
