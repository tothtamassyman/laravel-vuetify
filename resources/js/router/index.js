import { createRouter, createWebHistory } from 'vue-router';
import routes from './routes';
import { beforeEachGuard, afterEachGuard } from './guards';
import { errorHandler } from './errorHandler';

// Create a new router instance
const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
});

// Add global navigation guards
router.beforeEach(beforeEachGuard);
router.afterEach(afterEachGuard);
// Add global error handler
router.onError(errorHandler);

export default router;
