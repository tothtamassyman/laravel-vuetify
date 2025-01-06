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
                            :rules="emailRules(6,255,t('login.email'))"
                            :error-messages="backendErrors.email || []"
                            type="text"
                            clearable
                            density="compact"
                            variant="outlined"
                            prepend-inner-icon="mdi-email-outline"
                            @keyup.enter="onEnter"
                    ></v-text-field>
                    <div class="my-2"></div>
                    <v-text-field
                            id="passwordField"
                            ref="passwordInput"
                            v-model="password"
                            :label="t('login.password')"
                            :rules="passwordRules(8, 255, t('login.password'))"
                            :error-messages="backendErrors.password || []"
                            :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                            :type="showPassword ? 'text' : 'password'"
                            clearable
                            density="compact"
                            variant="outlined"
                            prepend-inner-icon="mdi-lock-outline"
                            @click:append-inner="showPassword = !showPassword"
                            @keyup.enter="onEnter"
                    ></v-text-field>
                    <v-btn
                            block
                            class="mt-2"
                            color="primary"
                            :loading="loading"
                            type="submit"
                    >
                        {{ t("login.submit") }}
                    </v-btn>
                </v-form>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script setup>
import {reactive, ref} from 'vue';
import {useI18n} from 'vue-i18n';
import {useAuthStore} from '@/stores/auth';
import {useValidationRules} from '@/utils/validationRules';

const {emailRules, passwordRules} = useValidationRules();
const {t} = useI18n();
const authStore = useAuthStore();

const loginForm = ref(null);
const email = ref('');
const password = ref('');
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
            const response = await authStore.login({
                email: email.value,
                password: password.value,
            });
            console.log('Login successful');
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
</script>
