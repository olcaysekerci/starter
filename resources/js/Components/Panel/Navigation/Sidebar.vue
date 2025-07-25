<template>
  <aside
    v-show="props.isOpen"
    :class="[
      // Mobilde tam genişlik, desktop'ta normal genişlik
      props.isCollapsed && !isMobile ? 'w-20' : !isMobile ? 'w-64' : 'w-full max-w-xs',
      'bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col transition-all duration-300 z-50 fixed left-0 top-0 h-screen'
    ]"
  >
    <!-- Logo ve Başlık -->
    <div class="flex items-center h-16 px-6 border-b border-gray-200 dark:border-gray-800 flex-shrink-0">
      <span v-if="!props.isCollapsed || isMobile" class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">Admin Panel</span>
      <span v-else class="text-lg font-bold text-gray-900 dark:text-white tracking-tight"><HomeIcon class="w-7 h-7" /></span>
      <button @click="$emit('close')" class="ml-auto p-1 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 lg:hidden">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    <!-- Menü -->
    <nav @click.stop class="flex-1 py-4 px-2 space-y-1 overflow-y-auto min-h-0 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 scrollbar-track-transparent hover:scrollbar-thumb-gray-400 dark:hover:scrollbar-thumb-gray-500 [&::-webkit-scrollbar]:w-1 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-transparent">
      <template v-for="item in menuItems" :key="item.name">
        <div v-if="item.children" class="relative">
          <button
            @click="toggleDropdown(item.name)"
            :class="[
              'w-full group flex items-center px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 relative',
              isParentActive(item) 
                ? 'bg-gradient-to-r from-indigo-500/10 to-purple-500/10 dark:from-indigo-500/20 dark:to-purple-500/20 text-indigo-700 dark:text-indigo-200 shadow-sm' 
                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800/80 hover:text-indigo-700 dark:hover:text-indigo-300'
            ]"
            :title="props.isCollapsed && !isMobile ? item.label : ''"
          >
            <span class="mr-3 flex-shrink-0 relative">
              <component :is="item.icon" :class="[
                'w-5 h-5 transition-all duration-200',
                isParentActive(item) ? 'text-indigo-600 dark:text-indigo-300 drop-shadow-sm' : 'text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400'
              ]" />
              <!-- Collapsed durumda aktif göstergesi -->
              <div v-if="isParentActive(item) && props.isCollapsed && !isMobile" class="absolute -top-1 -right-1 w-2 h-2 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full border-2 border-white dark:border-gray-900 shadow-sm"></div>
            </span>
            <span v-if="!props.isCollapsed || isMobile">{{ item.label }}</span>
            <svg v-if="!props.isCollapsed || isMobile" :class="[
              'ml-auto w-4 h-4 transition-all duration-200',
              isParentActive(item) ? 'text-indigo-600 dark:text-indigo-300 drop-shadow-sm' : 'text-gray-400',
              dropdownOpen === item.name ? 'rotate-180' : ''
            ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          <div v-if="dropdownOpen === item.name && (!props.isCollapsed || isMobile)" class="ml-4 mt-1 space-y-1">
            <Link
              v-for="child in item.children"
              :key="child.name"
              :href="child.href"
              :class="[
                'block px-6 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 relative',
                isActive(child) 
                  ? 'bg-indigo-50/60 dark:bg-gray-700/60 text-indigo-700 dark:text-indigo-300 font-medium' 
                  : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50/60 dark:hover:bg-gray-700/60 hover:text-indigo-700 dark:hover:text-indigo-300'
              ]"
            >
              {{ child.label }}
            </Link>
          </div>
        </div>
        <Link
          v-else
          :href="item.href"
          :class="[
            'group flex items-center px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 relative',
            isActive(item) 
              ? 'bg-gradient-to-r from-indigo-500/10 to-purple-500/10 dark:from-indigo-500/20 dark:to-purple-500/20 text-indigo-700 dark:text-indigo-200 font-semibold shadow-sm' 
              : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100/80 dark:hover:bg-gray-800/80 hover:text-indigo-700 dark:hover:text-indigo-300'
          ]"
          :title="props.isCollapsed && !isMobile ? item.label : ''"
        >
          <span class="mr-3 flex-shrink-0 relative">
            <component :is="item.icon" :class="[
              'w-5 h-5 transition-all duration-200',
              isActive(item) ? 'text-indigo-600 dark:text-indigo-300 drop-shadow-sm' : 'text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400'
            ]" />
            <!-- Collapsed durumda aktif göstergesi -->
            <div v-if="isActive(item) && props.isCollapsed && !isMobile" class="absolute -top-1 -right-1 w-2 h-2 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full border-2 border-white dark:border-gray-900 shadow-sm"></div>
          </span>
          <span v-if="!props.isCollapsed || isMobile">{{ item.label }}</span>
        </Link>
      </template>
    </nav>

    <!-- Sidebar Footer -->
    <div @click.stop class="flex-shrink-0 border-t border-gray-200 dark:border-gray-800 p-4">
      <div v-if="!props.isCollapsed || isMobile" class="space-y-3">
        <!-- Sistem Durumu -->
        <div class="flex items-center justify-between text-xs">
          <span class="text-gray-500 dark:text-gray-400">Sistem Durumu</span>
          <div class="flex items-center space-x-1">
            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
            <span class="text-green-600 dark:text-green-400 font-medium">Aktif</span>
          </div>
        </div>
        
        <!-- Versiyon Bilgisi -->
        <div class="text-xs text-gray-500 dark:text-gray-400">
          <div class="flex items-center justify-between">
            <span>Versiyon</span>
            <span class="font-medium">v2.1.0</span>
          </div>
        </div>
        

      </div>
      
      <!-- Collapsed durumda sadece sistem durumu -->
      <div v-else class="flex flex-col items-center space-y-3">
        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
      </div>
    </div>
  </aside>
  <!-- Mobilde overlay -->
  <div v-if="props.isOpen" @click="$emit('close')" class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"></div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { HomeIcon, UsersIcon, DocumentTextIcon, EnvelopeIcon, Cog6ToothIcon, ShieldCheckIcon } from '@heroicons/vue/24/outline'

