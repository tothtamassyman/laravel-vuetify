<script setup>
/** @name App */

import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import LanguageSelector from '@/components/LanguageSelector.vue';

const authStore = useAuthStore();
const router = useRouter();

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
            <v-app-bar-title>Alkalmazás</v-app-bar-title>
            <v-spacer></v-spacer>
            <LanguageSelector />
            <v-spacer></v-spacer>
            <v-btn to="/">Főoldal</v-btn>
            <v-btn v-if="authStore.token" to="/dashboard">Dashboard</v-btn>
            <v-btn v-if="authStore.token" @click="logout">Kijelentkezés</v-btn>
            <v-btn v-else to="/login">Bejelentkezés</v-btn>
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
