<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex">
    <Head :title="title" />

    <!-- Sidebar -->
    <Sidebar :is-open="sidebarOpen" :is-collapsed="isCollapsed" @close="sidebarOpen = false" />
    
    <!-- Sağ İçerik Alanı -->
    <div :class="[
      'flex-1 flex flex-col min-w-0 bg-gray-50 dark:bg-gray-900 w-full transition-all duration-300',
      isCollapsed ? 'ml-20' : 'ml-64'
    ]">
      <!-- Top Navigation -->
      <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
        <!-- Left side -->
        <div class="flex items-center space-x-3 sm:space-x-4">
          <!-- Toggle Button -->
          <button
              @click="toggleSidebar"
              class="p-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-600 lg:border-0 ml-1 lg:ml-0"
          >
              <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
          </button>
          
          <!-- Breadcrumb - Hidden on mobile -->
          <div class="hidden sm:block">
              <Breadcrumb :items="breadcrumbs" />
          </div>
        </div>

        <!-- Right side -->
        <div class="flex items-center space-x-2 sm:space-x-3">
          <!-- Dark Mode Toggle -->
          <DarkModeToggle />
          
          <!-- Quick Actions - Hidden on mobile -->
          <div class="relative hidden sm:block" @click.stop>
              <button 
                  @click="showNotifications = false; showUserMenu = false; showQuickActions = !showQuickActions"
                  class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-600"
              >
                  <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                  </svg>
              </button>
              
              <!-- Quick Actions Dropdown -->
              <div v-if="showQuickActions" class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                  <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                      <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Hızlı İşlemler</h3>
                  </div>
                  <div class="p-3">
                      <div class="grid grid-cols-2 gap-2">
                          <Link 
                              :href="route('panel.users.index')"
                              class="flex flex-col items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group"
                          >
                              <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 mb-1">
                                  <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                      <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0z"/>
                                  </svg>
                              </div>
                              <span class="text-xs font-medium text-gray-900 dark:text-gray-100">Kullanıcılar</span>
                          </Link>
                          
                          <button class="flex flex-col items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-green-300 dark:hover:border-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors group">
                              <div class="w-8 h-8 bg-green-100 dark:bg-green-900/40 rounded-lg flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-800/50 mb-1">
                                  <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                  </svg>
                              </div>
                              <span class="text-xs font-medium text-gray-900 dark:text-gray-100">Yeni Ekle</span>
                          </button>
                          
                          <button class="flex flex-col items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-yellow-300 dark:hover:border-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors group">
                              <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/40 rounded-lg flex items-center justify-center group-hover:bg-yellow-200 dark:group-hover:bg-yellow-800/50 mb-1">
                                  <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                  </svg>
                              </div>
                              <span class="text-xs font-medium text-gray-900 dark:text-gray-100">Raporlar</span>
                          </button>
                          
                          <button class="flex flex-col items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-purple-300 dark:hover:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors group">
                              <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/40 rounded-lg flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-800/50 mb-1">
                                  <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                  </svg>
                              </div>
                              <span class="text-xs font-medium text-gray-900 dark:text-gray-100">Ayarlar</span>
                          </button>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Notifications -->
          <div class="relative" @click.stop>
              <button 
                  @click="showQuickActions = false; showUserMenu = false; showNotifications = !showNotifications"
                  class="relative p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-600"
              >
                  <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                  </svg>
                  <span class="absolute -top-0.5 -right-0.5 h-3 w-3 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">3</span>
              </button>
              
              <!-- Notifications Dropdown -->
              <div v-if="showNotifications" class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                  <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                      <div class="flex items-center justify-between">
                          <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Bildirimler</h3>
                          <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full">3 yeni</span>
                      </div>
                  </div>
                  <div class="max-h-64 overflow-y-auto">
                      <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 flex items-start space-x-3">
                          <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                              <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                  <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                              </svg>
                          </div>
                          <div class="flex-1 min-w-0">
                              <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">Yeni kullanıcı kaydı</p>
                              <p class="text-xs text-gray-500 dark:text-gray-400 truncate">ahmet@example.com - 5 dakika önce</p>
                          </div>
                      </div>
                      <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 flex items-start space-x-3">
                          <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                              <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                  <path d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1z"/>
                              </svg>
                          </div>
                          <div class="flex-1 min-w-0">
                              <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">Sistem güncellemesi</p>
                              <p class="text-xs text-gray-500 dark:text-gray-400 truncate">v2.1.0 başarıyla yüklendi - 1 saat önce</p>
                          </div>
                      </div>
                      <div class="p-4 hover:bg-gray-50 flex items-start space-x-3">
                          <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                              <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                              </svg>
                          </div>
                          <div class="flex-1 min-w-0">
                              <p class="text-sm font-medium text-gray-900 truncate">Güvenlik uyarısı</p>
                              <p class="text-xs text-gray-500 truncate">Şüpheli giriş denemesi - 2 saat önce</p>
                          </div>
                      </div>
                  </div>
                  <div class="p-4 border-t border-gray-200">
                      <button class="text-sm text-blue-600 hover:text-blue-700 font-medium w-full text-center">
                          Tümünü görüntüle →
                      </button>
                  </div>
              </div>
          </div>

          <!-- User Dropdown -->
          <div class="relative" @click.stop>
              <button 
                  @click="showQuickActions = false; showNotifications = false; showUserMenu = !showUserMenu"
                  class="flex items-center space-x-2 px-2 sm:px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-600"
              >
                  <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  <span class="text-xs font-medium text-gray-600 dark:text-gray-400 hidden sm:block">Kullanıcı</span>
              </button>
              
              <!-- User Dropdown Menu -->
              <div v-if="showUserMenu" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                  <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                      <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">Admin Kullanıcı</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400 truncate">admin@example.com</p>
                  </div>
                  <div class="py-1">
                      <Link href="/profile" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                          <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                          </svg>
                          <span class="truncate">Profilim</span>
                      </Link>
                      <Link href="/" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                          <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                          </svg>
                          <span class="truncate">Siteye Git</span>
                      </Link>
                      <hr class="my-1 border-gray-100 dark:border-gray-700">
                      <Link 
                          :href="route('logout')" 
                          method="post" 
                          as="button"
                          class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                      >
                          <svg class="w-4 h-4 text-red-600 dark:text-red-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                          </svg>
                          <span class="truncate">Çıkış Yap</span>
                      </Link>
                  </div>
              </div>
          </div>
        </div>
      </header>

      <!-- Sayfa İçeriği -->
      <main class="flex-1 overflow-auto">
        <div class="p-4 sm:p-6 lg:p-8">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Sidebar from '@/Components/Panel/Navigation/Sidebar.vue'
