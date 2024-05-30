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
    data() {
        return {
            home: {
                label: 'Naslovna',
                route: '/',
            },

            items: [],
        };
    },
    computed: {
        ...mapStores(useBreadcrumbsStore),
    },
    watch: {
        '$route.path': function (newPath) {
            this.setCrumbs(newPath);
        }
    },
    mounted() {
        this.setCrumbs(this.$route.path);
    },
    methods: {
        setCrumbs(path) {
            if (this.$route.params['product']) {
                this.makeProductCrumbs();
            } else {
                this.makeCrumbs(path);
            }

            this.breadcrumbsStore.set(this.items);
        },

        makeProductCrumbs() {
            const { id } = this.$route.query;

            const product = {
                id,
                name: this.$route.params.product,
                breadcrumbs: this.breadcrumbsStore.products[id],
            };

            if (product.breadcrumbs) {
                this.items = product.breadcrumbs;
            } else {
                this.items = this.breadcrumbsStore.current;
            }

            const isProductIconInBreadcrumbs = this.items.find((entry) => {
                return entry.icon === 'pi pi-car';
            });

            if (!isProductIconInBreadcrumbs) {
                this.items.push({
                    icon: 'pi pi-car',
                    route: `/${slug(product.name)}?id=${product.id}`,
                });
            }
        },

        makeCrumbs(path) {
            const fullPath = decodeURIComponent(path);

            const parts = fullPath.slice(1).split('/');

            let url = '';
            this.items = parts.map((part) => {
                url += `/${part}`;

                return {
                    label: makeBreadcrumb(part),
                    route: `${url}`,
                    icon: part === 'košarica' && 'pi pi-shopping-cart'
                };
            });
        },

        handleBreadcrumbsNavigate(e, item, navigate) {
            e.preventDefault();

            if (item.route === '/košarica') return;
            else navigate();
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
                    @click="(e) => handleBreadcrumbsNavigate(e, item, navigate)"
                >
                    <span :class="[item.icon, 'text-800']" />
                    <span
                        :class="
                            item.label === 'Košarica'
                                ? 'ml-2'
                                : ''
                        "
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
