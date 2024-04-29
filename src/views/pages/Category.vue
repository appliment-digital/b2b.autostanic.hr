<script>
// utils
import { makeUrl } from '@/utils';

// components
import Header from '@/components/Header.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';
import { useCategoryStore } from '@/store/CategoryStore.js';

// services
import CategoryService from '@/service/CategoryService.js';

export default {
    components: {
        Header,
        Breadcrumbs,
    },
    data() {
        return {
            subcategories: null,
            isDataLoading: null,
        };
    },
    watch: {
        '$route.path': function (newPath) {
            this.handleNavigation(newPath);
        },
    },
    computed: {
        ...mapStores(useUserStore, useCategoryStore),
    },
    mounted() {
        this.setIsDataLoading(false);
        console.log('mounted', this.categoryStore.history);
        this.handleNavigation(this.$route.path);
    },
    methods: {
        handleNavigation(path) {
            const history = this.categoryStore.history;
            console.log({ history });

            const freshUrl = path
                .slice(1)
                .split('/')
                .map((entry) => decodeURIComponent(entry))
                .pop();

            console.log({ freshUrl });

            const freshUrlData = history.find(
                (entry) => entry.url === freshUrl,
            );

            console.log({ freshUrlData });

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
                    // console.log({ subcategories: response.data });

                    if (response.data.length) {
                        this.setIsDataLoading(false);

                        this.subcategories = response.data;
                    } else {
                        this.subcategories = null;
                    }
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
    <div id="test-styles">
        <Header />

        <Breadcrumbs />

        <div v-if="subcategories" class="grid grid-nogutter gap-3 mt-4">
            <!-- prettier-ignore -->
            <div
                v-for="subcategory in subcategories"
                class="col-2 h-10rem border-1 border-100 border-round p-2 
                cursor-pointer flex justify-content-center align-items-center 
                shadow-1 hover:bg-green-100 transition-ease-in transition-colors"
                @click="handleSubcategoryClick(subcategory)"
            >
                <span class="text-center">{{ subcategory.name }}</span>
            </div>
        </div>

        <div v-if="!subcategories">Nema podkategorija</div>

        <Dialog
            id="dialog-category"
            :visible="isDataLoading"
            style="transition: none"
            :closable="false"
        >
            <ProgressSpinner />
        </Dialog>
    </div>
</template>

<style scoped>
</style>
