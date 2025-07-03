<template>
  <PanelLayout 
    title="Yeni Kullanıcı" 
    page-title="Yeni Kullanıcı Oluştur"
    :breadcrumbs="[
      { title: 'Dashboard', url: '/dashboard' },
      { title: 'Kullanıcı Yönetimi', url: '/panel/users' },
      { title: 'Yeni Kullanıcı' }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Yeni Kullanıcı Oluştur"
      description="Sisteme yeni bir kullanıcı hesabı ekleyin. Kullanıcı bilgilerini ve rollerini belirleyin."
    >
      <template #actions>
        <ActionButton 
          @click="goBack" 
          variant="secondary" 
          size="sm"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Geri Dön
        </ActionButton>
        <ActionButton 
          @click="saveUser" 
          variant="primary" 
          size="sm"
          :loading="form.processing"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          Kullanıcı Oluştur
        </ActionButton>
      </template>
    </PageHeader>

    <!-- Error Alert -->
    <div v-if="form.errors.general" class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">
            Hata
          </h3>
          <div class="mt-2 text-sm text-red-700">
            {{ form.errors.general }}
          </div>
        </div>
      </div>
    </div>

    <!-- Form Card -->
    <FormCard
      title="Kullanıcı Bilgileri"
      description="Sisteme yeni bir kullanıcı hesabı ekleyin. Kullanıcı bilgilerini ve rollerini belirleyin."
      submit-text="Kullanıcı Oluştur"
      :processing="form.processing"
      @submit="saveUser"
      @cancel="goBack"
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
              :error="form.errors.phone"
              placeholder="+90 555 123 45 67"
            />
          </FormGroup>

          <FormGroup label="Şifre" required>
            <TextInput
              v-model="form.password"
              type="password"
              :error="form.errors.password"
              placeholder="Güçlü bir şifre girin"
              required
              minlength="6"
            />
          </FormGroup>

          <FormGroup label="Şifre Tekrarı" required>
            <TextInput
              v-model="form.password_confirmation"
              type="password"
              :error="form.errors.password_confirmation"
              placeholder="Şifreyi tekrar girin"
              required
              minlength="6"
            />
          </FormGroup>
        </div>

        <!-- Address -->
        <FormGroup label="Adres">
          <TextArea
            v-model="form.address"
            :error="form.errors.address"
            placeholder="Kullanıcının adres bilgileri"
            rows="3"
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
import { computed } from 'vue'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import FormGroup from '@/Components/Panel/Forms/FormGroup.vue'
import TextInput from '@/Components/Panel/Forms/TextInput.vue'
import TextArea from '@/Components/Panel/Forms/TextArea.vue'
import Select from '@/Components/Panel/Forms/Select.vue'
import FormCard from '@/Components/Panel/Forms/FormCard.vue'

const props = defineProps({
  roles: {
    type: Array,
    default: () => []
  }
})

// Form
const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  address: '',
  password: '',
  password_confirmation: '',
  role_id: ''
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
  
  if (!form.password) {
    form.setError('password', 'Şifre alanı zorunludur.')
    hasErrors = true
  } else if (form.password.length < 6) {
    form.setError('password', 'Şifre en az 6 karakter olmalıdır.')
    hasErrors = true
  }
  
  if (!form.password_confirmation) {
    form.setError('password_confirmation', 'Şifre tekrarı zorunludur.')
    hasErrors = true
  } else if (form.password !== form.password_confirmation) {
    form.setError('password_confirmation', 'Şifre tekrarı eşleşmiyor.')
    hasErrors = true
  }
  
  if (hasErrors) {
    return
  }
  
  form.post('/panel/users', {
    onSuccess: () => {
      // Başarılı olduğunda yapılacak işlemler
    },
    onError: (errors) => {
      // Hata durumunda yapılacak işlemler
      console.error('Form errors:', errors)
    }
  })
}

const goBack = () => {
  window.history.back()
}
</script> 