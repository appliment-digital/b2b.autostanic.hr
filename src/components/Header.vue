<script>
// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';

// services
import UserService from '../service/UserService.js';

// components
import UserMenu from '@/components/UserMenu.vue';

const userService = new UserService();

export default {
    components: {
        UserMenu,
    },
    mounted() {
        //console.log(this.userStore.initials);
    },
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
        <!-- Header: Logo -->
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

        <!-- Header: User Info & Shopping Cart -->
        <div class="flex-order-1 col-6 flex justify-content-end sm:col">
            <div class="flex relative">
                <Button
                    class="mr-2"
                    severity="secondary"
                    icon="pi pi-shopping-cart"
                    rounded
                    raised
                    text
                />
                <UserMenu />
            </div>
        </div>
    </div>
</template>

<style scoped></style>
