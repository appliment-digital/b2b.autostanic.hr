<template>
    <div class="layout mb-8">
        <div class="absolute top-0 right-0 mr-2">
            <RouterLink to="/home" class="mr-2">Home</RouterLink>
            <RouterLink to="/login" class="mr-2">Login</RouterLink>
            <RouterLink to="/forgot" class="mr-2">Forgot Password</RouterLink>
            <RouterLink to="/reset" class="mr-2">Reset Password</RouterLink>
            <RouterLink to="/admin">Admin</RouterLink>
        </div>
        <Toast></Toast>
        <router-view></router-view>
    </div>
</template>

<style lang="scss">
.layout {
    max-width: 1200px;
    margin: 0 auto;
    margin-top: 2rem;
    border-radius: 4px;
    padding: 1rem;
}
</style>
