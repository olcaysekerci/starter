import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/panel/users',
    name: 'panel.users.index',
    component: () => import('./Index.vue')
  },
  {
    path: '/panel/users/:id',
    name: 'panel.users.show',
    component: () => import('./Show.vue'),
    props: true
  },
  {
    path: '/panel/users/:id/edit',
    name: 'panel.users.edit',
    component: () => import('./Edit.vue'),
    props: true
  }
]

export default routes 