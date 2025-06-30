<template>
  <PanelLayout 
    title="Ayarlar" 
    page-title="Ayarlar"
    :breadcrumbs="[
      { title: 'Dashboard', url: route('panel.dashboard') },
      { title: 'Ayarlar' }
    ]"
  >
    <PageHeader
      title="Ayarlar"
      description="Sistem ayarlarını yönetin."
    >
      <template #actions>
        <ActionButton variant="primary" size="sm">
          <Cog6ToothIcon class="w-4 h-4 mr-2" />
          Kaydet
        </ActionButton>
      </template>
    </PageHeader>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Settings Navigation -->
      <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Ayarlar Menüsü</h3>
          </div>
          <div class="p-4 space-y-2">
            <Link 
              :href="route('panel.settings.profile')"
              class="flex items-center px-4 py-2 rounded-md text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-gray-700"
              :class="route().current('panel.settings.profile') ? 'bg-indigo-50 dark:bg-gray-800 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300'"
            >
              <UserIcon class="w-4 h-4 mr-3" />
              Profil Ayarları
            </Link>
            <Link 
              :href="route('panel.settings.security')"
              class="flex items-center px-4 py-2 rounded-md text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-gray-700"
              :class="route().current('panel.settings.security') ? 'bg-indigo-50 dark:bg-gray-800 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300'"
            >
              <ShieldCheckIcon class="w-4 h-4 mr-3" />
              Güvenlik
            </Link>
          </div>
        </div>
      </div>

      <!-- Settings Content -->
      <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Genel Ayarlar</h3>
          </div>
          <div class="p-6">
            <p class="text-gray-600 dark:text-gray-400">
              Sol menüden bir ayar kategorisi seçin.
            </p>
          </div>
        </div>
      </div>
    </div>
  </PanelLayout>
</template>

<script setup>
import { UserIcon, ShieldCheckIcon, Cog6ToothIcon } from '@heroicons/vue/24/outline'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import { Link } from '@inertiajs/vue3'
</script> 