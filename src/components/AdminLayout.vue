<script>
import Sidebar from './admin/Sidebar.vue';

export default {
    components: {
        Sidebar,
    }
}
</script>

<template>
    <div class="layout max-w-screen mx-auto mb-8 pt-6 col">
        <Toast></Toast>
        <Sidebar />
        <router-view></router-view>
    </div>
</template>

<style scoped>
.layout {
    max-width: 100vw;
    padding-left: 160px;
    padding-right: 80px;
}
</style>
