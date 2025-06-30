<template>
  <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between h-16 px-4">
      <!-- Sol Taraf: Menü Toggle ve Breadcrumb -->
      <div class="flex items-center">
        <!-- Menü Toggle Butonu -->
        <button
          @click="$emit('toggle-sidebar')"
          class="p-2 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 lg:hidden"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <!-- Breadcrumb -->
        <nav class="hidden lg:flex items-center space-x-4 ml-4">
          <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <span>Panel</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 dark:text-white">{{ currentPage }}</span>
          </div>
        </nav>
      </div>

      <!-- Sağ Taraf: Kullanıcı İşlemleri -->
      <div class="flex items-center space-x-4">
        <!-- Dark Mode Toggle -->
        <DarkModeToggle />

        <!-- Hızlı İşlemler Dropdown -->
        <Dropdown align="right" width="48">
          <template #trigger>
            <button class="p-2 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
            </button>
          </template>

          <template #content>
            <div class="w-48">
              <div class="block px-4 py-2 text-xs text-gray-400">
                Hızlı İşlemler
              </div>
              
              <DropdownLink :href="route('panel.users.index')">
                <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
                Kullanıcı Ekle
              </DropdownLink>
              
              <DropdownLink :href="route('dashboard')">
                <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                </svg>
                Dashboard
              </DropdownLink>
            </div>
          </template>
        </Dropdown>

        <!-- Bildirimler -->
        <button class="p-2 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 relative">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 00-6 6v7.5a2.25 2.25 0 002.25 2.25h7.5a2.25 2.25 0 002.25-2.25v-7.5a6 6 0 00-6-6z" />
          </svg>
          <!-- Bildirim Badge -->
          <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-400"></span>
        </button>

        <!-- Kullanıcı Dropdown -->
        <Dropdown align="right" width="48">
          <template #trigger>
            <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              <img 
                class="h-8 w-8 rounded-full object-cover" 
                :src="$page.props.auth.user.profile_photo_url" 
                :alt="$page.props.auth.user.name"
              >
              <span class="ml-2 hidden md:block text-gray-700 dark:text-gray-300">
                {{ $page.props.auth.user.name }}
              </span>
              <svg class="ml-1 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </template>

          <template #content>
            <div class="w-48">
              <!-- Kullanıcı Bilgileri -->
              <div class="block px-4 py-2 text-xs text-gray-400">
                Hesap Yönetimi
              </div>

              <DropdownLink :href="route('profile.show')">
                <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profil
              </DropdownLink>

              <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                API Tokens
              </DropdownLink>

              <div class="border-t border-gray-200 dark:border-gray-600"></div>

              <!-- Çıkış -->
              <form @submit.prevent="logout">
                <DropdownLink as="button">
                  <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                  </svg>
                  Çıkış Yap
                </DropdownLink>
              </form>
            </div>
          </template>
        </Dropdown>
      </div>
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