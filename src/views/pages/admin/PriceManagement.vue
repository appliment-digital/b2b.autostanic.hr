<script>
// primevue
import { FilterMatchMode } from 'primevue/api';

// components
import Sidebar from '@/components/admin/Sidebar.vue';

// services
import SupplierDetailService from '@/service/SupplierDetailService.js';
import SupplierService from '@/service/SupplierService.js';
import UserService from '@/service/UserService.js';
import ProductService from '@/service/ProductService.js';
import DeliveryDeadlineService from '@/service/DeliveryDeadlineService.js';
import WarrentService from '@/service/WarrentService.js';

const supplierDetailService = new SupplierDetailService();
const supplierService = new SupplierService();
const userService = new UserService();

export default {
    components: {
        Sidebar,
    },
    created() {
        //set filters for datatable
        this.filters = {
            global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        };
    },
    mounted() {
        this.getSuppliers();
        this.getWarrents();
        this.getDeliveryDeadlines();
    },
    data() {
        return {
            filters: {},
            suppliers: [],
            selectedSuppier: null,
            categories: [],
            selectedCategories: [],
            minPrice: 0,
            maxPrice: 0,
            showDialog: false,
            supplierDetail: {},
            warrents: [],
            deliveryDeadlines: [],
            productsWithCoust: [],
        };
    },
    watch: {
        selectedSuppier() {
            if (this.selectedSuppier) {
                this.getCategoriesForSupplier();
            }
        },
    },
    computed: {},
    methods: {
        getWarrents() {
            WarrentService.getAll().then((response) => {
                this.warrents = response.data.data;
            });
        },
        getDeliveryDeadlines() {
            DeliveryDeadlineService.getAll().then((response) => {
                this.deliveryDeadlines = response.data.data;
            });
        },
        getSuppliers() {
            supplierService.getAll().then((response) => {
                this.suppliers = response.data;
            });
        },
        getCategoriesForSupplier() {
            supplierService
                .getCategoriesForSupplier(this.selectedSuppier.id)
                .then((response) => {
                    this.categories = response.data;
                });
        },
        getProductsBySupplierCategoresAndPriceRange() {
            const categoriesIds = this.selectedCategories.map(
                (category) => category.id,
            );
            ProductService.getProductsBySupplierCategoresAndPriceRange({
                supplierId: this.selectedSuppier.id,
                categoryIds: categoriesIds,
                minPrice: this.minPrice,
                maxPrice: this.maxPrice,
            }).then((response) => {
                this.productsWithCoust = response.data;
            });
        },
        openDialog() {
            this.showDialog = true;
            if (this.minPrice > 0 && this.maxPrice > 0) {
                this.getProductsBySupplierCategoresAndPriceRange();
            }
        },
        closeDialog() {
            this.showDialog = false;
        },
        saveSuppliersDeatels() {
            this.closeDialog();
        },
        saveDetailsforSupplier() {
            const categoriesIds = this.selectedCategories.map(
                (category) => category.id,
            );
            supplierDetailService
                .addDetailsforSupplier({
                    supplierId: this.selectedSuppier.id,
                    categoriesIds: categoriesIds,
                    products: this.productsWithCoust,
                    minPrice: this.minPrice,
                    maxPrice: this.maxPrice,
                    supplierDetail: this.supplierDetail,
                })
                .then((response) => {
                    console.log(response.data);
                });
        },
    },
};
</script>

