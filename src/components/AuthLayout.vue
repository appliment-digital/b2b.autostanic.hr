<script>
export default {};
</script>

<template>
    <Toast></Toast>
    <div class="layout">
        <router-view></router-view>
    </div>
</template>

<style scoped>
.layout {
    max-width: 360px;
    height: 75vh;
    margin: 0 auto;
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>
