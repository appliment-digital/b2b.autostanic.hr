<script>
// pinia
import { mapStores } from 'pinia';
import { useBreadcrumbsStore } from '@/store/breadcrumbsStore.js';

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
            if (newPath === '/shopping-cart') {
                this.items = [
                    {
                        label: 'Košarica',
                        route: '/shopping-cart',
                    },
                ];
            }

            if (newPath === '/searchcodes') {
                this.items = [
                    {
                        label: 'Rezultati pretraživanja',
                        route: '',
                    },
                ];
            }
        },

        'breadcrumbsStore.current': function (newCrumbs) {
            this.items = newCrumbs;
        },
    },
    mounted() {
        if (this.$route.path === '/shopping-cart') {
            this.items = [
                {
                    label: 'Košarica',
                    route: '/shopping-cart',
                },
            ];
        }
        if (this.$route.path === '/searchcodes') {
            this.items = [
                {
                    label: 'Rezultati pretraživanja',
                    route: '',
                },
            ];
        }
    },
    methods: {
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
                    <span class="text-800">{{ item.label }}</span>
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
