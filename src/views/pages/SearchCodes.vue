<script>
// pinia
import { mapStores } from 'pinia';
import { useResultsStore } from '@/store/resultsStore';
import Results from '@/components/Results.vue';

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
                { name: 'Broj dijela', key: 'oem' },
                { name: 'Šifra motora', key: 'EngineCode' },
                { name: 'Šifra mjenjača', key: 'GearboxCode' },
            ],
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
        this.handleDefaultProductImage(this.resultsStore.searchcodes.products);
    },
    methods: {
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
        <span class="font-bold">Rezultati pretrage:</span>
        {{ this.searchCode }} = "{{ this.$route.query.value }}"
    </p>

    <Results
        :productCount="resultsStore.searchcodes.productCount"
        :products="resultsStore.searchcodes.products"
        :status="resultsStore.searchcodes.status"
        :manufacturers="resultsStore.searchcodes.manufacturers"
        :pageOptions="page"
    />
</template>
