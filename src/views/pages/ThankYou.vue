<script>
import Header from '@/components/Header.vue'

export default {
    components: {
        Header,
    }
};
</script>

<template>
    <div class="screen-75 flex flex-column justify-content-center align-items-center">
        <h2>Zahvaljujemo na narudžbi :)</h2>
        <span class="block">Vaša narudžba je uspješno zaprimljena!</span>
    </div>
</template>

<style scoped>
.screen-75 {
    height: 75vh;
}
</style>