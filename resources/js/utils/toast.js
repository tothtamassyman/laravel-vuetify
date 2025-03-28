/**
 * https://vue-toastification.maronato.dev/
 * https://github.com/Maronato/vue-toastification
 */

import { POSITION } from 'vue-toastification';

const toastOptions = {
    position: POSITION.TOP_RIGHT,
    timeout: 3000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: "button",
    icon: true,
    rtl: false
};

export default toastOptions;