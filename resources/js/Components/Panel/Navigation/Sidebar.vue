<template>
  <aside
    :class="[
      'fixed inset-y-0 left-0 z-40 w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col transition-transform duration-300',
      isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]"
  >
    <!-- Logo ve Başlık -->
    <div class="flex items-center h-16 px-6 border-b border-gray-200 dark:border-gray-800">
      <span class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">Admin Panel</span>
      <button @click="$emit('close')" class="ml-auto p-1 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 lg:hidden">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    <!-- Menü -->
    <nav class="flex-1 py-4 px-2 space-y-1">
      <template v-for="item in menuItems" :key="item.name">
        <a
          :href="item.href"
          :class="[
            'group flex items-center px-4 py-2 rounded-md font-medium text-sm transition-colors',
            isActive(item) ? 'bg-indigo-50 dark:bg-gray-800 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-indigo-700 dark:hover:text-indigo-300'
          ]"
        >
          <span class="mr-3">
            <component :is="item.icon" class="w-5 h-5" />
          </span>
          {{ item.label }}
        </a>
      </template>
    </nav>
  </aside>
  <!-- Mobilde overlay -->
  <div v-if="isOpen" @click="$emit('close')" class="fixed inset-0 z-30 bg-black bg-opacity-30 lg:hidden"></div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

// Basit ikonlar
const HomeIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m5 0h-2a2 2 0 01-2-2v-2a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2z"/></svg>'
}
const UsersIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 010 7.75"/></svg>'
}
const LogIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h4m0 0V7m0 4l-4-4m0 0l-4 4"/></svg>'
}
const MailIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8V8a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>'
}
const SettingsIcon = {
  template: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 0V4m0 16v-4m8-4h-4m-8 0H4"/></svg>'
}

const menuItems = [
  { name: 'dashboard', label: 'Dashboard', href: '/dashboard', icon: HomeIcon },
  { name: 'users', label: 'Kullanıcılar', href: '/panel/users', icon: UsersIcon },
  { name: 'logs', label: 'Aktivite Logları', href: '/panel/logs', icon: LogIcon },
  { name: 'mails', label: 'Mail Bildirimleri', href: '/panel/mails', icon: MailIcon },
  { name: 'settings', label: 'Ayarlar', href: '/panel/settings', icon: SettingsIcon },
]

defineProps({
  isOpen: { type: Boolean, default: false }
})

const page = usePage()
const isActive = (item) => {
  // Aktiflik kontrolü için route veya url karşılaştırması yapılabilir
  return page.url.startsWith(item.href)
}
</script> 