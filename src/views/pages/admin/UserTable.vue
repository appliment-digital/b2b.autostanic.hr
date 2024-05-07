<script>
// primevue
import { FilterMatchMode } from 'primevue/api';

// components
import Sidebar from '@/components/admin/Sidebar.vue';

// services
import UserService from '@/service/UserService.js';
import DiscountTypeService from '@/service/DiscountTypeService.js';

const userService = new UserService();
const discountTypeService = new DiscountTypeService();

export default {
    components: {
        Sidebar,
    },
    created() {
        // set filters for datatable
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
            paymentMethods: [
                {
                    id: 1,
                    name: 'Nepoznato',
                },
                {
                    id: 2,
                    name: 'Transakcijski račun',
                },
                {
                    id: 3,
                    name: 'Gotovina (novčanice)',
                },
                {
                    id: 4,
                    name: 'Kartica - MasterCard',
                },
                {
                    id: 5,
                    name: 'Kartica - Visa',
                },
                {
                    id: 6,
                    name: 'Kartica - Amex',
                },
                {
                    id: 7,
                    name: 'Kartica - Diners',
                },
                {
                    id: 8,
                    name: 'Kartica - Maestro',
                },
                {
                    id: 9,
                    name: 'Kartica',
                },
                {
                    id: 10,
                    name: 'Plaćanje pouzećem (kurirska služba)',
                },
                {
                    id: 11,
                    name: 'Kompenzacija',
                },
            ],
        };
    },
    computed: {
        showSaveButton() {
            if (
                this.user.name &&
                this.user.last_name &&
                this.user.email &&
                this.user.roles &&
                this.user.bitrix_company_id
            ) {
                return true;
            } else {
                return false;
            }
        },
    },
    methods: {
        closeDialog() {
            this.getAll();
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
            userService.getAllWithRelations().then((response) => {
                this.users = response.data.data;
                this.users.forEach(
                    (user) =>
                        (user.payment_method = this.paymentMethods.find(
                            (p) => p.name == user.payment_method,
                        )),
                );
            });
        },
        getDiscountTypes() {
            discountTypeService.getAll().then((response) => {
                response.data.data.forEach((v) => delete v.pivot);
                response.data.data.forEach((v) => delete v.users);
                this.discountTypes = response.data.data;
            });
        },
        changeStatus(user, active) {
            userService
                .changeStatus({ id: user.id, active: active })
                .then((response) => {
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
                        // deaktiviranje korisnika
                        this.changeStatus(user, 0);
                    },
                    reject: () => {
                        this.getAll();
                    },
                    onHide: () => {
                        this.getAll();
                    },
                });
            } else if (user.active == false) {
                // aktiviranje korisnika
                this.changeStatus(user, 1);
            }
        },
    },
};
</script>

<template>
    <h3 class="block mb-5">Upravljanje korisnicima</h3>

    <Dialog
        modal
        dismissableMask
        closeOnEscape
        header="Detalji korisnika"
        :visible="isDialogVisible"
        :style="{ width: '30rem' }"
        @update:visible="closeDialog"
    >
        <p class="text-red-500">Polja označena s zvijezdicom(*) su obavezna!</p>
        <!-- Dialog Input: First Name -->
        <label>Ime<span class="text-red-500">*</span></label>
        <InputText v-model="user.name" class="w-full mt-2 mb-3" />

        <!-- Dialog Input: Last Name -->
        <label>Prezime<span class="text-red-500">*</span></label>
        <InputText v-model="user.last_name" class="w-full mt-2 mb-3" />

        <!-- Dialog Input: E-mai -->
        <label>E-mail<span class="text-red-500">*</span></label>
        <InputText v-model="user.email" class="w-full mt-2 mb-3" />

        <label>Uloga<span class="text-red-500">*</span></label>
        <Dropdown
            v-model="user.roles"
            class="w-full mt-2 mb-3"
            :options="roles"
            optionLabel="name"
            placeholder="Odaberite ulogu"
        >
            <template #value="slotProps">
                <div v-if="slotProps.value && slotProps.value.name == 'admin'">
                    Administrator
                </div>
                <div
                    v-if="slotProps.value && slotProps.value.name == 'customer'"
                >
                    Korisnik
                </div>
                <div v-if="slotProps.value && slotProps.value.name == ''">
                    {{ slotProps.placeholder }}
                </div>
            </template>
            <template #option="slotProps">
                <div v-if="slotProps.option.name == 'admin'">Administrator</div>
                <div v-else>Korisnik</div>
            </template>
        </Dropdown>

        <label>Tip rabata</label>
        <Dropdown
            v-model="user.discount_type"
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
        </Dropdown>

        <!-- Dialog Input: Bitrix Company ID -->
        <label>Bitrix Company ID<span class="text-red-500">*</span></label>
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
        <Dropdown
            :options="paymentMethods"
            v-model="user.payment_method"
            placeholder="Način plaćanja"
            optionLabel="name"
            :showClear="ture"
            class="w-full mt-2 mb-3"
        ></Dropdown>

        <div class="flex justify-content-end">
            <Button
                class="block mt-5 mr-2"
                severity="danger"
                label="Odustani"
                style="min-width: 100px"
                @click="closeDialog()"
            />
            <Button
                v-if="showSaveButton"
                class="block mt-5"
                severity="success"
                label="Spremi"
                style="min-width: 100px"
                @click="saveUser()"
            />

            <Button
                v-else
                class="block mt-5"
                severity="success"
                label="Spremi"
                style="min-width: 100px"
                disabled
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
                :rows="10"
                :rowsPerPageOptions="[10, 20, 50, 100]"
                :selection="selectedUser"
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
                <Column field="discount_type" header="Tip rabata" sortable>
                    <template #body="{ data }">
                        <span v-if="data.discount_type_id">{{
                            data.discount_type.name
                        }}</span>
                        <span v-else> - </span>
                    </template>
                </Column>
                <Column field="roles" header="Uloga" sortable>
                    <template #body="{ data }">
                        <span v-if="data.roles && data.roles.length > 0">
                            <span v-if="data.roles[0].name === 'admin'">
                                Administrator
                            </span>
                            <span v-if="data.roles[0].name === 'customer'">
                                Korisnik
                            </span>
                        </span>
                        <span v-else> - </span>
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
                <Column field="payment_method" header="Način plaćanja" sortable
                    ><template #body="{ data }">
                        <span v-if="data.payment_method">{{
                            data.payment_method.name
                        }}</span>
                        <span v-else> - </span>
                    </template>
                </Column>

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
