<script>
// vue-router
import router from '@/router';

// primevue
import { FilterMatchMode } from 'primevue/api';

// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';

// services
import UserService from '@/service/UserService.js';

// components
import DiscountTable from '@/components/admin/DiscountTable.vue';
import UserTable from '@/components/admin/UserTable.vue';

const userService = new UserService();

export default {
    components: {
        UserTable,
        DiscountTable,
    },
    created() {
        // set filters for datatable
        this.filters = {
            global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        };
    },
    mounted() {},
    data() {
        return {
            // display tables based on icon click
            showTables: {
                users: true,
                discounts: false,
            },

            filters: {},
        };
    },
    computed: {
        ...mapStores(useUserStore),
    },
    methods: {
        handleTableChangeClick(icon) {
            if (icon === 'users') {
                this.showTables.users = true;
                this.showTables.discounts = false;
            }

            if (icon === 'discounts') {
                this.showTables.users = false;
                this.showTables.discounts = true;
            }
        },

        handleHomeClick() {
            this.$router.push('/');
        },

        handleLogoutClick() {
            this.logout();
        },

        add() {
            userService.add(this.user).then((response) => {
                if (response.data.id) {
                    this.isAddUser = false;
                    this.getAll();
                }
            });
        },
        getAll() {
            userService.getAll().then((response) => {
                this.users = response.data.data;
            });
        },
        logout() {
            userService.logout().then((response) => {
                this.$router.push('/login');
            });
            this.userStore.logout()
        },
    },
};
</script>

<template>
    <div>
        <h3 v-if="showTables.users" class="block mb-5 mt-3">
            Upravljanje korisnicima
        </h3>
        <h3 v-if="showTables.discounts" class="block mb-5 mt-3">
            Upravljanje tipovima rabata
        </h3>

        <UserTable v-if="showTables.users" />
        <DiscountTable v-if="showTables.discounts" />

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
                @click="handleHomeClick"
                rounded
                text
                raised
            />

            <Button
                class="block mx-auto mt-3 flex align-items-center"
                severity="secondary"
                icon="pi pi-sign-out"
                v-tooltip="'Odjavi se'"
                @click="handleLogoutClick"
                rounded
                text
                raised
            />

            <Button
                class="block mx-auto mt-7 mb-3 flex align-items-center"
                severity="secondary"
                icon="pi pi-user"
                v-tooltip="'Korisnici'"
                @click="handleTableChangeClick('users')"
                rounded
            />

            <Button
                class="block mx-auto mb-3 flex align-items-center"
                severity="success"
                icon="pi pi-wallet"
                v-tooltip="'Tipovi rabata'"
                @click="handleTableChangeClick('discounts')"
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
    </div>
</template>

<style scoped></style>
