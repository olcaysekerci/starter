<template>
  <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex items-center h-16 px-6">
    <!-- Sol: Hamburger ve Breadcrumb -->
    <div class="flex items-center flex-1 min-w-0">
      <button @click="$emit('toggle-sidebar')" class="mr-4 p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
      <div class="truncate">
        <slot name="breadcrumb" />
      </div>
    </div>
    <!-- Sağ: Butonlar -->
    <div class="flex items-center gap-2 ml-4">
      <!-- Dark Mode -->
      <slot name="actions">
        <DarkModeToggle />
        <button class="p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01" />
          </svg>
        </button>
        <button class="p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 relative">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 00-6 6v7.5a2.25 2.25 0 002.25 2.25h7.5a2.25 2.25 0 002.25-2.25v-7.5a6 6 0 00-6-6z" />
          </svg>
          <span class="absolute -top-1 -right-1 block h-2 w-2 rounded-full bg-red-500"></span>
        </button>
        <button class="p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 flex items-center">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Kullanıcı</span>
        </button>
      </slot>
    </div>
  </header>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import Dropdown from '@/Components/Shared/Dropdown.vue'
import DropdownLink from '@/Components/Shared/DropdownLink.vue'
import DarkModeToggle from '@/Components/Panel/DarkModeToggle.vue'

defineProps({
  currentPage: {
    type: String,
    default: 'Dashboard'
  }
})

defineEmits(['toggle-sidebar'])

const logout = () => {
  router.post(route('logout'))
}
</script> 