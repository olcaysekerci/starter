<template>
  <PanelLayout 
    title="Mail Ayarları" 
    page-title="Mail Ayarları"
    :breadcrumbs="[
      { title: 'Dashboard', url: route('panel.dashboard') },
      { title: 'Ayarlar', url: route('panel.settings.index') },
      { title: 'Mail Ayarları' }
    ]"
  >
    <PageHeader
      title="Mail Ayarları"
      description="SMTP ayarları ve mail konfigürasyonunu yönetin."
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

    <form @submit.prevent="saveSettings" class="space-y-6">
      <!-- Mail Durumu -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Mail Sistemi Durumu</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Mail sisteminin aktif olup olmadığını kontrol edin.
          </p>
        </div>
        <div class="p-6">
          <div class="flex items-center">
            <input
              id="enabled"
              v-model="form.enabled"
              type="checkbox"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            />
            <label for="enabled" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
              Mail sistemi aktif
            </label>
          </div>
          <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            Bu seçenek kapalıysa, sistem mail gönderimi yapmayacaktır.
          </p>
        </div>
      </div>

      <!-- SMTP Ayarları -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">SMTP Ayarları</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Mail sunucu konfigürasyonunu yapılandırın.
          </p>
        </div>
        <div class="p-6 space-y-6">
          <!-- Mail Sürücüsü -->
          <div>
            <label for="driver" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Mail Sürücüsü *
            </label>
            <select
              id="driver"
              v-model="form.driver"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
              required
            >
              <option value="smtp">SMTP</option>
              <option value="mailpit">Mailpit (Development)</option>
              <option value="log">Log (Development)</option>
              <option value="array">Array (Testing)</option>
            </select>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Mail gönderimi için kullanılacak sürücüyü seçin.
            </p>
          </div>

          <!-- SMTP Sunucu -->
          <div>
            <label for="host" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              SMTP Sunucu Adresi *
            </label>
            <input
              id="host"
              v-model="form.host"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
              placeholder="smtp.gmail.com"
              required
            />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              SMTP sunucunuzun adresini girin (örn: smtp.gmail.com).
            </p>
          </div>

          <!-- SMTP Port -->
          <div>
            <label for="port" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              SMTP Port *
            </label>
            <input
              id="port"
              v-model="form.port"
              type="number"
              min="1"
              max="65535"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
              placeholder="587"
              required
            />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              SMTP port numarasını girin (genellikle 587 veya 465).
            </p>
          </div>

          <!-- Kullanıcı Adı -->
          <div>
            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              SMTP Kullanıcı Adı
            </label>
            <input
              id="username"
              v-model="form.username"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
              placeholder="kullanici@example.com"
            />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              SMTP sunucu kullanıcı adınızı girin.
            </p>
          </div>

          <!-- Şifre -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              SMTP Şifre
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
              placeholder="Şifrenizi girin"
            />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              SMTP sunucu şifrenizi girin (güvenlik için şifrelenir).
            </p>
          </div>

          <!-- Şifreleme -->
          <div>
            <label for="encryption" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              SMTP Şifreleme
            </label>
            <select
              id="encryption"
              v-model="form.encryption"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
            >
              <option value="tls">TLS</option>
              <option value="ssl">SSL</option>
              <option value="">Yok</option>
            </select>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              SMTP bağlantısı için şifreleme türünü seçin.
            </p>
          </div>
        </div>
      </div>

      <!-- Gönderen Bilgileri -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Gönderen Bilgileri</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Mail gönderiminde kullanılacak gönderen bilgilerini ayarlayın.
          </p>
        </div>
        <div class="p-6 space-y-6">
          <!-- Gönderen E-posta -->
          <div>
            <label for="from_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Gönderen E-posta Adresi *
            </label>
            <input
              id="from_address"
              v-model="form.from_address"
              type="email"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
              placeholder="noreply@example.com"
              required
            />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Mail gönderiminde kullanılacak e-posta adresi.
            </p>
          </div>

          <!-- Gönderen Adı -->
          <div>
            <label for="from_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Gönderen Adı *
            </label>
            <input
              id="from_name"
              v-model="form.from_name"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
              placeholder="Site Adı"
              required
            />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Mail gönderiminde görünecek gönderen adı.
            </p>
          </div>
        </div>
      </div>

      <!-- Test Mail -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Mail Test</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Mail ayarlarınızı test etmek için bir test maili gönderin.
          </p>
        </div>
        <div class="p-6">
          <div class="flex items-end space-x-4">
            <div class="flex-1">
              <label for="test_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Test E-posta Adresi
              </label>
              <input
                id="test_email"
                v-model="testEmail"
                type="email"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                placeholder="test@example.com"
              />
            </div>
            <ActionButton 
              @click="sendTestMail" 
              variant="secondary" 
              size="sm"
              :loading="sendingTest"
              :disabled="sendingTest || !testEmail"
            >
              <PaperAirplaneIcon class="w-4 h-4 mr-2" />
              {{ sendingTest ? 'Gönderiliyor...' : 'Test Mail Gönder' }}
            </ActionButton>
          </div>
          <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            Test maili göndererek ayarlarınızın doğru çalışıp çalışmadığını kontrol edin.
          </p>
        </div>
      </div>

      <!-- Kaydet Butonu -->
      <div class="flex justify-end">
        <ActionButton 
          type="submit"
          variant="primary" 
          size="lg"
          :loading="saving"
          :disabled="saving"
        >
          <CheckIcon class="w-5 h-5 mr-2" />
          {{ saving ? 'Kaydediliyor...' : 'Mail Ayarlarını Kaydet' }}
        </ActionButton>
      </div>
    </form>
  </PanelLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import { CheckIcon, PaperAirplaneIcon } from '@heroicons/vue/24/outline'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'

const props = defineProps({
  mailSettings: {
    type: Object,
    required: true
  }
})

// Reactive data
const saving = ref(false)
const sendingTest = ref(false)
const testEmail = ref('')

// Form data
const form = reactive({
  driver: props.mailSettings.driver || 'smtp',
  host: props.mailSettings.host || '',
  port: props.mailSettings.port || 587,
  username: props.mailSettings.username || '',
  password: props.mailSettings.password || '',
  encryption: props.mailSettings.encryption || 'tls',
  from_address: props.mailSettings.from_address || '',
  from_name: props.mailSettings.from_name || '',
  enabled: props.mailSettings.enabled !== false
})

// Methods
const saveSettings = () => {
  saving.value = true
  
  router.post(route('panel.settings.mail.update'), form, {
    onSuccess: () => {
      saving.value = false
    },
    onError: () => {
      saving.value = false
    }
  })
}

const sendTestMail = () => {
  if (!testEmail.value) return
  
  sendingTest.value = true
  
  router.post(route('panel.settings.mail.test'), {
    email: testEmail.value
  }, {
    onSuccess: () => {
      sendingTest.value = false
      testEmail.value = ''
    },
    onError: () => {
      sendingTest.value = false
    }
  })
}
</script> 