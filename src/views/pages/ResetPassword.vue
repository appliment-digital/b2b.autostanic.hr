<script>
import router from '@/router';

export default {
    data() {
        return {};
    },
    methods: {
        changeMessage() {
            this.message = 'Vue.js is awesome!';
        },

        handleForgotPasswordClick() {
            // router.push('/forgot');
            console.log('hi');
        },
    },
};
</script>

<template>
    <h2 class="text-center">Postavi Lozinku</h2>

    <div class="flex flex-column sm:w-3 mx-auto">

        <div class="mt-6">
            <InputText
                class="block w-full mb-3"
                type="text"
                v-model="value"
                placeholder="email"
            />

            <InputText
                class="block w-full mb-3"
                type="text"
                v-model="value"
                placeholder="verification code"
            />

            <InputText
                class="block w-full mb-3"
                type="text"
                v-model="value"
                placeholder="new password"
            />

            <InputText
                class="block w-full mb-5"
                type="text"
                v-model="value"
                placeholder="confirm password"
            />

            <Button
                class="block w-full"
                label="Reset"
                @click="handleResetButtonClick"
            />
        </div>

    </div>
</template>

<style scoped></style>
