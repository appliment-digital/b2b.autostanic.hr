<template>
    <div class="layout mb-8">
        <Toast></Toast>
        <router-view></router-view>
    </div>
</template>

<style lang="scss">
.layout {
    max-width: 1200px;
    margin: 0 auto;
    margin-top: 2rem;
    border-radius: 4px;
    padding: 1rem;
}
</style>
