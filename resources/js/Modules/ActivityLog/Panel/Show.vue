<template>
  <PanelLayout 
    title="Log Detayı" 
    page-title="Log Detayı"
    :breadcrumbs="[
      { title: 'Aktivite Logları', url: '/panel/activity-logs' },
      { title: `Log #${log.id}` }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Log Detayı"
      description="Aktivite log detaylarını görüntüleyin"
    >
      <template #actions>
      </template>
    </PageHeader>

    <!-- Log Details -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Log #{{ log.id }}
          </h3>
          <span :class="getEventBadgeClass(log.resolved_event || log.event)" class="px-3 py-1 rounded-full text-sm font-semibold">
            {{ getEventLabel(log.resolved_event || log.event) }}
          </span>
        </div>
      </div>
      
      <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Basic Information -->
          <div class="space-y-4">
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Temel Bilgiler</h4>
            
            <div class="space-y-3">
              <div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Kullanıcı:</span>
                <div class="mt-1 flex items-center">
                  <div class="flex-shrink-0 h-8 w-8">
                    <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                      <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ getUserInitials(log.causer?.full_name || log.user_name || 'Sistem') }}
                      </span>
                    </div>
                  </div>
                  <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ log.causer?.full_name || log.user_name || 'Sistem' }}
                    </div>
                    <div v-if="log.causer?.email" class="text-sm text-gray-500 dark:text-gray-400">
                      {{ log.causer.email }}
                    </div>
                  </div>
                </div>
              </div>

              <div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Model:</span>
                <div class="mt-1">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                    {{ log.model_name }}
                  </span>
                </div>
              </div>

              <div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Tarih:</span>
                <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ formatDate(log.created_at) }} {{ formatTime(log.created_at) }}
                </div>
              </div>

              <div v-if="log.ip_address">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">IP Adresi:</span>
                <div class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">
                  {{ log.ip_address }}
                </div>
              </div>
            </div>
          </div>

          <!-- Additional Information -->
          <div class="space-y-4">
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Ek Bilgiler</h4>
            
            <div class="space-y-3">
              <div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Açıklama:</span>
                <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                  {{ log.description }}
                </div>
              </div>

              <div v-if="log.user_agent">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">User Agent:</span>
                <div class="mt-1 text-xs text-gray-600 dark:text-gray-400 break-all">
                  {{ log.user_agent }}
                </div>
              </div>

              <div v-if="log.batch_uuid">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Batch UUID:</span>
                <div class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">
                  {{ log.batch_uuid }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Changes Section -->
        <div v-if="log.formatted_changes && log.formatted_changes.length" class="mt-8">
          <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">
            Değişiklikler ({{ log.formatted_changes.length }} alan)
          </h4>
          <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <div class="space-y-4">
              <div v-for="change in log.formatted_changes" :key="change.field" class="border-b border-gray-200 dark:border-gray-600 pb-3 last:border-b-0">
                <div class="flex items-center justify-between mb-2">
                  <span class="font-medium text-gray-900 dark:text-gray-100 text-sm">
                    {{ change.field_name }}
                    <span v-if="change.is_important" class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200">
                      Önemli
                    </span>
                  </span>
                </div>
                <div class="flex items-center space-x-3">
                  <div class="flex-1">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Eski Değer:</div>
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded px-3 py-2 text-sm">
                      <span class="text-red-700 dark:text-red-300">{{ change.old_value }}</span>
                    </div>
                  </div>
                  <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Yeni Değer:</div>
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded px-3 py-2 text-sm">
                      <span class="text-green-700 dark:text-green-300">{{ change.new_value }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- No Changes Message -->
        <div v-else-if="log.event === 'updated'" class="mt-8">
          <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                  Değişiklik Detayı Bulunamadı
                </h3>
                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                  Bu güncelleme işleminde hangi alanların değiştiği detayı kaydedilmemiş. Bu durum genellikle sistem güncellemelerinde veya otomatik işlemlerde görülür.
                </div>
              </div>
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
import { useFormat } from '@/Composables/useFormat'

const props = defineProps({ 
  log: {
    type: Object,
    required: true
  }
})

const { formatDate, formatTime } = useFormat()

const getUserInitials = (name) => {
  if (!name || name === 'Sistem') return 'S'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const getEventLabel = (event) => {
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

</script> 