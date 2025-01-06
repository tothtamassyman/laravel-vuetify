import { useI18n } from 'vue-i18n';

export function useValidationRules() {
    const {t} = useI18n();

    return {
        requiredRules: (field = '') => [
            v => Array.isArray(v) ? !!v.length || t('validation.required', {field}) : !!v || t('validation.required', {field}),
        ],

        nameRules: (min = 8, max = 255, required = true) => {
            let rules = [
                v => !v || v.length >= min || t('validation.name.min', {min}),
                v => !v || v.length <= max || t('validation.name.max', {max}),
                v => !v || /^[a-zA-ZáéíóöőúüűÁÉÍÓÖŐÚÜŰ\s-.]+$/.test(v) || t('validation.name.upperLowerIncludingAccentedSpacesDotsHyphens'),
            ];

            if (required) {
                rules.unshift(v => !!v || t('validation.name.required'));
            }

            return rules;
        },

        emailRules: (min = 6, max = 255, field = 'Email', required = true) => {
            let rules = [
                v => !v || v.length >= min || t('validation.email.min', {min, field}),
                v => !v || v.length <= max || t('validation.email.max', {max, field}),
                v => !v || /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(v) || t('validation.email.notValid', {field}),
            ];

            if (required) {
                rules.unshift(v => !!v || t('validation.email.required', {field}));
            }

            return rules;
        },

        urlRules(min = 4, max = 255, field = 'URL', required = true) {
            let rules = [
                v => !v || v.length >= min || t('validation.url.min', {min, field}),
                v => !v || v.length <= max || t('validation.url.max', {max, field}),
                v => !v || /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([/\w .-]*)*\/?$/.test(v) || t('validation.url.notValid', {field}),
            ];

            if (required) {
                rules.unshift(v => !!v || t('validation.url.required', {field}));
            }

            return rules;
        },

        passwordRules(min = 8, max = 255, field = 'Password', required = true) {
            let rules = [
                v => !v || v.length >= min || t('validation.password.min', {min, field}),
                v => !v || v.length <= max || t('validation.password.max', {max, field}),
                v => !v || (/[a-z]/.test(v) && /[A-Z]/.test(v)) || t('validation.password.mixed', {field}),
                v => !v || /[0-9]/.test(v) || t('validation.password.numbers', {field}),
                // v => !v || /[\W_]/.test(v) || t('validation.password.symbols', {field}),
            ];

            if (required) {
                rules.unshift(v => !!v || t('validation.password.required', {field}));
            }

            return rules;
        },

        passwordConfirmationRules(newPassword = '', min = 8, max = 255, required = true, field = 'password_confirmation', mainField = 'password') {
            let rules = [
                v => !v || v.length >= min || t('validation.password_confirmation.min', {min, field}),
                v => !v || v.length <= max || t('validation.password_confirmation.max', {max, field}),
                v => !v || (/[a-z]/.test(v) && /[A-Z]/.test(v)) || t('validation.password_confirmation.mixed', {field}),
                v => !v || /[0-9]/.test(v) || t('validation.password_confirmation.numbers', {field}),
                //v => !v || /[\W_]/.test(v) || t('validation.password_confirmation.symbols', {field}),
                v => v === newPassword || t('validation.password_confirmation.notMatch', {field, mainField}),
            ];

            if (required) {
                rules.unshift(v => !!v || t('validation.password_confirmation.required', {field}));
            }

            return rules;
        },

        lengthRules(min = 1, max = 255, field = '', required = true) {
            let rules = [
                v => !v || v.length >= min || t('validation.length.min', {min, field}),
                v => !v || v.length <= max || t('validation.length.max', {max, field}),
            ];

            if (required) {
                rules.unshift(v => !!v || t('validation.length.required', {field}));
            }

            return rules;
        },

        numberRules(min = 1, max = 255, field = '', required = true) {
            let rules = [
                v => !v || !isNaN(v) || t('validation.number.valid', {field}),
                v => !v || v >= min || t('validation.number.min', {min, field}),
                v => !v || v <= max || t('validation.number.max', {max, field}),
            ];

            if (required) {
                rules.unshift(v => !!v || t('validation.number.required', {field}));
            }

            return rules;
        },

        mobilePhoneRules(field = '', required = true) {
            let rules = [
                v => !v || /^(\+36)?(20|30|70)\d{7}$/.test(v) || t('validation.mobilePhone.notValid', {field}),
            ];

            if (required) {
                rules.unshift(v => !!v || t('validation.mobilePhone.required', {field}));
            }

            return rules;
        },

        phoneRules(field = '', required = true) {
            let rules = [
                v => !v || /^(\+36)?(1|[2-9][0-9])\d{6,7}$/.test(v) || t('validation.phone.notValid', {field}),
            ];

            if (required) {
                rules.unshift(v => !!v || t('validation.phone.required', {field}));
            }

            return rules;
        }
    }
}
