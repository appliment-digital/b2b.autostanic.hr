<script>
import router from '@/router';

export default {
    emits: ['openSidebar'],
    data() {
        return {
            visible: false,
            dialog: false,

            categories: [],
            selectedCategories: null,
            searchQuery: '',
        };
    },
    methods: {},
    computed: {},
};
</script>

<template>
    <h1 class="text-center mb-2">Admin Panel</h1>
    <span class="block text-xl text-center mb-4">Upravljanje kategorijama</span>

    <div class="flex flex-column sm:w-6 mx-auto mb-4"></div>
    <Button label="hi" @click="$emit('openSidebar')" />

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
                    <InputText
                        v-model="searchQuery"
                        placeholder="Pretraži"
                        style="min-width: 240px"
                    />
                </IconField>
            </div>
        </template>
        <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
        <Column field="categoryName" header="Naziv" sortable></Column>
        <Column
            field="discountAmount"
            header="Iznos rabata %"
            sortable
        ></Column>
        <Column field="email" header="Korisnici" sortable></Column>
        <Column field="discountType" header="Tip rabata" sortable></Column>
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
