import { defineStore } from 'pinia';
import { useTheme } from 'vuetify';
import { computed } from 'vue';

export const useThemeStore = defineStore('theme', () => {
    const theme = useTheme();

    const currentTheme = computed(() => theme.global.name.value);

    const primaryColor = computed(() => theme.global.current.value.colors.primary);
    const secondaryColor = computed(() => theme.global.current.value.colors.secondary);
    const textColor = computed(() => (currentTheme.value === 'dark' ? 'white' : 'black'));

    const toggleTheme = () => {
        theme.global.name.value = currentTheme.value === 'dark' ? 'light' : 'dark';
    };

    return {
        currentTheme,
        primaryColor,
        secondaryColor,
        textColor,
        toggleTheme,
    };
});
