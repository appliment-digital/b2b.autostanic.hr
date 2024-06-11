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
import DiscountTable from '@/views/pages/admin/DiscountTable.vue';
import Category from '@/views/pages/Category.vue';
import Product from '@/views/pages/Product.vue';
import ShoppingCart from '@/views/pages/ShoppingCart.vue';
import ThankYou from '@/views/pages/ThankYou.vue';
import PriceManagement from '@/views/pages/admin/PriceManagement.vue';
import SearchCodes from '@/views/pages/SearchCodes.vue';
import SearchHeaderResults from '@/views/pages/SearchHeaderResults.vue';

// service
import CategoryService from '@/service/CategoryService';
import UserService from '@/service/UserService';

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
            },
            {
                path: '/thank-you',
                component: ThankYou,
            },
            {
                path: '/shopping-cart',
                component: ShoppingCart,
            },
            {
                path: '/category',
                component: Category,
            },
            {
                path: '/searchcodes',
                component: SearchCodes,
            },
            {
                path: '/search-header-results',
                component: SearchHeaderResults,
            },
            {
                path: '/:product',
                component: Product,
            },

            // create routes for all top-level categories
            // ...categoryRoutes.map((category) => {
            //     return {
            //         path: '/:categoryId',
            //         component: Category,
            // children: [
            //     {
            //         path: `/${slug(category.name)}/:subcategory(.*)`,
            //         component: Category,
            //     },
            // ],
            // };
            // }),
        ],
    },
    {
        path: '/admin',
        component: AdminLayout,
        children: [
            {
                path: 'users',
                name: 'users',
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
                name: 'login',
                component: Login,
                meta: { isPublic: true },
            },
            {
                path: 'forgot-password',
                component: ForgotPassword,
                meta: { isPublic: true },
            },
            {
                path: 'reset-password',
                component: ResetPassword,
                meta: { isPublic: true },
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

router.beforeResolve(async (to) => {
    if (to.meta.isPublic) {
        return true;
    } else {
        try {
            const res = await UserService.getCurrentUserData();
            if (!res.data) {
                throw new Error('Not authenticated...');
            }
        } catch (error) {
            console.error(error);
            return { name: 'login' };
        }
    }
});

export default router;
