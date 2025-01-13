<script setup>
/** @name Users */

import {useI18n} from "vue-i18n";
import {useThemeStore} from '@/stores/themeStore';

const {t} = useI18n();
const themeStore = useThemeStore();

</script>

<template>
    <v-container>
        <h1>{{ t('settings.access-management.users.welcome_message') }}</h1>
    </v-container>
</template>

<style scoped>
.v-container {
    color: v-bind(themeStore.secondaryColor);
}
</style>