import './bootstrap';

import {createApp} from 'vue';
import {createPinia, setActivePinia} from 'pinia';
import { abilitiesPlugin } from '@casl/vue';
import ability from '@/plugins/abilities.js';
import App from './App.vue';
import vuetify from '@/plugins/vuetify';
import router from '@/router/index';
import initI18n from '@/plugins/i18n';

import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import toastOptions from '@/utils/toast';

const pinia = createPinia();
setActivePinia(pinia);

(async () => {
    const i18n = await initI18n();

    const app = createApp(App);

    app.use(pinia);
    app.use(router);
    app.use(abilitiesPlugin, ability);
    app.use(vuetify);
    app.use(i18n);
    app.use(Toast, toastOptions);

    app.mount('#app');
})();
