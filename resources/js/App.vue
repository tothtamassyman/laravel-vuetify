<script setup>
/**
 * App.vue
 * Main application component. Handles global layout, navigation, and authentication logic.
 */

import {computed, onMounted, ref} from 'vue';
import {useRoute} from 'vue-router';
import {useAuthStore} from '@/stores/authStore.js';
import {useI18n} from 'vue-i18n';

import AppBar from '@/components/AppBar.vue';
import NavigationDrawer from '@/components/NavigationDrawer.vue';
import Footer from '@/components/Footer.vue';
import LanguageSelector from '@/components/LanguageSelector.vue';

// Reactive references and state management
const route = useRoute();
const authStore = useAuthStore();
const {t} = useI18n();

// Computed properties
const isAuthenticated = computed(() => !!authStore.token); // Check if the user is authenticated
const isLandingPage = computed(() => route.name === 'Welcome'); // Check if the current route is the landing page

// Drawer state
const drawerState = ref(false);

/**
 * Updates the drawer state when toggled.
 * @param {boolean} value - New state of the drawer.
 */
const updateDrawerState = (value) => {
    drawerState.value = value;
};

// Lifecycle hook to initialize state from localStorage
onMounted(() => {
    const savedState = localStorage.getItem('drawerState');
    if (savedState !== null) {
        drawerState.value = JSON.parse(savedState);
    }
});
</script>

<template>
    <v-app>
        <!-- AppBar: Visible only if not on landing page and user is authenticated -->
        <AppBar
            v-if="!isLandingPage && isAuthenticated"
            :drawerState="drawerState"
            @update:drawerState="updateDrawerState"
        />

        <!-- NavigationDrawer: Matches AppBar visibility -->
        <NavigationDrawer
            v-if="!isLandingPage && isAuthenticated"
            :model-value="drawerState"
            @update:model-value="updateDrawerState"
        />

        <!-- Main content -->
        <v-main style="height: 500px;">
            <v-container fluid>
                <!-- Centered button and language selector for unauthenticated users -->
                <v-row justify="center">
                    <v-col cols="12" class="text-center">
                        <v-btn v-if="!isLandingPage && !isAuthenticated" color="primary">
                            <router-link to="/">{{ t('app.welcome') }}</router-link>
                        </v-btn>
                    </v-col>
                </v-row>
                <!-- Language selector -->
                <LanguageSelector v-if="!isLandingPage && !isAuthenticated"/>

                <router-view/>
            </v-container>
        </v-main>

        <!-- Footer: Always visible -->
        <Footer/>
    </v-app>
</template>

<style scoped>

</style>
