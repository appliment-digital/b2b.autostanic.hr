<script>
// router
import router from '@/router';

// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';
import { useShoppingCartStore } from '@/store/shoppingCartStore.js';

// services
import UserService from '../service/UserService.js';

// components
import UserMenu from '@/components/UserMenu.vue';

const userService = new UserService();

export default {
    components: {
        UserMenu,
    },
    data() {
        return {};
    },
    watch: {},
    computed: {
        ...mapStores(useUserStore, useShoppingCartStore),
    },
    methods: {
        handleLogoClick() {
            this.$router.push('/');
        },

        handleShoppingCartClick() {
            this.$router.push('shopping-cart');
        },
    },
};
</script>

<template>
    <div
        class="flex flex-wrap row-gap-2 align-items-end sm:flex-row sm:grid sm:flex-row sm:flex-nowrap"
    >
        <!-- Header: Logo -->
        <div
            class="p-0 col-6 sm:col p-0 cursor-pointer"
            style="height: 48px"
            @click="handleLogoClick"
        >
            <img src="/images/as-logo.png" class="logo" />
        </div>

        <!-- Header: Search Bar -->
        <!-- prettier-ignore -->
        <div
            class="p-0 mt-2 flex-order-2 col-12 sm:col sm:flex-order-1 md:col-5
            lg:col-6"
        >
            <IconField v-if="!isAdminPage" iconPosition="left">
                <InputIcon class="pi pi-search"> </InputIcon>
                <InputText class="border-200 w-full" placeholder="Pretraži" />
            </IconField>
        </div>

        <!-- Header: User Info & Shopping Cart -->
        <div class="p-0 flex-order-1 col-6 flex justify-content-end sm:col">
            <div class="flex relative">
                <div class="relative">
                    <Button
                        class="button--no-shadow bg-white text-600 mr-2 border-200 hover:bg-white-alpha-10"
                        label="Košarica"
                        severity="secondary"
                        icon="pi pi-shopping-cart"
                        @click="handleShoppingCartClick"
                    />
                    <!-- Cart Icon -->
                    <!-- prettier-ignore -->
                    <span 
                    v-if="shoppingCartStore.cart && shoppingCartStore.cart.length"
                    class="absolute block p-2 rounded bg-red-500 
                    text-white border-circle flex justify-content-center 
                    align-items-center font-bold text-sm cursor-pointer 
                    border-1 border-white"
                    style="width: 24px; height: 24px; top: -8px; right:3px"
                    >
                        {{ shoppingCartStore.cart.length }}
                    </span>
                </div>
                <UserMenu />
            </div>
        </div>
    </div>
</template>

<style scoped>
.logo {
    max-height: 100%;
}
</style>
