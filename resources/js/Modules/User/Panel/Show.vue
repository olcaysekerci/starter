<template>
  <PanelLayout 
    title="Kullanıcı Detayı" 
    page-title="Kullanıcı Detayı"
    :breadcrumbs="[
      { title: 'Kullanıcı Yönetimi', url: '/panel/users' },
      { title: user.full_name }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Kullanıcı Detayı"
      :description="`${user.full_name} kullanıcısının detaylı bilgileri`"
    >
      <template #actions>
        <ActionButton 
          @click="editUser" 
          variant="primary" 
          size="sm"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
          Düzenle
        </ActionButton>
        <ActionButton 
          @click="deleteUser" 
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

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
      <!-- Ana Bilgiler Kartı -->
      <div class="lg:col-span-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Kullanıcı Bilgileri</h3>
        </div>
        <div class="p-6">
          <div class="space-y-0.5 divide-y divide-gray-100 dark:divide-gray-700">
            <!-- 1. Satır: Ad Soyad - E-posta -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-3 items-center">
              <div class="flex items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-32">Ad Soyad</span>
                <span class="text-sm text-gray-900 dark:text-gray-100 ml-2">{{ user.full_name }}</span>
              </div>
              <div class="flex items-center mt-2 md:mt-0">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-32">E-posta</span>
                <span class="text-sm text-gray-900 dark:text-gray-100 ml-2">{{ user.email }}</span>
              </div>
            </div>
            <!-- 2. Satır: Kullanıcı ID - Roller -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-3 items-center">
              <div class="flex items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-32">Kullanıcı ID</span>
                <span class="text-sm text-gray-900 dark:text-gray-100 ml-2">#{{ user.id }}</span>
              </div>
              <div class="flex items-center mt-2 md:mt-0">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-32">Roller</span>
                <div class="flex flex-wrap gap-1 ml-2">
                  <span v-for="role in user.roles" :key="role.id" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ role.display_name || role.name }}
                  </span>
                </div>
              </div>
            </div>
            <!-- 3. Satır: Durum - Hesap Yaşı -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-3 items-center">
              <div class="flex items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-32">Durum</span>
                <span v-if="user.is_active" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2">Aktif</span>
                <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">Pasif</span>
              </div>
              <div class="flex items-center mt-2 md:mt-0">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-32">Hesap Yaşı</span>
                <span class="text-sm text-gray-900 dark:text-gray-100 ml-2">{{ stats.account_age_days }} gün</span>
              </div>
            </div>
            <!-- 4. Satır: Kayıt Tarihi - Son Güncelleme -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-3 items-center">
              <div class="flex items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-32">Kayıt Tarihi</span>
                <span class="text-sm text-gray-900 dark:text-gray-100 ml-2">{{ formatDate(user.created_at) }}</span>
              </div>
              <div class="flex items-center mt-2 md:mt-0">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-32">Son Güncelleme</span>
                <span class="text-sm text-gray-900 dark:text-gray-100 ml-2">{{ formatDate(user.updated_at) }}</span>
              </div>
            </div>
            <!-- 5. Satır: Son Giriş - Son Aktivite -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-3 items-center">
              <div class="flex items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-32">Son Giriş</span>
                <span class="text-sm text-gray-900 dark:text-gray-100 ml-2">{{ formatDateTime(stats.last_login) }}</span>
              </div>
              <div class="flex items-center mt-2 md:mt-0">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-32">Son Aktivite</span>
                <span class="text-sm text-gray-900 dark:text-gray-100 ml-2">{{ formatDateTime(stats.last_activity) }}</span>
              </div>
            </div>
            <!-- 6. Satır: Telefon - Adres (varsa) -->
            <div v-if="user.phone || user.address" class="grid grid-cols-1 md:grid-cols-2 gap-4 py-3 items-center">
              <div v-if="user.phone" class="flex items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-32">Telefon</span>
                <span class="text-sm text-gray-900 dark:text-gray-100 ml-2">{{ user.formatted_phone }}</span>
              </div>
              <div v-if="user.address" class="flex items-center mt-2 md:mt-0">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-32">Adres</span>
                <span class="text-sm text-gray-900 dark:text-gray-100 ml-2">{{ user.address }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Son Hareketler Kartı -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Son Hareketler</h3>
        </div>
        <div class="p-3 min-h-[120px] flex flex-col justify-center">
          <template v-if="stats.recent_activities.length > 0">
            <div class="space-y-2">
              <div v-for="activity in stats.recent_activities.slice(0, 6)" :key="activity.id" class="flex items-start space-x-2 py-1.5 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                <span :class="getEventBadgeClass(activity.resolved_event || activity.event)" class="px-1.5 py-0.5 rounded text-xs font-medium flex-shrink-0">
                  {{ getEventLabel(activity.resolved_event || activity.event, activity.description) }}
                </span>
                <div class="flex-1 min-w-0">
                  <p class="text-xs text-gray-900 dark:text-gray-100 truncate">{{ activity.description }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDateTime(activity.created_at) }}</p>
                </div>
              </div>
            </div>
          </template>
          <template v-else>
            <div class="flex items-center justify-center h-full min-h-[80px]">
              <span class="text-xs text-gray-400">Henüz bir işlem yapılmamış</span>
            </div>
          </template>
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
import { router } from '@inertiajs/vue3'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import DeleteModal from '@/Components/Panel/DeleteModal.vue'
import { useDeleteModal } from '@/Composables/useDeleteModal'
import { useFormat } from '@/Composables/useFormat'

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  stats: {
    type: Object,
    required: true
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

// Format functions
const { formatDate, formatDateTime } = useFormat()

const getEventLabel = (event, description) => {
  // Eğer event boşsa, description'dan event'i çıkar
  if (!event && description) {
    if (description.includes('Giriş yaptı')) return 'Giriş'
    if (description.includes('Çıkış yaptı')) return 'Çıkış'
    if (description.includes('Başarısız giriş denemesi')) return 'Başarısız Giriş'
    if (description.includes('Şifre sıfırlandı')) return 'Şifre Sıfırlandı'
    if (description.includes('Kayıt oldu')) return 'Kayıt Oldu'
    if (description.includes('E-posta doğrulandı')) return 'E-posta Doğrulandı'
    if (description.includes('oluşturdu')) return 'Oluşturuldu'
    if (description.includes('güncelledi')) return 'Güncellendi'
    if (description.includes('sildi')) return 'Silindi'
    if (description.includes('geri yükledi')) return 'Geri Yüklendi'
  }

  const labels = {
    'created': 'Oluşturuldu',
    'updated': 'Güncellendi',
    'deleted': 'Silindi',
    'restored': 'Geri Yüklendi',
    'login': 'Giriş',
    'logout': 'Çıkış',
    'failed_login': 'Başarısız Giriş',
    'password_reset': 'Şifre Sıfırlandı',
    'registered': 'Kayıt Oldu',
    'email_verified': 'E-posta Doğrulandı',
    'password_changed': 'Şifre Değişti',
    'email_changed': 'E-posta Değişti',
    'profile_updated': 'Profil Güncellendi'
  }
  return labels[event] || event || 'Bilinmeyen İşlem'
}

const getEventBadgeClass = (event) => {
  const classes = {
    'created': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'updated': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'deleted': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    'restored': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    'login': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
    'logout': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
    'failed_login': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    'password_reset': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    'registered': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'email_verified': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'password_changed': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    'email_changed': 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
    'profile_updated': 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200'
  }
  return classes[event] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
}

const editUser = () => {
  router.visit(route('panel.users.edit', props.user.id))
}

const deleteUser = () => {
  openDeleteModal(props.user, {
    title: 'Kullanıcı Silme Onayı',
    description: `"${props.user.full_name}" kullanıcısını silmek istediğinizden emin misiniz?`,
    additionalInfo: 'Bu işlem geri alınamaz ve kullanıcının tüm verileri kalıcı olarak silinecektir.',
    route: 'panel.users.destroy'
  })
}
</script> 