export default [
    {
        path: '/',
        name: 'Welcome',
        component: () => import('@/views/Welcome.vue'),
        meta: {
            requiresAuth: false,
            title: 'Welcome Page',
        },
    },
    {
        path: '/login',
        name: 'Login',
        component: () => import('@/views/auth/Login.vue'),
        meta: {
            requiresAuth: false,
            title: 'Login Page',
        },
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: () => import('@/views/Dashboard.vue'),
        meta: {
            requiresAuth: true,
            title: 'Dashboard',
        },
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: () => import('@/views/NotFound.vue'),
        meta: {
            requiresAuth: false,
            title: 'Page Not Found',
        },
    },
];
