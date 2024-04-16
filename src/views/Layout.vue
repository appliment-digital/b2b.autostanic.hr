<template>
    <div class="p-2">
        <!-- <RouterLink to="/" class="mr-2">Home</RouterLink>
      <RouterLink to="/about" class="mr-2">About</RouterLink> -->
        <RouterLink to="/home" class="mr-2">Home</RouterLink>
        <RouterLink to="/login" class="mr-2">Login</RouterLink>
        <RouterLink to="/forgot" class="mr-2">Forgot Password</RouterLink>
        <RouterLink to="/reset" class="mr-2">Reset Password</RouterLink>
        <RouterLink to="/admin">Admin</RouterLink>
    </div>

    <div class="layout mb-10">
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
}
</style>
