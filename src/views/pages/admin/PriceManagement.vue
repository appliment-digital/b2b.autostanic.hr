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
        this.getAllSuppliersWithDetails();
    },
    data() {
        return {
            filters: {},
            suppliers: [],
            selectedSupplier: null,
            categories: [],
            selectedCategory: null,
            minPrice: 0,
            maxPrice: 0,
            showDialog: false,
            supplierDetail: {},
            warrents: [],
            deliveryDeadlines: [],
            productCount: null,
            suppliersWithDetails: [],
            selectedProduct: null,
            categoryName: null,
            productName: null,
            selectedDetailsId: null,
            disabledCategories: [],
            addedPricesForCategories: [],
            errorMessageMin: '',
            errorMessageMax: '',
            buttonDisabled: true,
        };
    },
    watch: {
        selectedSupplier() {
            if (this.selectedSupplier) {
                this.getCategoriesForSupplier();
            }
        },
        selectedCategory() {
            if (this.selectedCategory && this.selectedCategory.id) {
                this.getAddedPriceRange();
            }
        },
    },
    computed: {
        buttonDisabled() {
            if (
                this.selectedSupplier == null ||
                this.selectedCategory == null ||
                this.errorMessageMin != '' ||
                this.errorMessageMax != ''
            ) {
                return true;
            }
            if (this.errorMessageMin != '' || this.errorMessageMax != '') {
                return true;
            }
        },
    },
    methods: {
        validateMinPrices() {
            let isOverlap = this.addedPricesForCategories.some((range) => {
                if (
                    this.minPrice >= parseFloat(range.min_product_cost) &&
                    this.minPrice <= parseFloat(range.max_product_cost)
                ) {
                    return true;
                }
            });

            if (isOverlap) {
                this.errorMessageMin =
                    'Za navedenu minimalnu cijenu je već definirano';
            } else {
                this.errorMessageMin = '';
            }
        },
        validateMaxPrices() {
            let isOverlap = this.addedPricesForCategories.some((range) => {
                if (
                    this.maxPrice >= parseFloat(range.min_product_cost) &&
                    this.maxPrice <= parseFloat(range.max_product_cost)
                ) {
                    return true;
                }
            });

            if (isOverlap) {
                this.errorMessageMax =
                    'Za navedenu maximalnu cijenu je već definirano';
            } else {
                this.errorMessageMax = '';
            }
        },
        resetInputs() {
            this.selectedSupplier =
                this.selectedProduct =
                this.productName =
                    null;
            this.selectedCategory = null;
            this.minPrice = this.maxPrice = 0;
            this.supplierDetail = {};
            this.categoryName = null;
            this.addedPricesForCategories = [];
            this.errorMessageMin = this.errorMessageMax = '';
        },
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
                .getCategoriesForSupplier(this.selectedSupplier.id)
                .then((response) => {
                    this.categories = response.data;
                    this.categories.push({
                        id: null,
                        name: 'PROIZVODI BEZ KATEGORIJE',
                    });
                });
        },
        getCategoryName(categoryId) {
            return supplierDetailService
                .getCategoryName(categoryId)
                .then((response) => {
                    this.showDialog = true;
                    return response.data;
                });
        },
        getAddedPriceRange() {
            supplierDetailService
                .getAddedPriceRange({
                    supplierId: this.selectedSupplier.id,
                    categoryId: this.selectedCategory,
                })
                .then((response) => {
                    this.addedPricesForCategories = response.data;
                });
        },
        async openDialog(data) {
            if (data) {
                this.selectedDetailsId = data.id;
                this.getCategoryName(data.web_db_category_id);

                this.selectedSupplier = this.suppliers.find(
                    (s) => s.id == data.web_db_supplier_id,
                );
                if (data.web_db_category_id) {
                    this.categoryName = await this.getCategoryName(
                        data.web_db_category_id,
                    );
                }

                this.supplierDetail.markUp = data.mark_up;
                this.supplierDetail.min_product_cost =
                    data.min_product_cost ?? null;
                this.supplierDetail.max_product_cost =
                    data.max_product_cost ?? null;
                this.supplierDetail.expenses = data.expenses ?? null;
                this.supplierDetail.warrent = this.warrents.find(
                    (w) => w.id == data.warrent_id,
                );
                this.supplierDetail.deliveryDeadline =
                    this.deliveryDeadlines.find(
                        (dd) => dd.id == data.delivery_deadline_id,
                    );
            } else {
                this.showDialog = true;
                if (this.maxPrice > 0) {
                    this.getProductsBySupplierCategoresAndPriceRange();
                }
            }
        },
        closeDialog() {
            this.showDialog = false;
            this.resetInputs();
        },
        getAllSuppliersWithDetails() {
            supplierDetailService
                .getAllSuppliersWithDetails()
                .then((response) => {
                    this.suppliersWithDetails = response.data;
                });
        },
        getProductsBySupplierCategoresAndPriceRange() {
            ProductService.getProductsBySupplierCategoresAndPriceRange({
                supplierId: this.selectedSupplier.id,
                categoryId: this.selectedCategory,
                minPrice: this.minPrice,
                maxPrice: this.maxPrice,
            }).then((response) => {
                this.productCount = response.data;
            });
        },
        saveDetailsforSupplier() {
            if (this.selectedDetailsId) {
                supplierDetailService
                    .updateDetailsforSupplier(
                        this.selectedDetailsId,
                        this.supplierDetail,
                    )
                    .then((response) => {
                        if (response.data.success) {
                            this.$toast.add({
                                severity: 'success',
                                summary: 'Uspješno',
                                detail: response.data.message,
                                life: 3000,
                            });
                            this.getAllSuppliersWithDetails();
                            this.closeDialog();
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
                supplierDetailService
                    .addDetailsforSupplier({
                        supplierId: this.selectedSupplier.id,
                        categoryId: this.selectedCategory.id,
                        minProductCost: this.minPrice,
                        maxProductCost: this.maxPrice,
                        supplierDetail: this.supplierDetail,
                    })
                    .then((response) => {
                        if (response.data.success) {
                            this.$toast.add({
                                severity: 'success',
                                summary: 'Uspješno',
                                detail: response.data.message,
                                life: 3000,
                            });
                            this.getAllSuppliersWithDetails();
                            this.closeDialog();
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
                            v-model="selectedSupplier"
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
                        <Dropdown
                            v-if="selectedSupplier == null"
                            disabled
                            placeholder="Odaberite kategoriju"
                            class="w-full"
                        >
                        </Dropdown>
                        <Dropdown
                            v-else
                            v-model="selectedCategory"
                            :options="categories"
                            filter
                            display="chip"
                            optionLabel="name"
                            placeholder="Odaberite kategoriju"
                            class="w-full"
                        >
                            <template #option="slotProps">
                                <div
                                    v-if="
                                        slotProps.option.name ==
                                        'PROIZVODI BEZ KATEGORIJE'
                                    "
                                    class="font-bold"
                                >
                                    {{ slotProps.option.name }}
                                </div>
                                <div v-else>
                                    {{ slotProps.option.name }}
                                </div>
                            </template>
                        </Dropdown>
                    </div>
                </div>
                <div
                    v-if="
                        Array.isArray(this.addedPricesForCategories) &&
                        this.addedPricesForCategories.length > 0
                    "
                    class="field grid"
                >
                    <label class="col-12 md:col-4 mt-2">
                        Definirano za cijene
                    </label>
                    <div class="col-12 md:col-8">
                        <div class="flex flex-wrap">
                            <div
                                v-for="(
                                    detail, index
                                ) in addedPricesForCategories"
                                :key="detail.id"
                            >
                                <span class="mx-1">
                                    {{ detail.min_product_cost }} € -
                                    {{ detail.max_product_cost }} €
                                </span>
                                <span
                                    v-if="
                                        index <
                                        addedPricesForCategories.length - 1
                                    "
                                    class="font-bold"
                                    >|
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field grid">
                    <label class="col-12 md:col-4 mt-2">
                        Minimalna cijena
                    </label>
                    <div class="col-12 md:col-8">
                        <InputNumber
                            v-if="selectedCategory"
                            v-model="minPrice"
                            @blur="validateMinPrices()"
                            autofocus
                            class="w-full"
                            mode="currency"
                            currency="EUR"
                            locale="de-DE"
                        />
                        <InputNumber v-else disabled class="w-full" />
                        <div
                            v-if="errorMessageMin"
                            class="mt-1 text-red-500 text-xs"
                        >
                            {{ errorMessageMin }}
                        </div>
                    </div>
                </div>
                <div class="field grid">
                    <label class="col-12 md:col-4 mt-2">
                        Maksimalna cijena
                    </label>
                    <div class="col-12 md:col-8">
                        <InputNumber
                            v-if="selectedCategory"
                            v-model="maxPrice"
                            @blur="validateMaxPrices()"
                            autofocus
                            class="w-full"
                            mode="currency"
                            currency="EUR"
                            locale="de-DE"
                        />
                        <InputNumber v-else disabled class="w-full" />
                        <div
                            v-if="errorMessageMax"
                            class="mt-1 text-red-500 text-xs"
                        >
                            {{ errorMessageMax }}
                        </div>
                    </div>
                </div>

                <div class="field grid">
                    <div class="col-12 md:col-4 mt-2"></div>
                    <div class="col-12 md:col-8">
                        <Button
                            v-if="buttonDisabled"
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
                :value="suppliersWithDetails"
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
                        <h6>Dobavljači s definiranim cijenama</h6>
                        <div class="flex">
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
                    </div>
                </template>
                <Column
                    field="supplier_name"
                    header="Dobavljač"
                    sortable
                ></Column>
                <Column field="category_name" header="Kategorija" sortable>
                    <template #body="{ data }">
                        <span v-if="data.category_name">{{
                            data.category_name
                        }}</span>

                        <span v-else> - </span>
                    </template>
                </Column>
                <Column header="Nabavna cijena" sortable>
                    <template #body="{ data }">
                        <span v-if="data.min_product_cost"
                            >{{ data.min_product_cost }} -
                            {{ data.max_product_cost }} €</span
                        >

                        <span v-else> - </span>
                    </template>
                </Column>
                <Column field="mark_up" header="Marža" sortable></Column>
                <Column field="expenses" header="Troškovi">
                    <template #body="{ data }">
                        <span v-if="data.expenses">{{ data.expenses }}</span>

                        <span v-else> - </span>
                    </template>
                </Column>
                <Column
                    field="delivery_deadline_name"
                    header="Rok isporuke"
                ></Column>
                <Column field="warrent_name" header="Garancija"></Column>
                <Column field="actions" header="Akcije">
                    <template #body="slotProps">
                        <div class="flex">
                            <Button
                                class="mr-2"
                                icon="pi pi-pencil"
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
        dismissableMask
        closeOnEscape
        class="p-fluid"
    >
        <p class="text-red-500">Polja označena s zvijezdicom(*) su obavezna!</p>
        <div class="field grid">
            <label class="col-3">Naziv:</label>
            <label class="col-9 font-semibold">{{
                this.selectedSupplier.name
            }}</label>
        </div>
        <div class="field grid">
            <label class="col-3">Kategorija:</label>
            <div v-if="categoryName" class="col-9 font-semibold">
                <div class="block">
                    <label>{{ categoryName }}</label>
                </div>
            </div>
            <div v-if="!categoryName" class="col-9 font-semibold">
                PROIZVODI BEZ KATEGORIJE
            </div>
        </div>

        <div v-if="productName" class="field grid">
            <label class="col-3">Proizvod:</label>
            <div class="col-9 font-semibold">
                <div class="block">
                    <label>{{ productName }}</label>
                </div>
            </div>
        </div>

        <div v-if="maxPrice > 0" class="field grid">
            <label class="col-3">Cijene:</label>
            <div class="col-9 font-semibold">
                <label><span>od </span>{{ this.minPrice }} €</label>
                <label><span> do </span>{{ this.maxPrice }} €</label>
            </div>
        </div>

        <div v-if="maxPrice > 0" class="field grid">
            <label class="col-3">Odabrano:</label>
            <div class="col-9 font-semibold">
                <label>{{ this.productCount }} proizvod/a</label>
            </div>
        </div>

        <div v-if="productName" class="field grid">
            <label class="col-3">Cijena:</label>
            <div class="col-9 font-semibold">
                <div class="block">
                    <label>{{ supplierDetail.min_product_cost }}</label>
                </div>
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
