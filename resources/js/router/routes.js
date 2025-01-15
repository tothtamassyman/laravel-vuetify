/**
 * The routes of the application.
 */
export default [
    {
        path: '/',
        name: 'Welcome',
        component: () => import('@/views/Welcome.vue'),
        meta: {
            icon: 'mdi-home',
            title: 'routes.welcome.title',
            subtitle: 'routes.welcome.subtitle',
            gate: null,
            requiresAuth: false,
            hidden: true,
        },
    },
    {
        path: '/login',
        name: 'Login',
        component: () => import('@/views/auth/Login.vue'),
        meta: {
            icon: 'mdi-login',
            title: 'routes.login.title',
            subtitle: 'routes.login.subtitle',
            gate: null,
            requiresAuth: false,
            hidden: true,
        },
    },
    {
        path: '/reset-password/:token',
        name: 'ResetPassword',
        component: () => import('@/views/auth/ResetPassword.vue'),
        props: (route) => ({ token: route.params.token, email: route.query.email }),
        meta: {
            icon: 'mdi-key',
            title: 'routes.reset-password.title',
            subtitle: 'routes.reset-password.subtitle',
            gate: null,
            requiresAuth: false,
            hidden: true,
        },
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: () => import('@/views/Dashboard.vue'),
        meta: {
            icon: 'mdi-view-dashboard',
            title: 'routes.dashboard.title',
            subtitle: 'routes.dashboard.subtitle',
            gate: {
                action: 'view',
                subject: 'dashboard link',
            },
            requiresAuth: true,
            hidden: false,
        },
    },
    {
        path: '/settings',
        name: 'Settings',
        component: () => import('@/views/Settings/Settings.vue'),
        meta: {
            icon: 'mdi-cog',
            title: 'routes.settings.title',
            subtitle: 'routes.settings.subtitle',
            gate: {
                action: 'view',
                subject: 'settings link',
            },
            requiresAuth: true,
            hidden: false,
        },
        children: [
            {
                path: '/user-profile',
                name: 'UserProfile',
                component: () => import('@/views/Settings/UserProfile.vue'),
                meta: {
                    icon: 'mdi-account-wrench',
                    title: 'routes.settings.user-profile.title',
                    subtitle: 'routes.settings.user-profile.subtitle',
                    gate: {
                        action: 'view',
                        subject: 'own-profile link',
                    },
                    requiresAuth: true,
                    hidden: false,
                },
            },
            {
                path: '/access-management',
                name: 'AccessManagement',
                component: () => import('@/views/Settings/AccessManagement/AccessManagement.vue'),
                meta: {
                    icon: 'mdi-account-cog',
                    title: 'routes.settings.access-management.title',
                    subtitle: 'routes.settings.access-management.subtitle',
                    gate: {
                        action: 'view',
                        subject: 'access-management link',
                    },
                    requiresAuth: true,
                    hidden: false,
                },
                children: [
                    {
                        path: '/groups',
                        name: 'Groups',
                        component: () => import('@/views/Settings/AccessManagement/Groups.vue'),
                        meta: {
                            icon: 'mdi-account-group',
                            title: 'routes.settings.access-management.groups.title',
                            subtitle: 'routes.settings.access-management.groups.subtitle',
                            gate: {
                                action: 'view',
                                subject: 'groups link',
                            },
                            requiresAuth: true,
                            hidden: false,
                        },
                    },
                    {
                        path: '/users',
                        name: 'Users',
                        component: () => import('@/views/Settings/AccessManagement/Users.vue'),
                        meta: {
                            icon: 'mdi-account',
                            title: 'routes.settings.access-management.users.title',
                            subtitle: 'routes.settings.access-management.users.subtitle',
                            gate: {
                                action: 'view',
                                subject: 'users link',
                            },
                            requiresAuth: true,
                            hidden: false,
                        },
                    },
                    {
                        path: '/roles',
                        name: 'Roles',
                        component: () => import('@/views/Settings/AccessManagement/Roles.vue'),
                        meta: {
                            icon: 'mdi-account-details',
                            title: 'routes.settings.access-management.roles.title',
                            subtitle: 'routes.settings.access-management.roles.subtitle',
                            gate: {
                                action: 'view',
                                subject: 'roles link',
                            },
                            requiresAuth: true,
                            hidden: false,
                        },
                    },
                    {
                        path: '/permissions',
                        name: 'Permissions',
                        component: () => import('@/views/Settings/AccessManagement/Permissions.vue'),
                        meta: {
                            icon: 'mdi-account-key',
                            title: 'routes.settings.access-management.permissions.title',
                            subtitle: 'routes.settings.access-management.permissions.subtitle',
                            gate: {
                                action: 'view',
                                subject: 'permissions link',
                            },
                            requiresAuth: true,
                            hidden: false,
                        },
                    }
                ],
            },
        ],
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: () => import('@/views/NotFound.vue'),
        meta: {
            icon: 'mdi-alert',
            title: 'routes.not-found.title',
            subtitle: 'routes.not-found.subtitle',
            gate: null,
            requiresAuth: false,
            hidden: true,
        },
    },
    {
        path: '/forbidden',
        name: 'Forbidden',
        component: () => import('@/views/Forbidden.vue'),
        meta: {
            icon: 'mdi-alert',
            title: 'routes.forbidden.title',
            subtitle: 'routes.forbidden.subtitle',
            gate: null,
            requiresAuth: false,
            hidden: true,
        },
    },
];
