<script>
import UserService from '../service/UserService.js';
import router from '@/router';

const userService = new UserService();

export default {
    mounted() {
        this.getCurrentUserData();
    },
    data() {
        return {
            userData: null,
        };
    },
    methods: {
        logout() {
            userService.logout().then((response) => {
                this.$router.push('/login');
            });
        },
        getCurrentUserData() {
            userService.getCurrentUserData().then((response) => {
                this.userData = response;
            });
        },
    },
};
</script>

<template>
    <div 
        class="flex flex-wrap row-gap-2 align-items-center sm:flex-row sm:grid sm:flex-row sm:flex-nowrap">
        <div class="col-6 sm:col">
            <img src="images/as-logo.png" class="logo" style="height: 48px" />
        </div>

        <div class="flex-order-2 col-12 sm:col-6 sm:flex-order-1">
            <IconField v-if="!isAdminPage" iconPosition="left">
                <InputIcon class="pi pi-search"> </InputIcon>
                <InputText
                    class="w-full"
                    placeholder="Pretraži"
                />
            </IconField>
        </div>

        <div class="flex-order-1 col-6 flex justify-content-end sm:col">
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
    </div>
</template>

<style scoped></style>
