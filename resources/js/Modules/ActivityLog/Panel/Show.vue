<template>
  <PanelLayout 
    title="Log Detayı" 
    page-title="Log Detayı"
    :breadcrumbs="[
      { title: 'Dashboard', url: '/dashboard' },
      { title: 'Aktivite Logları', url: '/panel/activity-logs' },
      { title: `Log #${log.id}` }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Log Detayı"
      :description="`Log #${log.id} detaylı bilgileri`"
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
      </template>
    </PageHeader>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Log Bilgileri -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Log Bilgileri</h3>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Log ID</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ log.id }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event</label>
                <p class="mt-1 text-sm">
                  <span :class="getEventBadgeClass(log.event)">
                    {{ getEventLabel(log.event) }}
                  </span>
                </p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Açıklama</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ log.formatted_description || log.description }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Model</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ log.model_name || 'Sistem' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- User Information -->
        <div v-if="log.hasUser" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Kullanıcı Bilgileri</h3>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kullanıcı</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ log.user_name || 'Bilinmeyen' }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kullanıcı ID</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ log.causer_id }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Changes Information -->
        <div v-if="log.has_changes" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Değişiklikler</h3>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div v-for="change in log.formatted_changes" :key="change.field" class="border-b border-gray-200 dark:border-gray-700 pb-3 last:border-b-0">
                <div class="flex justify-between items-start">
                  <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ change.field_name }}</h4>
                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Eski Değer</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ change.old_value || 'Boş' }}</p>
                      </div>
                      <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Yeni Değer</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ change.new_value || 'Boş' }}</p>
                      </div>
                    </div>
                  </div>
                  <div v-if="change.is_important" class="ml-3">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                      Önemli
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Technical Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Teknik Bilgiler</h3>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Oluşturulma Tarihi</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ formatDate(log.created_at) }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">IP Adresi</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ log.ip_address || 'Bilinmiyor' }}</p>
              </div>
              <div v-if="log.user_agent" class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">User Agent</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 break-all">{{ log.user_agent }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1 space-y-6">
        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Hızlı İşlemler</h3>
          </div>
          <div class="p-6 space-y-3">
            <ActionButton @click="goBack" variant="secondary" class="w-full">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
              </svg>
              Geri Dön
            </ActionButton>
          </div>
        </div>

        <!-- Log Statistics -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Log Bilgileri</h3>
          </div>
          <div class="p-6 space-y-4">
            <div class="flex justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Event Türü</span>
              <span :class="getEventBadgeClass(log.event)" class="text-xs">
                {{ getEventLabel(log.event) }}
              </span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Değişiklik</span>
              <span v-if="log.has_changes" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                Var
              </span>
              <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                Yok
              </span>
            </div>
            <div v-if="log.is_password_change" class="flex justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Şifre Değişikliği</span>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                Evet
              </span>
            </div>
            <div v-if="log.is_email_change" class="flex justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Email Değişikliği</span>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                Evet
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PanelLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'

const props = defineProps({
  log: {
    type: Object,
    required: true
  }
})

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('tr-TR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
}

const getEventLabel = (event) => {
  const labels = {
    'created': 'Oluşturuldu',
    'updated': 'Güncellendi',
    'deleted': 'Silindi',
    'restored': 'Geri Yüklendi',
    'login': 'Giriş Yapıldı',
    'logout': 'Çıkış Yapıldı',
    'profile_updated': 'Profil Güncellendi',
    'password_changed': 'Şifre Değiştirildi'
  }
  
  return labels[event] || event
}

const getEventBadgeClass = (event) => {
  const classes = {
    'created': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800',
    'updated': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800',
    'deleted': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800',
    'restored': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800',
    'login': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800',
    'logout': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800',
    'profile_updated': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800',
    'password_changed': 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800'
  }
  
  return classes[event] || 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800'
}

const goBack = () => {
  router.visit('/panel/activity-logs')
}
</script> 