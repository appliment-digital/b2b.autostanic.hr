<script>
import { users } from '@/users.js';

export default {
    data() {
        return {
            isDialogVisible: false,

            dialogTitle: '',
            dialogFields: {
                firstName: '',
                lastName: '',
                email: '',
                bitrixCompanyID: '',
                deliveryAddress: '',
                paymentType: '',
                discountType: '',
            },

            users: users,

            selectedUser: null,

            searchQuery: '',
        };
    },
    methods: {
        handleTableButtonClick(button, event) {
            if (button === 'add') {
                this.setDialogTitle('Dodaj korisnika');
                this.setDialogFields(null);
                this.openDialog();
            }

            if (button === 'edit') {
                this.setDialogTitle('Uredi korisnika');
                this.setDialogFields(event.data);
                this.openDialog();
            }
        },

        handleCloseDialogClick() {
            this.closeDialog();
        },

        closeDialog() {
            this.isDialogVisible = false;
        },

        openDialog() {
            this.isDialogVisible = true;
        },

        setDialogTitle(title) {
            this.dialogTitle = title;
        },

        setDialogFields(data) {
            Object.keys(this.dialogFields).forEach((key) => {
                if (!data) {
                    this.dialogFields[key] = "";
                } else {
                    this.dialogFields[key] = data[key];
                }
            });
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
    <Dialog
        modal
        dismissableMask
        closeOnEscape
        :header="dialogTitle"
        :visible="isDialogVisible"
        :style="{ width: '30rem' }"
        @update:visible="closeDialog"
    >
        <!-- Dialog Input: First Name -->
        <label for="firstName">Ime</label>
        <InputText v-model="dialogFields.firstName" id="firstName" class="w-full mt-2 mb-3" type="text" />

        <!-- Dialog Input: Last Name -->
        <label for="lastName">Prezime</label>
        <InputText v-model="dialogFields.lastName" id="lastName" class="w-full mt-2 mb-3" type="text" />

        <!-- Dialog Input: E-mai -->
        <label for="email">E-mail</label>
        <InputText v-model="dialogFields.email" id="email" class="w-full mt-2 mb-3" type="text" />

        <!-- Dialog Input: Bitrix Company ID -->
        <label for="bitrixCompanyID">Bitrix Company ID</label>
        <InputText v-model="dialogFields.bitrixCompanyID" id="bitrixCompanyID" class="w-full mt-2 mb-3" type="text" />

        <!-- Dialog Input: Delivery Address -->
        <label for="deliveryAddress">Mjesto isporuke</label>
        <InputText v-model="dialogFields.deliveryAddress" id="deliveryAddress" class="w-full mt-2 mb-3" type="text" />

        <!-- Dialog Input: Payment Type -->
        <label for="paymentType">Način plaćanja</label>
        <InputText v-model="dialogFields.paymentType" id="paymentType" class="w-full mt-2 mb-3" type="text" />

        <!-- Dialog Input: Discount Type -->
        <label for="discountType">Tip rabata</label>
        <InputText v-model="dialogFields.discountType" id="discountType" class="w-full mt-2 mb-3" type="text" />

        <div class="flex justify-content-end">
            <Button
                class="block mt-5 mr-2"
                severity="danger"
                label="Odustani"
                style="min-width: 100px"
                @click="handleCloseDialogClick"
            />
            <Button
                class="block mt-5"
                severity="success"
                label="Spremi"
                style="min-width: 100px"
                @click=""
            />
        </div>
    </Dialog>

    <Card>
        <template #content>
            <DataTable
                tableStyle="min-width: 50rem"
                paginator
                dataKey="id"
                selectionMode="single"
                :value="filteredUsers"
                :rows="5"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                :selection="selectedUser"
                @row-click="(e) => handleTableButtonClick('edit', e)"
            >
                <template #header>
                    <div class="flex justify-content-between">
                        <Button
                            label="Dodaj"
                            icon="pi pi-plus"
                            class="p-button"
                            @click="handleTableButtonClick('add')"
                            outlined
                        />

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
                <Column
                    selectionMode="multiple"
                    headerStyle="width: 3rem"
                ></Column>
                <Column field="firstName" header="Ime" sortable></Column>
                <Column field="lastName" header="Prezime" sortable></Column>
                <Column field="email" header="E-mail" sortable></Column>
                <Column
                    field="deliveryPlace"
                    header="Mjesto isporuke"
                    sortable
                ></Column>
                <Column
                    field="paymentType"
                    header="Način plaćanja"
                    sortable
                ></Column>
                <Column
                    field="discountType"
                    header="Tip rabata"
                    sortable
                ></Column>
                <Column field="isActive" header="Aktivan">
                    <template #body="{ data }">
                        <Checkbox
                            class="custom-checkbox"
                            v-model="data.isActive"
                            :binary="true"
                        />
                    </template>
                </Column>
                <!-- <Column field="actions" header="Akcije">
                    <template #body>
                        <div class="flex">
                            <Button
                                class="mr-2"
                                icon="pi pi-user-edit"
                                aria-label="Edit"
                                @click="handleTableButtonClick('edit')"
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
                </Column> -->
            </DataTable>
        </template>
    </Card>
</template>

<style scoped></style>
