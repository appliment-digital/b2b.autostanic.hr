<script>
// lib
import slug from 'slug';

// utils
import { makeUrl, setSlugCharMap } from '@/utils';

// components
import Header from '@/components/Header.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import Results from '@/components/Results.vue';

// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';
import { useCategoryStore } from '@/store/categoryStore.js';
import { useResultsStore } from '@/store/resultsStore.js';
import { useBreadcrumbsStore } from '@/store/breadcrumbsStore.js';

// services
import CategoryService from '@/service/CategoryService.js';
import ProductService from '@/service/ProductService.js';

// modify slug library (add croatian chars)
setSlugCharMap(slug);

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
            productCount: null,

            hasResults: false,
            results: null,
            resultsFilter: {},

            selectedCategory: null,
            selectedCategoryId: null,
        };
    },
    watch: {
        '$route.path': function (newPath) {
            this.handleNavigation(newPath, this.selectedCategory);
        },
    },
    computed: {
        ...mapStores(
            useUserStore,
            useCategoryStore,
            useResultsStore,
            useBreadcrumbsStore,
        ),
    },
    mounted() {
        this.setIsDataLoading(false);
        // console.log('mounted', this.categoryStore.history);

        this.handleNavigation(this.$route.path);
    },
    methods: {
        handleNavigation(path, selectedCategory) {
            const history = this.categoryStore.history;

            if (selectedCategory) {
                this.getSubcategories(this.selectedCategory.id);
                return;
            }

            const freshUrl = path
                .slice(1)
                .split('/')
                .map((entry) => decodeURIComponent(entry))
                .pop();

            // console.log({ freshUrl });

            const freshUrlData = history.find(
                (entry) => entry.url === freshUrl,
            );

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
                        this.selectedCategory = null;

                        this.subcategories = response.data;
                    } else {
                        this.selectedCategoryId = id;
                        this.getProductsByCategoryId(id);
                    }
                })
                .catch((err) => console.error(err));
        },

        getProductsByCategoryId(id, page = 1, pageSize = 9, filters = {}) {
            this.setIsDataLoading(true);
            this.selectedCategory = null;

            ProductService.getProductsByCategoryId(id, page, pageSize, filters)
                .then((response) => {
                    const { data } = response;

                    // add default image to products that have no image
                    data.products.forEach((entry) => {
                        if (
                            entry.picture_urls[0] ===
                            'https://www.autostanic.hr/content/images/thumbs/default-image_280.png'
                        ) {
                            entry.hasDefaultImage = 'true';
                            entry.picture_urls[0] =
                                '/images/as_logo_single.png';
                        }
                    });

                    console.log('getting products', { response });

                    // set produckjut results data
                    this.resultsStore.addResults(data.products);
                    this.resultsFilter['manufacturers'] = data.manufacturers;
                    this.results = data.products;

                    this.subcategories = null;
                    this.hasResults = true;
                    this.setIsDataLoading(false);
                })
                .catch((err) => console.error(err));
        },

        handleSubcategoryClick(subcategory) {
            if (this.isDataLoading) return;

            const { path } = this.$route;

            this.selectedCategory = subcategory;

            // set product count to display on results page
            this.productCount = subcategory.productCount;

            this.categoryStore.addHistory(subcategory);

            this.$router.push(
                `${path}/${slug(subcategory.name, { lower: true })}`,
            );
        },

        setIsDataLoading(val) {
            this.isDataLoading = val;
        },

        handleResultsPageChange(event) {
            console.log('changing page, hello', event);
            this.getProductsByCategoryId(
                this.selectedCategoryId,
                event.page + 1,
            );
        },

        handleResultsFilterSelect(states) {
            const filters = Object.keys(states).filter((key) => states[key]);

            console.log({ filters });
            console.log({ states });

            // console.log(this.selectedCategoryId);
            // console.log('filter changes...', { manufacturer, val });

            if (filters[0]) {
                this.getProductsByCategoryId(this.selectedCategoryId, 1, 10, {
                    ManufacturerName: filters,
                });
            }
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
        <Results
            :productCount="productCount"
            :results="results"
            :resultsFilter="resultsFilter"
            @on-page-change="handleResultsPageChange"
            @on-filter-select="handleResultsFilterSelect"
        />
    </div>
</template>

<style scoped></style>

{ manufacturerName: 'Bosch' }
