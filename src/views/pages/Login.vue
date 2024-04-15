<script>
import router from '@/router';
import UserService from "../../service/UserService.js";

const userService = new UserService();
export default {
    data() {
        return {
            email: null,
            password: null
        };
    },
    methods: {
        changeMessage() {
            this.message = 'Vue.js is awesome!';
        },

        handleForgotPasswordClick() {
            router.push('/forgot');
        },
        login() {
            userService.login(this.email, this.password).then((data) => {
                if (data && data.success) {
                    //redirect to dashboard
                    this.$router.push("/admin");
                } else {
                    this.$toast.add({
                        severity: "error",
                        summary: "Error",
                        detail: "Login failed",
                        life: 3000,
                    });
                }
            }).catch((response) => {
                this.$toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: "Login failed",
                    life: 3000,
                });
            });
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
            v-model="email"
            placeholder="E-mail"
        />
        <InputText
            class="login-input"
            type="text"
            v-model="password"
            placeholder="Password"
        />

        <div class="flex justify-content-between mt-6">
            <Button
                @click="handleForgotPasswordClick"
                label="Forgot password"
                class="login-button underline"
                link
            />
            <Button @click="login()" label="Submit" class="login-button" />
        </div>
    </div>
</template>

<style scoped></style>
