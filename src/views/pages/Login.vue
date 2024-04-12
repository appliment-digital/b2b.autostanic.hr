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
            router.push('/forgot');
        },
    },
};
</script>

<template>
    <h1 class="mb-7 text-center">Auto Stanić</h1>

    <div class="flex flex-column sm:w-4 mx-auto">
        <InputText
            class="login-input mb-3"
            type="text"
            v-model="value"
            placeholder="username"
        />
        <InputText
            class="login-input"
            type="text"
            v-model="value"
            placeholder="password"
        />

        <div class="flex justify-content-between mt-6">
            <Button
                @click="handleForgotPasswordClick"
                label="Forgot password"
                class="login-button underline"
                link
            />
            <Button label="Submit" class="login-button" />
        </div>
    </div>
</template>

<style scoped></style>
