<script>
// lib
import slug from 'slug';

// utils
import { makeBreadcrumb, setSlugCharMap } from '@/utils';

// pinia
import { mapStores } from 'pinia';
import { useBreadcrumbsStore } from '@/store/breadcrumbsStore.js';

setSlugCharMap(slug);

export default {
    props: ['page', 'product', 'crumbs'],
    data() {
        return {
            home: {
                icon: 'pi pi-home',
                route: '/',
            },

            items: [],
        };
    },
    watch: {
        '$route.path': function (newPath) {
            if (this.page === 'product') return;

            this.makeBreadcrumbFromUrl(newPath);
        },

        items: function () {
            this.breadcrumbsStore.set(this.items);
        },
    },
    computed: {
        ...mapStores(useBreadcrumbsStore),
    },

    mounted() {
        switch (this.page) {
            case 'product':
                this.setProductCrumbs();
                break;

            case 'shoppingCart':
                this.setShoppingCartCrumbs();
                break;
            default:
                this.makeBreadcrumbFromUrl(this.$route.fullPath);
                break;
        }
    },
    unmounted() {
        if (this.$route.path === '/') {
            return this.breadcrumbsStore.set([]);
        }

        if (this.page === 'shoppingCart') {
            this.items.pop();
        }
    },

    methods: {
        setProductCrumbs() {
            const history = this.breadcrumbsStore.products[this.product.id];

            if (history) {
                this.items = history
            } else {
                this.items = this.crumbs;
            }

            const hasProductCrumb = this.items.find((entry) => {
                return entry.icon === 'pi pi-car';
            });

            if (!hasProductCrumb) {
                this.items.push({
                    icon: 'pi pi-car',
                    route: `/${slug(this.product.name)}?id=${this.product.id}`,
                });
            }
        },

        setShoppingCartCrumbs() {
            this.items = this.crumbs;

            const hasShoppingCartCrumb = this.items.find((entry) => {
                return entry.icon === 'pi pi-shopping-cart';
            });

            if (!hasShoppingCartCrumb) {
                this.items.push({
                    icon: 'pi pi-shopping-cart',
                    route: '/košarica',
                });
            }
        },

        makeBreadcrumbFromUrl(path) {
            const fullPath = decodeURIComponent(path);

            const parts = fullPath.slice(1).split('/');

            let url = '';
            this.items = parts.map((part) => {
                url += `/${part}`;

                return {
                    label: makeBreadcrumb(part),
                    route: `${url}`,
                };
            });
        },

        handleBreadcrumbsNavigate(e, item, navigate) {
            e.preventDefault();

            if (item.route === '/košarica') return
            else navigate()
        }
    },
};
</script>

<template>
    <Breadcrumb :home="home" :model="items">
        <template #item="{ item, props }">
            <RouterLink
                v-if="item.route"
                v-slot="{ href, navigate }"
                :to="item.route"
                custom
            >
                <a :href="href" v-bind="props.action" @click="(e) => handleBreadcrumbsNavigate(e, item, navigate)">
                    <span :class="[item.icon, 'text-800']" />
                    <span
                        :class="item.label === 'Košarica' && 'ml-2'"
                        class="text-800"
                        >{{ item.label }}</span
                    >
                </a>
            </RouterLink>
            <a
                v-else
                :href="item.url"
                :target="item.target"
                v-bind="props.action"
            >
                <span class="text-color">{{ item.label }}</span>
            </a>
        </template>
    </Breadcrumb>
</template>
