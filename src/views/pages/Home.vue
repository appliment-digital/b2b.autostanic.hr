<script>
// utils
import { makeUrl } from '@/utils';

// pinia
import { mapStores } from 'pinia';
import { useCategoryStore } from '@/store/categoryStore.js';

// components
import Header from '@/components/Header.vue';

// services
import CategoryService from '@/service/CategoryService.js';

export default {
    components: {
        Header,
    },
    data() {
        return {
            categories: null,
            isDataLoading: true,
        };
    },
    beforeMount() {
        CategoryService.getMainCategories()
            .then((response) => {

                if (response.data.length) {
                    this.setIsDataLoading(false);

                    this.categories = response.data;

                    // update category name to be shorter for better UX
                    this.categories.forEach((category) => {
                        if (category.id == '39597') {
                            category.name = 'Akustika i elektronika';
                        }
                    });
                }
            })
            .catch((err) => console.error(err));
    },
    computed: {
        ...mapStores(useCategoryStore),
    },
    methods: {
        setIsDataLoading(val) {
            this.isDataLoading = val;
        },

        handleCategoryClick(category) {
            this.categoryStore.addHistory(category);

            this.$router.push({
                path: `/${makeUrl(category.name)}`,
            });
        },
    },
};
</script>

<template>
    <Header />

    <!-- Home Page: Banner -->
    <div
        class="banner mt-3 surface-300 flex flex-column align-items-center justify-content-center overflow-hidden border-round"
    >
        <h3 class="text-white uppercase text-5xl m-0">B2B Auto Stanić</h3>
        <span class="text-white text-xl mt-2">Sve na jednom mjestu</span>
    </div>

    <!-- Home Page: Search & Categories -->
    <div class="w-full mx-auto mt-2 grid gap-2">
        <!-- Search -->
        <div
            class="col-12 pt-7 flex justify-content-center surface-100 bg-white border-100 border-round border-1 md:col"
        >
            <div>
                <h3 class="text-700 mt-0 mb-2 pt-1">Pretraži prema kodu</h3>
                <span class="block text-500 mb-6"
                    >Pretraži auto dijelove koristeći tvoj kod proizvoda za brzu
                    pretragu.</span
                >

                <!-- Checkboxes -->
                <div
                    class="p-0 flex flex-column column-gap-3 sm:col sm:flex-row"
                >
                    <div class="flex mb-3 md:mb-3 align-items-center">
                        <Checkbox
                            v-model="pizza"
                            inputId="ingredient1"
                            name="pizza"
                            value="Cheese"
                        />
                        <label for="ingredient1" class="ml-2"
                            >Broj artikla</label
                        >
                    </div>
                    <div class="flex mb-3 md:mb-3 align-items-center">
                        <Checkbox
                            v-model="pizza"
                            inputId="ingredient1"
                            name="pizza"
                            value="Cheese"
                        />
                        <label for="ingredient1" class="ml-2">Broj djela</label>
                    </div>
                    <div class="flex mb-3 md:mb-3 align-items-center">
                        <Checkbox
                            v-model="pizza"
                            inputId="ingredient1"
                            name="pizza"
                            value="Cheese"
                        />
                        <label for="ingredient1" class="ml-2"
                            >Šifra motora</label
                        >
                    </div>
                    <div class="flex align-items-center md:mb-3">
                        <Checkbox
                            v-model="pizza"
                            inputId="ingredient1"
                            name="pizza"
                            value="Cheese"
                        />
                        <label for="ingredient1" class="ml-2"
                            >Šifra mjenjača</label
                        >
                    </div>
                </div>

                <!-- Input -->
                <div
                    class="flex flex-column justify-content-between mt-2 gap-2 sm:flex-row md:flex-column lg:flex-row"
                >
                    <InputText
                        type="text"
                        class="w-20rem sm:w-full md:w-23rem"
                        placeholder="Upišite svoj kod ovdje"
                    />
                    <Button
                        label="pretraži"
                        class="button--submit block w-full sm:w-4 md:w-full lg:w-4"
                    />
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div
            class="categories py-7 col-12 md:col bg-white border-100 border-round border-1 flex align-items-center justify-content-center"
        >
            <div
                class="grid justify-content-center row-gap-1 column-gap-1 px-2 align-items-start"
            >
                <!-- prettier-ignore -->
                <ProgressSpinner v-if="isDataLoading" class="mx-auto" strokeWidth="2"/>
                <div
                    v-else
                    v-for="category in categories"
                    class="col-4 md:col-4 lg:col-3 flex flex-column justify-content-center            
                    align-items-center cursor-pointer" 
                    @click="handleCategoryClick(category)"
                >
                    <div class="shadow-1 py-3 border-1 border-100 p-4 border-round hover:shadow-3">
                        <img :src="category.pictureUrls[0]" style="width:62px" class="block mx-auto" >
                    </div>
                    <span class="text-sm text-center mt-2">{{ category.name }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Home Page: News -->
    <section class="mt-2 bg-white border-1 border-100 border-round p-8">
        <h3 class="text-700 mt-0 mb-2 text-center">
            Savjeti za Vas i Vaš automobil
        </h3>
        <span class="block text-500 mb-7 text-center"
            >Pročitajte kratke zanimljivosti vezane uz vašeg limenog
            ljubimca.</span
        >

        <div
            class="grid grid-nogutter row-gap-6 md:column-gap-0 lg:column-gap-3"
        >
            <div class="mx-auto col-12 sm:col-8 md:col">
                <Card
                    style="overflow: hidden"
                    class="cursor-pointer border-1 border-100 shadow-2 hover:shadow-5"
                >
                    <template #header>
                        <img
                            src="https://source.unsplash.com/random/?Car&1"
                            class="img--placeholder"
                        />
                    </template>
                    <template #title>News 1</template>
                    <template #content>
                        <p>
                            Lorem ipsum dolor sit amet consectetur adipisicing
                            elit. Fuga earum harum non reiciendis ipsum!
                            Voluptate quibusdam molestias consectetur odit
                            dolorum earum, esse nemo inventore neque corporis ea
                            voluptas dolorem culpa!
                        </p>
                    </template>
                </Card>
            </div>
            <div class="mx-auto col-12 sm:col-8 md:col">
                <Card
                    style="overflow: hidden"
                    class="cursor-pointer border-1 border-100 shadow-2 hover:shadow-5"
                >
                    <template #header>
                        <img
                            src="https://source.unsplash.com/random/?Car&2"
                            class="img--placeholder"
                        />
                    </template>
                    <template #title>News 2</template>
                    <template #content>
                        <p>
                            Lorem ipsum dolor sit amet consectetur adipisicing
                            elit. Fuga earum harum non reiciendis ipsum!
                            Voluptate quibusdam molestias consectetur odit
                            dolorum earum, esse nemo inventore neque corporis ea
                            voluptas dolorem culpa!
                        </p>
                    </template>
                    hello
                </Card>
            </div>
            <div class="mx-auto col-12 sm:col-8 md:col">
                <Card
                    style="overflow: hidden"
                    class="cursor-pointer border-1 border-100 shadow-2 hover:shadow-5"
                >
                    <template #header>
                        <img
                            src="https://source.unsplash.com/random/?Car&3"
                            class="img--placeholder"
                        />
                    </template>
                    <template #title>News 3</template>
                    <template #content>
                        <p>
                            Lorem ipsum dolor sit amet consectetur adipisicing
                            elit. Fuga earum harum non reiciendis ipsum!
                            Voluptate quibusdam molestias consectetur odit
                            dolorum earum, esse nemo inventore neque corporis ea
                            voluptas dolorem culpa!
                        </p>
                    </template>
                </Card>
            </div>
        </div>
    </section>

    <!-- Home Page: Footer -->
    <footer class="mt-3">
        <div
            class="w-full mx-auto p-8 bg-white border-round grid column-gap-2 row-gap-8 justify-content-center align-items-center border-100 border-round border-1 md:column-gap-4"
        >
            <!-- Footer: Company Information -->
            <div class="col-12 md:col-5">
                <span class="block"
                    ><span class="font-bold">Naziv</span>: Auto Stanić
                    d.o.o.</span
                >
                <span class="block mt-2"
                    ><span class="font-bold">Sjedište</span>: Dravska 80c, 42202
                    Trnovec Bartolovečki</span
                >
                <span class="block mt-2"
                    ><span class="font-bold">OIB</span>: 75746481867</span
                >
                <span class="block mt-2"
                    ><span class="font-bold">Direktor</span>: mag. inf. Alen
                    Stanić</span
                >
                <span class="block mt-2"
                    ><span class="font-bold">Nadležni sud</span>: Trgovački sud
                    u Varaždinu</span
                >
                <span class="block mt-2"
                    ><span class="font-bold">Temeljni kapital</span>: 2.654,46
                    EUR uplaćen u cijelosti</span
                >
                <span class="block mt-2"
                    ><span class="font-bold">Transakcijski računi</span>:<br />
                    OTP banka d.d. <br />
                    SWIFT:OTPVHR2X <br />
                    Varaždin. IBAN: HR9824070001100504805
                </span>
            </div>

            <!-- Footer: Contact Us  -->
            <div class="flex-order-1 col-12 md:col-5">
                <div
                    class="flex flex-column justify-content-center align-items-center gap-3 cursor-pointer"
                >
                    <div>
                        <h2>Kontaktiraj nas</h2>
                        <Button
                            icon="pi pi-envelope"
                            label="Pošalji upit"
                            class="button--submit block w-full"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer: Copy Rights -->
        <div class="h-6rem">
            <p class="mt-6 text-center">
                Copyright & copy; 2024 Auto Stanić. Sva prava pridržana. Powered
                by Appliment
            </p>
        </div>
    </footer>
</template>

<style scoped>
.banner {
    width: 100%;
    height: 280px;
    background: url('/images/banner-old.jpeg');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
}

.img--placeholder {
    position: relative;
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.img--banner {
    width: 100%;
    object-fit: cover;
}

.button--submit {
    background-color: #123649;
    border: none;
}

.categories {
    min-height: 390.5px;
}

</style>
