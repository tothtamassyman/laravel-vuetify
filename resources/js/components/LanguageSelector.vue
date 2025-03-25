<script setup>
import {ref, onMounted} from 'vue';
import {useI18n} from 'vue-i18n';
import {useLanguageStore} from '@/stores/languageStore.js';

const {locale, t} = useI18n();
const languageStore = useLanguageStore();

const currentLocale = ref(locale.value);
const languageOptions = ref([]);
const isLoading = ref(true);

const changeLanguage = async (value) => {
    await languageStore.setLocale(value);
    locale.value = value;
    currentLocale.value = value;
};

onMounted(async () => {
    await languageStore.fetchLocales();
    await languageStore.fetchCurrentLocale();
    languageOptions.value = Object.entries(languageStore.locales).map(([value, label]) => ({
        label,
        value,
    }));
    currentLocale.value = languageStore.currentLocale;
    locale.value = languageStore.currentLocale;
    isLoading.value = false;
});
</script>

<template>
    <v-container class="w-xl-25 w-lg-33 w-md-50 w-sm-66">
        <v-select
                v-if="!isLoading"
                base-color="primary"
                color="primary"
                v-model="currentLocale"
                :items="languageOptions"
                item-title="label"
                item-value="value"
                :label="t('languageSelector.label')"
                class="mt-6"
                density="compact"
                variant="outlined"
                @update:modelValue="changeLanguage"
        ></v-select>
        <v-progress-circular v-else indeterminate color="primary"/>
    </v-container>
</template>
