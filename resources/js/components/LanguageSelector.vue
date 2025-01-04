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
  <v-select
      :items="languageOptions"
      v-model="currentLocale"
      :label="t('languageSelector.label')"
      dense
      outlined
      item-title="label"
      item-value="value"
      @update:modelValue="changeLanguage"
  ></v-select>
</template>
