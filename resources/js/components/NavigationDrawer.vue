<script setup>
/**
 * Import required Vue utilities and external modules.
 */
import {computed, ref} from 'vue';
import routes from '@/router/routes.js';
import { useAuthStore } from '@/stores/authStore.js';
import {useI18n} from 'vue-i18n';
import RecursiveList from './RecursiveList.vue';

/**
 * Define props for NavigationDrawer.
 * - `modelValue`: Controls the drawer's open/closed state.
 */
const props = defineProps({
    modelValue: {
        type: Boolean,
        required: true,
    },
});

/**
 * Define emits for communication with parent components.
 * - `update:modelValue`: Triggered when the drawer's state changes.
 */
const emit = defineEmits(['update:modelValue']);

/**
 * Setup internationalization for translating UI texts.
 */
const { t } = useI18n();

/**
 * State and reactive variables.
 */
// Authentication store for user data and permissions
const authStore = useAuthStore();

// Tracks whether the drawer is in rail mode
const isRail = ref(false);

// Tracks which groups in the navigation drawer are currently opened
const openedGroups = ref([]);

/**
 * Computed properties.
 */

// Filter routes based on visibility, authentication, and permissions
const filteredRoutes = computed(() => {
    const filterRoutes = (routes, level = 0) => {
        return routes
            .filter((route) => {
                // Exclude hidden routes
                if (route.meta?.hidden) return false;

                // Exclude routes requiring authentication if the user is not authenticated
                if (route.meta?.requiresAuth && !authStore.isAuthenticated) return false;

                // Exclude routes if the user lacks required permissions
                if (route.meta?.gate && !authStore.hasAbility(route.meta.gate.action, route.meta.gate.subject)) return false;

                return true; // Keep the route if it passes all checks
            })
            .map((route) => {
                // Recursively filter child routes
                const filteredChildren =
                    route.children?.length > 0 ? filterRoutes(route.children, level + 1) : undefined;

                return {
                    ...route,
                    ...(filteredChildren && { children: filteredChildren }),
                };
            });
    };

    return filterRoutes(routes);
});

// Calculate the rail width dynamically based on the depth of opened groups
const railWidth = computed(() => {
    const baseWidth = 56; // Default rail width
    const paddingPerLevel = 12; // Additional padding per nesting level
    const currentDepth = calculateDepthFromOpenedGroups(routes, openedGroups.value);
    return baseWidth + currentDepth * paddingPerLevel;
});

/**
 * Methods.
 */

// Update the opened groups in the drawer
const updateOpenedGroups = (groupPath, isOpened) => {
    if (isOpened) {
        if (!openedGroups.value.includes(groupPath)) {
            openedGroups.value.push(groupPath);
        }
    } else {
        openedGroups.value = openedGroups.value.filter((path) => path !== groupPath);
    }
};

// Calculate the maximum depth of opened groups for rail width adjustment
const calculateDepthFromOpenedGroups = (routes, openedGroups, level = 0) => {
    let maxDepth = level;
    for (const route of routes) {
        if (openedGroups.includes(route.path)) {
            const childDepth = calculateDepthFromOpenedGroups(route.children || [], openedGroups, level + 1);
            maxDepth = Math.max(maxDepth, childDepth);
        }
    }
    return maxDepth;
};

// Toggle rail mode (expanded/collapsed)
const toggleRail = () => {
    isRail.value = !isRail.value;
    localStorage.setItem('isRail', JSON.stringify(isRail.value));
};

// Update the drawer state and persist it to localStorage
const updateDrawerState = (value) => {
    localStorage.setItem('drawerState', JSON.stringify(value));
    emit('update:modelValue', value);
};

// Initialize the state from localStorage values
const initializeState = () => {
    const savedRailState = localStorage.getItem('isRail');
    if (savedRailState !== null) {
        isRail.value = JSON.parse(savedRailState);
    }

    const savedDrawerState = localStorage.getItem('drawerState');
    if (savedDrawerState !== null) {
        emit('update:modelValue', JSON.parse(savedDrawerState));
    }
};

// Initialize state on component load
initializeState();
</script>

<template>
    <v-navigation-drawer
        :model-value="modelValue"
        @update:model-value="updateDrawerState"
        app
        :rail="isRail"
        :rail-width="railWidth"
        width="370"
        permanent
    >
        <!-- User information -->
        <v-list>
            <v-list-item @click="toggleRail">
                <template v-slot:prepend>
                    <v-icon :class="isRail ? 'customIcon' : null">
                        {{ isRail ? 'mdi-arrow-expand-horizontal' : 'mdi-arrow-collapse-horizontal' }}
                    </v-icon>
                </template>
                <v-list-item-title v-if="authStore.user?.name">{{ authStore.user.name }}</v-list-item-title>
                <v-list-item-subtitle v-if="authStore.user?.email">{{ authStore.user.email }}</v-list-item-subtitle>
            </v-list-item>
        </v-list>
        <v-divider />

        <!-- Recursive navigation menu -->
        <v-list v-model:opened="openedGroups">
            <RecursiveList
                :routes="filteredRoutes"
                :isRail="isRail"
                @update-opened="updateOpenedGroups"
            />
        </v-list>
    </v-navigation-drawer>
</template>

<style scoped>
/**
 * Custom style for the rail toggle icon in collapsed mode.
 */
.customIcon {
    color: rgba(var(--v-theme-primary), 0.8) !important;
}
</style>
