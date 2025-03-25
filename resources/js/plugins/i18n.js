/**
 * Import required Vue utilities and external modules.
 */
import {createI18n} from 'vue-i18n';
import {useLanguageStore} from '@/stores/languageStore.js';
import axios from '@/plugins/axios';

// Global instance for the i18n configuration
let i18nInstance = null;

/**
 * Fetches the available locales from the language store.
 * Ensures that locales are loaded before initializing i18n.
 * @returns {Object} An object containing locale information.
 */
const fetchAvailableLocales = async () => {
    const languageStore = useLanguageStore();
    if (!languageStore.locales || Object.keys(languageStore.locales).length === 0) {
        await languageStore.fetchLocales();
    }
    return languageStore.locales;
};

// Default locale to use if no other preference is detected
const defaultLocale = 'en';

/**
 * Initializes the i18n instance with dynamic locale loading and configuration.
 * Fetches the initial locale from the backend or falls back to browser/default settings.
 * @returns {Object} The initialized i18n instance.
 */
const initI18n = async () => {
    // Load messages dynamically for supported locales
    const messages = {
        en: await import('@/locales/en.json'),
        hu: await import('@/locales/hu.json'),
    };

    // Fetch available locales from the store
    const locales = await fetchAvailableLocales();
    const localeKeys = Object.keys(locales);

    /**
     * Determines the initial locale by querying the backend.
     * Falls back to browser settings or default if the backend call fails.
     * @returns {string} The resolved locale.
     */
    const getInitialLocale = async () => {
        try {
            const response = await axios.get('/current-locale');
            const backendLocale = response.data.locale;
            if (localeKeys.includes(backendLocale)) {
                return backendLocale;
            }
            console.warn(`Backend locale (${backendLocale}) is not supported. Falling back.`);
        } catch (error) {
            console.error('Failed to fetch current locale from backend:', error);
        }

        const browserLocale = navigator.language.split('-')[0];
        if (localeKeys.includes(browserLocale)) {
            return browserLocale;
        }

        return defaultLocale;
    };

    const initialLocale = getInitialLocale();

    // Create the i18n instance with configuration
    i18nInstance = createI18n({
        legacy: false, // Use Composition API-based syntax
        globalInjection: true, // Make `$t` globally available in templates
        locale: initialLocale, // Initial locale from backend or fallback
        fallbackLocale: defaultLocale, // Fallback locale if translation is missing
        messages, // Loaded translations
        missing: (locale, key) => {
            console.warn(`Missing translation: ${key} in locale: ${locale}`);
            return key; // Return the key if translation is missing
        },
    });

    return i18nInstance;
};

// Export the i18n instance and initialization function
export {i18nInstance};
export default initI18n;
