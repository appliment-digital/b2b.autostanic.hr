<script>
// vue-router
import router from '@/router';

// services
import UserService from '../service/UserService.js';

const userService = new UserService();

export default {
    mounted() {},
    updated() {},
    data() {
        return {};
    },
    watch: {},
    computed: {},
    methods: {
        handleAdminIconClick() {
            this.$router.push('/admin');
        },

        logout() {
            userService.logout().then((response) => {
                this.$router.push('/login');
            });
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
                label="HM"
                style="width: 42px; height: 42px"
                class="mr-2 flex justify-content-center"
                severity="primary"
                rounded
                raised
                text
                v-ripple
            />

            <ul
                :class="'topbar-menu active-topbar-menu p-4 w-12rem z-5 hidden bg-white border-round absolute shadow-1'"
                style="list-style: none; top: 0; right: 102px"
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
                        <span>Admin</span>
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
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</template>

<style scoped></style>
