<script>
// primevue
import { FilterMatchMode } from 'primevue/api';

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
    mounted() {
        // this.getAll();
    },
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
    },
    computed: {},
};
</script>

<template>
    <h3 v-if="showTables.users" class="block mb-6">Upravljanje korisnicima</h3>
    <h3 v-if="showTables.discounts" class="block mb-6">
        Upravljanje popustima
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
            class="block mx-auto mt-2 mb-7"
            severity="secondary"
            icon="pi pi-sign-out"
            v-tooltip="'Logout'"
            rounded
            text
            raised
        />

        <Button
            class="block mx-auto mb-3"
            severity="secondary"
            icon="pi pi-user"
            v-tooltip="'Korisnici'"
            @click="handleTableChangeClick('users')"
            rounded
        />

        <Button
            class="block mx-auto mb-3"
            severity="success"
            icon="pi pi-wallet"
            v-tooltip="'Kategorije popusta'"
            @click="handleTableChangeClick('discounts')"
            rounded
        />
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
        />
    </Sidebar>
</template>

<style scoped></style>
