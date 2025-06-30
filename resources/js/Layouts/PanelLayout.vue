<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex">
    <Head :title="title" />

    <!-- Sidebar -->
    <Sidebar :is-open="sidebarOpen" :is-collapsed="isCollapsed" @close="sidebarOpen = false" />
    <!-- Sağ İçerik Alanı -->
    <div class="flex-1 flex flex-col">
      <!-- Top Navigation -->
      <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex items-center h-16 px-6">
        <div class="flex items-center flex-1 min-w-0">
          <button @click="toggleSidebar" class="mr-4 p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <div class="truncate">
            <slot name="breadcrumb" />
          </div>
        </div>
        <div class="flex items-center gap-4 ml-4">
          <button class="action-btn">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1112.21 3a7 7 0 008.79 9.79z" />
            </svg>
          </button>
          <button class="action-btn">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01" />
            </svg>
          </button>
          <button class="action-btn relative">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 00-6 6v7.5a2.25 2.25 0 002.25 2.25h7.5a2.25 2.25 0 002.25-2.25v-7.5a6 6 0 00-6-6z" />
            </svg>
            <span class="absolute -top-2 -right-2 flex items-center justify-center h-5 w-5 rounded-full bg-red-500 text-white text-xs font-bold border-2 border-white">3</span>
          </button>
          <button class="action-btn flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Kullanıcı</span>
          </button>
        </div>
      </header>
      <!-- Sayfa İçeriği -->
      <main class="p-6 flex-1">
        <div class="mb-6">
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            {{ pageTitle }}
          </h1>
          <p v-if="pageDescription" class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ pageDescription }}
          </p>
        </div>
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import Sidebar from '@/Components/Panel/Navigation/Sidebar.vue'
import DarkModeToggle from '@/Components/Panel/DarkModeToggle.vue'

defineProps({
  title: {
    type: String,
    required: true
  },
  pageTitle: {
    type: String,
    required: true
  },
  pageDescription: {
    type: String,
    default: ''
  },
  currentPage: {
    type: String,
    default: 'Dashboard'
  }
})

const sidebarOpen = ref(false)
const isCollapsed = ref(false)

onMounted(() => {
  const checkScreenSize = () => {
    if (window.innerWidth >= 1024) {
      sidebarOpen.value = true
    } else {
      sidebarOpen.value = false
    }
  }
  checkScreenSize()
  window.addEventListener('resize', checkScreenSize)
})

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value
}
</script>

<style scoped>
.action-btn {
  @apply w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition;
  box-sizing: border-box;
  padding: 0;
}
</style> 