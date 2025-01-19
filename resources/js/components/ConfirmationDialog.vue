<script setup>
import {defineProps, defineEmits} from 'vue';
import {useI18n} from 'vue-i18n';
import ConfirmCancelButtons from '@/components/ConfirmCancelButtons.vue';

const {t} = useI18n();

defineProps({
    modelValue: {
        type: Boolean,
        required: true // Options: true, false
    },
    persistent: {
        type: Boolean,
        default: false // Options: true, false
    },
    transition: {
        type: String,
        default: 'dialog-bottom-transition' // Options: 'dialog-top-transition', 'dialog-bottom-transition'
    },
    width: {
        type: [String, Number],
        default: 500 // Options: 300, 400, 500, 600, 700, 800, 900, 1000, or auto
    },
    title: {
        type: [String, null],
        default: null // null for i18n text or any string value
    },
    subtitle: {
        type: [String, null],
        default: null // null for i18n text or any string value
    },
    text: {
        type: [String, null],
        default: null // null for i18n text or any string value
    },
    confirmButton: {
        type: Object,
        default: () => ({}) // Custom button props
    },
    cancelButton: {
        type: Object,
        default: () => ({}) // Custom button props
    },
    buttonsAlignment: {
        type: String,
        default: 'end' // Options: 'start', 'center', 'end'
    }
});

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel']);

const closeDialog = () => {
    emit('update:modelValue', false);
};

const handleConfirm = () => {
    emit('confirm');
    closeDialog();
};

const handleCancel = () => {
    emit('cancel');
    closeDialog();
};

</script>

<template>
    <v-dialog
            :model-value="modelValue"
            @update:modelValue="emit('update:modelValue', $event)"
            :persistent="persistent ?? false"
            :transition="transition"
            :width="width"
    >
        <v-card>
            <v-card-title class="headline">{{ title || t('dialog.defaultTitle') }}</v-card-title>
            <v-card-subtitle>{{ subtitle || t('dialog.defaultSubtitle') }}</v-card-subtitle>
            <v-card-text>{{ text || t('dialog.defaultText') }}</v-card-text>
            <v-card-actions :class="`justify-${buttonsAlignment}`">
                <ConfirmCancelButtons
                        :confirmButton="confirmButton"
                        :cancelButton="cancelButton"
                        @confirm="handleConfirm"
                        @cancel="handleCancel"
                />
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style scoped>

</style>