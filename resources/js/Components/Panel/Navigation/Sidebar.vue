<template>
  <div 
    :class="[
      'fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform duration-300 ease-in-out',
      'lg:translate-x-0', // Desktop'ta her zaman görünür
      isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0' // Mobilde toggle, desktop'ta her zaman açık
    ]"
  >
    <!-- Logo Alanı -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center">
        <ApplicationMark class="h-8 w-auto" />
        <span class="ml-2 text-lg font-semibold text-gray-900 dark:text-white">Panel</span>
      </div>
      <button 
        @click="$emit('close')"
        class="p-1 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 lg:hidden"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Menü Öğeleri -->
    <nav class="mt-4 px-2">
      <div class="space-y-1">
        <!-- Dashboard -->
        <NavLink 
          :href="route('dashboard')" 
          :active="route().current('dashboard')"
          class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
        >
          <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
          </svg>
          Dashboard
        </NavLink>

        <!-- Kullanıcı Yönetimi -->
        <div class="space-y-1">
          <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Yönetim
          </h3>
          
          <NavLink 
            :href="route('panel.users.index')" 
            :active="route().current('panel.users.*')"
            class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
          >
            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
            </svg>
            Kullanıcılar
          </NavLink>
        </div>

        <!-- Sistem -->
        <div class="space-y-1">
          <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Sistem
          </h3>
          
          <NavLink 
            :href="route('profile.show')" 
            :active="route().current('profile.*')"
            class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
          >
            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Profil
          </NavLink>
        </div>
      </div>
    </nav>
  </div>

  <!-- Overlay (sadece mobil için) -->
  <div 
    v-if="isOpen" 
    @click="$emit('close')"
    class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
  ></div>
</template>

<script setup>
import ApplicationMark from '@/Components/Shared/ApplicationMark.vue'
import NavLink from '@/Components/Shared/NavLink.vue'

defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

defineEmits(['close'])
</script> 