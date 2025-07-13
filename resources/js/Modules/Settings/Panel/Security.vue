<template>
  <PanelLayout 
    title="Güvenlik Ayarları" 
    page-title="Güvenlik Ayarları"
    :breadcrumbs="[
      { title: 'Ayarlar', url: route('panel.settings.index') },
      { title: 'Güvenlik Ayarları' }
    ]"
  >
    <PageHeader
      title="Güvenlik Ayarları"
      description="Hesap güvenliğinizi yönetin."
    >
      <template #actions>
        <ActionButton variant="primary" size="sm">
          <ShieldCheckIcon class="w-4 h-4 mr-2" />
          Kaydet
        </ActionButton>
      </template>
    </PageHeader>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Güvenlik Seçenekleri</h3>
      </div>
      <div class="p-6">
        <p class="text-gray-600 dark:text-gray-400">
          Güvenlik ayarları sayfası içeriği buraya gelecek.
        </p>
      </div>
    </div>
  </PanelLayout>
</template>

<script setup>
import { ShieldCheckIcon } from '@heroicons/vue/24/outline'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
</script> 