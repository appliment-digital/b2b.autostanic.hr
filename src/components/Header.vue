<script>
import router from '@/router';

export default {
    data() {
        return {};
    },
};
</script>

<template>
    <div class="flex justify-content-between align-items-center">
        <img src="images/as-logo.png" class="logo" />
        <IconField iconPosition="left">
            <InputIcon class="pi pi-search"> </InputIcon>
            <InputText
                class="my-search"
                v-model="value1"
                placeholder="Pretraži"
                style="min-width: 680px"
            />
        </IconField>
        <div class="flex">
            <Button
                class="block mr-2"
                severity="secondary"
                icon="pi pi-shopping-cart"
                rounded
                raised
                text
            />
            <Button
                class="block"
                severity="secondary"
                icon="pi pi-sign-out"
                rounded
                raised
                text
            />
        </div>
    </div>
</template>

<style scoped>
    .logo {
        height: 48px;
    }
</style>
