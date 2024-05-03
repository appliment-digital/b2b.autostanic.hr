// vue router
import { createRouter, createWebHistory } from 'vue-router';

// utils
import { makeUrl } from '@/utils';

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
import Product from '@/views/pages/Product.vue';
import SearchResults from '@/views/pages/SearchResults.vue';
import ShoppingCart from '@/views/pages/ShoppingCart.vue';

// pinia
import { useUserStore } from '@/store/userStore';

// service
import CategoryService from '../service/CategoryService';

/**
 * Fetch main categories to add them as routes.
 */
const makeCategoryRoutes = async () => {
    const { data } = await CategoryService.getMainCategories();

    if (data.length) {
        return data;
    }
};

const categoryRoutes = await makeCategoryRoutes();

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
            {
                path: '/cart',
                component: ShoppingCart,
            },
            {
                path: '/results',
                component: SearchResults,
                meta: { requiresAuth: true },
                children: [
                    {
                        path: `/:subcategory(.*)`,
                        component: SearchResults,
                    },
                ],
            },
            {
                path: '/:product',
                component: Product,
                meta: { requiresAuth: true },
            },

            // create routes for all top-level categories
            ...categoryRoutes.map((category) => {
                return {
                    path: `/${makeUrl(category.name)}`,
                    component: Category,
                    meta: { requiresAuth: true },
                    children: [
                        {
                            path: `/${makeUrl(category.name)}/:subcategory(.*)`,
                            component: Category,
                        },
                    ],
                };
            }),
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

    // console.log({ to });

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
