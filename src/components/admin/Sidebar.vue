<script>
// vue-router
import router from '@/router';

// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';

// services
import UserService from '@/service/UserService.js';

const userService = new UserService();

export default {
    data() {
        return {};
    },

    computed: {
        ...mapStores(useUserStore),
    },

    methods: {
        handleSidebarIconClick(icon) {
            if (icon === 'users') {
                this.$router.push('/admin/users');
            }

            if (icon === 'discounts') {
                this.$router.push('/admin/discounts');
            }

            if (icon === 'priceManagement') {
                this.$router.push('/admin/price-management');
            }

            if (icon === 'home') {
                this.$router.push('/');
            }

            if (icon === 'logout') {
                this.logout();
            }
        },

        logout() {
            userService.logout().then((response) => {
                this.$router.push('/login');
            });
            this.userStore.logout();
        },
    },
};
</script>

<template>
    <Sidebar
        :modal="false"
        :visible="true"
        :dismissable="false"
        :show-close-icon="false"
        style="width: 80px; transition: none"
    >
        <Button
            :label="userStore.initials"
            v-tooltip="userStore.fullName"
            class="mx-auto mt-2 mr-2 flex align-items-center justify-content-center"
            style="width: 42px; height: 42px"
            severity="secondary"
            rounded
            raised
            text
        />

        <Button
            class="block mx-auto mt-3 flex align-items-center"
            severity="secondary"
            icon="pi pi-home"
            v-tooltip="'Naslovna'"
            @click="handleSidebarIconClick('home')"
            rounded
            text
            raised
        />

        <Button
            class="block mx-auto mt-3 flex align-items-center"
            severity="secondary"
            icon="pi pi-sign-out"
            v-tooltip="'Odjavi se'"
            @click="handleSidebarIconClick('logout')"
            rounded
            text
            raised
        />

        <Button
            class="block mx-auto mt-7 mb-3 flex align-items-center bg-blue-500"
            icon="pi pi-users"
            v-tooltip="'Korisnici'"
            @click="handleSidebarIconClick('users')"
            rounded
        />

        <Button
            class="block mx-auto mb-3 flex align-items-center"
            severity="success"
            icon="pi pi-percentage"
            v-tooltip="'Tipovi rabata'"
            @click="handleSidebarIconClick('discounts')"
            rounded
        />

        <Button
            class="block mx-auto mb-3 flex align-items-center bg-orange-400 border-orange-400"
            icon="pi pi-euro"
            v-tooltip="'Upravljanje cijenama'"
            @click="handleSidebarIconClick('priceManagement')"
            rounded
        />

        <!-- 
        <Button
            v-tooltip="'Baneri'"
            class="block mx-auto mb-3"
            severity="warning"
            icon="pi pi-globe"
            rounded
        />

        <Button
            v-tooltip="'Objave'"
            severity="help"
            class="block mx-auto mb-3"
            icon="pi pi-megaphone"
            rounded
        /> -->
    </Sidebar>
</template>
