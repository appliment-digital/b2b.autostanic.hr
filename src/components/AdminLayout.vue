<template>
    <div class="layout max-w-screen mx-auto mb-8 col">
        <Toast></Toast>
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
