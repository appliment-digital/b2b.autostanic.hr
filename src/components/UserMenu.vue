<script>
// vue-router
import router from '@/router';

// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';

// services
import UserService from '../service/UserService.js';

const userService = new UserService();

export default {
    props: ['userInitials'],
    mounted() {
        //console.log(this.userInitials);
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
        handleAdminIconClick() {
            this.$router.push('/admin');
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
                style="width: 42px; height: 42px"
                class="flex justify-content-center"
                severity="primary"
                rounded
                raised
                text
                v-ripple
            />

            <ul
                :class="'topbar-menu active-topbar-menu p-4 w-15rem z-5 hidden bg-white border-round absolute shadow-1'"
                style="
                    list-style: none;
                    top: calc(100% + 8px);
                    right: -7px;
                    border-bottom-right-radius: 0 !important;
                "
            >
                <!-- <li role="menuitem" class="m-0 mb-3">
                    <a
                        href="#"
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
                        <i class="pi pi-fw pi-lock mr-2"></i>
                        <span>Privacy</span>
                    </a>
                </li> -->
                <li role="menuitem" class="m-0 mb-3">
                    <a
                        href="#"
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
                        <i class="pi pi-fw pi-user mr-2"></i>
                        <span>{{ userStore.fullName }}</span>
                    </a>
                </li>
                <li role="menuitem" class="m-0 mb-3">
                    <a
                        href="#"
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