import DarkModeToggle from '@/Components/Panel/DarkModeToggle.vue'
import Breadcrumb from '@/Components/Panel/Header/Breadcrumb.vue'

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
  },
  breadcrumbs: {
    type: Array,
    default: () => []
  }
})

const sidebarOpen = ref(false)
const isCollapsed = ref(false)
const isMobile = ref(false)

// Dropdown states
const showNotifications = ref(false)
const showUserMenu = ref(false)
const showQuickActions = ref(false)

// Check if mobile
const checkMobile = () => {
    isMobile.value = window.innerWidth < 1024
    if (isMobile.value) {
        sidebarOpen.value = false
    } else {
        sidebarOpen.value = true
    }
}

// Toggle sidebar
const toggleSidebar = () => {
    if (isMobile.value) {
        sidebarOpen.value = !sidebarOpen.value
    } else {
        isCollapsed.value = !isCollapsed.value
    }
}

// Close all dropdowns when clicking outside
const closeDropdowns = () => {
    showNotifications.value = false
    showUserMenu.value = false
    showQuickActions.value = false
}

onMounted(() => {
    checkMobile()
    window.addEventListener('resize', checkMobile)
    document.addEventListener('click', closeDropdowns)
})

onUnmounted(() => {
    window.removeEventListener('resize', checkMobile)
    document.removeEventListener('click', closeDropdowns)
})
</script> 