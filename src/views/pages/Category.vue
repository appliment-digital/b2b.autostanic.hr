<script>
// utils
import { makeUrl } from '@/utils';

// components
import Header from '@/components/Header.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import Results from '@/components/Results.vue';

// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';
import { useCategoryStore } from '@/store/categoryStore.js';
import { useResultsStore } from '@/store/resultsStore.js';

// services
import CategoryService from '@/service/CategoryService.js';
import ProductService from '@/service/ProductService.js';

export default {
    components: {
        Header,
        Breadcrumbs,
        Results,
    },
    data() {
        return {
            subcategories: null,
            isDataLoading: null,
            products: [],

            hasResults: false,
        };
    },
    watch: {
        '$route.path': function (newPath) {
            this.handleNavigation(newPath);
        },
    },
    computed: {
        ...mapStores(useUserStore, useCategoryStore, useResultsStore),
    },
    mounted() {
        this.setIsDataLoading(false);
        // console.log('mounted', this.categoryStore.history);

        this.handleNavigation(this.$route.path);
    },
    methods: {
        handleNavigation(path) {
            const history = this.categoryStore.history;
            // console.log({ history });

            const freshUrl = path
                .slice(1)
                .split('/')
                .map((entry) => decodeURIComponent(entry))
                .pop();

            // console.log({ freshUrl });

            const freshUrlData = history.find(
                (entry) => entry.url === freshUrl,
            );

            // console.log({ freshUrlData });

            if (!freshUrlData) {
                this.subcategories = null;
            }

            this.getSubcategories(freshUrlData.id);
        },

        getSubcategories(id) {
            this.setIsDataLoading(true);
            // console.log('getting subcategories');

            CategoryService.getSubcategories(id)
                .then((response) => {
                    this.hasResults = false;
                    // console.log({ subcategories: response.data });

                    if (response.data.length) {
                        this.setIsDataLoading(false);

                        this.subcategories = response.data;
                    } else {
                        this.getProductsByCategoryId(id);
                    }
                })
                .catch((err) => console.error(err));
        },

        getProductsByCategoryId(id) {
            this.setIsDataLoading(true);

            ProductService.getProductsByCategoryId(id, 1, 10)
                .then((response) => {
                    const { data } = response;
                    console.log('getting products', {response});

                    this.resultsStore.addResults(data.products);
                    this.subcategories = null;
                    this.hasResults = true;
                    this.setIsDataLoading(false);
                })
                .catch((err) => console.error(err));
        },

        handleSubcategoryClick(subcategory) {
            if (this.isDataLoading) return;

            const { path } = this.$route;

            this.categoryStore.addHistory(subcategory);

            this.$router.push(`${path}/${makeUrl(subcategory.name)}`);
        },

        setIsDataLoading(val) {
            this.isDataLoading = val;
        },
    },
};
</script>

<template>
    <Header />

    <div class="mt-3 flex justify-content-between align-items-center">
        <Breadcrumbs />
        <div v-if="isDataLoading" class="flex align-items-center column-gap-2">
            <ProgressSpinner class="w-2rem h-3rem text-400" strokeWidth="3" />
            učitavanje podataka...
        </div>
    </div>

    <div v-if="!hasResults && subcategories" class="grid">
        <div
            v-for="subcategory in subcategories"
            class="col-3 cursor-pointer"
            @click="handleSubcategoryClick(subcategory)"
        >
                <!-- prettier-ignore -->
                <div class="w-full h-full border-1 border-100 p-4 
                flex flex-column justify-content-center align-items-center 
                border-round bg-white transition-ease-in transition-color
                hover:shadow-3">
                    <img class="mb-2" :src="subcategory.pictureUrls[0]" style="width:64px; block"/>
                    <span class="mb-1 text-center">{{ subcategory.name }}</span>
                    <span class="text-center text-xs">({{ subcategory.productCount }})</span>
                </div>
        </div>
    </div>

    <div v-if="hasResults">
        <Results :data="['1', '2']" />
    </div>
</template>

<style scoped></style>
