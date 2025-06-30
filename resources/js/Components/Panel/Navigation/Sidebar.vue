<template>
  <aside
    v-show="props.isOpen"
    :class="[
      props.isCollapsed ? 'w-20' : 'w-56',
      'bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col transition-all duration-300 overflow-hidden'
    ]"
  >
    <!-- Logo ve Başlık -->
    <div class="flex items-center h-16 px-6 border-b border-gray-200 dark:border-gray-800">
      <span v-if="!props.isCollapsed" class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">Admin Panel</span>
      <span v-else class="text-lg font-bold text-gray-900 dark:text-white tracking-tight"><HomeIcon class="w-7 h-7" /></span>
      <button @click="$emit('close')" class="ml-auto p-1 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 lg:hidden">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    <!-- Menü -->
    <nav class="flex-1 py-4 px-2 space-y-1">
      <template v-for="item in menuItems" :key="item.name">
        <div v-if="item.children" class="relative">
          <button
            @click="toggleDropdown(item.name)"
            :class="[
              'w-full group flex items-center px-4 py-2 rounded-md font-medium text-sm transition-colors',
              isActive(item) ? 'bg-indigo-50 dark:bg-gray-800 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-indigo-700 dark:hover:text-indigo-300'
            ]"
          >
            <span class="mr-3 flex-shrink-0">
              <component :is="item.icon" class="w-5 h-5" />
            </span>
            <span v-if="!props.isCollapsed">{{ item.label }}</span>
            <svg v-if="!props.isCollapsed" class="ml-auto w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          <div v-if="dropdownOpen === item.name && !props.isCollapsed" class="ml-8 mt-1 space-y-1">
            <a
              v-for="child in item.children"
              :key="child.name"
              :href="child.href"
              :class="[
                'block px-4 py-2 rounded-md text-sm font-medium transition-colors',
                isActive(child) ? 'bg-indigo-50 dark:bg-gray-800 text-indigo-700 dark:text-indigo-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-indigo-700 dark:hover:text-indigo-300'
              ]"
            >
              {{ child.label }}
            </a>
          </div>
        </div>
        <a
          v-else
          :href="item.href"
          :class="[
            'group flex items-center px-4 py-2 rounded-md font-medium text-sm transition-colors',
            isActive(item) ? 'bg-indigo-50 dark:bg-gray-800 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-indigo-700 dark:hover:text-indigo-300'
          ]"
        >
          <span class="mr-3 flex-shrink-0">
            <component :is="item.icon" class="w-5 h-5" />
          </span>
          <span v-if="!props.isCollapsed">{{ item.label }}</span>
        </a>
      </template>
    </nav>
  </aside>
  <!-- Mobilde overlay -->
  <div v-if="props.isOpen" @click="$emit('close')" class="fixed inset-0 z-30 bg-black bg-opacity-30 lg:hidden"></div>
</template>

<script setup>
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { HomeIcon, UsersIcon, DocumentTextIcon, EnvelopeIcon, Cog6ToothIcon } from '@heroicons/vue/24/outline'

const emit = defineEmits(['close'])
const props = defineProps({
  isOpen: { type: Boolean, default: false },
  isCollapsed: { type: Boolean, default: false }
})
const page = usePage()
const dropdownOpen = ref(null)
const menuItems = [
  { name: 'dashboard', label: 'Dashboard', href: '/dashboard', icon: HomeIcon },
  { name: 'users', label: 'Kullanıcılar', href: '/panel/users', icon: UsersIcon },
  { name: 'logs', label: 'Aktivite Logları', href: '/panel/logs', icon: DocumentTextIcon },
  { name: 'mails', label: 'Mail Bildirimleri', href: '/panel/mails', icon: EnvelopeIcon },
  {
    name: 'settings', label: 'Ayarlar', icon: Cog6ToothIcon, children: [
      { name: 'profile', label: 'Profil Ayarları', href: '/panel/settings/profile' },
      { name: 'security', label: 'Güvenlik', href: '/panel/settings/security' }
    ]
  }
]

const isActive = (item) => {
  if (item.children) {
    return item.children.some(child => page.url.startsWith(child.href))
  }
  return page.url.startsWith(item.href)
}

const toggleDropdown = (name) => {
  dropdownOpen.value = dropdownOpen.value === name ? null : name
}
</script> 