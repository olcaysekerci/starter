<template>
  <PanelLayout 
    title="Rol Detayı" 
    page-title="Rol Detayı"
    :breadcrumbs="[
      { title: 'Dashboard', url: '/dashboard' },
      { title: 'Roller', url: '/panel/roles' },
      { title: role.name, url: null }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Rol Detayı"
      :description="`${role.name} rolünün detaylı bilgileri ve izinleri`"
    >
      <template #actions>
        <div class="flex items-center gap-2">
          <button @click="showStats = !showStats" class="inline-flex items-center px-3 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
            İstatistikler
          </button>
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
          <ActionButton 
            @click="openUsersModal" 
            variant="secondary" 
            size="sm"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
            Kullanıcıları Gör
          </ActionButton>
          <ActionButton 
            @click="deleteRole" 
            variant="danger" 
            size="sm"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Sil
          </ActionButton>
        </div>
        <div v-if="showStats" class="flex gap-4 mt-4">
          <div class="flex flex-col items-center bg-blue-50 dark:bg-blue-900/20 px-4 py-2 rounded">
            <span class="text-xs text-gray-500 dark:text-gray-400">Kullanıcı</span>
            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ role.users_count || 0 }}</span>
          </div>
          <div class="flex flex-col items-center bg-green-50 dark:bg-green-900/20 px-4 py-2 rounded">
            <span class="text-xs text-gray-500 dark:text-gray-400">İzin</span>
            <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ role.permissions?.length || 0 }}</span>
          </div>
        </div>
      </template>
    </PageHeader>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Rol Bilgileri -->
      <div class="space-y-6">
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
                  {{ role.name }}
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
            <div v-if="role.permissions && role.permissions.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
              <div
                v-for="permission in role.permissions"
                :key="permission.id"
                class="flex flex-col px-3 py-2 rounded-lg text-sm font-medium bg-green-50 text-green-800 dark:bg-green-900/20 dark:text-green-200 border border-green-200 dark:border-green-700"
              >
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span>{{ permission.display_name || permission.name }}</span>
                </div>
                <div v-if="permission.description" class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-6">
                  {{ permission.description }}
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
    </div>

    <!-- Kullanıcıları Gör Modalı -->
    <div v-if="showUsersModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6 relative">
        <button @click="showUsersModal = false" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Bu role sahip kullanıcılar</h3>
        <div v-if="role.users && role.users.length > 0" class="space-y-2 max-h-80 overflow-y-auto">
          <div v-for="user in role.users" :key="user.id" class="flex items-center gap-2 p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700">
            <span class="font-medium text-gray-900 dark:text-gray-100">{{ user.full_name || user.name }}</span>
            <span class="text-xs text-gray-500">{{ user.email }}</span>
          </div>
        </div>
        <div v-else class="text-center text-gray-400 py-8">Bu role sahip kullanıcı yok.</div>
      </div>
    </div>

    <!-- Delete Modal -->
    <DeleteModal
      :show="showDeleteModal"
      :title="deleteConfig.title"
      :description="deleteConfig.description"
      :additional-info="deleteConfig.additionalInfo"
      :loading="deleteLoading"
      @close="closeDeleteModal"
      @confirm="confirmDelete"
    />
  </PanelLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import DeleteModal from '@/Components/Panel/DeleteModal.vue'
import { useDeleteModal, useNotification } from '@/Composables'

const props = defineProps({ 
  role: {
    type: Object,
    required: true
  }
})

const showStats = ref(false)
const showUsersModal = ref(false)

// Composables
const { showSuccess, showError } = useNotification()
const { 
  showDeleteModal, 
  deleteLoading, 
  deleteConfig, 
  openDeleteModal, 
  closeDeleteModal, 
  confirmDelete 
} = useDeleteModal()

// Methods

const editRole = () => {
  router.visit(`/panel/roles/${props.role.id}/edit`)
}

const openUsersModal = () => {
  showUsersModal.value = true
}

const deleteRole = () => {
  openDeleteModal(props.role, {
    title: 'Rol Silme Onayı',
    description: `"${props.role.name}" rolünü silmek istediğinizden emin misiniz?`,
    additionalInfo: 'Bu işlem geri alınamaz ve role sahip tüm kullanıcılar bu rolü kaybedecektir.',
    route: 'panel.users.roles.destroy'
  })
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
</script>