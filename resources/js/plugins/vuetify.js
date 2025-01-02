import 'vuetify/styles';
import '@mdi/font/css/materialdesignicons.css';
import colors from 'vuetify/lib/util/colors';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import {aliases, mdi} from 'vuetify/iconsets/mdi';

const vuetify = createVuetify({
    components,
    directives,
    icons: {
        defaultSet: 'mdi',
        aliases,
        sets: {
            mdi,
        },
    },
    theme: {
        defaultTheme: "light",
        themes: {
            light: {
                dark: false,
                colors: {
                    primary: colors.blue.lighten1,
                    secondary: colors.grey.lighten1,
                    success: colors.green.lighten1,
                    info: colors.blue.lighten4,
                    warning: colors.orange.lighten1,
                    error: colors.red.lighten1,
                },
            },
            dark: {
                dark: true,
                colors: {
                    primary: colors.blue.darken1,
                    secondary: colors.grey.darken1,
                    success: colors.green.darken1,
                    info: colors.blue.darken4,
                    warning: colors.orange.darken4,
                    error: colors.red.darken1,
                },
            },
        },
    },
});

export default vuetify;
