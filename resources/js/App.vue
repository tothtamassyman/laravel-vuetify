<script setup>
/** @name App */

import {computed} from "vue";
import {useRoute} from 'vue-router';
import {useAuthStore} from '@/stores/authStore.js';
import {useI18n} from 'vue-i18n';

import AppBar from "@/components/AppBar.vue";
import Footer from '@/components/Footer.vue';
import LanguageSelector from "@/components/LanguageSelector.vue";

const route = useRoute();
const authStore = useAuthStore();
const {locale, t} = useI18n();
const isAuthenticated = computed(() => !!authStore.token);
const isLandingPage = computed(() => route.name === 'Welcome');
</script>

<template>
    <v-app>
        <!-- AppBar -->
        <AppBar v-if="isAuthenticated"></AppBar>

        <!-- Main -->
        <v-main>
            <v-container fluid>
                <v-row justify="center">
                    <v-col cols="12" class="text-center">
                        <v-btn v-if="!isLandingPage && !isAuthenticated" color="primary">
                            <router-link to="/">{{ t('app.welcome') }}</router-link>
                        </v-btn>
                    </v-col>
                </v-row>
                <LanguageSelector v-if="!isLandingPage && !isAuthenticated"/>
                <router-view/>
            </v-container>
        </v-main>

        <!-- Footer -->
        <Footer/>
    </v-app>
</template>

<style scoped>

</style>
