<template>
  <PanelLayout>
    <template #header>
      <PageHeader title="Mail Detayı">
        <template #actions>
          <Button @click="goBack" variant="secondary">
            <ArrowLeftIcon class="w-4 h-4 mr-2" />
            Geri Dön
          </Button>
          <Button
            v-if="mailLog.status === 'failed' && mailLog.can_retry"
            @click="retryMail"
            variant="warning"
          >
            <ArrowPathIcon class="w-4 h-4 mr-2" />
            Yeniden Dene
          </Button>
        </template>
      </PageHeader>
    </template>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Mail Detayları -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Temel Bilgiler -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Mail Bilgileri
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Alıcı
              </label>
              <p class="text-sm text-gray-900 dark:text-gray-100">{{ mailLog.recipient }}</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Konu
              </label>
              <p class="text-sm text-gray-900 dark:text-gray-100">{{ mailLog.subject }}</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Tür
              </label>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                {{ mailLog.type }}
              </span>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Durum
              </label>
              <span :class="mailLog.status_badge_class" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                {{ mailLog.status_label }}
              </span>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Oluşturulma Tarihi
              </label>
              <p class="text-sm text-gray-900 dark:text-gray-100">
                {{ formatDate(mailLog.created_at) }}
              </p>
            </div>
            
            <div v-if="mailLog.sent_at">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Gönderim Tarihi
              </label>
              <p class="text-sm text-gray-900 dark:text-gray-100">
                {{ formatDate(mailLog.sent_at) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Mail İçeriği -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Mail İçeriği
          </h3>
          
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <pre class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ mailLog.content }}</pre>
          </div>
        </div>

        <!-- Hata Mesajı -->
        <div v-if="mailLog.error_message" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-red-600 dark:text-red-400 mb-4">
            Hata Mesajı
          </h3>
          
          <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
            <p class="text-sm text-red-800 dark:text-red-200">{{ mailLog.error_message }}</p>
          </div>
        </div>
      </div>

      <!-- Yan Panel -->
      <div class="space-y-6">
        <!-- Teknik Bilgiler -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Teknik Bilgiler
          </h3>
          
          <div class="space-y-3">
            <div v-if="mailLog.ip_address">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                IP Adresi
              </label>
              <p class="text-sm text-gray-900 dark:text-gray-100">{{ mailLog.ip_address }}</p>
            </div>
            
            <div v-if="mailLog.user_agent">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                User Agent
              </label>
              <p class="text-sm text-gray-900 dark:text-gray-100 break-all">{{ mailLog.user_agent }}</p>
            </div>
            
            <div v-if="mailLog.retry_count > 0">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Deneme Sayısı
              </label>
              <p class="text-sm text-gray-900 dark:text-gray-100">{{ mailLog.retry_count }}</p>
            </div>
            
            <div v-if="mailLog.last_retry_at">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Son Deneme
              </label>
              <p class="text-sm text-gray-900 dark:text-gray-100">
                {{ formatDate(mailLog.last_retry_at) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Metadata -->
        <div v-if="mailLog.metadata && Object.keys(mailLog.metadata).length > 0" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Ek Veriler
          </h3>
          
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <pre class="text-sm text-gray-900 dark:text-gray-100">{{ JSON.stringify(mailLog.metadata, null, 2) }}</pre>
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
import Button from '@/Components/Global/Button.vue'
import {
  ArrowLeftIcon,
  ArrowPathIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  mailLog: Object,
})

// Methods
const goBack = () => {
  router.get(route('panel.mail-notifications.index'))
}

const retryMail = async () => {
  await router.post(route('panel.mail-notifications.retry-single', props.mailLog.id))
}

const formatDate = (date) => {
  return new Date(date).toLocaleString('tr-TR')
}
</script> 