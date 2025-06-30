<template>
  <PanelLayout 
    title="Profil Ayarları" 
    page-title="Profil Ayarları"
    :breadcrumbs="[
      { title: 'Dashboard', url: route('panel.dashboard') },
      { title: 'Ayarlar', url: route('panel.settings.index') },
      { title: 'Profil Ayarları' }
    ]"
  >
    <PageHeader
      title="Profil Ayarları"
      description="Kişisel bilgilerinizi güncelleyin."
    >
      <template #actions>
        <ActionButton variant="primary" size="sm">
          <UserIcon class="w-4 h-4 mr-2" />
          Kaydet
        </ActionButton>
      </template>
    </PageHeader>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Kişisel Bilgiler</h3>
      </div>
      <div class="p-6">
        <p class="text-gray-600 dark:text-gray-400">
          Profil ayarları sayfası içeriği buraya gelecek.
        </p>
      </div>
    </div>
  </PanelLayout>
</template>

<script setup>
import { UserIcon } from '@heroicons/vue/24/outline'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
</script> 