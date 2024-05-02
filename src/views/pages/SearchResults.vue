<script>
// components
import Header from '@/components/Header.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

// pinia
import { mapStores } from 'pinia';
import { useResultsStore } from '@/store/resultsStore.js';

// generate random 12-digit SKU
function generateRandomDigits() {
    let sku = '';
    for (let i = 0; i < 12; i++) {
        sku += Math.floor(Math.random() * 10);
    }
    return sku;
}

export default {
    components: {
        Header,
        Breadcrumbs,
    },
    data() {
        return {
            mock: {
                numOfResults: 8,
                searchResults: [
                    {
                        brand: 'TechNova',
                        SKU: generateRandomDigits(),
                        description:
                            'Experience the future with our TechNova series. Built with cutting-edge technology, these products offer unparalleled performance and reliability. Elevate your digital experience with TechNova.',
                        price: 1299.99,
                        condition: 'new',
                    },
                    {
                        brand: 'AeroGlide',
                        SKU: generateRandomDigits(),
                        description:
                            "Introducing AeroGlide, where innovation meets comfort. Our products are designed to provide a seamless experience, whether you're on the go or relaxing at home. Explore the world in style with AeroGlide.",
                        price: 899.95,
                        condition: 'old',
                    },
                    {
                        brand: 'DreamWave',
                        SKU: generateRandomDigits(),
                        description:
                            'ElectraTech brings you the latest in electronic excellence. Our products combine sleek design with advanced functionality to enhance your everyday life. Discover a new level of convenience with ElectraTech.',
                        price: 599.99,
                        condition: 'repaired',
                    },
                    {
                        brand: 'DreamWave',
                        SKU: generateRandomDigits(),
                        description:
                            'Xcelerate your productivity with our powerful line of products. Engineered for peak performance, Xcelerate devices are your ultimate tool for success. Get ahead of the competition with Xcelerate.',
                        price: 1599.99,
                        condition: 'repaired',
                    },
                    {
                        brand: 'DreamWave',
                        SKU: generateRandomDigits(),
                        description:
                            'Immerse yourself in sound with DreamWave. Our audio solutions deliver crystal-clear quality and immersive experiences. Transform your space into an auditory oasis with DreamWave.',
                        price: 299.95,
                        condition: 'repaired',
                    },
                    {
                        brand: 'AeroGlide',
                        SKU: generateRandomDigits(),
                        description:
                            'Step into the future with NexGen. Our innovative products redefine industry standards, offering unmatched performance and reliability. Experience the next generation of technology with NexGen.',
                        price: 999.99,
                        condition: 'repaired',
                    },
                    {
                        brand: 'DreamWave',
                        SKU: generateRandomDigits(),
                        description:
                            'Illuminate your world with LumiTech. Our lighting solutions combine style and functionality to enhance any environment. Brighten up your life with LumiTech.',
                        price: 79.99,
                        condition: 'repaired',
                    },
                    {
                        brand: 'DreamWave',
                        SKU: generateRandomDigits(),
                        description:
                            'Go green with EcoBlend. Our eco-friendly products are designed to reduce environmental impact without sacrificing performance. Join the movement towards sustainability with EcoBlend.',
                        price: 49.99,
                        condition: 'repaired',
                    },
                    {
                        brand: 'DreamWave',
                        SKU: generateRandomDigits(),
                        description:
                            'Sync up with SwiftSync. Our synchronization solutions keep you connected across all your devices, ensuring seamless communication and collaboration. Stay in sync with SwiftSync.',
                        price: 199.99,
                        condition: 'repaired',
                    },
                    {
                        brand: 'DreamWave',
                        SKU: generateRandomDigits(),
                        description:
                            'Boost your vitality with VitaBoost. Our health and wellness products are formulated to support your well-being and vitality. Experience the benefits of holistic living with VitaBoost.',
                        price: 149.99,
                        condition: 'repaired',
                    },
                ],

                // filters
                filters: {},

                // checkboxes
                checkboxes: { condition: false },
            },

            searchResults: null,

            // paginate results
            currentPage: 1,
            itemsPerPage: 3,
            totalItems: 10,

        };
    },
    beforeMount() {
        // set filters

        this.mock.filters.condition = new Set(
            this.mock.searchResults.map((entry) => entry.condition),
        );
        this.mock.filters.brand = new Set(
            this.mock.searchResults.map((entry) => entry.brand),
        );
    },
    mounted() {
        console.log('results store', this.resultsStore.searchResults);
    },

    computed: {
        paginatedData() {
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            return this.mock.searchResults.slice(startIndex, endIndex);
        },

        ...mapStores(useResultsStore),
    },
    methods: {
        onPageChange(event) {
            console.log('page change...');
            this.currentPage = event.page + 1;
        },

        handleProductClick(product) {
            console.log('clicking product', {product});
            this.$router.push(`/${product.brand}`)
        }
    },
};
</script>

<template>
    <Header />

    <Breadcrumbs />

    <div class="mt-4 grid">
        <!-- Filters -->
        <div class="col-2">
            <div class="h-30rem flex flex-column pl-4">
                <!-- Filters: Condition -->
                <span class="mb-2">Stanje</span>

                <div
                    v-for="condition in mock.filters.condition"
                    class="flex align-items-center mb-1"
                >
                    <!-- Checkbox -->
                    <Checkbox
                        v-model="mock.checkboxes.condition"
                        inputId="ingredient2"
                        name="pizza"
                        value="Mushroom"
                    />
                    <label for="ingredient2" class="ml-2">
                        {{ condition }}
                    </label>
                </div>

                <!-- Filters: Brand -->
                <span class="mt-3 mb-2">Brand</span>
                <div
                    v-for="brand in mock.filters.brand"
                    class="flex align-items-center mb-1"
                >
                    <!-- Checkbox -->
                    <Checkbox
                        v-model="mock.checkboxes.condition"
                        inputId="ingredient2"
                        name="pizza"
                        value="Mushroom"
                    />
                    <label for="ingredient2" class="ml-2">
                        {{ brand }}
                    </label>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div class="col grid">
            <div v-for="product in resultsStore.searchResults" class="col-4">
                <!-- Product -->
                <!-- prettier-ignore -->
                <div
                    class="p-4 border-1 border-round cursor-pointer flex 
                    flex-column justify-content-center align-items-center 
                    row-gap-2 hover:shadow-3 text-left border-200"
                    @click="handleProductClick(product)"
                >
                    <div class="product-image border-200" />
                    <!-- <img :src="product.pictureUrls[0]" class="product-image border-200" /> -->
                    <!-- Product: Brand -->
                    <span>{{product.manufacturerName}}</span>

                    <!-- Product: Description -->
                    <span>{{product.name}}</span>

                    <!-- Product: SKU -->
                    <span>{{product.sku}}</span>

                    <!-- Product: Price -->
                    <span>{{product.price}}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Paginator -->
    <Paginator
        :rows="itemsPerPage"
        :totalRecords="totalItems"
        v-model="currentPage"
        @page="onPageChange"
        class="mt-4"
        style="background: none;"
    />
</template>

<style scoped>
.product-image {
    width: 100%;
    height: 130px;
    border: 1px solid black;
    border-radius: 4px;
}
</style>
