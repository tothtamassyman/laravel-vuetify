<script setup>
import {computed, ref} from 'vue';
import {useI18n} from 'vue-i18n';

const {t} = useI18n();
const localShow = ref(false);

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    policy: {
        type: Object,
        default: () => ({
            min_length: 8,
            max_length: 255,
            mixed_case: true,
            numbers: true,
            symbols: true,
            letters: false,
            uncompromised: true,
            history_check: true,
            history_limit: 10,
        }),
    },
    openOnHover: {
        type: Boolean,
        default: true,
    },
    location: {
        type: String,
        default: 'bottom',
    },
    color: {
        type: String,
        default: 'primary',
    },
    activeColor: {
        type: String,
        default: 'secondary',
    },
});

const requiredItems = computed(() => {
    const items = [];
    if (props.policy.mixed_case) {
        items.push(t('passwordPolicyToolTip.lowercaseChar'));
        items.push(t('passwordPolicyToolTip.uppercaseChar'));
    } else {
        if (props.policy.letters) items.push(t('passwordPolicyToolTip.letter'));
    }
    if (props.policy.numbers) items.push(t('passwordPolicyToolTip.number'));
    if (props.policy.symbols) items.push(t('passwordPolicyToolTip.specialCharacter'));
    if (props.policy.history_check) {
        items.push(t('passwordPolicyToolTip.historyCheck', { limit: props.policy.history_limit }))
    }
    if (props.policy.uncompromised) items.push(t('passwordPolicyToolTip.uncompromised'));

    return items;
});
</script>

<template>
    <v-tooltip
            v-model="localShow"
            :location="location"
            :open-on-hover="openOnHover"
            @click:outside="localShow = false"
    >
        <template v-slot:activator="{ props }">
            <v-btn
                    v-bind="props"
                    variant="plain"
                    @click="localShow = !localShow"
            >
                <template v-slot:default>
                    <v-icon :color="localShow ? activeColor : color" size="x-large">
                        mdi-information-variant
                    </v-icon>
                    {{ title }}
                </template>
            </v-btn>
        </template>
        <span>
      <strong>{{ t('passwordPolicyToolTip.header') }}</strong><br>
      - {{ t('passwordPolicyToolTip.minimumLength') }} {{ policy.min_length }} {{ t('passwordPolicyToolTip.characters') }}<br>
      - {{ t('passwordPolicyToolTip.maximumLength') }} {{ policy.max_length }} {{ t('passwordPolicyToolTip.characters') }}<br>
      <strong>{{ t('passwordPolicyToolTip.mustContain') }}</strong><br>
      <template v-for="(item, index) in requiredItems" :key="index">
        - {{ item }}<br>
      </template>
      <template v-if="requiredItems.length === 0">
        - {{ t('passwordPolicyToolTip.noRequirements') }}<br>
      </template>
    </span>
    </v-tooltip>
</template>

<style scoped>
.v-tooltip span {
    font-size: 14px;
    line-height: 1.5;
    color: var(--v-text-base);
}
</style>