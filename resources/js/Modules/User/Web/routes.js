import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/users',
    name: 'users.index',
    component: () => import('./Pages/Index.vue')
  },
  {
    path: '/users/:id',
    name: 'users.show',
    component: () => import('./Pages/Show.vue'),
    props: true
  },
  {
    path: '/profile',
    name: 'users.profile',
    component: () => import('./Pages/Profile.vue')
  }
]

export default routes 