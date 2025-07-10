<template>
  <PanelLayout 
    title="Rol Detayı" 
    page-title="Rol Detayı"
    :breadcrumbs="[
      { title: 'Dashboard', url: '/dashboard' },
      { title: 'Kullanıcı Yönetimi', url: '/panel/users' },
      { title: 'Roller', url: '/panel/users/roles' },
      { title: role.name }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Rol Detayı"
      :description="`${role.name} rolünün detaylı bilgileri ve izinleri`"
    >
      <template #actions>
        <ActionButton 
          @click="goBack" 
          variant="secondary" 
          size="sm"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Geri Dön
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
      </template>
    </PageHeader>

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
                  {{ role.name }}
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Guard Name
                </label>
                <div class="text-sm text-gray-900 dark:text-gray-100">
                  {{ role.guard_name }}
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
                class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-green-50 text-green-800 dark:bg-green-900/20 dark:text-green-200 border border-green-200 dark:border-green-700"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ permission.name }}
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

      <!-- İstatistikler -->
      <div class="space-y-6">
        <!-- User Count -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Kullanıcı Sayısı</h3>
          </div>
          <div class="p-6">
            <div class="text-center">
              <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                {{ role.users_count || 0 }}
              </div>
              <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                kullanıcı bu role sahip
              </div>
            </div>
          </div>
        </div>

        <!-- Permission Count -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">İzin Sayısı</h3>
          </div>
          <div class="p-6">
            <div class="text-center">
              <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                {{ role.permissions?.length || 0 }}
              </div>
              <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                izin tanımlanmış
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Hızlı İşlemler</h3>
          </div>
          <div class="p-6 space-y-3">
            <ActionButton 
              @click="editRole" 
              variant="primary" 
              size="sm"
              class="w-full justify-center"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              Düzenle
            </ActionButton>
            <ActionButton 
              @click="viewUsers" 
              variant="secondary" 
              size="sm"
              class="w-full justify-center"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
              </svg>
              Kullanıcıları Görüntüle
            </ActionButton>
          </div>
        </div>
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
const goBack = () => {
  router.visit('/panel/users/roles')
}

const editRole = () => {
  router.visit(`/panel/users/roles/${props.role.id}/edit`)
}

const viewUsers = () => {
  router.visit('/panel/users', {
    data: { role: props.role.name }
  })
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