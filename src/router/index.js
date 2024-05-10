// vue router
import { createRouter, createWebHistory } from 'vue-router';

// lib
import slug from 'slug';

// utils
import {
    setSlugCharMap,
    shortenCarAcousticsAndElectronicsCategoryName,
} from '@/utils';

// components
import Layout from '@/components/Layout.vue';
import AdminLayout from '@/components/AdminLayout.vue';
import AuthLayout from '@/components/AuthLayout.vue';

// pages
import Login from '@/views/pages/Login.vue';
import ForgotPassword from '@/views/pages/ForgotPassword.vue';
import ResetPassword from '@/views/pages/ResetPassword.vue';
import Home from '@/views/pages/Home.vue';
import UserTable from '@/views/pages/admin/UserTable.vue';
import PriceManagement from '@/views/pages/admin/PriceManagement.vue';
import DiscountTable from '@/views/pages/admin/DiscountTable.vue';
import Category from '@/views/pages/Category.vue';
import Product from '@/views/pages/Product.vue';
import ShoppingCart from '@/views/pages/ShoppingCart.vue';
import ThankYou from '@/views/pages/ThankYou.vue';

// pinia
import { useUserStore } from '@/store/userStore';

// service
import CategoryService from '../service/CategoryService';

setSlugCharMap(slug);

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

// update category name to be shorter for better UX
categoryRoutes.forEach(shortenCarAcousticsAndElectronicsCategoryName);         

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
                path: '/hvala',
                component: ThankYou,
            },
            {
                path: '/' + encodeURI('košarica'),
                name: 'košarica',
                component: ShoppingCart,
            },
            {
                path: '/:product',
                component: Product,
                meta: { requiresAuth: true },
            },

            // create routes for all top-level categories
            ...categoryRoutes.map((category) => {
                return {
                    path: `/${slug(category.name)}`,
                    component: Category,
                    meta: { requiresAuth: true },
                    children: [
                        {
                            path: `/${slug(category.name)}/:subcategory(.*)`,
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
            {
                path: 'price-management',
                component: PriceManagement,
            },
        ],
    },
    {
        path: '/auth',
        component: AuthLayout,
        children: [
            {
                path: 'login',
                component: Login,
            },
            {
                path: 'forgot-password',
                component: ForgotPassword,
            },
            {
                path: 'reset-password',
                component: ResetPassword,
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
        next('/auth/login');
    } else {
        next();
    }
});

export default router;
