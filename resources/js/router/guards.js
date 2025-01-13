/**
 * Import required Vue utilities and external modules.
 */
import { useAuthStore } from '@/stores/authStore.js';
import { i18nInstance } from '@/plugins/i18n.js';
import { useToast } from 'vue-toastification';

/**
 * Ensures that the i18n module is initialized.
 * If not initialized, displays a warning toast and logs an error.
 * @returns {boolean} True if i18n is initialized, otherwise false.
 */
const ensureI18nInitialized = () => {
    const toast = useToast();
    if (!i18nInstance) {
        console.error(t('errors.guard.i18n_not_initialized'));
        const t = (key) => key; // Minimal fallback for translation keys
        toast.warning(t('errors.guard.i18n_not_initialized'));
        return false;
    }
    return true;
};

/**
 * Global beforeEach navigation guard.
 * Handles authentication, permission checks, and redirections.
 */
export async function beforeEachGuard(to, from, next) {
    const toast = useToast(); // Initialize toast instance
    if (!ensureI18nInitialized()) {
        return next(false); // Stop navigation if i18n is not initialized
    }

    const t = i18nInstance.global.t; // Translation function
    const authStore = useAuthStore();

    const isAuthenticated = authStore.isAuthenticated;
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);

    // Redirect unauthenticated users attempting to access protected routes
    if (requiresAuth && !isAuthenticated) {
        console.warn(`Unauthorized access attempt to: ${to.fullPath}. Redirecting to: /login`);
        toast.warning(t('errors.guard.login_required'));
        return next({ name: 'Login' });
    }

    // Prevent authenticated users from accessing public routes like /login
    if (!requiresAuth && isAuthenticated && (to.name === 'Login' || to.path === '/login')) {
        console.warn(`Authenticated user attempted to access: ${to.fullPath}. Redirecting to: /dashboard`);
        toast.info(t('errors.guard.already_logged_in'));
        return next({ name: 'Dashboard' });
    }

    // Check if the user has required permissions for gated routes
    if (to.meta.gate && authStore.hasPermission && !authStore.hasPermission(to.meta.gate)) {
        console.warn(`Permission denied. Gate: ${to.meta.gate}, User: ${authStore.user?.email || 'unknown'}`);
        toast.error(t('errors.guard.permission_denied'));
        return next({ name: 'Forbidden' });
    }

    next(); // Allow navigation if all checks pass
}

/**
 * Global afterEach navigation guard.
 * Updates the document title and logs navigation details in development mode.
 */
export function afterEachGuard(to, from) {
    if (!ensureI18nInitialized()) {
        return; // Exit if i18n is not initialized
    }

    const t = i18nInstance.global.t; // Translation function

    // Set the document title based on route meta information
    const translatedTitle = to.meta.title ? t(to.meta.title) : null;
    document.title = translatedTitle || import.meta.env.VITE_APP_NAME || '';

    // Log navigation details in development mode
    if (import.meta.env.MODE === 'development') {
        console.log(`[${new Date().toISOString()}] Navigating from ${from.fullPath} to ${to.fullPath}`);
    }
}
