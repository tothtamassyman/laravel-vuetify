<script setup>
/** @name Dashboard */

import {useI18n} from "vue-i18n";

const {t} = useI18n();
</script>

<template>
    <div>
        <h1>{{ t('dashboard.welcome_message') }}</h1>
    </div>
</template>

<style scoped>

</style>
