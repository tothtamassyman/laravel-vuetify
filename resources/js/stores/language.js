import {defineStore} from 'pinia';
import axios from '@/plugins/axios';

export const useLanguageStore = defineStore('language', {
    state: () => ({
        locales: {},
    }),

    actions: {
        async fetchLocales() {
            if (Object.keys(this.locales).length === 0) {
                try {
                    const response = await axios.get('/locales');
                    this.locales = response.data;
                } catch (error) {
                    console.error('Failed to load available locales:', error);
                    this.locales = {en: 'English'};
                }
            }
        },
    },
});
