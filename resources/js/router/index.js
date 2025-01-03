import { createRouter, createWebHistory } from 'vue-router';
import routes from './routes';
import { globalBeforeEachGuard } from './guards';
import { globalErrorHandler } from './errorHandlers';

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
});

router.beforeEach(globalBeforeEachGuard);

router.onError(globalErrorHandler);

export default router;
