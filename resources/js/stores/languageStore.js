import {defineStore} from 'pinia';
import axios from '@/plugins/axios';

export const useLanguageStore = defineStore('language', {
    state: () => ({
        locales: {},
        currentLocale: null,
    }),

    actions: {
        async fetchLocales() {
            try {
                const response = await axios.get('/locales');
                this.locales = response.data;
            } catch (error) {
                console.error('Failed to load available locales:', error);
                this.locales = {en: 'English'};
            }
        },
        async fetchCurrentLocale() {
            try {
                const response = await axios.get('/current-locale');
                this.currentLocale = response.data.locale;
            } catch (error) {
                console.error('Failed to load current locale:', error);
                this.currentLocale = 'en';
            }
        },
        async setLocale(locale) {
            try {
                const response = await axios.get(`/set-locale/${locale}`);
                this.currentLocale = response.data.locale;
            } catch (error) {
                console.error('Failed to set locale:', error);
            }
        },
    },
});
