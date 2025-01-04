import {createI18n} from 'vue-i18n';
import {useLanguageStore} from '@/stores/language';

const fetchAvailableLocales = async () => {
    const languageStore = useLanguageStore();
    await languageStore.fetchLocales();
    return languageStore.locales;
};

const defaultLocale = 'en';

const initI18n = async () => {
    const locales = await fetchAvailableLocales();
    const localeKeys = Object.keys(locales);

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

    return createI18n({
        legacy: false,
        globalInjection: true,
        locale: getInitialLocale(),
        fallbackLocale: defaultLocale,
        messages: {
            en: await import('@/locales/en.json'),
            hu: await import('@/locales/hu.json'),
        },
    });
};

export default initI18n;
