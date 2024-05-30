<script>
// pinia
import { mapStores } from 'pinia';
import { useUIStore } from '@/store/UIStore.js';

// components
import Header from '@/components/Header.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

export default {
    components: {
        Header,
        Breadcrumbs,
    },
    data() {
        return {
            isHomePageMounted: null,
        };
    },
    watch: {
        '$route.path': function (newPath) {
            this.isHomePageMounted = newPath === '/' ? true : false;
        },
    },
    computed: {
        ...mapStores(useUIStore),
    },
    mounted() {
        this.isHomePageMounted = this.$route.path === '/' ? true : false;
    },
};
</script>

<template>
    <div class="layout mx-auto mb-8 mt-4 sm:p-2 md:p-0">
        <Toast></Toast>
        <Header />

        <div
            v-if="!this.isHomePageMounted"
            class="mt-3 flex justify-content-between align-items-center"
        >
            <Breadcrumbs />
            <div
                v-if="this.UIStore.isDataLoading"
                class="flex align-items-center column-gap-2"
            >
                <ProgressSpinner
                    class="w-2rem h-3rem text-400"
                    strokeWidth="3"
                />
                učitavanje podataka...
            </div>
        </div>

        <router-view></router-view>
    </div>
</template>

<style scoped>
.layout {
    max-width: 1100px;
}
</style>
