<script setup>
import {defineProps, defineEmits} from 'vue';
import {useI18n} from 'vue-i18n';

const {t} = useI18n();

defineProps({
    density: {
        type: String,
        default: 'default' // Options: 'compact', 'comfortable', 'default'
    },
    size: {
        type: String,
        default: 'default' // Options: 'x-small', 'small', 'large', 'x-large', 'default'
    },
    block: {
        type: [Boolean, null],
        default: null // Options: true, false, null (default)
    },
    rounded: {
        type: [Boolean, null],
        default: null // Options: true (rounded), false (square), null (default auto behavior)
    },
    elevation: {
        type: [Number, null],
        default: null // Options: 2, 4, 8, 12, 16, 20, 24
    },
    ripple: {
        type: [Boolean, null],
        default: null // Options: null, true (enabled), false (disabled)
    },
    variant: {
        type: String,
        default: 'elevated' // Options: 'elevated', 'flat', 'tonal', 'outlined', 'text', 'plain'
    },
    icon: {
        type: [String, null],
        default: null // Any valid Material icon name or custom component
    },
    iconColor: {
        type: String,
        default: 'primary' // Options: 'primary', 'secondary', 'success', 'info', 'warning', 'error' or custom hex/rgb color code
    },
    prependIcon: {
        type: [String, null],
        default: null // Any valid Material icon name or custom component
    },
    prependIconColor: {
        type: String,
        default: 'primary' // Options: 'primary', 'secondary', 'success', 'info', 'warning', 'error' or custom hex/rgb color code
    },
    appendIcon: {
        type: [String, null],
        default: null // Any valid Material icon name or custom component
    },
    appendIconColor: {
        type: String,
        default: 'primary' // Options: 'primary', 'secondary', 'success', 'info', 'warning', 'error' or custom hex/rgb color code
    },
    loading: {
        type: Boolean,
        default: false // Options: true, false
    },
    color: {
        type: String,
        default: 'primary' // Options: 'primary', 'secondary', 'success', 'info', 'warning', 'error' or custom hex/rgb color code
    },
    disabled: {
        type: Boolean,
        default: false // Options: true, false
    },
    text: {
        type: [String, null],
        default: null // null for i18n text or any string value
    },
    class: {
        type: [String, Array, Object, null],
        default: null // null or any valid CSS class name(s)
    },
});

const emit = defineEmits(['click']);
</script>

<template>
    <v-btn
            @click="emit('click')"
            :density="density"
            :size="size"
            :block="block"
            :rounded="rounded"
            :elevation="elevation"
            :ripple="ripple"
            :variant="variant"
            :loading="loading"
            :color="color"
            :disabled="disabled"
            :class="class"
    >
        <template v-if="prependIcon" v-slot:prepend>
            <v-icon :color="prependIconColor">{{ prependIcon }}</v-icon>
        </template>

        <v-icon v-if="icon" :color="iconColor">
            {{ icon }}
        </v-icon>

        <slot v-if="!icon">{{ text || t('buttons.ok') }}</slot>

        <template v-if="appendIcon" v-slot:append>
            <v-icon :color="appendIconColor">{{ appendIcon }}</v-icon>
        </template>
    </v-btn>
</template>

<style scoped>

</style>
