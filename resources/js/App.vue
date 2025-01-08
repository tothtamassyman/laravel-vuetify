<script setup>
/** @name App */

import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import {useI18n} from "vue-i18n";
import LanguageSelector from '@/components/LanguageSelector.vue';

const router = useRouter();
const authStore = useAuthStore();
const {t} = useI18n();

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
    <v-app>
        <v-app-bar app>
            <v-app-bar-title>{{ t('app.title') }}</v-app-bar-title>
            <v-spacer></v-spacer>
            <LanguageSelector />
            <v-spacer></v-spacer>
            <v-btn to="/">{{ t('welcome.title') }}</v-btn>
            <v-btn v-if="authStore.token" to="/dashboard">{{ t('dashboard.title') }}</v-btn>
            <v-btn v-if="authStore.token" @click="logout">{{ t('app.logout') }}</v-btn>
            <v-btn v-else to="/login">{{ t('app.login') }}</v-btn>
        </v-app-bar>
        <v-main>
            <v-container>
                <router-view/>
            </v-container>
        </v-main>
    </v-app>
</template>

<style scoped>

</style>
