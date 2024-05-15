<script>
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
            this.$router.push('/auth/forgot-password');
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
    <section>
        <img
            src="/images/as-logo.png"
            class="block mx-auto mb-6"
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
            @keyup.enter="login()"
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
                style="background-color: #123649; border: none"
            />
        </div>
    </section>
</template>
