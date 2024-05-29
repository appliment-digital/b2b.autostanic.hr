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
    watch: {
        '$route.path': function (newPath) {
            this.isHomePageMounted = newPath === '/' ? true : false;
        },
    },
    computed: {
        ...mapStores(useUIStore),
    },
    data() {
        return {
            isHomePageMounted: null,
        };
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

        <router-view></router-view>
    </div>
</template>

<style scoped>
.layout {
    max-width: 1100px;
}
</style>
