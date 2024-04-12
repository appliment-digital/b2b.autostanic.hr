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
    <div class="flex flex-column sm:w-3 mx-auto">
        <h2>Postavi Lozinku</h2>

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
