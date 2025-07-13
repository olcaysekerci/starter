<template>
  <PanelLayout 
    title="Ayarlar" 
    page-title="Ayarlar"
    :breadcrumbs="[
      { title: 'Ayarlar' }
    ]"
  >
    <PageHeader
      title="Ayarlar"
      description="Sistem ayarlarını yönetin ve yapılandırın."
    />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Settings Navigation -->
      <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Ayarlar Menüsü</h3>
          </div>
          <div class="p-4 space-y-2">
            <Link 
              :href="route('panel.settings.app.index')"
              class="flex items-center px-4 py-2 rounded-md text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-gray-700"
              :class="route().current('panel.settings.app.*') ? 'bg-indigo-50 dark:bg-gray-800 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300'"
            >
              <GlobeAltIcon class="w-4 h-4 mr-3" />
              Uygulama Ayarları
            </Link>
            <Link 
              :href="route('panel.settings.mail.index')"
              class="flex items-center px-4 py-2 rounded-md text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-gray-700"
              :class="route().current('panel.settings.mail.*') ? 'bg-indigo-50 dark:bg-gray-800 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300'"
            >
              <EnvelopeIcon class="w-4 h-4 mr-3" />
              Mail Ayarları
            </Link>
            <Link 
              :href="route('panel.settings.profile.index')"
              class="flex items-center px-4 py-2 rounded-md text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-gray-700"
              :class="route().current('panel.settings.profile.index') ? 'bg-indigo-50 dark:bg-gray-800 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300'"
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- App Settings Card -->
              <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-6 rounded-lg border border-blue-200 dark:border-blue-700">
                <div class="flex items-center mb-4">
                  <GlobeAltIcon class="w-8 h-8 text-blue-600 dark:text-blue-400 mr-3" />
                  <div>
                    <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Uygulama Ayarları</h4>
                    <p class="text-sm text-blue-700 dark:text-blue-300">Site bilgileri ve genel ayarlar</p>
                  </div>
                </div>
                <p class="text-blue-600 dark:text-blue-400 text-sm mb-4">
                  Site adı, açıklama, logo ve iletişim bilgilerini yönetin.
                </p>
                <Link 
                  :href="route('panel.settings.app.index')"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors"
                >
                  Düzenle
                  <ArrowRightIcon class="w-4 h-4 ml-2" />
                </Link>
              </div>

              <!-- Mail Settings Card -->
              <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-6 rounded-lg border border-green-200 dark:border-green-700">
                <div class="flex items-center mb-4">
                  <EnvelopeIcon class="w-8 h-8 text-green-600 dark:text-green-400 mr-3" />
                  <div>
                    <h4 class="text-lg font-semibold text-green-900 dark:text-green-100">Mail Ayarları</h4>
                    <p class="text-sm text-green-700 dark:text-green-300">SMTP ve mail konfigürasyonu</p>
                  </div>
                </div>
                <p class="text-green-600 dark:text-green-400 text-sm mb-4">
                  SMTP ayarları, mail test ve gönderim konfigürasyonu.
                </p>
                <Link 
                  :href="route('panel.settings.mail.index')"
                  class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors"
                >
                  Düzenle
                  <ArrowRightIcon class="w-4 h-4 ml-2" />
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PanelLayout>
</template>

<script setup>
import { 
  UserIcon, 
  ShieldCheckIcon, 
  GlobeAltIcon, 
  EnvelopeIcon, 
  ArrowRightIcon 
} from '@heroicons/vue/24/outline'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import { Link } from '@inertiajs/vue3'
</script> 