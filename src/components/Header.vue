<script>
// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';

// services
import UserService from '../service/UserService.js';

// components
import UserMenu from '@/components/UserMenu.vue'

const userService = new UserService();

export default {
    components: {
        UserMenu,
    },
    mounted() {},
    updated() {},
    data() {
        return {};
    },
    watch: {},
    computed: {
        ...mapStores(useUserStore),
    },
    methods: {
        logout() {
            userService.logout().then((response) => {
                this.$router.push('/login');
            });
        },
    },
};
</script>

<template>
    <div
        class="flex flex-wrap row-gap-2 align-items-center sm:flex-row sm:grid sm:flex-row sm:flex-nowrap"
    >
        <div class="col-6 sm:col">
            <img src="images/as-logo.png" class="logo" style="height: 48px" />
        </div>

        <!-- Header: Search Bar -->
        <div
            class="flex-order-2 col-12 sm:col sm:flex-order-1 md:col-5 lg:col-6"
        >
            <IconField v-if="!isAdminPage" iconPosition="left">
                <InputIcon class="pi pi-search"> </InputIcon>
                <InputText class="w-full" placeholder="Pretraži" />
            </IconField>
        </div>

        <!-- Header: User & Shopping Cart & Logout -->
        <div class="flex-order-1 col-6 flex justify-content-end sm:col">
            <div class="flex relative">
                <!-- <Dropdown :items="items" label="AA" :optionLabel="a" /> -->
                <UserMenu />
                <!-- <Button
                    :label="userStore.initials"
                    style="width: 42px; height: 42px"
                    class="mr-2 flex justify-content-center"
                    severity="primary"
                    rounded
                    raised
                    text
                /> -->
                <Button
                    severity="secondary"
                    icon="pi pi-shopping-cart"
                    rounded
                    raised
                    text
                />
                <!-- <Button
                    @click="logout()"
                    class=""
                    severity="secondary"
                    icon="pi pi-sign-out"
                    rounded
                    raised
                    text
                /> -->
            </div>
        </div>
    </div>
</template>

<style scoped>
.p-tooltip {
    background-color: blue;
    color: red;
}
</style>
