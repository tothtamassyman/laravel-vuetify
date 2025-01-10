<script setup>
/** @name AppBar */
import {useRoute, useRouter} from 'vue-router';
import {useAuthStore} from '@/stores/auth';
import {useThemeStore} from '@/stores/themeStore';
import {useI18n} from 'vue-i18n';

import LanguageSelector from "@/components/LanguageSelector.vue";
import Logo from "@/components/Logo.vue";
import {computed} from "vue";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const themeStore = useThemeStore();
const {locale, t} = useI18n();

const isAuthenticated = computed(() => !!authStore.token);
const isLandingPage = computed(() => route.name === 'Welcome');

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
    <v-app-bar v-if="!isLandingPage" app>
        <v-toolbar-title>
            <Logo
                    src="/images/logo.png"
                    :size="50"
            />
        </v-toolbar-title>
        <v-spacer/>
        <LanguageSelector/>
        <v-spacer/>
        <v-btn @click="themeStore.toggleTheme" :color="themeStore.primaryColor">
            <v-icon>
                {{ themeStore.currentTheme === 'dark' ? 'mdi-white-balance-sunny' : 'mdi-moon-waning-crescent' }}
            </v-icon>
        </v-btn>
        <v-btn v-if="!isAuthenticated" @click="router.push({ name: 'Login' })" :color="themeStore.primaryColor" prepend-icon="mdi-login">
            {{ t('app.login') }}
        </v-btn>
        <v-btn v-else @click="logout" :color="themeStore.primaryColor" prepend-icon="mdi-logout">
            {{ t('app.logout') }}
        </v-btn>
    </v-app-bar>
</template>
<style scoped>

</style>