import { createRouter, createWebHistory } from 'vue-router'
//import HomeView from '../views/HomeView.vue'
import TestApi from '../views/TestApi.vue'
import Products from '../views/Products.vue'
import ApiTester from '../components/ApiTester.vue'
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: ApiTester,
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue'),
    },
    {
      path:'/test-api',
      name:'test-api',
      component:TestApi,
    },
    {
      path:'/products',
      name:'products',
      component:Products,
    },
  ],
})

export default router
