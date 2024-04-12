import { createRouter, createWebHistory } from 'vue-router';
import Layout from '@/views/Layout.vue';
import Login from '@/views/pages/Login.vue';
import ForgotPassword from '@/views/pages/ForgotPassword.vue';
import ResetPassword from '@/views/pages/ResetPassword.vue';
import Admin from '@/views/pages/Admin.vue';

const routes = [
    {
        path: '/',
        redirect: '/login',
        component: Layout,
        children: [
            {
                path: '/login',
                component: Login,
            },
            {
                path: '/forgot',
                component: ForgotPassword,
            },
            {
                path: '/reset',
                component: ResetPassword,
            },
            {
                path: '/admin',
                component: Admin,
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return { left: 0, top: 0 };
    }
});

export default router;
