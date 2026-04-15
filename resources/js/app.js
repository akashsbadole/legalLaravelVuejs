import "./bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia";
import router from "./router";
import App from "./App.vue";
import axios from "axios";
import { useAuthStore } from "./stores/auth";

const app = createApp(App);
const pinia = createPinia();

axios.defaults.baseURL = "/api";
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const token = localStorage.getItem("token");
if (token) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}

// Axios interceptor to add tenant scoping to API calls
axios.interceptors.request.use(
    (config) => {
        // Skip tenant scoping for endpoints that are NOT under tenant/{tenant} prefix
        // These endpoints either handle their own tenant scoping or are global
        const skipTenantEndpoints = [
            "/login",
            "/register",
            "/logout",
            "/user",
            "/settings",
            "/session-token",
            "/portal",
            "/admin",
            "/reports",
            "/legal", // covers all legal API routes (legal/chatbot, legal/ipc, legal/crpc, legal/cpc, legal/constitution)
            "/chatbot/documents",
            "/search", // global search
        ];

        // Check if the URL starts with any of the skip endpoints
        const shouldSkipTenant = skipTenantEndpoints.some((endpoint) =>
            config.url.startsWith(endpoint),
        );

        if (!shouldSkipTenant && window.authStore?.tenantId) {
            // Prepend tenant path to the URL
            config.url = `/tenant/${window.authStore.tenantId}${config.url}`;
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    },
);

app.use(pinia);
app.use(router);

// Make auth store globally available for axios interceptor
const authStore = (window.authStore = useAuthStore());

app.mount("#app");
