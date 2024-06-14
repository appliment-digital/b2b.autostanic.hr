<script>
import ProductService from '@/service/ProductService.js';

import Results from '@/components/Results.vue';

export default {
    components: {
        Results,
    },
    mounted() {
        this.term = this.$route.query.q;
        this.getHeaderSearchResults();
    },
    data() {
        return {
            term: null,
            page: {
                current: 1,
                size: 24,
            },
            products: [],
            productCount: null,
            status: [],
            manufacturers: [],
            isSearchDone: false,
        };
    },
    watch: {
        '$route.query.q'(newValue, oldValue) {
            if (newValue !== oldValue) {
                this.term = newValue;
                this.getHeaderSearchResults();
            }
        },
    },
    methods: {
        handleFilterSelect(filters) {
            this.getHeaderSearchResults(filters);
        },
        handleResultsPageChange(event, filters) {
            this.page.current = event.page + 1;
            this.getHeaderSearchResults(filters);
        },
        handleNumOfResultsChange(val) {
            this.page.size = val;
            //this.getHeaderSearchResults({});
        },
        handleResetPaginator() {
            this.page.current = 1;
            this.page.size = 24;
        },
        getHeaderSearchResults() {
            ProductService.searchProductsByTerm(
                this.page.current,
                this.page.size,
                this.term,
                {},
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
    },
};
</script>

<template>
    <p class="text-xl">
        <span class="font-bold">Rezultati pretrage:</span>
        <span class="bg-blue-100 p-1 border-round-md">{{ this.term }}</span>
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
