<script setup>
import { RouterView, useRoute } from 'vue-router'

const route = useRoute()
</script>

<template>
  <div v-if="route.path !== '/'">
    <header>
      <img alt="Vue logo" class="logo" src="@/assets/logo.svg" width="125" height="125" />
      <nav>
        <RouterLink to="/">Home</RouterLink>
        <RouterLink to="/about">About</RouterLink>
        <RouterLink to="/test-api">API Tester</RouterLink>
        <RouterLink to="/products">Products</RouterLink>
      </nav>
    </header>
  </div>

  <RouterView />
</template>

<style scoped>
header {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
}

.logo {
  margin-bottom: 1rem;
}

nav {
  display: flex;
  gap: 1rem;
  font-size: 1rem;
}

nav a {
  text-decoration: none;
  color: #007bff;
}

nav a.router-link-exact-active {
  font-weight: bold;
  text-decoration: underline;
}
</style>
