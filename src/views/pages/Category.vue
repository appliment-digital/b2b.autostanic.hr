<script>
// lib
import slug from 'slug';

// utils
import { getLastUrlPart, setSlugCharMap } from '@/utils';

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
            products: null,
            productCount: null,
            manufacturers: null,

            page: {
                current: 1,
                size: 9,
            },
        };
    },
    computed: {
        ...mapStores(
            useUserStore,
            useCategoryStore,
            useResultsStore,
            useUIStore,
        ),
    },
    watch: {
        '$route.path': function (newPath) {
            this.handleNavigation();

            // hide product results and show category cards 
            this.products = null; 
        },
    },
    mounted() {
        this.handleNavigation();
    },
    methods: {
        /**
         * Handle navigating categories.
         * Get category data from history (e.g. /karoserija/retrovizori --> retrovizori).
         */
        handleNavigation() {
            const category = this.getCategoryDataFromHistory();

            this.UIStore.setIsDataLoading(true);

            CategoryService.getSubcategories(category.id)
                .then((response) => {
                    if (response.data.length) {
                        this.UIStore.setIsDataLoading(false);
                        this.subcategories = response.data;
                    } else {
                        this.getProducts(category.id);
                    }
                })
                .catch((err) => console.error(err));
        },

        /**
         * Get category data fronm history (e.g. /karoserija/retrovizori --> retrovizori).
         */
        getCategoryDataFromHistory() {
            return this.categoryStore.history.find(
                (entry) => entry.url === getLastUrlPart(this.$route.path),
            );
        },

        /**
         * Get products based on the category id, page options, and filters.
         */
        getProducts(id, filters) {
            this.UIStore.setIsDataLoading(true);

            ProductService.getProductsByCategoryId(
                id,
                this.page.current,
                this.page.size,
                filters,
            )
                .then((response) => {
                    const { data } = response;
                    console.log('getProductsByCategoryId', { data });

                    // store response data
                    this.products = data.products;
                    this.manufacturers = data.manufacturers;

                    this.UIStore.setIsDataLoading(false);
                })
                .catch((err) => console.error(err));
        },

        /**
         * Handle clicking subcategory cards.
         */
        handleSubcategoryClick(subcategory) {
            // prevent user clicking on the next category if data still loads 
            if (this.UIStore.isDataLoading) return;

            // set selected category
            this.selectedCategory = subcategory;

            // set product count to display on results page
            this.productCount = subcategory.productCount;

            // store
            this.categoryStore.setSelectedCategory(subcategory);
            this.categoryStore.addHistory(subcategory);

            this.$router.push(
                `${this.$route.path}/${slug(subcategory.name)}`,
            );
        },

        handleResultsPageChange(event) {
            console.log('handleResultsPageChange', {event});

            // this.getProductsByCategoryId(
            //     this.selectedCategoryId,
            //     event.page + 1,
            // );
        },

        handleFilterSelect(filters, categoryId) {
            console.log('filters:', filters);

            this.getProducts(categoryId, filters)
        },

        setDefaultImageToProducts(products) {
            products.forEach((entry) => {
                if (
                    entry.picture_urls &&
                    entry.picture_urls[0] ===
                        'https://www.autostanic.hr/content/images/thumbs/default-image_280.png'
                ) {
                    entry.hasDefaultImage = 'true';
                    entry.picture_urls[0] = '/images/as_logo_single.png';
                }
            });
        },
    },
};
</script>

<template>
    <div v-if="subcategories && !products" class="grid">
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

    <div v-if="products">
        <Results
            :productCount="productCount"
            :products="products"
            :manufacturers="manufacturers"
            @on-page-change="handleResultsPageChange"
            @on-filter-select="handleFilterSelect"
        />
    </div>
</template>

<style scoped></style>
