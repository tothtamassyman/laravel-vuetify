import { defineStore } from 'pinia';
import { useTheme } from 'vuetify';
import { computed, onMounted, onUnmounted } from 'vue';

export const useThemeStore = defineStore('theme', () => {
    const theme = useTheme();

    const getDefaultTheme = () => {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            return savedTheme;
        }

        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        return prefersDark ? 'dark' : 'light';
    };

    theme.global.name.value = getDefaultTheme();

    const currentTheme = computed(() => theme.global.name.value);

    const primaryColor = computed(() => theme.global.current.value.colors.primary);
    const secondaryColor = computed(() => theme.global.current.value.colors.secondary);
    const successColor = computed(() => theme.global.current.value.colors.success);
    const infoColor = computed(() => theme.global.current.value.colors.info);
    const warningColor = computed(() => theme.global.current.value.colors.warning);
    const errorColor = computed(() => theme.global.current.value.colors.error);

    const textColor = computed(() => (currentTheme.value === 'dark' ? 'white' : 'black'));

    const toggleTheme = () => {
        const newTheme = currentTheme.value === 'dark' ? 'light' : 'dark';
        theme.global.name.value = newTheme;
        localStorage.setItem('theme', newTheme);
    };

    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    const handleMediaQueryChange = (event) => {
        if (!localStorage.getItem('theme')) {
            theme.global.name.value = event.matches ? 'dark' : 'light';
        }
    };

    onMounted(() => {
        mediaQuery.addEventListener('change', handleMediaQueryChange);
    });

    onUnmounted(() => {
        mediaQuery.removeEventListener('change', handleMediaQueryChange);
    });

    return {
        currentTheme,
        primaryColor,
        secondaryColor,
        successColor,
        infoColor,
        warningColor,
        errorColor,
        textColor,
        toggleTheme,
    };
});
