import { useToast } from 'vue-toastification';
import { i18nInstance } from '@/plugins/i18n.js';

export function errorHandler(error) {
    const toast = useToast();
    const t = i18nInstance?.global?.t || ((key) => key); // Fallback for translation function

    // Log the error to the console for debugging
    console.error(`${t('errors.router')}: ${error.message || t('errors.unknown')}`);

    // Development mode specific handling
    if (import.meta.env.MODE === 'development') {
        console.error(error); // Log detailed error information
        toast.error(t('errors.navigation') + `: ${error.message || t('errors.unknown')}`);
    } else {
        // Production mode error notification
        toast.error(t('errors.unexpected'));
    }
}
