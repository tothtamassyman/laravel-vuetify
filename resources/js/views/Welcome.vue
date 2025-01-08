<script setup>
/** @name Welcome */

import {useI18n} from "vue-i18n";

const {t} = useI18n();
</script>

<template>
    <div>
        <h1>{{ t('welcome.welcome_message') }}</h1>
    </div>
</template>

<style scoped>

</style>
