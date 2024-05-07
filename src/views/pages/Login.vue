<script>
// vue-router
import router from '@/router';

// pinia
import { mapStores } from 'pinia';
import { useUserStore } from '@/store/userStore.js';

// services
import UserService from '../../service/UserService.js';

const userService = new UserService();

export default {
    data() {
        return {
            email: null,
            password: null,
        };
    },
    computed: {
        ...mapStores(useUserStore),
    },
    methods: {
        handleForgotPasswordClick() {
            router.push('/forgot');
        },
        login() {
            userService
                .login(this.email, this.password)
                .then((data) => {
                    if (data.error) {
                        this.$toast.add({
                            severity: 'error',
                            summary: 'Error',
                            detail: data.error,
                            life: 3000,
                        });
                    }
                    if (data.success) {
                        this.$toast.add({
                            severity: 'success',
                            summary: 'Uspješna prijava',
                            detail:
                                data.message +
                                ', ' +
                                data.data.name +
                                ' ' +
                                data.data.last_name,
                            life: 3000,
                        });
                        const { data: userData } = data;
                        this.userStore.addUser(userData);
                        this.userStore.login();
                        this.$router.push('/');
                    }
                })
                .catch((error) => {
                    console.log({ error });
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Greška',
                        detail: error.data.error,
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
            placeholder="Lozinka"
            :feedback="false"
            v-model="password"
        />

        <div class="flex justify-content-between mt-6 w-full">
            <a
                @click="handleForgotPasswordClick"
                label=""
                class="underline text-300 flex align-items-center cursor-pointer"
                >Zaboravljena lozinka?</a
            >
            <Button
                @click="login()"
                label="Prijavi se"
                class="button--submit"
            />
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
