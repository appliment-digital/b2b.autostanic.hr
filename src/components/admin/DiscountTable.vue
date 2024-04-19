<script>
import { FilterMatchMode } from 'primevue/api';

import DiscountTypeService from '@/service/DiscountTypeService.js';
import UserService from '@/service/UserService.js';

const discountTypeService = new DiscountTypeService();
const userService = new UserService();

export default {
    created() {
        //set filters for datatable
        this.filters = {
            global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        };
    },
    mounted() {
        this.getAll();
        this.getAllUsers();
    },
    data() {
        return {
            filters: {},
            discountTypes: [],
            discountType: {},
            discountTypeDialog: false,
            users: [],
            expandedRows: [],
        };
    },
    methods: {
        getAll() {
            discountTypeService.getAll().then((response) => {
                this.discountTypes = response.data.data;
            });
        },
        openDialog(selectedType) {
            if (selectedType) {
                selectedType.users.forEach((v) => delete v.pivot);
                this.discountType = selectedType;
            }
            this.discountTypeDialog = true;
        },
        closeDialog() {
            this.discountTypeDialog = false;
            this.discountType = {};
        },
        saveDiscountType() {
            if (!this.discountType.id) {
                discountTypeService.add(this.discountType).then((response) => {
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
                discountTypeService
                    .update(this.discountType)
                    .then((response) => {
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
        getAllUsers() {
            userService.getAll().then((response) => {
                response.data.data.forEach((v) => delete v.discount_types);
                response.data.data.forEach((v) => delete v.pivot);
                response.data.data.forEach((v) => delete v.roles);

                this.users = response.data.data;
            });
        },
        expandAll() {
            this.expandedRows = this.discountTypes.reduce(
                (acc, p) => (acc[p.id] = true) && acc,
                {},
            );
        },
        collapseAll() {
            this.expandedRows = null;
        },
        deleteItem(id) {
            discountTypeService.delete(id).then((response) => {
                if (response.data.success) {
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Uspješno',
                        detail: response.data.message,
                        life: 3000,
                    });
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
        },
        confirmDialog(discountType) {
            this.$confirm.require({
                group: 'templating',
                header: 'Potvrda',
                message:
                    'Molim vas potvrdite da želite obrisati tip rabata ' +
                    discountType.name,
                icon: 'pi pi-exclamation-circle',
                acceptIcon: 'pi pi-check',
                rejectIcon: 'pi pi-times',
                rejectClass: 'p-button-outlined p-button-sm',
                acceptClass: 'p-button-sm',
                rejectLabel: 'Odustani',
                acceptLabel: 'Potvrdi',
                accept: () => {
                    this.deleteItem(discountType.id);
                },
                reject: () => {},
            });
        },
    },
};
</script>

<template>
    <Card>
        <template #content>
            <DataTable
                v-model:expandedRows="expandedRows"
                :value="discountTypes"
                @rowExpand="onRowExpand"
                @rowCollapse="onRowCollapse"
                :paginator="true"
                :rows="5"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                :filters="filters"
                responsiveLayout="scroll"
            >
                <template #header>
                    <div
                        class="flex flex-column md:flex-row md:justify-content-between md:align-items-center"
                    >
                        <div>
                            <Button
                                label="Dodaj"
                                icon="pi pi-plus"
                                class="p-button mr-2"
                                @click="openDialog()"
                                outlined
                            />
                            <!-- <Button
                                v-if="expandedRows == null"
                                icon="pi pi-window-maximize"
                                label="Prošiti"
                                class="mr-2"
                                @click="expandAll()"
                                outlined
                            />
                            <Button
                                v-else
                                icon="pi pi-angle-double-down"
                                label="Proširi"
                                class="mr-2"
                                @click="collapseAll()"
                                outlined
                            /> -->
                        </div>
                        <IconField iconPosition="left">
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText
                                v-model="filters['global'].value"
                                placeholder="Pretraži"
                                style="min-width: 240px"
                            />
                        </IconField>
                    </div>
                </template>
                <Column expander style="width: 5rem" />
                <Column field="name" header="Naziv"></Column>
                <Column field="discount" header="Rabat %"></Column>
                <Column field="users" header="Korisnici">
                    <template #body="{ data }">
                        <span
                            v-for="(user, index) in data.users"
                            :key="user.id"
                        >
                            {{ user.name }} {{ user.last_name }}
                            <template v-if="index !== data.users.length - 1"
                                >,
                            </template>
                        </span>
                    </template>
                </Column>
                <Column field="actions" header="Akcije">
                    <template #body="slotProps">
                        <div class="flex">
                            <Button
                                class="mr-2"
                                icon="pi pi-user-edit"
                                aria-label="Edit"
                                @click="openDialog(slotProps.data)"
                                text
                                raised
                                rounded
                                outlined
                            />
                            <Button
                                @click="confirmDialog(slotProps.data)"
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
                <template #expansion="slotProps">
                    <div class="p-2">
                        <h6>Korisnici</h6>
                        <DataTable :value="slotProps.data.users">
                            <Column header="Ime i prezime">
                                <template #body="{ data }">
                                    <span
                                        >{{ data.name }}
                                        {{ data.last_name }}</span
                                    >
                                </template>
                            </Column>
                            <Column field="email" header="E-mail"> </Column>
                            <Column
                                field="bitrix_company_id"
                                header="Bitrix Company ID"
                            ></Column>
                            <Column
                                field="delivery_point"
                                header="Mjesto isporuke"
                            >
                            </Column>
                            <Column
                                field="payment_method"
                                header="Način plaćanja"
                            ></Column>
                            <Column field="active" header="Aktivan">
                                <template #body="{ data }">
                                    <div v-if="data.active">
                                        <span class="text-green-500">DA</span>
                                    </div>
                                    <div v-else>
                                        <span class="text-red-500">NE</span>
                                    </div>
                                </template>
                            </Column>
                            <template #empty>
                                Još nema dodanih korisnika
                            </template>
                        </DataTable>
                    </div>
                </template>
                <template #empty> Još nema unesenih tipova rabata. </template>
            </DataTable>
        </template>
    </Card>

    <Dialog
        v-model:visible="discountTypeDialog"
        :style="{ width: '450px' }"
        header="Detalji tipa rabata"
        :modal="true"
        class="p-fluid"
    >
        <p class="text-red-500">Polja označena s zvijezdicom(*) su obavezna!</p>
        <div class="field">
            <label>Naziv<span class="text-red-500">*</span></label>
            <InputText v-model="discountType.name" />
        </div>
        <div class="field">
            <label>Iznos rabata<span class="text-red-500">*</span></label>
            <InputNumber
                v-model="discountType.discount"
                autofocus
                :min="1"
                :max="100"
            />
        </div>

        <div class="field">
            <label>Korisnici<span class="text-red-500">*</span></label>
            <MultiSelect
                v-model="discountType.users"
                display="chip"
                :options="users"
                optionLabel="name"
                placeholder="Odaberite korisnika"
                :maxSelectedLabels="3"
                class="w-full"
            >
                <template #option="slotProps">
                    <span>
                        {{ slotProps.option.name }}
                        {{ slotProps.option.last_name }}
                    </span>
                </template>
            </MultiSelect>
        </div>
        <template #footer>
            <Button
                label="Odustani"
                icon="pi pi-times"
                class="p-button-text"
                @click="closeDialog()"
                raised
            />
            <Button
                v-if="
                    discountType.name &&
                    discountType.discount &&
                    discountType.users
                "
                label="Spremi"
                icon="pi pi-check"
                class="p-button-text"
                @click="saveDiscountType()"
                raised
            />
        </template>
    </Dialog>

    <ConfirmDialog group="templating">
        <template #message="slotProps">
            <div
                class="flex flex-column align-items-center w-full gap-3 border-bottom-1 surface-border"
            >
                <i
                    :class="slotProps.message.icon"
                    class="text-5xl text-primary-500"
                ></i>
                <p>{{ slotProps.message.message }}</p>
            </div>
        </template>
    </ConfirmDialog>
</template>

<style scoped></style>
