<script setup>
import {defineProps, defineEmits} from 'vue';

defineProps({
    modelValue: String
})

let emit = defineEmits(['update:modelValue'])

/**
 * Tab key event handler
 *
 * Inserts a tab character at the current cursor position
 *
 * @param {KeyboardEvent} event
 * @return {void}
 */
function onTabPress(event) {
    let textarea = event.target;

    let val = textarea.value,
        start = textarea.selectionStart,
        end = textarea.selectionEnd;

    textarea.value = val.substring(0, start) + '\t' + val.substring(end);

    textarea.selectionStart = textarea.selectionEnd = start + 1;
}
</script>

<template>
    <textarea
            @keydown.tab.prevent="onTabPress"
            @keyup="emit('update:modelValue', $event.target.value)"
            v-text="modelValue"
    />
</template>

<style scoped>

</style>