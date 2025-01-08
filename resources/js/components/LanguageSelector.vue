<script setup>
import {ref, onMounted} from 'vue';
import {useI18n} from 'vue-i18n';
import {useLanguageStore} from '@/stores/language';

const {locale, t} = useI18n();
const languageStore = useLanguageStore();

const currentLocale = ref(locale.value);
const languageOptions = ref([]);

const changeLanguage = (value) => {
    document.cookie = `locale=${value}; path=/; max-age=31536000`;
    locale.value = value;
};

onMounted(async () => {
    await languageStore.fetchLocales();
    languageOptions.value = Object.entries(languageStore.locales).map(([value, label]) => ({
        label,
        value,
    }));
});
</script>

<template>
    <v-container class="w-xl-25 w-lg-33 w-md-50 w-sm-66">
        <v-select
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
    </v-container>
</template>
