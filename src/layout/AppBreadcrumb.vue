<script setup>
import { useRoute } from 'vue-router';
import { ref, watch } from 'vue';

const route = useRoute();
const breadcrumbRoutes = ref([]);

const setBreadcrumbRoutes = () => {
    if (route.meta.breadcrumb) {
        breadcrumbRoutes.value = route.meta.breadcrumb;

        return;
    }

    breadcrumbRoutes.value = route.fullPath
        .split('/')
        .filter((item) => item !== '')
        .filter((item) => isNaN(Number(item)))
        .map((item) => item.charAt(0).toUpperCase() + item.slice(1));
};

const pageTitle = ref(null);

const setPageTitle = () => {
    pageTitle.value = breadcrumbRoutes.value.slice(-1).pop();
};

watch(
    route,
    () => {
        setBreadcrumbRoutes();
        setPageTitle();
    },
    { immediate: true }
);
</script>

<template>
    <nav class="layout-breadcrumb ml-4">
        <span class="text-3xl font-bold">{{ pageTitle }}</span>
        <ol v-if="breadcrumbRoutes.length > 1" class="text-xs">
            <template v-for="(breadcrumbRoute, i) in breadcrumbRoutes" :key="breadcrumbRoute">
                <li>{{ breadcrumbRoute }}</li>
                <li v-if="i !== breadcrumbRoutes.length - 1" class="layout-breadcrumb-chevron">/</li>
            </template>
        </ol>
    </nav>
</template>
