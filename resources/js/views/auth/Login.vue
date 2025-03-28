<script setup>
import {onMounted, reactive, ref} from 'vue';
import {useI18n} from 'vue-i18n';
import { useRouter } from 'vue-router';
import {useAuthStore} from '@/stores/authStore.js';
import {useValidationRules} from '@/utils/validationRules';
import axios from "@/plugins/axios.js";

const router = useRouter();
const authStore = useAuthStore();
const {t} = useI18n();
const {emailRules, passwordRules} = useValidationRules();

const loginForm = ref(null);
const email = ref('');
const emailPolicy = ref({});
const password = ref('');
const passwordPolicy = ref({});
const showPassword = ref(false);
const loading = ref(false);
const backendErrors = reactive({});
const backendErrorsTimeout = ref(3000);

const onEnter = () => {
    login();
};

const login = async () => {
    const result = await loginForm.value.validate();

    if (result.valid) {
        loading.value = true;
        try {
            await authStore.login({
                email: email.value,
                password: password.value,
            });

            await router.push({name: 'Dashboard'});
        } catch (error) {
            console.error('Login error:', error);
            Object.assign(backendErrors, error.backendErrors || {});
            setTimeout(() => {
                Object.keys(backendErrors).forEach((key) => (backendErrors[key] = ''));
            }, backendErrorsTimeout.value);
        } finally {
            loading.value = false;
        }
    } else {
        setTimeout(() => {
            loginForm?.value?.resetValidation();
        }, backendErrorsTimeout.value);
    }
};

async function fetchPolicies() {
    try {
        const response = await axios.get('/policies');
        passwordPolicy.value = response.data.password || {
            min_length: 8,
            max_length: 255,
            mixed_case: true,
            numbers: true,
            symbols: true,
            letters: false,
            uncompromised: true,
            history_check: true,
            history_limit: 10,
        };
        emailPolicy.value = response.data.email || {
            min_length: 6,
            max_length: 255,
            validation_standards: {
                rfc: true,
                strict: false,
                dns: true,
                spoof: true,
                filter: true,
                filter_unicode: false,
            },
        };
    } catch (error) {
        console.error('Error fetching policies:', error);
        passwordPolicy.value = {
            min_length: 8,
            max_length: 255,
            mixed_case: true,
            numbers: true,
            symbols: true,
            letters: false,
            uncompromised: true,
            history_check: true,
            history_limit: 10,
        };
        emailPolicy.value = {
            min_length: 6,
            max_length: 255,
            validation_standards: {
                rfc: true,
                strict: false,
                dns: true,
                spoof: true,
                filter: true,
                filter_unicode: false,
            },
        };
    }
}

onMounted(async () => {
    await fetchPolicies();
});
</script>

<template>
    <v-container>
        <v-card class="mx-auto pa-12 pb-8" elevation="4" max-width="448" rounded="lg">
            <v-card-title class="text-center">{{ t('login.title') }}</v-card-title>
            <v-card-text>
                <v-form
                        id="loginForm"
                        ref="loginForm"
                        validate-on="blur lazy"
                        @submit.prevent="login"
                >
                    <v-text-field
                            id="emailField"
                            ref="emailInput"
                            v-model="email"
                            :label="t('login.email')"
                            :rules="emailRules(emailPolicy, t('login.email'))"
                            :error-messages="backendErrors.email || []"
                            type="text"
                            clearable
                            density="compact"
                            variant="outlined"
                            prepend-inner-icon="mdi-email-outline"
                            @keyup.enter="onEnter"
                            required
                    ></v-text-field>
                    <div class="my-2"></div>
                    <v-text-field
                            id="passwordField"
                            ref="passwordInput"
                            v-model="password"
                            :label="t('login.password')"
                            :rules="passwordRules(passwordPolicy, t('login.password'))"
                            :error-messages="backendErrors.password || []"
                            :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                            :type="showPassword ? 'text' : 'password'"
                            clearable
                            density="compact"
                            variant="outlined"
                            prepend-inner-icon="mdi-lock-outline"
                            @click:append-inner="showPassword = !showPassword"
                            @keyup.enter="onEnter"
                            required
                    ></v-text-field>
                    <v-btn
                            block
                            class="mt-2"
                            color="primary"
                            :loading="loading"
                            @click="login"
                    >
                        {{ t("login.submit") }}
                    </v-btn>
                </v-form>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<style scoped>

</style>