<script setup>
import {computed} from 'vue';
import {useThemeStore} from '@/stores/themeStore';

const themeStore = useThemeStore();

const props = defineProps({
    src: {
        type: String,
        required: true,
        default: 'logo.png',
    },
    alt: {
        type: String,
        default: 'Logo',
    },
    size: {
        type: [String, Number],
        default: 100,
    },
    text: {
        type: String,
        default: import.meta.env.VITE_APP_NAME || '',
    },
    textColor: {
        type: String,
        default: null,
    },
    showText: {
        type: Boolean,
        default: true,
    },
});

const computedTextColor = computed(() =>
  props.textColor || themeStore.primaryColor
);
</script>

<template>
  <div class="logo-container">
    <v-img
      :src="props.src"
      :alt="props.alt"
      :max-width="props.size"
      :max-height="props.size"
      contain
    />
    <span v-if="props.showText" class="logo-text" :style="{ color: computedTextColor }">
      {{ props.text }}
    </span>
  </div>
</template>

<style scoped>
.logo-container {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.logo-text {
    font-size: 1.25rem;
    font-weight: bold;
}
</style>
