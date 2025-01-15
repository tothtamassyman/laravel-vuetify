<script setup>
/** @name Dashboard */

import {useAbility} from '@casl/vue';
import {useI18n} from "vue-i18n";
import {useThemeStore} from '@/stores/themeStore';

const {can} = useAbility();
const {t} = useI18n();
const themeStore = useThemeStore();
</script>

<template>
    <v-container>
        <h1>{{ t('dashboard.welcome_message') }}</h1>
        <v-btn color="primary" v-if="can('view', 'settings button')">You can view settings button</v-btn>
    </v-container>
</template>

<style scoped>
.v-container {
    color: v-bind(themeStore.secondaryColor);
}
</style>
