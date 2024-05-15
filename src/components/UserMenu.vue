<script>
// lib
import axios from 'axios';

// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';

// services
import UserService from '../service/UserService.js';

const userService = new UserService();

export default {
    data() {
        return {};
    },
    watch: {},
    computed: {
        ...mapStores(useUserStore),
    },
    methods: {
        handleAdminIconClick() {
            this.$router.push('/admin/users');
        },

        logout() {
            userService.logout().then(() => this.$router.push('/auth/login'));
            this.userStore.logout();
        },
    },
};
</script>

<template>
    <ul class="topbar-menu m-0 p-0" style="list-style: none">
        <li class="topbar-item">
            <Button
                v-styleclass="{
                    selector: '@next',
                    enterClass: 'hidden',
                    enterActiveClass: 'px-scalein',
                    leaveToClass: 'hidden',
                    leaveActiveClass: 'px-fadeout',
                    hideOnOutsideClick: 'true',
                }"
                :label="userStore.initials"
                class="flex justify-content-center"
                severity="secondary"
                v-ripple
                icon="pi pi-user"
                raised
                text
            />

            <ul
                :class="'topbar-menu active-topbar-menu p-4 w-15rem z-5 hidden bg-white border-round absolute shadow-1'"
                style="
                    list-style: none;
                    top: calc(100% + 8px);
                    right: 0;
                    border-bottom-right-radius: 0 !important;
                "
            >
                <li role="menuitem" class="m-0 mb-3">
                    <span
                        class="flex align-items-center transition-duration-200"
                        style="user-select: none"
                    >
                        <i class="pi pi-fw pi-user mr-2"></i>
                        <span>{{ userStore.fullName }}</span>
                    </span>
                </li>
                <li
                    v-if="userStore.isUserAdmin"
                    role="menuitem"
                    class="m-0 mb-3"
                >
                    <a
                        @click="handleAdminIconClick"
                        class="flex align-items-center hover:text-primary-500 transition-duration-200"
                        v-styleclass="{
                            selector: '@grandparent',
                            enterClass: 'hidden',
                            enterActiveClass: 'px-scalein',
                            leaveToClass: 'hidden',
                            leaveActiveClass: 'px-fadeout',
                            hideOnOutsideClick: 'true',
                        }"
                    >
                        <i class="pi pi-fw pi-cog mr-2"></i>
                        <span>Postavke</span>
                    </a>
                </li>
                <li>
                    <a
                        href="#"
                        @click="logout()"
                        class="flex align-items-center hover:text-primary-500 transition-duration-200"
                        v-styleclass="{
                            selector: '@grandparent',
                            enterClass: 'hidden',
                            enterActiveClass: 'px-scalein',
                            leaveActiveClass: 'px-fadeout',
                            hideOnOutsideClick: 'true',
                        }"
                    >
                        <i class="pi pi-fw pi-sign-out mr-2"></i>
                        <span>Odjavi se</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</template>

<style scoped></style>
