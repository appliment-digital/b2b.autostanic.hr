<script>
import router from '@/router';
import Header from '@/components/Header.vue';

export default {
    components: {
        Header,
    },
    data() {
        return {
            home: {
                icon: 'pi pi-home',
            },
            items: [{ label: 'Naslovna' }],
        };
    },
    methods: {
        handleCategoryClick(category) {
            this.$router.push(`/${category}`);
        },
    },
    computed: {},
};
</script>

<template>
    <Header />

    <!-- Home Page: Banner -->
    <div
        class="banner mt-6 surface-300 flex flex-column align-items-center justify-content-center overflow-hidden border-round"
    >
        <h3 class="text-white uppercase text-5xl m-0">B2B Auto Stanić</h3>
        <span class="text-white text-xl mt-2">Sve na jednom mjestu</span>
    </div>

    <!-- Home Page: Search & Categories -->
    <div class="w-full mx-auto grid mt-3 gap-3">
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
            <div class="flex flex-wrap justify-content-center row-gap-4">
                <div
                    class="flex flex-column align-items-center w-11rem cursor-pointer"
                    @click="handleCategoryClick('karoserija')"
                >
                    <img
                        src="/images/car-body.jpeg"
                        class="p-2 border-round mb-1 img--category"
                    />
                    <span class="text-sm">Karoserija</span>
                </div>
                <div
                    class="flex flex-column align-items-center w-11rem cursor-pointer"
                    @click="handleCategoryClick('dijelovi-za-popravak-vozila')"
                >
                    <img
                        src="/images/car-parts.jpeg"
                        class="p-2 border-round mb-1 img--category"
                    />
                    <span class="text-sm text-center">Autodijelovi</span>
                </div>
                <div
                    class="flex flex-column align-items-center w-11rem cursor-pointer"
                    @click="handleCategoryClick('auto-akustika-i-elektronika')"
                >
                    <img
                        src="/images/car-sound-electronics.jpeg"
                        class="p-2 border-round mb-1 img--category"
                    />
                    <span class="text-sm">Auto akustika i elektronika</span>
                </div>
                <div
                    class="flex flex-column align-items-center w-11rem cursor-pointer"
                    @click="handleCategoryClick('sve-za-auto')"
                >
                    <img
                        src="/images/car-all.jpeg"
                        class="p-2 border-round mb-1 img--category"
                    />
                    <span class="text-sm">Sve za auto</span>
                </div>
                <div
                    class="flex flex-column align-items-center w-11rem cursor-pointer"
                    @click="handleCategoryClick('sve-za-radionu')"
                >
                    <img
                        src="/images/car-garage.jpeg"
                        class="p-2 border-round mb-1 img--category"
                    />
                    <span class="text-sm">Sve za radionu</span>
                </div>
                <div
                    class="flex flex-column align-items-center w-11rem cursor-pointer"
                    @click="handleCategoryClick('akcijska-ponuda')"
                >
                    <img
                        src="/images/car-sale.png"
                        class="p-2 border-round mb-1 img--category"
                    />
                    <span class="text-sm">Akcijska ponuda</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Home Page: News -->
    <div class="grid mt-6 row-gap-6 md:column-gap-0 lg:column-gap-3">
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

.category {
    width: 42px;
    height: 42px;
    border: 1px solid lightslategray;
    border-radius: 4px;
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

.img--category {
    width: 100px;
    height: 100px;
}

.button--submit {
    background-color: #123649;
    border: none;
}
</style>
