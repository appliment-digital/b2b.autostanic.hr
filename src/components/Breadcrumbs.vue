<script>
// lib
import slug from 'slug';

// utils
import { makeBreadcrumb, setSlugCharMap, capitalizeFirstLetter } from '@/utils';

// pinia
import { mapStores } from 'pinia';
import { useCategoryStore } from '@/store/categoryStore.js';
import { useBreadcrumbsStore } from '@/store/breadcrumbsStore.js';
import { useResultsStore } from '@/store/resultsStore.js';

setSlugCharMap(slug);

export default {
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
            if (this.isShoppingCartPageMounted) {
                const shoppingCartRoute = this.items.find(
                    (entry) => entry.route === '/košarica',
                );
                console.log(this.items);

                if (!shoppingCartRoute) {
                    return this.items.push({
                        icon: 'pi pi-shopping-cart',
                        route: '/košarica',
                    });
                }

                console.log(this.items);
            } else if (this.isProductPageMounted) {
                const shoppingCartRoute = this.items.find(
                    (entry) => entry.route === '/košarica',
                );

                if (shoppingCartRoute) {
                    this.items.pop();
                }

                const product = decodeURIComponent(this.$route.path.slice(1));
                const pbh = this.breadcrumbsStore.productBreadcrumbsHistory;

                if (pbh && pbh[product]) {
                    return (this.items = pbh[product]);
                }

                this.items.push({
                    label: capitalizeFirstLetter(
                        this.resultsStore.product.name,
                    ),
                    route: `/${slug(this.resultsStore.product.name)}`,
                });

                this.breadcrumbsStore.addProductBreadcrumbsHistory(
                    product,
                    this.items,
                );
            } else {
                this.makeBreadcrumbs(newPath);
            }
        },
    },
    computed: {
        ...mapStores(useCategoryStore, useBreadcrumbsStore, useResultsStore),
        isProductPageMounted() {
            if (this.$route.path === '/') return false;

            const urlPartsMap = Object.fromEntries(
                this.$route.path
                    .split('/')
                    .slice(1)
                    .map((key) => [key, true]),
            );

            return this.categoryStore.mainCategories.every(
                (category) => !urlPartsMap[slug(category.name)],
            );
        },
        isShoppingCartPageMounted() {
            if (decodeURI(this.$route.path) === '/košarica') return true;
        },
    },
    mounted() {
        if (this.isShoppingCartPageMounted) {
            if (!this.items.length) {
                this.home = {
                    label: 'Košarica',
                    icon: 'pi pi-shopping-cart',
                    route: '/košarica',
                };
            }
            return;
        }

        if (this.isProductPageMounted) {
            const product = decodeURIComponent(this.$route.path.slice(1));
            const pbh = this.breadcrumbsStore.productBreadcrumbsHistory;

            if (pbh && pbh[product]) {
                return (this.items = pbh[product]);
            }
        }

        this.makeBreadcrumbs(this.$route.fullPath);
    },
    methods: {
        makeBreadcrumbs(path) {
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

        handleNavigate(e, navigate, item) {
            e.preventDefault();

            if (item.route === '/košarica') return;

            navigate();
        },
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
                <a
                    :href="href"
                    v-bind="props.action"
                    @click="handleNavigate($event, navigate, item)"
                >
                    <span :class="[item.icon, 'text-800']" />
                    <span :class="item.label === 'Košarica' && 'ml-2'" class="text-800">{{ item.label }}</span>
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
