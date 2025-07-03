<template>
  <PanelLayout 
    title="Yetki Detayı" 
    page-title="Yetki Detayı"
    :breadcrumbs="[
      { title: 'Dashboard', url: '/dashboard' },
      { title: 'Yetki Yönetimi', url: route('panel.permissions.index') },
      { title: permission.display_name || permission.name }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Yetki Detayı"
      :description="`${permission.display_name || permission.name} yetkisinin detayları`"
    >
      <template #actions>
        <ActionButton 
          @click="$router.back()" 
          variant="secondary" 
          size="sm"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Geri Dön
        </ActionButton>
        <ActionButton 
          @click="editPermission" 
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Yetki Bilgileri -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Temel Bilgiler -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Temel Bilgiler</h3>
          </div>
          <div class="p-6 space-y-4">
            <div class="flex items-center">
              <div class="flex-shrink-0 h-10 w-10">
                <div class="h-10 w-10 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                  <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                  {{ permission.display_name || permission.name }}
                </h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  {{ permission.name }}
                </p>
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Modül</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  <span v-if="permission.module" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                    {{ permission.module }}
                  </span>
                  <span v-else class="text-gray-500 dark:text-gray-400">-</span>
                </p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Durum</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  <span 
                    :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                      permission.is_active 
                        ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                        : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
                    ]"
                  >
                    {{ permission.is_active ? 'Aktif' : 'Pasif' }}
                  </span>
                </p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Guard</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ permission.guard_name }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Oluşturulma</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ formatDate(permission.created_at) }}</p>
              </div>
            </div>
            
            <div v-if="permission.description">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Açıklama</label>
              <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ permission.description }}</p>
            </div>
          </div>
        </div>

        <!-- Roller -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Bu Yetkiye Sahip Roller</h3>
          </div>
          <div class="p-6">
            <div v-if="roles && roles.length > 0" class="space-y-3">
              <div v-for="role in roles" :key="role.id" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-8 w-8">
                    <div class="h-8 w-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                      <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                      </svg>
                    </div>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ role.display_name || role.name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ role.name }}</p>
                  </div>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                  {{ role.users_count || 0 }} kullanıcı
                </span>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Rol bulunamadı</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Bu yetkiye sahip hiç rol yok.
              </p>
            </div>
          </div>
        </div>

        <!-- Kullanıcılar -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Bu Yetkiye Sahip Kullanıcılar</h3>
          </div>
          <div class="p-6">
            <div v-if="users && users.length > 0" class="space-y-3">
              <div v-for="user in users" :key="user.id" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-8 w-8">
                    <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                      <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ getUserInitials(user.name) }}
                      </span>
                    </div>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ user.name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ user.email }}</p>
                  </div>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                  {{ formatDate(user.created_at) }}
                </span>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Kullanıcı bulunamadı</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Bu yetkiye sahip hiç kullanıcı yok.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Yan Panel -->
      <div class="space-y-6">
        <!-- İstatistikler -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">İstatistikler</h3>
          </div>
          <div class="p-6 space-y-4">
            <div class="flex justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Toplam Rol</span>
              <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ roles?.length || 0 }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Toplam Kullanıcı</span>
              <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ users?.length || 0 }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Oluşturulma</span>
              <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ formatDate(permission.created_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Son Güncelleme</span>
              <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ formatDate(permission.updated_at) }}</span>
            </div>
          </div>
        </div>

        <!-- Hızlı İşlemler -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Hızlı İşlemler</h3>
          </div>
          <div class="p-6 space-y-3">
            <ActionButton 
              @click="editPermission" 
              variant="primary" 
              size="sm"
              class="w-full"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              Düzenle
            </ActionButton>
            
            <ActionButton 
              v-if="!isSystemPermission"
              @click="deletePermission" 
              variant="danger" 
              size="sm"
              class="w-full"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
              Sil
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
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import DeleteModal from '@/Components/Panel/DeleteModal.vue'
import { useDeleteModal } from '@/Composables/useDeleteModal'

const props = defineProps({
  permission: {
    type: Object,
    required: true
  },
  users: {
    type: Array,
    default: () => []
  },
  roles: {
    type: Array,
    default: () => []
  }
})

// Delete modal
const { 
  showDeleteModal, 
  deleteLoading, 
  deleteConfig, 
  openDeleteModal, 
  closeDeleteModal, 
  confirmDelete 
} = useDeleteModal()

const isSystemPermission = computed(() => {
  return ['super-admin', 'admin'].includes(props.permission.name)
})

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('tr-TR')
}

const getUserInitials = (name) => {
  if (!name) return '?'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const editPermission = () => {
  router.visit(route('panel.permissions.edit', props.permission.id))
}

const deletePermission = () => {
  openDeleteModal(props.permission, {
    title: 'Yetki Silme Onayı',
    description: `"${props.permission.display_name}" yetkisini silmek istediğinizden emin misiniz?`,
    additionalInfo: 'Bu yetkiyi silmek, bu yetkiye sahip tüm kullanıcıları etkileyecektir.',
    route: 'panel.permissions.destroy'
  })
}
</script> 