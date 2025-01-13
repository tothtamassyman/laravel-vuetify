<script setup>
/**
 * AppBar.vue
 * Application top bar component. Handles navigation, theme toggling, and user authentication actions.
 */

import {defineProps, defineEmits} from 'vue';
import {useRouter} from 'vue-router';
import {useAuthStore} from '@/stores/authStore.js';
import {useThemeStore} from '@/stores/themeStore.js';
import {useI18n} from 'vue-i18n';

import LanguageSelector from '@/components/LanguageSelector.vue';
import Logo from '@/components/Logo.vue';

// Props for the AppBar
const props = defineProps({
    drawerState: {
        type: Boolean,
        required: true,
    },
});

// Reactive references and state management
const router = useRouter();
const authStore = useAuthStore();
const themeStore = useThemeStore();
const {t} = useI18n();

// Emits to communicate drawer state changes to the parent
const emit = defineEmits(['update:drawerState']);

/**
 * Toggles the drawer state and persists it in localStorage.
 */
const onToggleDrawer = () => {
    const newDrawerState = !props.drawerState;
    emit('update:drawerState', newDrawerState);
    localStorage.setItem('drawerState', JSON.stringify(newDrawerState));
};

/**
 * Logs out the user and redirects to the welcome page.
 */
const logout = async () => {
    try {
        await authStore.logout();
        await router.push({name: 'Welcome'});
    } catch (error) {
        console.error('Logout error:', error);
    }
};
</script>

<template>
    <v-app-bar app>
        <!-- Prepend section with navigation icon and logo -->
        <template v-slot:prepend>
            <v-app-bar-nav-icon
                    variant="text"
                    :color="themeStore.primaryColor"
                    @click="onToggleDrawer"
            ></v-app-bar-nav-icon>
            <Logo src="/images/logo.png" :size="50" :show-text="false"/>
        </template>

        <v-spacer/>

        <!-- Language selector component -->
        <LanguageSelector/>

        <v-spacer/>

        <!-- Append section with theme toggle and authentication buttons -->
        <template v-slot:append>
            <!-- Theme toggle button -->
            <v-btn @click="themeStore.toggleTheme" :color="themeStore.primaryColor">
                <v-icon>
                    {{ themeStore.currentTheme === 'dark' ? 'mdi-white-balance-sunny' : 'mdi-moon-waning-crescent' }}
                </v-icon>
            </v-btn>

            <!-- Login button for unauthenticated users -->
            <v-btn
                    v-if="!authStore.isAuthenticated"
                    @click="router.push({ name: 'Login' })"
                    :color="themeStore.primaryColor"
                    prepend-icon="mdi-login"
            >
                {{ t('app.login') }}
            </v-btn>

            <!-- Logout button for authenticated users -->
            <v-btn
                    v-else
                    @click="logout"
                    prepend-icon="mdi-logout"
                    :color="themeStore.primaryColor"
            >
                {{ t('app.logout') }}
            </v-btn>
        </template>
    </v-app-bar>
</template>

<style scoped>

</style>
