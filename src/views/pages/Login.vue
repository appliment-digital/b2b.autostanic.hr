<script>
import router from '@/router';
import UserService from '../../service/UserService.js';

const userService = new UserService();
export default {
    data() {
        return {
            email: null,
            password: null,
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
            userService
                .login(this.email, this.password)
                .then((data) => {
                    if (data && data.success) {
                        // redirect to dashboard
                        this.$router.push('/admin');
                    } else {
                        this.$toast.add({
                            severity: 'error',
                            summary: 'Error',
                            detail: 'Login failed',
                            life: 3000,
                        });
                    }
                })
                .catch((response) => {
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'Login failed',
                        life: 3000,
                    });
                });
        },
    },
};
</script>

<template>
    <div
        class="login-screen flex flex-column justify-content-center mx-auto w-10 sm:w-7 md:w-4 lg:w-3"
    >
        <img
            src="images/as-logo.png"
            class="logo mx-auto mb-6"
            style="height: 48px"
        />

        <InputText
            class="w-full mb-3"
            type="text"
            v-model="email"
            placeholder="E-mail"
        />
        <Password
            class="w-full"
            inputClass="w-full"
            placeholder="Password"
            :feedback="false"
            v-model="password"
        />

        <div class="flex justify-content-between mt-6 w-full">
            <a
                @click="handleForgotPasswordClick"
                label=""
                class="underline text-300 flex align-items-center cursor-pointer"
            >Zaboravljena lozinka?</a>
            <Button @click="login()" label="Submit" class="button--submit" />
        </div>
    </div>
</template>

<style scoped>
.login-screen {
    height: 70vh;
}
.button--submit {
    background-color: #123649;
    border: none;
}
</style>