const emit = defineEmits(['close'])
const props = defineProps({
  isOpen: { type: Boolean, default: false },
  isCollapsed: { type: Boolean, default: false }
})

// Mobil kontrolü
const isMobile = ref(false)
const checkMobile = () => {
  isMobile.value = window.innerWidth < 1024
}

const page = usePage()
const route = window.route
const menuItems = [
  { name: 'Dashboard', label: 'Dashboard', href: route('panel.dashboard'), icon: HomeIcon, routeName: 'panel.dashboard' },
  {
    name: 'User/Panel', label: 'Kullanıcı Yönetimi', icon: UsersIcon, children: [
      { name: 'User/Panel', label: 'Kullanıcılar', href: route('panel.users.index'), routeName: 'panel.users.' },
      { name: 'User/Panel/Roles', label: 'Rol ve Yetki Yönetimi', href: route('panel.roles.index'), routeName: 'panel.roles.' }
    ]
  },
  { name: 'ActivityLog/Panel', label: 'Aktivite Logları', href: route('panel.activity-logs.index'), icon: DocumentTextIcon, routeName: 'panel.activity-logs.' },
  { name: 'MailNotification/Panel', label: 'Mail Bildirimleri', href: route('panel.mail-notifications.index'), icon: EnvelopeIcon, routeName: 'panel.mail-notifications.' },
  {
    name: 'Settings', label: 'Ayarlar', icon: Cog6ToothIcon, children: [
      { name: 'Settings/Panel/App', label: 'Uygulama Ayarları', href: route('panel.settings.app.index'), routeName: 'panel.settings.app.' },
      { name: 'Settings/Panel/Mail', label: 'Mail Ayarları', href: route('panel.settings.mail.index'), routeName: 'panel.settings.mail.' },
      { name: 'Settings/Panel/Profile', label: 'Profil Ayarları', href: route('panel.settings.profile.index'), routeName: 'panel.settings.profile.index' },
      { name: 'Settings/Panel/Security', label: 'Güvenlik', href: route('panel.settings.security'), routeName: 'panel.settings.security' }
    ]
  }
]

const isActive = (item) => {
  if (!item.routeName) return false
  return route().current(item.routeName + '*')
}

const isParentActive = (item) => {
  if (!item.children) return false
  return item.children.some(child => isActive(child))
}

// Basit dropdown kontrolü
const dropdownOpen = ref(null)

// Sayfa yüklendiğinde ve değiştiğinde dropdown'ı kontrol et
const checkAndOpenDropdown = () => {
  // Her menü öğesini kontrol et
  menuItems.forEach(item => {
    if (item.children && isParentActive(item)) {
      dropdownOpen.value = item.name
    }
  })
  
  // Eğer hiçbir parent aktif değilse dropdown'ı kapat
  if (!menuItems.some(item => item.children && isParentActive(item))) {
    dropdownOpen.value = null
  }
}

// Sayfa değiştiğinde kontrol et
watch(() => page.url, checkAndOpenDropdown, { immediate: true })

// Mobil kontrolü için event listener
onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})

const toggleDropdown = (name) => {
  if (dropdownOpen.value === name) {
    dropdownOpen.value = null
  } else {
    dropdownOpen.value = name
  }
}
</script> 