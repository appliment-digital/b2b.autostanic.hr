<script>
import router from '@/router';
import { users } from '@/users.js';

export default {
    data() {
        return {
            visible: false,
            dialog: false,

            users: users,
            selectedUsers: null,
            searchQuery: '',

            isAddUser: false,
            isEditUser: false,
        };
    },
    methods: {
        handleAddUserClick() {
            this.isAddUser = true;
        },

        handleCloseAddUserClick() {
            this.isAddUser = false;
        },

        handleSaveAddUserClick() {
            this.isAddUser = false;
        },

        handleEditUserActionClick() {
            this.isEditUser = true;
        },

        handleCloseEditUserClick() {
            this.isEditUser = false;
        },

        handleSaveEditUserClick() {
            this.isEditUser = false;
        },
    },
    computed: {
        filteredUsers() {
            return this.users.filter((user) =>
                Object.values(user).some((value) =>
                    value
                        .toString()
                        .toLowerCase()
                        .includes(this.searchQuery.toLowerCase()),
                ),
            );
        },
    },
};
</script>

<template>
    <h2 class="text-center">Admin Panel</h2>
    <span class="block text-center">Upravljanje korisnicima</span>

    <div class="flex flex-column sm:w-6 mx-auto mb-4"></div>

    <Sidebar v-model:visible="visible" header="Panel">
        <Button
            class="block mt-5 mb-3"
            severity="secondary"
            icon="pi pi-user"
            label="Korisnici"
            style="min-width: 220px"
        />
        <Button
            class="block mb-3"
            severity="success"
            icon="pi pi-wallet"
            label="Kategorije popusta"
            style="min-width: 220px"
        />
        <Button
            class="block mb-3"
            severity="warning"
            icon="pi pi-globe"
            label="Baneri"
            style="min-width: 220px"
        />
        <Button
            severity="help"
            class="block mb-3"
            icon="pi pi-megaphone"
            label="Objave"
            style="min-width: 220px"
        />
    </Sidebar>

    <DataTable
        :value="filteredUsers"
        v-model:selection="selectedUsers"
        :rows="5"
        :rowsPerPageOptions="[5, 10, 20, 50]"
        tableStyle="min-width: 50rem"
        paginator
        dataKey="id"
    >
        <template #header>
            <div class="flex justify-content-between">
                <div class="flex">
                    <Button
                        class="mr-3"
                        icon="pi pi-chevron-right"
                        label="Panel"
                        @click="visible = true"
                    />
                    <Button
                        icon="pi pi-plus"
                        label="Dodaj"
                        @click="handleAddUserClick"
                    />
                </div>
                <IconField iconPosition="left">
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText v-model="searchQuery" placeholder="Pretraži" />
                </IconField>
            </div>
        </template>
        <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
        <Column field="firstName" header="Ime" sortable></Column>
        <Column field="lastName" header="Prezime" sortable></Column>
        <Column field="email" header="E-mail" sortable></Column>
        <Column
            field="deliveryPlace"
            header="Mjesto isporuke"
            sortable
        ></Column>
        <Column field="paymentType" header="Način plaćanja" sortable></Column>
        <Column field="discountType" header="Tip rabata" sortable></Column>
        <Column field="isActive" header="Aktivan">
            <template #body="{ data }">
                <Checkbox class="custom-checkbox" v-model="data.isActive" :binary="true" />
            </template>
        </Column>
        <Column field="actions" header="Akcije">
            <template #body>
                <div class="flex">
                    <Button
                        class="mr-2"
                        icon="pi pi-user-edit"
                        aria-label="Edit"
                        @click="handleEditUserActionClick"
                        text
                        raised
                        rounded
                        outlined
                    />
                    <Button
                        icon="pi pi-times"
                        aria-label="Remove"
                        text
                        raised
                        rounded
                        outlined
                    />
                </div>
            </template>
        </Column>
    </DataTable>

    <Dialog
        v-model:visible="isAddUser"
        modal
        header="Dodaj korisnika"
        :style="{ width: '25rem' }"
    >
        <InputText class="w-full mt-1 mb-3" type="text" placeholder="Ime" />
        <InputText class="w-full mb-3" type="text" placeholder="Prezime" />
        <InputText class="w-full mb-3" type="text" placeholder="E-mail" />
        <InputText
            class="w-full mb-3"
            type="text"
            placeholder="Bitrix company ID"
        />
        <InputText
            class="w-full mb-3"
            type="text"
            placeholder="Mjesto isporuke"
        />
        <InputText
            class="w-full mb-3"
            type="text"
            placeholder="Naćin plaćanja"
        />
        <InputText class="w-full mb-3" type="text" placeholder="Tip rabata" />

        <div class="flex justify-content-between">
            <Button
                class="block mt-5"
                severity="danger"
                label="Odustani"
                style="min-width: 120px"
                @click="handleCloseAddUserClick"
            />
            <Button
                class="block mt-5"
                severity="success"
                label="Spremi"
                style="min-width: 120px"
                @click="handleSaveAddUserClick"
            />
        </div>
    </Dialog>

    <Dialog
        v-model:visible="isEditUser"
        modal
        header="Uredi korisnika"
        :style="{ width: '25rem' }"
    >
        <InputText class="w-full mt-1 mb-3" type="text" placeholder="Ime" />
        <InputText class="w-full mb-3" type="text" placeholder="Prezime" />
        <InputText class="w-full mb-3" type="text" placeholder="E-mail" />
        <InputText
            class="w-full mb-3"
            type="text"
            placeholder="Bitrix company ID"
        />
        <InputText
            class="w-full mb-3"
            type="text"
            placeholder="Mjesto isporuke"
        />
        <InputText
            class="w-full mb-3"
            type="text"
            placeholder="Naćin plaćanja"
        />
        <InputText class="w-full mb-3" type="text" placeholder="Tip rabata" />

        <div class="flex justify-content-between">
            <Button
                class="block mt-5"
                severity="danger"
                label="Odustani"
                style="min-width: 120px"
                @click="handleCloseEditUserClick"
            />
            <Button
                class="block mt-5"
                severity="success"
                label="Spremi"
                style="min-width: 120px"
                @click="handleSaveEditUserClick"
            />
        </div>
    </Dialog>
</template>

<style scoped></style>
