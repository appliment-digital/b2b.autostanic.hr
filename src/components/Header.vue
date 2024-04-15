<script>
import UserService from "../service/UserService.js";
import router from '@/router';

const userService = new UserService();

export default {
    data() {
        return {};
    },
    methods: {
        logout(){
            userService.logout()
                .then(response => {
                    this.$router.push("/login");
                });
        }
    }
};
</script>

<template>
    <div class="flex justify-content-between align-items-center">
        <img src="images/as-logo.png" class="logo" style="height: 48px" />
        <IconField iconPosition="left">
            <InputIcon class="pi pi-search"> </InputIcon>
            <InputText
                class="my-search"
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
                @click="logout()"
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
</style>
