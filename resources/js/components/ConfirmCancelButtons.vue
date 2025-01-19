<script setup>
import {defineProps, defineEmits, computed} from 'vue';
import {useI18n} from 'vue-i18n';
import CustomButton from '@/components/CustomButton.vue';

const {t} = useI18n();

const defaultConfirmButtonProps = computed(() => ({
    text: t('buttons.confirm'),
}));

const defaultCancelButtonProps = computed(() => ({
    text: t('buttons.cancel'),
    color: 'secondary'
}));

const props = defineProps({
    confirmButton: {
        type: Object,
        default: () => ({})
    },
    cancelButton: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['confirm', 'cancel']);

const confirmButton = computed(() => ({
    ...defaultConfirmButtonProps.value,
    ...props.confirmButton
}));

const cancelButton = computed(() => ({
    ...defaultCancelButtonProps.value,
    ...props.cancelButton
}));
</script>

<template>
    <div class="confirm-cancel-buttons">
        <CustomButton
                v-bind="confirmButton"
                @click="emit('confirm')"
        />
        <CustomButton
                v-bind="cancelButton"
                @click="emit('cancel')"
        />
    </div>
</template>

<style scoped>
.confirm-cancel-buttons {
    display: flex;
    gap: 1rem;
}
</style>
