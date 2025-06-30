import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/users',
    name: 'users.index',
    component: () => import('./Index.vue')
  },
  {
    path: '/users/:id',
    name: 'users.show',
    component: () => import('./Show.vue'),
    props: true
  },
  {
    path: '/profile',
    name: 'users.profile',
    component: () => import('./Profile.vue')
  }
]

export default routes 