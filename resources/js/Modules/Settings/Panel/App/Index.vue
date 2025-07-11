<template>
  <PanelLayout 
    title="Uygulama Ayarları" 
    page-title="Uygulama Ayarları"
    :breadcrumbs="[
      { title: 'Dashboard', url: route('panel.dashboard') },
      { title: 'Ayarlar', url: route('panel.settings.index') },
      { title: 'Uygulama Ayarları' }
    ]"
  >
    <PageHeader
      title="Uygulama Ayarları"
      description="Site adı, açıklama, logo ve iletişim bilgilerini yönetin."
    >
      <template #actions>
        <ActionButton 
          @click="saveSettings" 
          variant="primary" 
          size="sm"
          :loading="saving"
          :disabled="saving"
        >
          <CheckIcon class="w-4 h-4 mr-2" />
          {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
        </ActionButton>
      </template>
    </PageHeader>

    <!-- Form Card -->
    <FormCard
      title="Uygulama Ayarları"
      description="Site adı, açıklama, logo ve iletişim bilgilerini yönetin."
      submit-text="Ayarları Kaydet"
      :processing="saving"
      @submit="saveSettings"
    >
      <div class="space-y-8">
        <!-- Site Bilgileri -->
        <div>
          <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Site Bilgileri</h4>
          <div class="space-y-6">
            <!-- Site Adı -->
            <div>
              <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Site Adı *
              </label>
              <input
                id="site_name"
                v-model="form.site_name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                placeholder="Site adınızı girin"
                required
              />
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Bu ad tarayıcı sekmesinde ve e-posta konularında görünür.
              </p>
            </div>

            <!-- Site Açıklaması -->
            <div>
              <label for="site_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Site Açıklaması
              </label>
              <textarea
                id="site_description"
                v-model="form.site_description"
                :rows="3"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                placeholder="Sitenizin kısa açıklamasını girin"
              ></textarea>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                SEO için kullanılır ve arama motorlarında görünür.
              </p>
            </div>

            <!-- Site Logo -->
            <div>
              <label for="site_logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Site Logo URL
              </label>
              <input
                id="site_logo"
                v-model="form.site_logo"
                type="url"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                placeholder="https://example.com/logo.png"
              />
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Logo için tam URL adresi girin (PNG, JPG, SVG önerilir).
              </p>
            </div>

            <!-- Site Favicon -->
            <div>
              <label for="site_favicon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Site Favicon URL
              </label>
              <input
                id="site_favicon"
                v-model="form.site_favicon"
                type="url"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                placeholder="https://example.com/favicon.ico"
              />
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Favicon için tam URL adresi girin (ICO, PNG önerilir).
              </p>
            </div>
          </div>
        </div>

        <!-- İletişim Bilgileri -->
        <div>
          <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">İletişim Bilgileri</h4>
          <div class="space-y-6">
            <!-- E-posta -->
            <div>
              <label for="contact_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                İletişim E-posta Adresi
              </label>
              <input
                id="contact_email"
                v-model="form.contact_email"
                type="email"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                placeholder="info@example.com"
              />
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Genel iletişim için kullanılacak e-posta adresi.
              </p>
            </div>

            <!-- Telefon -->
            <div>
              <label for="contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                İletişim Telefon Numarası
              </label>
              <input
                id="contact_phone"
                v-model="form.contact_phone"
                type="tel"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                placeholder="+90 555 123 45 67"
              />
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Müşteri hizmetleri için telefon numarası.
              </p>
            </div>

            <!-- Adres -->
            <div>
              <label for="contact_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                İletişim Adresi
              </label>
              <textarea
                id="contact_address"
                v-model="form.contact_address"
                :rows="3"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                placeholder="Şirket adresinizi girin"
              ></textarea>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Fiziksel adres bilgileri.
              </p>
            </div>
          </div>
        </div>
      </div>
    </FormCard>
  </PanelLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import { CheckIcon } from '@heroicons/vue/24/outline'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import FormCard from '@/Components/Panel/Forms/FormCard.vue'

const props = defineProps({
  appSettings: {
    type: Object,
    required: true
  }
})

// Reactive data
const saving = ref(false)

// Form data
const form = reactive({
  site_name: props.appSettings.site_name || '',
  site_description: props.appSettings.site_description || '',
  site_logo: props.appSettings.site_logo || '',
  site_favicon: props.appSettings.site_favicon || '',
  contact_email: props.appSettings.contact_email || '',
  contact_phone: props.appSettings.contact_phone || '',
  contact_address: props.appSettings.contact_address || ''
})

// Methods
const saveSettings = () => {
  saving.value = true
  
  router.post(route('panel.settings.app.update'), form, {
    onSuccess: () => {
      saving.value = false
    },
    onError: () => {
      saving.value = false
    }
  })
}
</script> 