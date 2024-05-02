<script>
// utils
import { makeBreadcrumb } from '@/utils';

// pinia
import { mapStores } from 'pinia';
import { useCategoryStore } from '@/store/categoryStore.js';

export default {
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
        '$route.path': function (newPath) {
            // console.log(newPath);

            this.makeBreadcrumbs(newPath);
        },
    },
    computed: {
        ...mapStores(useCategoryStore),
    },
    mounted() {
        const previousUrl = window.history.state.back;

        // TODO: handle users copying url
        if (!previousUrl) return;

        this.makeBreadcrumbs(this.$route.fullPath);
    },
    methods: {
        makeBreadcrumbs(path) {
            // console.log('making breadcrumbs');

            const fullPath = decodeURIComponent(path);

            const parts = fullPath.slice(1).split('/');
            // console.log({parts});

            let url = '';
            this.items = parts.map((part) => {
                url += `/${part}`;

                return {
                    label: makeBreadcrumb(part),
                    route: `${url}`,
                };
            });
        },
    },
};
</script>

<template>
    <Breadcrumb :home="home" :model="items" class="mt-5">
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