<template>
    <h3 class="block mb-5">Upravljanje cijenama</h3>

    <Card>
        <template #content>
            <div class="w-full md:w-6 ml-2">
                <div class="field grid">
                    <label class="col-12 md:col-4 mt-2"> Dobaljvač </label>

                    <div class="col-12 md:col-8">
                        <Dropdown
                            v-model="selectedSuppier"
                            :options="suppliers"
                            optionLabel="name"
                            placeholder="Odaberite dobavljača"
                            class="w-full"
                        >
                        </Dropdown>
                    </div>
                </div>
                <div class="field grid">
                    <label class="col-12 md:col-4 mt-2"> Kategorija </label>

                    <div class="col-12 md:col-8">
                        <MultiSelect
                            v-if="selectedSuppier == null"
                            disabled
                            placeholder="Odaberite kategorije"
                            class="w-full"
                        >
                        </MultiSelect>

                        <MultiSelect
                            v-else
                            v-model="selectedCategories"
                            :options="categories"
                            display="chip"
                            optionLabel="name"
                            placeholder="Odaberite kategoriju"
                            class="w-full"
                        >
                        </MultiSelect>
                    </div>
                </div>
                <div class="field grid">
                    <label class="col-12 md:col-4 mt-2">
                        Minimalna cijena
                    </label>

                    <div class="col-12 md:col-8">
                        <InputNumber
                            v-if="this.selectedCategories.length == 0"
                            disabled
                            class="w-full"
                        />
                        <InputNumber
                            v-else
                            v-model="minPrice"
                            autofocus
                            class="w-full"
                        />
                    </div>
                </div>
                <div class="field grid">
                    <label class="col-12 md:col-4 mt-2">
                        Maksimalna cijena
                    </label>

                    <div class="col-12 md:col-8">
                        <InputNumber
                            v-if="this.selectedCategories.length == 0"
                            disabled
                            class="w-full"
                        />
                        <InputNumber
                            v-else
                            v-model="maxPrice"
                            autofocus
                            class="w-full"
                        />
                    </div>
                </div>
                <div class="field grid">
                    <div class="col-12 md:col-4 mt-2"></div>
                    <div class="col-12 md:col-8">
                        <Button
                            v-if="
                                selectedSuppier == null &&
                                selectedCategories.length == 0
                            "
                            disabled
                            class="w-full mb-2"
                            label="Odaberi"
                        />
                        <Button
                            v-else
                            class="w-full mb-2"
                            label="Odaberi"
                            @click="openDialog()"
                        />
                    </div>
                </div>
            </div>

            <DataTable
                v-model:expandedRows="expandedRows"
                :value="discountTypes"
                @rowExpand="onRowExpand"
                @rowCollapse="onRowCollapse"
                :paginator="true"
                :rows="10"
                :rowsPerPageOptions="[10, 20, 50, 100]"
                :filters="filters"
                responsiveLayout="scroll"
            >
                <template #header>
                    <div
                        class="flex flex-column md:flex-row md:justify-content-between md:align-items-center"
                    >
                        <h6>Kategorije i proizvodi s definiranim cijenama</h6>
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
                <Column field="name" header="Dobavljač"></Column>
                <Column field="discount" header="Kategorija"></Column>
                <Column field="users" header="Proizvod"></Column>
                <Column field="users" header="Marža"></Column>
                <Column field="users" header="Nabavna cijena"></Column>
                <Column field="users" header="Rok isporuke"></Column>
                <Column field="users" header="Garancija"></Column>
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
                        </div>
                    </template>
                </Column>

                <template #empty>
                    Još nema definiranih cijena za kategorije/proizvode.
                </template>
            </DataTable>
        </template>
    </Card>

    <Dialog
        v-model:visible="showDialog"
        :style="{ width: '450px' }"
        header="Detalji dobavljača"
        :modal="true"
        class="p-fluid"
    >
        <p class="text-red-500">Polja označena s zvijezdicom(*) su obavezna!</p>
        <div class="field grid">
            <label class="col-3">Naziv:</label>
            <label class="col-9 font-semibold">{{
                this.selectedSuppier.name
            }}</label>
        </div>
        <div class="field grid">
            <label class="col-3">Kategorije:</label>
            <div class="col-9 font-semibold">
                <template
                    v-for="(category, index) in selectedCategories"
                    :key="index"
                >
                    <div class="block">
                        <label>{{ category.name }}</label
                        ><br />
                    </div>
                </template>
            </div>
        </div>

        <div v-if="minPrice > 0 && maxPrice > 0" class="field grid">
            <label class="col-3">Cijene:</label>
            <div class="col-9 font-semibold">
                <label><span>od </span>{{ this.minPrice }} €</label>
                <label><span> do </span>{{ this.maxPrice }} €</label>
            </div>
        </div>

        <div v-if="minPrice > 0 && maxPrice > 0" class="field grid">
            <label class="col-3">Odabrano:</label>
            <div class="col-9 font-semibold">
                <label>{{ this.productsWithCoust.length }} proizvoda</label>
            </div>
        </div>

        <div class="field">
            <label>Marža</label>
            <InputNumber v-model="supplierDetail.markUp" />
        </div>
        <div class="field">
            <label>Transport i ostali troškovi</label>
            <InputNumber v-model="supplierDetail.expenses" />
        </div>
        <div class="field">
            <label>Rok isporuke</label>
            <Dropdown
                v-model="supplierDetail.deliveryDeadline"
                :options="deliveryDeadlines"
                optionLabel="description"
                placeholder="Odaberite rok isporuke"
                class="w-full"
            >
            </Dropdown>
        </div>
        <div class="field">
            <label>Garancija</label>
            <Dropdown
                v-model="supplierDetail.warrent"
                :options="warrents"
                optionLabel="description"
                placeholder="Odaberite garanciju"
                class="w-full"
            >
            </Dropdown>
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
                label="Spremi"
                icon="pi pi-check"
                class="p-button-text"
                @click="saveDetailsforSupplier()"
                raised
            />
        </template>
    </Dialog>
</template>

<style scoped></style>
