<template>
  <PanelLayout 
    title="Kullanıcı Düzenle" 
    page-title="Kullanıcı Düzenle"
    :breadcrumbs="[
      { title: 'Kullanıcı Yönetimi', url: '/panel/users' },
      { title: user.full_name }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Kullanıcı Düzenle"
      :description="`${user.full_name} kullanıcısının bilgilerini güncelleyin.`"
    >
      <template #actions>
        <ActionButton 
          @click="saveUser" 
          variant="primary" 
          size="sm"
          :loading="form.processing"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          Değişiklikleri Kaydet
        </ActionButton>
      </template>
    </PageHeader>

    <!-- Form Card -->
    <FormCard
      title="Kullanıcı Bilgileri"
      :description="`${user.full_name} kullanıcısının bilgilerini güncelleyin.`"
      submit-text="Değişiklikleri Kaydet"
      :processing="form.processing"
      @submit="saveUser"
    >
      <div class="space-y-6">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <FormGroup label="Ad" required>
            <TextInput
              v-model="form.first_name"
              :error="form.errors.first_name"
              placeholder="Kullanıcının adı"
              required
            />
          </FormGroup>

          <FormGroup label="Soyad" required>
            <TextInput
              v-model="form.last_name"
              :error="form.errors.last_name"
              placeholder="Kullanıcının soyadı"
              required
            />
          </FormGroup>

          <FormGroup label="E-posta Adresi" required>
            <TextInput
              v-model="form.email"
              type="email"
              :error="form.errors.email"
              placeholder="ornek@email.com"
              required
            />
          </FormGroup>

          <FormGroup label="Telefon">
            <TextInput
              v-model="form.phone"
              type="tel"
              :error="form.errors.phone"
              placeholder="0555 123 45 67"
              @input="formatPhone"
            />
          </FormGroup>

          <FormGroup label="Yeni Şifre">
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                :error="form.errors.password"
                placeholder="Yeni şifre girin"
                :minlength="6"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-indigo-400 dark:focus:ring-indigo-400 sm:text-sm pr-10"
                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500 dark:border-red-600 dark:focus:border-red-400 dark:focus:ring-red-400': form.errors.password }"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
              >
                <EyeIcon v-if="!showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" />
                <EyeSlashIcon v-else class="h-5 w-5 text-gray-400 hover:text-gray-600" />
              </button>
            </div>
            <p v-if="form.errors.password" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.password }}</p>
          </FormGroup>

          <FormGroup label="Şifre Tekrarı">
            <div class="relative">
              <input
                v-model="form.password_confirmation"
                :type="showPasswordConfirmation ? 'text' : 'password'"
                :error="form.errors.password_confirmation"
                placeholder="Yeni şifreyi tekrar girin"
                :minlength="6"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-indigo-400 dark:focus:ring-indigo-400 sm:text-sm pr-10"
                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500 dark:border-red-600 dark:focus:border-red-400 dark:focus:ring-red-400': form.errors.password_confirmation }"
              />
              <button
                type="button"
                @click="showPasswordConfirmation = !showPasswordConfirmation"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
              >
                <EyeIcon v-if="!showPasswordConfirmation" class="h-5 w-5 text-gray-400 hover:text-gray-600" />
                <EyeSlashIcon v-else class="h-5 w-5 text-gray-400 hover:text-gray-600" />
              </button>
            </div>
            <p v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.password_confirmation }}</p>
          </FormGroup>
        </div>

        <!-- Address -->
        <FormGroup label="Adres">
          <TextArea
            v-model="form.address"
            :error="form.errors.address"
            placeholder="Kullanıcının adres bilgileri"
            :rows="3"
          />
        </FormGroup>

        <!-- Role -->
        <FormGroup label="Rol">
          <Select
            v-model="form.role_id"
            :options="roleOptions"
            :error="form.errors.role_id"
            placeholder="Kullanıcı rolü seçin"
          />
        </FormGroup>
      </div>
    </FormCard>
  </PanelLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import FormGroup from '@/Components/Panel/Forms/FormGroup.vue'
import TextInput from '@/Components/Panel/Forms/TextInput.vue'
import TextArea from '@/Components/Panel/Forms/TextArea.vue'
import Select from '@/Components/Panel/Forms/Select.vue'
import FormCard from '@/Components/Panel/Forms/FormCard.vue'
import { useNotification } from '@/Composables'
import { formatTurkishPhone } from '@/utils'

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  roles: {
    type: Array,
    default: () => []
  }
})

// Composables
const { showSuccess, showError } = useNotification()

// Password visibility states
const showPassword = ref(false)
const showPasswordConfirmation = ref(false)

// Form
const form = useForm({
  first_name: props.user.first_name || '',
  last_name: props.user.last_name || '',
  email: props.user.email || '',
  phone: props.user.phone || '',
  address: props.user.address || '',
  password: '',
  password_confirmation: '',
  role_id: props.user.roles?.[0]?.id || ''
})

// Role options for select
const roleOptions = computed(() => {
  return props.roles.map(role => ({
    value: role.id,
    label: role.display_name || role.name,
    description: role.description
  }))
})

// Methods
const formatPhone = (event) => {
  form.phone = formatTurkishPhone(event.target.value)
}

const saveUser = () => {
  // Client-side validation
  form.clearErrors()
  
  let hasErrors = false
  
  if (!form.first_name.trim()) {
    form.setError('first_name', 'Ad alanı zorunludur.')
    hasErrors = true
  }
  
  if (!form.last_name.trim()) {
    form.setError('last_name', 'Soyad alanı zorunludur.')
    hasErrors = true
  }
  
  if (!form.email.trim()) {
    form.setError('email', 'E-posta alanı zorunludur.')
    hasErrors = true
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    form.setError('email', 'Geçerli bir e-posta adresi giriniz.')
    hasErrors = true
  }
  
  // Şifre değişikliği varsa kontrol et
  if (form.password) {
    if (form.password.length < 6) {
      form.setError('password', 'Şifre en az 6 karakter olmalıdır.')
      hasErrors = true
    } else if (form.password !== form.password_confirmation) {
      form.setError('password_confirmation', 'Şifre tekrarı eşleşmiyor.')
      hasErrors = true
    }
  }
  
  if (hasErrors) {
    return
  }
  
  form.put(`/panel/users/${props.user.id}`, {
    onSuccess: () => {
      showSuccess('Kullanıcı başarıyla güncellendi')
    },
    onError: (errors) => {
      // Hata durumunda yapılacak işlemler
      // Server side validation errors will be shown in the form
      if (Object.keys(errors).length === 0) {
        showError('Kullanıcı güncellenirken bir hata oluştu')
      }
    }
  })
}

</script> 