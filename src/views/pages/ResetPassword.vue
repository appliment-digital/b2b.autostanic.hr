<script>
import router from '@/router';

export default {
    data() {
        return {
            email: null,
            verificationCode: null,
            password: null,
            passwordConfirm: null,
        };
    },
    methods: {
        changeMessage() {
            this.message = 'Vue.js is awesome!';
        },
        forgotPassword() {
            userService
                .forgotPassword({
                    email: this.email,
                    verification_code: this.verificationCode,
                    password: this.password,
                })
                .then((data) => {
                    router.push('/');
                })
                .catch((response) => {});
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
                v-model="email"
                placeholder="email"
            />

            <InputText
                class="block w-full mb-3"
                type="text"
                v-model="verificationCode"
                placeholder="verification code"
            />

            <InputText
                class="block w-full mb-3"
                type="text"
                v-model="password"
                placeholder="new password"
            />

            <InputText
                class="block w-full mb-5"
                type="text"
                v-model="passwordConfirm"
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
