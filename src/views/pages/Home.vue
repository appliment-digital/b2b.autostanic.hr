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
        };
    },
    beforeMount() {
        CategoryService.getMainCategories()
            .then((response) => {
                if (response.data.length) {
                    this.categories = response.data;
                }
            })
            .catch((err) => console.error(err));
    },
    computed: {
        ...mapStores(useCategoryStore),
    },
    methods: {
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
    <div class="w-full mx-auto mt-3 grid gap-3">
        <!-- Search -->
        <div
            class="col-12 py-8 flex flex-column align-items-center justify-content-center surface-100 bg-white border-100 border-round border-1 md:col"
        >
            <div>
                <!-- Checkboxes -->
                <div
                    class="flex flex-column column-gap-3 sm:col sm:flex-row md:flex-column"
                >
                    <div class="flex mb-3 md:mb-3">
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
                    <div class="flex mb-3 md:mb-3">
                        <Checkbox
                            v-model="pizza"
                            inputId="ingredient1"
                            name="pizza"
                            value="Cheese"
                        />
                        <label for="ingredient1" class="ml-2">Broj djela</label>
                    </div>
                    <div class="flex mb-3 md:mb-3">
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
                    <div class="flex">
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
                    class="flex flex-column justify-content-between mt-5 gap-3 sm:flex-row md:flex-column lg:flex-row"
                >
                    <InputText
                        type="text"
                        class="w-20rem sm:w-full md:w-20rem"
                        placeholder="Upišite svoj kod ovdje"
                    />
                    <Button
                        label="pretraži"
                        class="button--submit block w-full sm:w-4 md:w-full lg:w-4"
                        icon="pi pi-search"
                    />
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div
            class="col-12 flex flex-column py-8 surface-100 bg-white border-100 border-round border-1 justify-content-center align-items-center md:col"
        >
            <div
                class="grid justify-content-center row-gap-4 column-gap-3 px-2"
            >
                <!-- prettier-ignore -->
                <div
                    v-for="category in categories"
                    class="col-4 flex flex-column justify-content-center            
                    row-gap-2 p-4
                    align-items-center cursor-pointer 
                    border-round border-1 border-100 hover:shadow-3" 
                    @click="handleCategoryClick(category)"
                >
                    <img :src="category.pictureUrls[0]" style="width:64px" class="block mx-auto" >
                    <span class="text-base text-center">{{ category.name }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Home Page: News -->
    <div
        class="mt-6 grid grid-nogutter row-gap-6 md:column-gap-0 lg:column-gap-3"
    >
        <div class="mx-auto col-12 sm:col-8 md:col">
            <Card style="overflow: hidden" class="my-card cursor-pointer">
                <template #header>
                    <img
                        src="https://source.unsplash.com/random/?Car&1"
                        class="img--placeholder"
                    />
                </template>
                <template #title>News 1</template>
                <template #content>
                    <p>
                        Lorem ipsum dolor sit amet consectetur adipisicing elit.
                        Fuga earum harum non reiciendis ipsum! Voluptate
                        quibusdam molestias consectetur odit dolorum earum, esse
                        nemo inventore neque corporis ea voluptas dolorem culpa!
                    </p>
                </template>
            </Card>
        </div>
        <div class="mx-auto col-12 sm:col-8 md:col">
            <Card style="overflow: hidden" class="my-card cursor-pointer">
                <template #header>
                    <img
                        src="https://source.unsplash.com/random/?Car&2"
                        class="img--placeholder"
                    />
                </template>
                <template #title>News 2</template>
                <template #content>
                    <p>
                        Lorem ipsum dolor sit amet consectetur adipisicing elit.
                        Fuga earum harum non reiciendis ipsum! Voluptate
                        quibusdam molestias consectetur odit dolorum earum, esse
                        nemo inventore neque corporis ea voluptas dolorem culpa!
                    </p>
                </template>
                hello
            </Card>
        </div>
        <div class="mx-auto col-12 sm:col-8 md:col">
            <Card style="overflow: hidden" class="my-card cursor-pointer">
                <template #header>
                    <img
                        src="https://source.unsplash.com/random/?Car&3"
                        class="img--placeholder"
                    />
                </template>
                <template #title>News 3</template>
                <template #content>
                    <p>
                        Lorem ipsum dolor sit amet consectetur adipisicing elit.
                        Fuga earum harum non reiciendis ipsum! Voluptate
                        quibusdam molestias consectetur odit dolorum earum, esse
                        nemo inventore neque corporis ea voluptas dolorem culpa!
                    </p>
                </template>
            </Card>
        </div>
    </div>

    <!-- Home Page: Footer -->
    <footer>
        <div
            class="w-full mx-auto mt-6 p-8 bg-white border-round grid column-gap-2 row-gap-8 justify-content-center align-items-center border-100 border-round border-1 md:column-gap-4"
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

.my-card {
    transition: box-shadow;
    transition-duration: 150ms;
}

.my-card:hover {
    box-shadow: 2px 2px 4px 2px rgba(0, 0, 0, 0.1);
}

.button--submit {
    background-color: #123649;
    border: none;
}
</style>
