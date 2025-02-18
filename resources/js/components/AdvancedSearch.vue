<script setup>
import {useI18n} from 'vue-i18n';
import {useThemeStore} from '@/stores/themeStore';
import ConfirmCancelButtons from '@/components/ConfirmCancelButtons.vue';

const {t} = useI18n();
const themeStore = useThemeStore();

defineProps({
    modelValue: {
        type: Object,
        required: true,
        default: () => ({})
    },
    title: {type: [String, null], default: null},
    subtitle: {type: [String, null], default: null},
    confirmButtonText: {type: [String, null], default: null},
    cancelButtonText: {type: [String, null], default: null},
});

let emit = defineEmits(['update:modelValue', 'apply-filters', 'reset-filters']);

const applyFilters = () => {
    emit('apply-filters');
};

const resetFilters = () => {
    emit('reset-filters');
};
</script>

<template>
    <v-card flat class="ma-2">
        <v-card-title>{{ title || t('default.advanced-search.title') }}</v-card-title>
        <v-card-subtitle>
            <strong>{{ subtitle || t('default.advanced-search.subtitle') }}</strong>
        </v-card-subtitle>
        <v-form @submit.prevent="applyFilters">
            <v-container>
                <slot></slot>

                <v-row>
                    <v-col>
                        <v-radio-group
                                :model-value="modelValue.condition"
                                @update:modelValue="emit('update:modelValue', { ...modelValue, condition: $event })"
                                density="compact"
                                color="primary"
                        >
                            <template v-slot:label
                                      class="ms-0">
                                {{ t('default.advanced-search.conditionLabel') }}:
                            </template>
                            <v-radio
                                    :label="t('default.advanced-search.andCondition')"
                                    value="and"
                            />
                            <v-radio
                                    :label="t('default.advanced-search.orCondition')"
                                    value="or"
                            />
                        </v-radio-group>
                    </v-col>
                </v-row>
                <v-row>
                    <v-spacer></v-spacer>
                    <v-card-actions>
                        <ConfirmCancelButtons
                                :confirmButton="{text: confirmButtonText || t('default.advanced-search.search')}"
                                @confirm="applyFilters"
                                :cancelButton="{text: cancelButtonText || t('default.advanced-search.reset')}"
                                @cancel="resetFilters"
                        />
                    </v-card-actions>
                </v-row>
            </v-container>
        </v-form>
    </v-card>
</template>

<style scoped>
.v-card-subtitle {
    color: v-bind(themeStore.secondaryColor);
}
</style>
