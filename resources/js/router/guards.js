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
    const toast = useToast(); // Toast instance initialized at the top
    const t = (key) => key; // Minimal fallback for translation keys

    if (!i18nInstance) {
        console.error(t('errors.guard.i18n_not_initialized'));
        toast.warning(t('errors.guard.i18n_not_initialized'));
        return false;
    }
    return true;
};

/**
 * Fetches user data if the user is authenticated but data is missing.
 * @param {Object} authStore - The authentication store instance.
 * @param {Function} toast - Toast instance for notifications.
 * @param {Function} t - Translation function.
 * @returns {Promise<boolean>} True if the user data is fetched successfully, false otherwise.
 */
const fetchUserDataIfNeeded = async (authStore, toast, t) => {
    if (authStore.isAuthenticated && !authStore.user) {
        try {
            await authStore.fetchUser();
            return true;
        } catch (error) {
            console.error(t('errors.guard.fetch_user_failed'), error);
            toast.error(t('errors.guard.fetch_user_failed'));
            authStore.clearAuth(); // Clear authentication if fetching user data fails
            return false;
        }
    }
    return true;
};

/**
 * Redirects unauthenticated users attempting to access protected routes.
 * @param {Object} to - The target route.
 * @param {Object} authStore - The authentication store instance.
 * @param {Function} toast - Toast instance for notifications.
 * @param {Function} t - Translation function.
 * @returns {boolean} True if navigation should continue, false otherwise.
 */
const handleAuthentication = (to, authStore, toast, t) => {
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);

    if (requiresAuth && !authStore.isAuthenticated) {
        console.warn(t('errors.guard.unauthorized_access', { path: to.fullPath }));
        toast.warning(t('errors.guard.login_required'));
        return false;
    }
    return true;
};

/**
 * Redirects authenticated users attempting to access public routes like /login.
 * @param {Object} to - The target route.
 * @param {Object} authStore - The authentication store instance.
 * @param {Function} toast - Toast instance for notifications.
 * @param {Function} t - Translation function.
 * @returns {boolean} True if navigation should continue, false otherwise.
 */
const handlePublicRoute = (to, authStore, toast, t) => {
    if (!to.meta.requiresAuth && authStore.isAuthenticated && (to.name === 'Login' || to.path === '/login')) {
        console.warn(t('errors.guard.already_logged_in', { path: to.fullPath }));
        toast.info(t('errors.guard.already_logged_in_toast'));
        return false;
    }
    return true;
};

/**
 * Checks user abilities for gated routes.
 * @param {Object} to - The target route.
 * @param {Object} authStore - The authentication store instance.
 * @param {Function} toast - Toast instance for notifications.
 * @param {Function} t - Translation function.
 * @returns {boolean} True if navigation should continue, false otherwise.
 */
const handleAbilities = (to, authStore, toast, t) => {
    if (!authStore) {
        console.error(t('errors.guard.auth_store_not_found'));
        return false;
    }

    const gate = to.meta.gate;
    if (!gate) return true;

    const { action, subject } = gate;
    if (!authStore.hasAbility(action, subject)) {
        const errorDetails = {
            message: t('errors.guard.permission_denied', {
                action,
                subject,
                user: authStore.user?.email || 'unknown',
            }),
            user: authStore.user,
            gate,
        };

        console.warn(errorDetails.message, errorDetails);
        toast.error(t('errors.guard.permission_denied_toast'));
        return false;
    }
    return true;
};

/**
 * Global beforeEach navigation guard.
 * Handles authentication, permission checks, user data fetching, and redirections.
 */
export async function beforeEachGuard(to, from, next) {
    if (!ensureI18nInitialized()) {
        return next(false); // Stop navigation if i18n is not initialized
    }

    const toast = useToast(); // Initialize toast instance
    const t = i18nInstance.global.t; // Translation function
    const authStore = useAuthStore();

    // Fetch user data if authenticated but missing
    if (!(await fetchUserDataIfNeeded(authStore, toast, t))) {
        return next({ name: 'Login' }); // Redirect to login if fetching user data fails
    }

    // Redirect unauthenticated users attempting to access protected routes
    if (!handleAuthentication(to, authStore, toast, t)) {
        return next({ name: 'Login' });
    }

    // Prevent authenticated users from accessing public routes like /login
    if (!handlePublicRoute(to, authStore, toast, t)) {
        return next({ name: 'Dashboard' });
    }

    // Check if the user has required abilities for gated routes
    if (!handleAbilities(to, authStore, toast, t)) {
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
        console.log(
            t('logs.navigation', {
                from: from.fullPath,
                to: to.fullPath,
                timestamp: new Date().toISOString(),
            })
        );
    }
}
