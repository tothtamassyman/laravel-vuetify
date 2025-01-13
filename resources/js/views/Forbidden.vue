<script setup>
/** @name Forbidden */

import { useI18n } from 'vue-i18n';
import { useAuthStore } from '@/stores/authStore.js';

const { t } = useI18n();
const authStore = useAuthStore();
</script>

<template>
    <v-container>
        <div class="forbidden-container">
            <h1>{{ t('forbidden.title') }}</h1>
            <p>{{ t('forbidden.message') }}</p>
            <router-link :to="authStore.isAuthenticated ? '/dashboard' : '/'">
                <v-btn
                        prepend-icon="mdi-arrow-left"
                        color="primary"
                        large
                >{{ t('forbidden.redirect_button') }}</v-btn>
            </router-link>
        </div>
    </v-container>
</template>

<style scoped>
.forbidden-container {
    text-align: center;
    margin-top: 10vh;
    padding: 20px;
}

h1 {
    font-size: 3rem;
    color: #ff5252;
}

p {
    font-size: 1.5rem;
    color: #616161;
    margin-bottom: 20px;
}
</style>