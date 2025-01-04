import './bootstrap';

import {createApp} from 'vue';
import {createPinia, setActivePinia} from 'pinia';
import App from './App.vue';
import vuetify from '@/plugins/vuetify';
import router from '@/router/index';
import initI18n from '@/plugins/i18n';

const pinia = createPinia();
setActivePinia(pinia);

(async () => {
    const i18n = await initI18n();

    const app = createApp(App);

    app.use(pinia);
    app.use(router);
    app.use(vuetify);
    app.use(i18n);

    app.mount('#app');
})();
