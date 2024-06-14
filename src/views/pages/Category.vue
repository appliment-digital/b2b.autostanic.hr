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
import { useUIStore } from '@/store/UIStore.js';
import { useBreadcrumbsStore } from '@/store/breadcrumbsStore';

// services
import CategoryService from '@/service/CategoryService.js';
import ProductService from '@/service/ProductService.js';
import camelcaseKeys from 'camelcase-keys';

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
            status: null,
            productCount: null,
            manufacturers: null,

            page: {
                current: 1,
                size: 24,
            },
        };
    },
    computed: {
        ...mapStores(
            useUserStore,
            useCategoryStore,
            useUIStore,
            useBreadcrumbsStore,
        ),
    },
    watch: {
        '$route.query.id': function () {
            const ids = this.$route.query.id.split('&');

            this.handleNavigation(ids[ids.length - 1]);

            this.products = null;
        },
    },
    mounted() {
        const ids = this.$route.query.id.split('&');
        this.handleNavigation(ids[ids.length - 1]);
    },
    methods: {
        /**
         * Handle navigating categories.
         * Get category data from history (e.g. /karoserija/retrovizori --> retrovizori).
         */
        handleNavigation(id) {
            this.UIStore.setIsDataLoading(true);

            CategoryService.getSubcategories(id)
                .then((response) => {
                    if (response.data.length) {
                        // make breadcrumbs
                        const ids = this.$route.query.id.split('&');
                        const breadcrumbs = response.data[0].breadcrumb
                            .split('>')
                            .slice(0, -1)
                            .map((b, i) => ({
                                label: b.trim(),
                                route: `/category?id=${ids.slice(0, i + 1).join(encodeURIComponent('&'))}`,
                            }));

                        this.breadcrumbsStore.set(breadcrumbs);

                        this.UIStore.setIsDataLoading(false);
                        this.subcategories = response.data;
                    } else {
                        this.getProducts(id);
                    }
                })
                .catch((err) => console.error(err));
        },

        /**
         * Get products based on the category id, page options, and filters.
         */
        getProducts(id, filters) {
            //console.log(id);
            this.UIStore.setIsDataLoading(true);

            ProductService.getProductsByCategoryId(
                id,
                this.page.current,
                this.page.size,
                filters,
            )
                .then((response) => {
                    const { data } = response;

                    // make breadcrumbs
                    if (data.categories) {
                        const ids = this.$route.query.id.split('&');
                        const breadcrumbs = camelcaseKeys(data.categories)
                            .reverse()
                            .map((c, i) => ({
                                label: c.name.trim(),
                                route: `/category?id=${ids.slice(0, i + 1).join(encodeURIComponent('&'))}`,
                            }));

                        this.breadcrumbsStore.set(breadcrumbs);
                    }

                    // store response data
                    this.products = data.products;
                    this.status = data.status;
                    this.manufacturers = data.manufacturers;
                    this.productCount = data.productCount[0];

                    this.handleDefaultProductImage(this.products);
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

            this.$router.push({
                path: '/category',
                query: { id: `${this.$route.query.id}&${subcategory.id}` },
            });
        },

        handleResultsPageChange(event, filters) {
            this.page.current = event.page + 1;
            this.getProducts(this.$route.query.id, filters);
        },

        handleFilterSelect(filters, categoryId) {
            this.getProducts(categoryId, filters);
        },

        handleDefaultProductImage(products) {
            products.forEach((entry) => {
                if (
                    entry.pictureUrl &&
                    entry.pictureUrl ===
                        'https://www.autostanic.hr/content/images/thumbs/default-image_280.png'
                ) {
                    entry.hasDefaultImage = 'true';
                    entry.pictureUrl = '/images/as_logo_single.png';
                }
            });
        },

        handleNumOfResultsChange(val) {
            this.page.size = val;
            this.getProducts(this.categoryStore.selectedCategory.id, {});
        },

        handleResetPaginator() {
            this.page.current = 1;
            this.page.size = 24;
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
            <div class="w-full h-full border-1 border-100 px-1 
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
            :status="status"
            :manufacturers="manufacturers"
            :pageOptions="page"
            @on-page-change="handleResultsPageChange"
            @on-filter-select="handleFilterSelect"
            @on-num-of-results-change="handleNumOfResultsChange"
            @on-reset-paginator="handleResetPaginator"
        />
    </div>
</template>

<style scoped></style>
