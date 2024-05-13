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
import { useUIStore } from '@/store/UIStore.js';

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
            useUIStore,
        ),
    },
    mounted() {
        this.handleNavigation(this.$route.path);
    },
    methods: {
        handleNavigation(path, selectedCategory) {
            const history = this.categoryStore.history;

            if (selectedCategory) {
                return this.getSubcategories(this.selectedCategory.id);
            }

            const freshUrl = path
                .slice(1)
                .split('/')
                .map((entry) => decodeURIComponent(entry))
                .pop();

            const freshUrlData = history.find(
                (entry) => entry.url === freshUrl,
            );

            if (!freshUrlData) {
                this.subcategories = null;
                this.$router.push('/');
            }

            this.getSubcategories(freshUrlData.id);
        },

        getSubcategories(id) {
            this.UIStore.setIsDataLoading(true);
            // console.log('getting subcategories');

            CategoryService.getSubcategories(id)
                .then((response) => {
                    this.hasResults = false;
                    // console.log({ subcategories: response.data });

                    if (response.data.length) {
                        this.UIStore.setIsDataLoading(false);
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
            this.UIStore.setIsDataLoading(true);
            this.selectedCategory = null;

            ProductService.getProductsByCategoryId(id, page, pageSize, filters)
                .then((response) => {
                    const { data } = response;
                    //this is done in controller method
                    // // add default image to products that have no image
                    // data.products.forEach((entry) => {
                    //     if (
                    //         entry.picture_url ===
                    //         'https://www.autostanic.hr/content/images/thumbs/default-image_280.png'
                    //     ) {
                    //         entry.hasDefaultImage = 'true';
                    //         entry.picture_url = '/images/as_logo_single.png';
                    //     }
                    // });

                    console.log('getting products', { response });

                    // set product results data
                    this.resultsStore.addResults(data.products);
                    this.resultsFilter['manufacturers'] = data.manufacturers;
                    this.results = data.products;

                    this.subcategories = null;
                    this.hasResults = true;
                    this.UIStore.setIsDataLoading(false);
                })
                .catch((err) => console.error(err));
        },

        handleSubcategoryClick(subcategory) {
            if (this.UIStore.isDataLoading) return;

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
    <div v-if="!hasResults && subcategories" class="grid">
        <div
            v-for="subcategory in subcategories"
            class="col-3 cursor-pointer"
            @click="handleSubcategoryClick(subcategory)"
        >
            <!-- prettier-ignore -->
            <div class="w-full h-full border-1 border-100 pl-0 pr-0
                flex flex-column justify-content-center align-items-center 
                border-round bg-white transition-ease-in transition-color
                hover:shadow-3"
                style="min-height: 180px"
                >
                    <img class="mb-2" :src="subcategory.pictureUrl" style="width:72px; block"/>
                    <span class="block text-center mb-1">{{ subcategory.name }}</span>
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
