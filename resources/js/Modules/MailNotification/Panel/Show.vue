<template>
  <PanelLayout 
    title="Mail Detayı" 
    page-title="Mail Detayı"
    :breadcrumbs="[
      { title: 'Mail Bildirimleri', url: route('panel.mail-notifications.index') },
      { title: 'Mail Detayı' }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Mail Detayı"
      :description="`${mailLog.recipient} adresine gönderilen mail detayları`"
    >
      <template #actions>
        <ActionButton 
          @click="resendMail" 
          variant="primary" 
          size="sm"
          :disabled="mailLog.status === 'sent'"
        >
          <EnvelopeIcon class="w-4 h-4 mr-2" />
          Yeniden Gönder
        </ActionButton>
      </template>
    </PageHeader>

    <!-- Mail Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Mail Content -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Mail İçeriği</h3>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Konu</label>
                <div class="text-sm text-gray-900 dark:text-gray-100 font-medium">
                  {{ mailLog.subject }}
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">İçerik</label>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                  <div class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                    {{ mailLog.content }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Error Details -->
        <div v-if="mailLog.error_message" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-red-600 dark:text-red-400">Hata Detayları</h3>
          </div>
          <div class="p-6">
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
              <div class="text-sm text-red-800 dark:text-red-200">
                {{ mailLog.error_message }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Mail Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Mail Bilgileri</h3>
          </div>
          <div class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alıcı</label>
              <div class="text-sm text-gray-900 dark:text-gray-100">
                {{ mailLog.recipient }}
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gönderen</label>
              <div class="text-sm text-gray-900 dark:text-gray-100">
                {{ mailLog.from_name }} &lt;{{ mailLog.from_email }}&gt;
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tür</label>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                {{ mailLog.type }}
              </span>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durum</label>
              <span :class="mailLog.status_badge_class" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                {{ mailLog.status_label }}
              </span>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Oluşturulma</label>
              <div class="text-sm text-gray-900 dark:text-gray-100">
                {{ formatDate(mailLog.created_at) }}
              </div>
            </div>
            
            <div v-if="mailLog.sent_at">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gönderilme</label>
              <div class="text-sm text-gray-900 dark:text-gray-100">
                {{ formatDate(mailLog.sent_at) }}
              </div>
            </div>
            
            <div v-if="mailLog.attempts > 0">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deneme Sayısı</label>
              <div class="text-sm text-gray-900 dark:text-gray-100">
                {{ mailLog.attempts }}
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
import {
  EnvelopeIcon
} from '@heroicons/vue/24/outline'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'

// Props
const props = defineProps({
  mailLog: Object,
})

// Methods
const resendMail = async () => {
  await router.post(route('panel.mail-notifications.resend', props.mailLog.id))
}

const formatDate = (date) => {
  return new Date(date).toLocaleString('tr-TR')
}
</script> 