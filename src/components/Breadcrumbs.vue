<script>
// utils
import { makeBreadcrumb } from '@/utils';

// pinia
import { mapStores } from 'pinia';
import { useCategoryStore } from '@/store/categoryStore.js';
import { useBreadcrumbsStore } from '@/store/breadcrumbsStore.js';

export default {
    props: ['crumbs'],
    components: {},
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
        '$route.path': function (newPath, oldPath) {
            // console.log({newPath, oldPath});

            this.makeBreadcrumbs(newPath);

            // console.log('this.items', this.items);

            this.breadcrumbsStore.set(this.items);
        },
    },
    computed: {
        ...mapStores(useCategoryStore, useBreadcrumbsStore),
    },
    mounted() {
        // const previousUrl = window.history.state.back;

        // if (!previousUrl) return;

        this.makeBreadcrumbs(this.$route.fullPath);

        // console.log('breadcrumbs --> items:', this.items);

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

            if (this.crumbs) {
                this.items = [...this.crumbs, this.items[this.items.length - 1]];
                return;
            }
        },
    },
};
</script>

<template>
    <Breadcrumb :home="home" false :model="items">
        <template #item="{ item, props }">
            <RouterLink
                v-if="item.route"
                v-slot="{ href, navigate }"
                :to="item.route"
                custom
            >
                <a :href="href" v-bind="props.action" @click="navigate">
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
