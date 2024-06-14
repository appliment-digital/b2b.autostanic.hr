<script>
// pinia
import { mapStores } from 'pinia';
import { useResultsStore } from '@/store/resultsStore';
import Results from '@/components/Results.vue';

import ProductService from '@/service/ProductService.js';
export default {
    components: {
        Results,
    },
    data() {
        return {
            page: {
                current: 1,
                size: 24,
            },
            searchCodeType: [
                { name: 'Broj artikla', key: 'sku' },
                { name: 'Kataloški kod', key: 'oem' },
                { name: 'Kod motora', key: 'EngineCode' },
                { name: 'Kod mjenjača', key: 'GearboxCode' },
            ],
            code: null,
            value: null,
            products: [],
            productCount: null,
            status: [],
            manufacturers: [],
            isSearchDone: false,
        };
    },
    computed: {
        ...mapStores(useResultsStore),
        searchCode() {
            return this.searchCodeType.find(
                (c) => c.key == this.$route.query.code,
            ).name;
        },
    },
    mounted() {
        this.code = this.$route.query.code;
        this.value = this.$route.query.value;
        this.getSearchResults({});
        this.handleDefaultProductImage(this.products);
    },
    watch: {
        '$route.query.code'(newValue, oldValue) {
            if (newValue !== oldValue) {
                this.getSearchResults({});
            }
        },
        '$route.query.value'(newValue, oldValue) {
            if (newValue !== oldValue) {
                this.getSearchResults({});
            }
        },
    },
    methods: {
        getSearchResults(filters) {
            ProductService.getProductsByCodeAndTerm(
                this.page.current,
                this.page.size,
                this.code,
                this.value,
                filters,
            )
                .then((response) => {
                    var data = response.data;
                    this.products = data.products;
                    this.productCount = data.productCount;
                    this.status = data.status;
                    this.manufacturers = data.manufacturers;
                    this.isSearchDone = true;
                })
                .catch((err) => console.error(err));
        },
        handleFilterSelect(filters) {
            this.getSearchResults(filters);
        },
        handleResultsPageChange(event, filters) {
            this.page.current = event.page + 1;
            this.getSearchResults(filters);
        },
        handleNumOfResultsChange(val) {
            this.page.size = val;
            //this.getSearchResults();
        },
        handleResetPaginator() {
            this.page.current = 1;
            this.page.size = 24;
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
    },
};
</script>

<template>
    <p class="text-xl">
        <span class="font-bold mr-1">Rezultati pretrage:</span>
        <span class="bg-blue-100 p-1 border-round-md"
            >{{ this.searchCode }} = "{{ this.value }}"</span
        >
    </p>
    <Results
        v-if="isSearchDone"
        :productCount="productCount"
        :products="products"
        :status="status"
        :manufacturers="manufacturers"
        :pageOptions="page"
        @on-filter-select="handleFilterSelect"
        @on-page-change="handleResultsPageChange"
        @on-num-of-results-change="handleNumOfResultsChange"
        @on-reset-paginator="handleResetPaginator"
    />
</template>
