import { createRouter, createWebHistory } from 'vue-router';

// components
import Layout from '@/components/Layout.vue';
import AdminLayout from '@/components/AdminLayout.vue';

// pages
import Login from '@/views/pages/Login.vue';
import ForgotPassword from '@/views/pages/ForgotPassword.vue';
import ResetPassword from '@/views/pages/ResetPassword.vue';
import Home from '@/views/pages/Home.vue';
import UserTable from '@/views/pages/admin/UserTable.vue';
import DiscountTable from '@/views/pages/admin/DiscountTable.vue';
import Category from '@/views/pages/Category.vue';
import Product from '@/views/pages/Product.vue'

// pinia
import { useUserStore } from '@/store/userStore';

const categories = [
    { path: 'karoserija' },
    { path: 'dijelovi-za-popravak-vozila' },
    { path: 'auto-akustika-i-elektronika' },
    { path: 'sve-za-auto' },
    { path: 'sve-za-radionu' },
    { path: 'akcijska-ponuda' },
];

const routes = [
    {
        path: '/',
        component: Layout,
        children: [
            {
                path: '/',
                component: Home,
                meta: { requiresAuth: true },
            },
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

            // create routes for all top-level categories
            ...categories.map((c) => ({
                path: `/${c.path}`,
                component: Category,
                children: [
                    {
                        path: `/${c.path}/:subcategory(.*)`,
                        component: Category,
                    },
                ],
            })),

            // product
            {
                path: '/:product',
                component: Product,
            }
        ],
    },

    {
        path: '/admin',
        component: AdminLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: 'users',
                component: UserTable,
            },
            {
                path: 'discounts',
                component: DiscountTable,
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return { left: 0, top: 0 };
    },
});

router.beforeEach(async (to, from, next) => {
    const userStore = useUserStore();
    const storedIsUserLoggedIn = JSON.parse(localStorage.getItem('isLoggedIn'));

    if (to.meta.requiresAuth && storedIsUserLoggedIn) {
        next();
    }

    if (to.meta.requiresAuth && !userStore.isLoggedIn) {
        next('/login');
    } else {
        next();
    }
});

export default router;
