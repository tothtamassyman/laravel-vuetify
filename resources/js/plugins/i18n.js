/**
 * Import required Vue utilities and external modules.
 */
import {createI18n} from 'vue-i18n';
import {useLanguageStore} from '@/stores/languageStore.js';

// Global instance for the i18n configuration
let i18nInstance = null;

/**
 * Fetches the available locales from the language store.
 * Ensures that locales are loaded before initializing i18n.
 * @returns {Object} An object containing locale information.
 */
const fetchAvailableLocales = async () => {
    const languageStore = useLanguageStore();
    if (!languageStore.locales) {
        await languageStore.fetchLocales();
    }
    return languageStore.locales;
};

// Default locale to use if no other preference is detected
const defaultLocale = 'en';

/**
 * Initializes the i18n instance with dynamic locale loading and configuration.
 * Determines the initial locale based on cookies, browser settings, or defaults.
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
     * Determines the initial locale.
     * Checks for a saved locale in cookies, browser settings, or falls back to default.
     * @returns {string} The resolved locale.
     */
    const getInitialLocale = () => {
        const savedLocale = document.cookie
            .split('; ')
            .find((row) => row.startsWith('locale='))
            ?.split('=')[1];

        const browserLocale = navigator.language.split('-')[0];

        if (savedLocale && localeKeys.includes(savedLocale)) {
            return savedLocale;
        }

        if (localeKeys.includes(browserLocale)) {
            return browserLocale;
        }

        return defaultLocale;
    };

    // Create the i18n instance with configuration
    i18nInstance = createI18n({
        legacy: false, // Use Composition API-based syntax
        globalInjection: true, // Make `$t` globally available in templates
        locale: getInitialLocale(), // Initial locale
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
export { i18nInstance };
export default initI18n;
