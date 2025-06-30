<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <Head :title="title" />
    <Sidebar :is-open="sidebarOpen" @close="sidebarOpen = false" />
    <div :class="['lg:pl-64 transition-all duration-300', sidebarOpen ? 'pl-64' : 'pl-0']">
      <TopNav :current-page="currentPage" @toggle-sidebar="toggleSidebar">
        <template #breadcrumb>
          <slot name="breadcrumb" />
        </template>
        <template #actions>
          <slot name="topActions" />
        </template>
      </TopNav>
      <main class="p-6">
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
  title: String,
  currentPage: { type: String, default: '' }
})
const sidebarOpen = ref(false)
onMounted(() => {
  const checkScreenSize = () => {
    if (window.innerWidth >= 1024) sidebarOpen.value = true
    else sidebarOpen.value = false
  }
  checkScreenSize()
  window.addEventListener('resize', checkScreenSize)
})
const toggleSidebar = () => { sidebarOpen.value = !sidebarOpen.value }
</script> 