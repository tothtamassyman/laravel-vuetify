import { useAuthStore } from '@/stores/authStore.js';

export async function globalBeforeEachGuard(to, from, next) {
    const authStore = useAuthStore();

    const isAuthenticated = !!authStore.token;
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);

    if (requiresAuth && !isAuthenticated) {
        console.warn(
            `Unauthorized access attempt to: ${to.fullPath}. Redirecting to: /login`
        );
        return next({ name: 'Login' });
    }

    if (!requiresAuth && isAuthenticated && (to.name === 'Login' || to.path === '/login')) {
        console.warn(
            `Authenticated user attempted to access: ${to.fullPath}. Redirecting to: /dashboard`
        );
        return next({ name: 'Dashboard' });
    }

    next();

    document.title = to.meta.title || import.meta.env.VITE_APP_NAME || '';

    if (import.meta.env.MODE === 'development') {
        console.log(`[${new Date().toISOString()}] Navigating from ${from.fullPath} to ${to.fullPath}`);
    }
}
