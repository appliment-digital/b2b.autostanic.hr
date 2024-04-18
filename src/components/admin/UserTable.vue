<script>
import { FilterMatchMode } from 'primevue/api';
import UserService from '../../service/UserService.js';
import DiscountTypeService from '@/service/DiscountTypeService.js';

const userService = new UserService();
const discountTypeService = new DiscountTypeService();

export default {
    created() {
        //set filters for datatable
        this.filters = {
            global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        };
    },
    mounted() {
        this.getAll();
        this.getRoles();
        this.getDiscountTypes();
    },
    data() {
        return {
            isDialogVisible: false,
            users: [],
            user: {},
            selectedUser: null,
            roles: [],
            discountTypes: [],
        };
    },
    methods: {
        closeDialog() {
            this.isDialogVisible = false;
        },
        openDialog(user) {
            if (user) {
                this.user = user.data;
            }
            this.isDialogVisible = true;
        },
        getRoles() {
            userService.getRoles().then((response) => {
                this.roles = response.data;
            });
        },
        saveUser() {
            if (!this.user.id) {
                userService.add(this.user).then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        this.$toast.add({
                            severity: 'success',
                            summary: 'Uspješno',
                            detail: response.data.message,
                            life: 3000,
                        });
                        this.closeDialog();
                        this.getAll();
                    } else {
                        this.$toast.add({
                            severity: 'error',
                            summary: 'Greška',
                            detail: response.data.message,
                            life: 3000,
                        });
                    }
                });
            } else {
                userService.update(this.user).then((response) => {
                    if (response.data.success) {
                        this.$toast.add({
                            severity: 'success',
                            summary: 'Uspješno',
                            detail: response.data.message,
                            life: 3000,
                        });
                        this.closeDialog();
                        this.getAll();
                    } else {
                        this.$toast.add({
                            severity: 'error',
                            summary: 'Greška',
                            detail: response.data.message,
                            life: 3000,
                        });
                    }
                });
            }
        },
        getAll() {
            userService.getAll().then((response) => {
                this.users = response.data.data;
            });
        },
        getDiscountTypes() {
            discountTypeService.getAll().then((response) => {
                this.discountTypes = response.data.data;
            });
        },
    },
    computed: {},
};
</script>

<template>
    <Dialog
        modal
        dismissableMask
        closeOnEscape
        header="Detalji korisnika"
        :visible="isDialogVisible"
        :style="{ width: '30rem' }"
        @update:visible="closeDialog"
    >
        <!-- Dialog Input: First Name -->
        <label>Ime</label>
        <InputText v-model="user.name" class="w-full mt-2 mb-3" />

        <!-- Dialog Input: Last Name -->
        <label>Prezime</label>
        <InputText v-model="user.last_name" class="w-full mt-2 mb-3" />

        <!-- Dialog Input: E-mai -->
        <label>E-mail</label>
        <InputText v-model="user.email" class="w-full mt-2 mb-3" />

        <label>Lozinka</label>
        <Password
            v-model="user.password"
            class="w-full mt-2 mb-3"
            inputClass="w-full"
            :feedback="false"
        />

        <label>Uloga</label>
        <Dropdown
            v-model="user.role"
            class="w-full mt-2 mb-3"
            filter
            :options="roles"
            optionLabel="name"
            placeholder="Odaberite ulogu"
        />

        <label>Tip rabata</label>
        <MultiSelect
            v-model="user.discountTypes"
            display="chip"
            :options="discountTypes"
            optionLabel="name"
            placeholder="Odaberite tip rabata"
            :maxSelectedLabels="3"
            class="w-full mt-2 mb-3"
        >
            <template #option="slotProps">
                <span>
                    {{ slotProps.option.name }}
                </span>
            </template>
        </MultiSelect>

        <!-- Dialog Input: Bitrix Company ID -->
        <label>Bitrix Company ID</label>
        <InputNumber
            v-model="user.bitrix_company_id"
            class="w-full mt-2 mb-3"
            :min="1"
        />

        <!-- Dialog Input: Delivery Address -->
        <label>Mjesto isporuke</label>
        <InputText v-model="user.delivery_point" class="w-full mt-2 mb-3" />

        <!-- Dialog Input: Payment Type -->
        <label>Način plaćanja</label>
        <InputText v-model="user.payment_method" class="w-full mt-2 mb-3" />

        <div class="flex justify-content-end">
            <Button
                class="block mt-5 mr-2"
                severity="danger"
                label="Odustani"
                style="min-width: 100px"
                @click="closeDialog()"
            />
            <Button
                class="block mt-5"
                severity="success"
                label="Spremi"
                style="min-width: 100px"
                @click="saveUser()"
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
                :value="users"
                :rows="5"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                :selection="selectedUser"
                @row-click="(e) => openDialog(e)"
            >
                <template #header>
                    <div class="flex justify-content-between">
                        <Button
                            label="Dodaj"
                            icon="pi pi-plus"
                            class="p-button"
                            @click="openDialog()"
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
                <Column field="name" header="Ime" sortable></Column>
                <Column field="last_name" header="Prezime" sortable></Column>
                <Column field="email" header="E-mail" sortable></Column>
                <Column
                    field="bitrix_company_id"
                    header="Bitrix company ID"
                    sortable
                ></Column>
                <Column
                    field="delivery_point"
                    header="Mjesto isporuke"
                    sortable
                ></Column>
                <Column
                    field="payment_type"
                    header="Način plaćanja"
                    sortable
                ></Column>
                <Column field="discount_types" header="Tip rabata" sortable>
                    <template #body="{ data }">
                        <span
                            v-for="(discountType, index) in data.discount_types"
                            :key="discountType.id"
                        >
                            {{ discountType.name }}
                            <template
                                v-if="index !== data.discount_types.length - 1"
                                >,
                            </template>
                        </span>
                    </template>
                </Column>
                <Column field="active" header="Aktivan">
                    <template #body="{ data }">
                        <Checkbox
                            class="custom-checkbox"
                            v-model="data.active"
                            :binary="true"
                        />
                    </template>
                </Column>
                <Column field="actions" header="Akcije">
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
                        </div>
                    </template>
                </Column>
            </DataTable>
        </template>
    </Card>
</template>

<style scoped></style>
