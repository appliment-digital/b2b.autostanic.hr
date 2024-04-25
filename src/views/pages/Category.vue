<script>
// vue-router
import { RouterLink } from 'vue-router';

// components
import Header from '@/components/Header.vue';

// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';

// utils
import { capitalizeFirstLetter } from '@/utils';

export default {
    components: {
        Header,
    },
    data() {
        return {
            breadcrumbs: {
                home: {
                    icon: 'pi pi-home',
                    route: '/',
                },
                items: [],
            },

            subcategories: ['user 1', 'user 2', 'user 3'],

            users: null,
        };
    },
    watch: {
        '$route.path': function () {
            this.handleNavigatingCategories();
        },
    },
    computed: {
        ...mapStores(useUserStore),
    },
    mounted() {
        this.handleNavigatingCategories();

        fetch('https://jsonplaceholder.typicode.com/users/')
            .then((response) => response.json())
            .then((json) => {
                const users = json.slice(0, 7);
                this.users = users;
            });

    },
    methods: {
        handleSubcategoryClick(subcategory) {
            // test pinia
            this.userStore.robohash(subcategory);
            // testing product
            subcategory = subcategory.replaceAll(' ', '-');
            subcategory = subcategory.toLowerCase();

            if (subcategory === 'kurtis-weissnat') {
                return this.$router.push({
                    path: `/${subcategory}`,
                });
                console.log('yes');
            }

            const route = `${this.$route.path}/${subcategory}`;
            this.$router.push(route);
        },

        handleNavigatingCategories() {
            const { path } = this.$route;

            let parts;
            parts = path.split('/');
            parts = parts.filter((part) => part !== '');

            let url = '';

            this.breadcrumbs.items = parts.map((part, i) => {
                url += `/${part}`;

                return {
                    label: capitalizeFirstLetter(part.replaceAll('-', ' ')),
                    route: url,
                };
            });
        },
    },
};
</script>

<template>
    <Header />

    <Breadcrumb
        :home="breadcrumbs.home"
        :model="breadcrumbs.items"
        class="mt-5"
    >
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

    <h3>Ostale kategorije</h3>
    <div class="grid gap-3 mt-4">
        <div
            v-for="user in users"
            class="col border-1 border-100 border-round p-2 cursor-pointer"
            @click="handleSubcategoryClick(user.name)"
        >
            <img :src="'https://robohash.org/' + user.name" />
            <span class="block text-center mt-2">{{ user.name }}</span>
        </div>
    </div>
</template>

<style scoped>
img {
    display: block;
    max-width: 100%;
}
</style>
