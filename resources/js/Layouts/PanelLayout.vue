<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <Head :title="title" />

    <!-- Sidebar -->
    <Sidebar 
      :is-open="sidebarOpen" 
      @close="sidebarOpen = false" 
    />

    <!-- Ana İçerik Alanı -->
    <div :class="[
      'lg:pl-64 transition-all duration-300',
      sidebarOpen ? 'pl-64' : 'pl-0'
    ]">
      <!-- Top Navigation -->
      <TopNav 
        :current-page="currentPage"
        @toggle-sidebar="toggleSidebar" 
      />

      <!-- Sayfa İçeriği -->
      <main class="p-6">
        <!-- Sayfa Başlığı -->
        <div class="mb-6">
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            {{ pageTitle }}
          </h1>
          <p v-if="pageDescription" class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ pageDescription }}
          </p>
        </div>

        <!-- Slot İçeriği -->
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import Sidebar from '@/Components/Panel/Navigation/Sidebar.vue'
import TopNav from '@/Components/Panel/Header/TopNav.vue'

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

// Desktop'ta sidebar'ı açık tut
onMounted(() => {
  const checkScreenSize = () => {
    if (window.innerWidth >= 1024) { // lg breakpoint
      sidebarOpen.value = true
    } else {
      sidebarOpen.value = false
    }
  }
  
  checkScreenSize()
  window.addEventListener('resize', checkScreenSize)
})

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}
</script> 