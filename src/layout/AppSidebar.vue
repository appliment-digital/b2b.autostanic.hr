<script setup>
import AppMenu from './AppMenu.vue';
import { useLayout } from '@/layout/composables/layout';
import { useRouter } from 'vue-router';

const router = useRouter();

const navigateToDashboard = () => {
    router.push('/');
};

const { layoutState } = useLayout();

let timeout = null;

const onMouseEnter = () => {
    if (!layoutState.anchored.value) {
        if (timeout) {
            clearTimeout(timeout);
            timeout = null;
        }
        layoutState.sidebarActive.value = true;
    }
};

const onMouseLeave = () => {
    if (!layoutState.anchored.value) {
        if (!timeout) {
            timeout = setTimeout(() => (layoutState.sidebarActive.value = false), 300);
        }
    }
};

const anchor = () => {
    layoutState.anchored.value = !layoutState.anchored.value;
};
</script>

<template>
    <div class="layout-sidebar" @mouseenter="onMouseEnter()" @mouseleave="onMouseLeave()">
        <div class="sidebar-header pr-2">
            <a @click="navigateToDashboard" class="app-logo cursor-pointer font-bold text-white bg-blue-500 py-2 px-3 border-round-lg">Auto Stanić</a>
            <button class="layout-sidebar-anchor p-link" type="button" @click="anchor()"></button>
        </div>
    </div>
</template>

<style lang="scss" scoped></style>
