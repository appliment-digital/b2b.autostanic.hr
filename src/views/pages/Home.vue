<script>
import router from '@/router';
import Header from '@/components/Header.vue';

export default {
    components: {
        Header,
    },
    data() {
        return {};
    },
    methods: {},
    computed: {},
};
</script>

<template>
    <Header />
    <div
        class="banner surface-300 flex align-items-center justify-content-center mt-4 mb-4"
    >
        <span class="text-2xl font-bold">Banner</span>
    </div>

    <!-- Search & Categories -->
    <div class="flex justify-content-between mb-4 gap-4">
        <!-- Search -->
        <div
            class="flex flex-column p-4 surface-100 w-6 bg-white border-100 border-round border-1"
        >
            <!-- Checkboxes -->
            <div class="flex mb-3">
                <Checkbox
                    v-model="pizza"
                    inputId="ingredient1"
                    name="pizza"
                    value="Cheese"
                />
                <label for="ingredient1" class="ml-2">Broj artikla</label>
            </div>
            <div class="flex mb-3">
                <Checkbox
                    v-model="pizza"
                    inputId="ingredient1"
                    name="pizza"
                    value="Cheese"
                />
                <label for="ingredient1" class="ml-2">Broj djela</label>
            </div>
            <div class="flex mb-3">
                <Checkbox
                    v-model="pizza"
                    inputId="ingredient1"
                    name="pizza"
                    value="Cheese"
                />
                <label for="ingredient1" class="ml-2">Šifa motora</label>
            </div>
            <div class="flex">
                <Checkbox
                    v-model="pizza"
                    inputId="ingredient1"
                    name="pizza"
                    value="Cheese"
                />
                <label for="ingredient1" class="ml-2">Šifa mjenjača</label>
            </div>

            <!-- Input -->
            <div class="flex justify-content-between mt-3 gap-3">
                <InputText
                    type="text"
                    class="w-9"
                    placeholder="Upišite svoj kod ovdje"
                />
                <Button label="pretraži" class="w-3" />
            </div>
        </div>

        <!-- Categories -->
        <div
            class="flex flex-column p-4 surface-100 w-6 bg-white border-100
            border-round border-1 justify-content-center align-items-center"
        >
            <div class="flex gap-3">
                <span class="block category"></span>
                <span class="block category"></span>
                <span class="block category"></span>
            </div>
            <div class="flex gap-3 m-3">
                <span class="block category"></span>
                <span class="block category"></span>
                <span class="block category"></span>
            </div>
            <div class="flex gap-3">
                <span class="block category"></span>
                <span class="block category"></span>
                <span class="block category"></span>
            </div>
        </div>
    </div>
</template>

<style scoped>
.banner {
    width: 100%;
    height: 240px;
}

.category {
    width: 42px;
    height: 42px;
    border: 1px solid lightslategray;
    border-radius: 4px;
}
</style>
