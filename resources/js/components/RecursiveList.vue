<script setup>
/**
 * Import required Vue utilities and i18n.
 */
import { useI18n } from 'vue-i18n';

/**
 * Define props for RecursiveList.
 * - `routes`: Array of navigation routes to render.
 * - `level`: Current nesting level for padding calculation (default: 0).
 * - `isRail`: Indicates whether the drawer is in rail mode (default: false).
 */
const props = defineProps({
    routes: {
        type: Array,
        required: true,
    },
    level: {
        type: Number,
        default: 0,
    },
    isRail: {
        type: Boolean,
        default: false,
    },
});

/**
 * Setup internationalization for translating route titles and subtitles.
 */
const { t } = useI18n();

/**
 * Define emits for communication with parent components.
 * - `update-opened`: Triggered when a group is toggled open/closed.
 */
const emit = defineEmits(['update-opened']);

/**
 * Handle group toggle events to notify the parent component.
 * @param {string} groupPath - The path of the group being toggled.
 * @param {boolean} isOpened - Whether the group is opened.
 */
const handleGroupToggle = (groupPath, isOpened) => {
    emit('update-opened', groupPath, isOpened);
};
</script>

<template>
    <!-- Render each route recursively -->
    <template v-for="(route, index) in routes" :key="index">
        <!-- Render a list group if the route has children -->
        <v-list-group
            v-if="route.children"
            :value="route.path"
            @update:value="(isOpened) => handleGroupToggle(route.path, isOpened)"
        >
            <template #activator="{ props }">
                <!-- List item for the group activator -->
                <v-list-item
                    v-bind="props"
                    :prepend-icon="route.meta.icon"
                    :title="t(route.meta.title)"
                    :subtitle="t(route.meta.subtitle)"
                    :style="{ '--level': level }"
                    class="ms-2"
                >
                    <!-- Tooltip for rail mode -->
                    <v-tooltip
                        v-if="isRail"
                        activator="parent"
                        :text="t(route.meta.title)"
                    />
                </v-list-item>
            </template>
            <!-- Recursive rendering for child routes -->
            <RecursiveList
                :routes="route.children"
                :level="level + 1"
                :isRail="isRail"
                @update-opened="emit('update-opened', $event)"
            />
        </v-list-group>

        <!-- Render a simple list item if the route has no children -->
        <v-list-item
            v-else
            :to="route.path"
            :prepend-icon="route.meta.icon"
            :title="t(route.meta.title)"
            :subtitle="t(route.meta.subtitle)"
            :style="{ '--level': level }"
            class="ms-2"
        >
            <!-- Tooltip for rail mode -->
            <v-tooltip
                v-if="isRail"
                activator="parent"
                :text="t(route.meta.title)"
            />
        </v-list-item>
    </template>
</template>

<style scoped>
/**
 * Add padding for nested list items based on the current level.
 * Smooth transition for visual feedback.
 */
.v-list-item {
    padding-inline-start: calc(var(--base-padding, 8px) + var(--level) * 12px) !important;
    transition: all 0.3s ease;
}

/**
 * Customize active list item color to match the theme.
 */
.v-list-item--active {
    color: rgba(var(--v-theme-primary), 0.8) !important;
}
</style>
