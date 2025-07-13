<template>
  <PanelLayout 
    title="Rol Detayı" 
    page-title="Rol Detayı"
    :breadcrumbs="[
      { title: 'Dashboard', url: '/dashboard' },
      { title: 'Roller', url: '/panel/roles' },
      { title: role.display_name || role.name }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Rol Detayı"
      :description="`${role.display_name || role.name} rolünün detaylı bilgileri ve izinleri`"
    >
      <template #actions>
        <ActionButton 
          @click="showStats = !showStats" 
          variant="ghost" 
          size="sm"
          :class="{ 'bg-gray-100 dark:bg-gray-700': showStats }"
          title="İstatistikleri Göster/Gizle"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
        </ActionButton>
        <ActionButton 
          @click="editRole" 
          variant="primary" 
          size="sm"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
          Düzenle
        </ActionButton>
      </template>
    </PageHeader>

    <!-- İstatistikler -->
    <div v-if="showStats" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <InPageStatCard
        title="Kullanıcı Sayısı"
        :value="users.length"
        icon="UsersIcon"
        color="blue"
      />
      <InPageStatCard
        title="İzin Sayısı"
        :value="role.permissions?.length || 0"
        icon="CheckCircleIcon"
        color="green"
      />
      <InPageStatCard
        title="Oluşturma Tarihi"
        :value="formatDateShort(role.created_at)"
        icon="CalendarIcon"
        color="purple"
      />
      <InPageStatCard
        title="Son Güncelleme"
        :value="formatDateShort(role.updated_at)"
        icon="ClockIcon"
        color="gray"
      />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Rol Bilgileri -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Rol Bilgileri</h3>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Rol Adı
                </label>
                <div class="text-sm text-gray-900 dark:text-gray-100 font-medium">
                  {{ role.display_name || role.name }}
                </div>
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Açıklama
                </label>
                <div class="text-sm text-gray-900 dark:text-gray-100">
                  {{ role.description || 'Açıklama eklenmemiş' }}
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Oluşturma Tarihi
                </label>
                <div class="text-sm text-gray-900 dark:text-gray-100">
                  {{ formatDate(role.created_at) }}
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Son Güncelleme
                </label>
                <div class="text-sm text-gray-900 dark:text-gray-100">
                  {{ formatDate(role.updated_at) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Permissions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                İzinler
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                  {{ role.permissions?.length || 0 }}
                </span>
              </h3>
            </div>
          </div>
          <div class="p-6">
            <div v-if="role.permissions && role.permissions.length > 0" class="space-y-4">
              <!-- İzinleri modüllere göre grupla -->
              <div v-for="(permissions, module) in groupedPermissions" :key="module" class="space-y-3">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                  {{ getModuleDisplayName(module) }}
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                  <div
                    v-for="permission in permissions"
                    :key="permission.id"
                    class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-green-50 text-green-800 dark:bg-green-900/20 dark:text-green-200 border border-green-200 dark:border-green-700"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ getPermissionDisplayName(permission) }}
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">İzin bulunamadı</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Bu role henüz izin atanmamış.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Kullanıcılar -->
      <div class="space-y-6">
        <!-- Kullanıcı Listesi -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Bu Role Sahip Kullanıcılar
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                  {{ users.length }}
                </span>
              </h3>
            </div>
          </div>
          <div class="p-6">
            <div v-if="users && users.length > 0" class="space-y-3 max-h-[28rem] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 scrollbar-track-transparent hover:scrollbar-thumb-gray-400 dark:hover:scrollbar-thumb-gray-500 [&::-webkit-scrollbar]:w-1 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-transparent">
              <div
                v-for="user in users"
                :key="user.id"
                class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-600/50 transition-colors"
              >
                <div class="flex items-center space-x-3 flex-1 min-w-0">
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                      {{ user.full_name || user.name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                      {{ user.email }}
                    </p>
                  </div>
                </div>
                <div class="flex-shrink-0">
                  <ActionButton 
                    @click="viewUser(user)" 
                    variant="ghost" 
                    size="sm"
                    title="Kullanıcı Detayı"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </ActionButton>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Kullanıcı bulunamadı</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Bu role sahip kullanıcı bulunmuyor.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PanelLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import InPageStatCard from '@/Components/Panel/Page/InPageStatCard.vue'
import { useNotification } from '@/Composables'

const props = defineProps({ 
  role: {
    type: Object,
    required: true
  },
  users: {
    type: Array,
    default: () => []
  }
})

// Composables
const { showSuccess, showError } = useNotification()

// Reactive state
const showStats = ref(false)

// Computed
const groupedPermissions = computed(() => {
  if (!props.role.permissions) return {}
  
  return props.role.permissions.reduce((groups, permission) => {
    const module = permission.module || 'Genel'
    if (!groups[module]) {
      groups[module] = []
    }
    groups[module].push(permission)
    return groups
  }, {})
})

// Methods
const editRole = () => {
  router.visit(`/panel/roles/${props.role.id}/edit`)
}

const viewUser = (user) => {
  router.visit(`/panel/users/${user.id}`)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatDateShort = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('tr-TR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const getModuleDisplayName = (module) => {
  const moduleNames = {
    'User': 'Kullanıcı Yönetimi',
    'ActivityLog': 'Aktivite Logları',
    'MailNotification': 'Mail Bildirimleri',
    'Settings': 'Ayarlar',
    'Dashboard': 'Dashboard',
    'Genel': 'Genel İzinler'
  }
  return moduleNames[module] || module
}

const getPermissionDisplayName = (permission) => {
  // Önce display_name varsa onu kullan
  if (permission.display_name) {
    return permission.display_name
  }
  
  // Yoksa name'i daha okunabilir hale getir
  const name = permission.name
  const parts = name.split('.')
  
  if (parts.length >= 2) {
    const action = parts[parts.length - 1]
    const resource = parts[parts.length - 2]
    
    const actionNames = {
      'index': 'Listeleme',
      'show': 'Görüntüleme',
      'create': 'Oluşturma',
      'store': 'Kaydetme',
      'edit': 'Düzenleme',
      'update': 'Güncelleme',
      'destroy': 'Silme',
      'delete': 'Silme',
      'export': 'Dışa Aktarma',
      'import': 'İçe Aktarma',
      'approve': 'Onaylama',
      'reject': 'Reddetme',
      'publish': 'Yayınlama',
      'unpublish': 'Yayından Kaldırma'
    }
    
    const resourceNames = {
      'users': 'Kullanıcılar',
      'roles': 'Roller',
      'permissions': 'İzinler',
      'activity-logs': 'Aktivite Logları',
      'mail-notifications': 'Mail Bildirimleri',
      'settings': 'Ayarlar',
      'dashboard': 'Dashboard'
    }
    
    const actionName = actionNames[action] || action
    const resourceName = resourceNames[resource] || resource
    
    return `${resourceName} ${actionName}`
  }
  
  return name
}
</script>