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
            this.user = {};
        },
        openDialog(user) {
            if (user) {
                if (user.discount_types && user.discount_types.length > 0) {
                    user.discount_types.forEach((v) => delete v.pivot);
                }
                if (user.roles && user.roles.length > 0) {
                    user.roles.forEach((v) => delete v.pivot);
                    user.roles = user.roles[0];
                }

                this.user = user;
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
                response.data.data.forEach((v) => delete v.pivot);
                response.data.data.forEach((v) => delete v.users);
                this.discountTypes = response.data.data;
            });
        },
        changeStatus(user) {
            userService.changeStatus(user).then((response) => {
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
        confirmDialog(user) {
            console.log(user.active);
            if (user.active == true) {
                this.$confirm.require({
                    group: 'templating',
                    header: 'Potvrda',
                    message:
                        'Molim vas potvrdite da želite deaktivirati korisnika ' +
                        user.name +
                        ' ' +
                        user.last_name,
                    icon: 'pi pi-exclamation-circle',
                    acceptIcon: 'pi pi-check',
                    rejectIcon: 'pi pi-times',
                    rejectClass: 'p-button-outlined p-button-sm',
                    acceptClass: 'p-button-sm',
                    rejectLabel: 'Odustani',
                    acceptLabel: 'Potvrdi',
                    accept: () => {
                        this.changeStatus(user);
                    },
                    reject: () => {},
                });
            } else if (user.active == false) {
                user.status = 'activate';
                this.changeStatus(user);
            }
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
            v-model="user.roles"
            class="w-full mt-2 mb-3"
            :options="roles"
            optionLabel="name"
            placeholder="Odaberite ulogu"
        >
            <!-- <template #value="slotProps">
                <div v-if="slotProps.value && slotProps.value.name == 'admin'">
                    <div>{{ slotProps.value.name }}</div>
                </div>
                <span v-else>
                    {{ slotProps.placeholder }}
                </span>
            </template>
            <template #option="slotProps">
                <div v-if="slotProps.option.name == 'admin'">Administrator</div>
                <div v-else>Korisnik</div>
            </template> -->
        </Dropdown>

        <label>Tip rabata</label>
        <MultiSelect
            v-model="user.discount_types"
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
                <Column field="name" header="Ime" sortable></Column>
                <Column field="last_name" header="Prezime" sortable></Column>
                <Column field="email" header="E-mail" sortable></Column>
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
                <Column field="roles" header="Uloga" sortable>
                    <template #body="{ data }">
                        <span v-if="data.roles && data.roles.length > 0">
                            <span v-if="data.roles[0].name === 'admin'">
                                Administrator
                            </span>
                            <span v-else> Korisnik </span>
                        </span>
                        <span v-else> Korisnik </span>
                    </template>
                </Column>
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
                    field="payment_method"
                    header="Način plaćanja"
                    sortable
                ></Column>

                <Column field="active" header="Aktivan">
                    <template #body="{ data }">
                        <Checkbox
                            @click="confirmDialog(data)"
                            class="custom-checkbox"
                            v-model="data.active"
                            :binary="true"
                        />
                    </template>
                </Column>
                <Column field="actions" header="Akcije">
                    <template #body="{ data }">
                        <div class="flex">
                            <Button
                                class="mr-2"
                                icon="pi pi-user-edit"
                                aria-label="Edit"
                                @click="openDialog(data)"
                                text
                                raised
                                rounded
                                outlined
                            />
                        </div>
                    </template>
                </Column>
                <template #empty> Još nema dodanih korisnika. </template>
            </DataTable>

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
    </Card>
</template>

<style scoped></style>
