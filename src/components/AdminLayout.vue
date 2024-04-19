<template>
    <div class="layout mb-8">
        <Toast></Toast>
        <router-view></router-view>
    </div>
</template>

<style scoped>
.layout {
    max-width: 1460px;
    margin: 0 auto;
    margin-top: 2rem;
    border-radius: 4px;
    margin-left: 80px;
    display: grid;
    place-content: center;
}
</style>
