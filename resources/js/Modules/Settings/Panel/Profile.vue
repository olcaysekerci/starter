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
      description="Kişisel bilgilerinizi güncelleyin ve hesap güvenliğinizi yönetin."
    >
      <template #actions>
        <ActionButton 
          @click="saveProfile" 
          variant="primary" 
          size="sm"
          :loading="saving"
          :disabled="!form.isDirty"
        >
          <UserIcon class="w-4 h-4 mr-2" />
          Kaydet
        </ActionButton>
      </template>
    </PageHeader>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Sol Kolon - Profil Bilgileri -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Kişisel Bilgiler -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Kişisel Bilgiler</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Ad, soyad ve iletişim bilgilerinizi güncelleyin</p>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Ad *
                </label>
                <input
                  v-model="form.first_name"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                  :class="{ 'border-red-500': form.errors.first_name }"
                />
                <p v-if="form.errors.first_name" class="mt-1 text-sm text-red-600">
                  {{ form.errors.first_name }}
                </p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Soyad *
                </label>
                <input
                  v-model="form.last_name"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                  :class="{ 'border-red-500': form.errors.last_name }"
                />
                <p v-if="form.errors.last_name" class="mt-1 text-sm text-red-600">
                  {{ form.errors.last_name }}
                </p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  E-posta *
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                  :class="{ 'border-red-500': form.errors.email }"
                />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                  {{ form.errors.email }}
                </p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Telefon
                </label>
                <input
                  v-model="form.phone"
                  type="tel"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                  :class="{ 'border-red-500': form.errors.phone }"
                  placeholder="+90 555 123 45 67"
                />
                <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">
                  {{ form.errors.phone }}
                </p>
              </div>
              
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Adres
                </label>
                <textarea
                  v-model="form.address"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                  :class="{ 'border-red-500': form.errors.address }"
                  placeholder="Adres bilgilerinizi girin..."
                ></textarea>
                <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">
                  {{ form.errors.address }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Şifre Değiştirme -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Şifre Değiştir</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Hesap güvenliğiniz için şifrenizi güncelleyin</p>
          </div>
          <div class="p-6">
            <div class="space-y-6">
              <!-- Mevcut Şifre -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Mevcut Şifre
                </label>
                <div class="relative">
                  <input
                    v-model="passwordForm.current_password"
                    :type="showCurrentPassword ? 'text' : 'password'"
                    class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    :class="{ 'border-red-500': passwordForm.errors.current_password }"
                  />
                  <button
                    type="button"
                    @click="showCurrentPassword = !showCurrentPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                  >
                    <EyeIcon v-if="!showCurrentPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" />
                    <EyeSlashIcon v-else class="h-5 w-5 text-gray-400 hover:text-gray-600" />
                  </button>
                </div>
                <p v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-600">
                  {{ passwordForm.errors.current_password }}
                </p>
              </div>
              
              <!-- Yeni Şifre ve Tekrarı -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Yeni Şifre
                  </label>
                  <div class="relative">
                    <input
                      v-model="passwordForm.password"
                      :type="showNewPassword ? 'text' : 'password'"
                      class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                      :class="{ 'border-red-500': passwordForm.errors.password }"
                    />
                    <button
                      type="button"
                      @click="showNewPassword = !showNewPassword"
                      class="absolute inset-y-0 right-0 pr-3 flex items-center"
                    >
                      <EyeIcon v-if="!showNewPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" />
                      <EyeSlashIcon v-else class="h-5 w-5 text-gray-400 hover:text-gray-600" />
                    </button>
                  </div>
                  <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-600">
                    {{ passwordForm.errors.password }}
                  </p>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Yeni Şifre (Tekrar)
                  </label>
                  <div class="relative">
                    <input
                      v-model="passwordForm.password_confirmation"
                      :type="showConfirmPassword ? 'text' : 'password'"
                      class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                      :class="{ 'border-red-500': passwordForm.errors.password_confirmation }"
                    />
                    <button
                      type="button"
                      @click="showConfirmPassword = !showConfirmPassword"
                      class="absolute inset-y-0 right-0 pr-3 flex items-center"
                    >
                      <EyeIcon v-if="!showConfirmPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" />
                      <EyeSlashIcon v-else class="h-5 w-5 text-gray-400 hover:text-gray-600" />
                    </button>
                  </div>
                  <p v-if="passwordForm.errors.password_confirmation" class="mt-1 text-sm text-red-600">
                    {{ passwordForm.errors.password_confirmation }}
                  </p>
                </div>
              </div>
              
              <!-- Şifre Değiştir Butonu -->
              <div>
                <ActionButton 
                  @click="changePassword" 
                  variant="secondary" 
                  size="sm"
                  :loading="changingPassword"
                  :disabled="!passwordForm.isDirty"
                >
                  <KeyIcon class="w-4 h-4 mr-2" />
                  Şifre Değiştir
                </ActionButton>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sağ Kolon - Hesap İstatistikleri -->
      <div class="space-y-6">
        <!-- Hesap İstatistikleri -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Hesap Bilgileri</h3>
          </div>
          <div class="p-6 space-y-4">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600 dark:text-gray-400">Üyelik Tarihi</span>
              <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ formatDate(user.created_at) }}
              </span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600 dark:text-gray-400">Son Güncelleme</span>
              <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ formatDate(user.updated_at) }}
              </span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600 dark:text-gray-400">Durum</span>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                Aktif
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PanelLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import { UserIcon, KeyIcon, EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

// Form states
const saving = ref(false)
const changingPassword = ref(false)

// Password visibility states
const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

// Profile form
const form = useForm({
  first_name: props.user.first_name || '',
  last_name: props.user.last_name || '',
  email: props.user.email || '',
  phone: props.user.phone || '',
  address: props.user.address || ''
})

// Password form
const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
})

// Methods
const saveProfile = () => {
  saving.value = true
  form.put(route('panel.settings.profile.update'), {
    onSuccess: () => {
      saving.value = false
    },
    onError: () => {
      saving.value = false
    }
  })
}

const changePassword = () => {
  changingPassword.value = true
  passwordForm.put(route('panel.settings.profile.password'), {
    onSuccess: () => {
      changingPassword.value = false
      passwordForm.reset()
    },
    onError: () => {
      changingPassword.value = false
    }
  })
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script> 