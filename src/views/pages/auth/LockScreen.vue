<script setup>
import AppConfig from '@/layout/AppConfig.vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const navigateToDashboard = () => {
    router.push('/');
};
</script>
<template>
    <div class="surface-0">
        <div class="flex align-items-center justify-content-between flex-column h-screen">
            <div class="flex flex-column align-items-center justify-content-center w-full md:w-4 h-full text-center py-6 px-4">
                <a @click="navigateToDashboard" style="cursor: pointer">
                    <svg height="56" viewBox="0 0 17 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 0H6.00019V3.82345L17 1.66667V6.66667L6.00019 8.82345V10.4901L17 8.33333V13.3333L6.00019 15.4901V20H0V0Z" fill="url(#paint0_linear)" />
                        <defs>
                            <linearGradient id="paint0_linear" x1="3.33335" y1="3.08442e-08" x2="8.49995" y2="20" gradientUnits="userSpaceOnUse">
                                <stop stop-color="var(--primary-400)" />
                                <stop offset="1" stop-color="var(--primary-700)" />
                            </linearGradient>
                        </defs>
                    </svg>
                </a>
                <div class="text-center mt-6" style="margin-bottom: 3.5rem">
                    <div class="text-primary text-xl font-semibold mb-2">Lock Screen</div>
                    <span class="text-500 font-normal mb-5">Please enter your password</span>
                </div>
                <div class="mb-4 w-full md:w-25rem flex gap-4 align-items-center justify-content-between">
                    <div class="text-center flex align-items-center">
                        <img src="@public/demo/images/avatar/amyelsner.png" class="w-3rem h-3rem mb-2" alt="Avatar" />
                        <div class="flex flex-column gap-1 ml-3 text-left">
                            <span class="font-normal text-lg text-900">Amy Elsner</span>
                            <span class="font-normal text-500">Webmaster</span>
                        </div>
                    </div>

                    <Button icon="pi pi-sign-out" label="Signout" text @click="navigateToDashboard"></Button>
                </div>
                <div class="flex flex-column w-full md:w-25rem">
                    <IconField iconPosition="left" class="w-full mb-4">
                        <InputIcon class="pi pi-lock" />
                        <InputText id="password" type="password" class="w-full md:w-25rem text-color-secondary surface-50 border-200" placeholder="Password" />
                    </IconField>
                    <Button label="Unlock" class="w-full" @click="navigateToDashboard"></Button>
                    <span class="font-normal text-500 mt-4">Forgot password? </span>
                </div>
            </div>
            <div class="flex flex-wrap align-items-center pb-8 px-4">
                <h4 class="m-0 mr-5" style="line-height: 22px">freya</h4>
                <h6 class="m-0 font-medium text-300" style="line-height: 17px">Copyright Ⓒ PrimeTek Informatics</h6>
            </div>
        </div>
    </div>

    <AppConfig simple />
</template>
