<script setup>
import { RouterView, useRoute } from 'vue-router'

const route = useRoute()
</script>

<template>
  <div v-if="!['/', '/login'].includes(route.path)">
    <header>
      <img alt="Vue logo" class="logo" src="@/assets/logo.svg" width="125" height="125" />
      <nav>
        <RouterLink to="/">API小工具</RouterLink>
        <RouterLink to="/login">消費者登入</RouterLink>
        
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
