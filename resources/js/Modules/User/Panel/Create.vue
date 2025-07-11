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
    <div v-if="errors && errors.general" class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
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
            {{ errors.general }}
          </div>
        </div>
      </div>
    </div>

    <!-- Form Card -->
    <FormCard
      title="Kullanıcı Bilgileri"
      description="Sisteme yeni bir kullanıcı hesabı ekleyin. Kullanıcı bilgilerini ve rollerini belirleyin."
      submit-text="Kullanıcı Oluştur"
      :processing="processing"
      @submit="saveUser"
    >
      <div class="space-y-6">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <FormGroup label="Ad" required>
            <TextInput
              v-model="form.first_name"
              :error="errors.first_name"
              placeholder="Kullanıcının adı"
              required
            />
          </FormGroup>

          <FormGroup label="Soyad" required>
            <TextInput
              v-model="form.last_name"
              :error="errors.last_name"
              placeholder="Kullanıcının soyadı"
              required
            />
          </FormGroup>

          <FormGroup label="E-posta Adresi" required>
            <TextInput
              v-model="form.email"
              type="email"
              :error="errors.email"
              placeholder="ornek@email.com"
              required
            />
          </FormGroup>

          <FormGroup label="Telefon">
            <TextInput
              v-model="form.phone"
              type="tel"
              :error="errors.phone"
              placeholder="0555 123 45 67"
              @input="formatPhone"
            />
          </FormGroup>

          <FormGroup label="Şifre" required>
            <TextInput
              v-model="form.password"
              type="password"
              :error="errors.password"
              placeholder="Güçlü bir şifre girin"
              required
              :minlength="6"
            />
          </FormGroup>

          <FormGroup label="Şifre Tekrarı" required>
            <TextInput
              v-model="form.password_confirmation"
              type="password"
              :error="errors.password_confirmation"
              placeholder="Şifreyi tekrar girin"
              required
              :minlength="6"
            />
          </FormGroup>
        </div>

        <!-- Address -->
        <FormGroup label="Adres">
          <TextArea
            v-model="form.address"
            :error="errors.address"
            placeholder="Kullanıcının adres bilgileri"
            :rows="3"
          />
        </FormGroup>

        <!-- Role -->
        <FormGroup label="Rol">
          <Select
            v-model="form.role_id"
            :options="roleOptions"
            :error="errors.role_id"
            placeholder="Kullanıcı rolü seçin"
          />
        </FormGroup>
      </div>
    </FormCard>
  </PanelLayout>
</template>

<script setup>
import { computed } from 'vue'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import FormGroup from '@/Components/Panel/Forms/FormGroup.vue'
import TextInput from '@/Components/Panel/Forms/TextInput.vue'
import TextArea from '@/Components/Panel/Forms/TextArea.vue'
import Select from '@/Components/Panel/Forms/Select.vue'
import FormCard from '@/Components/Panel/Forms/FormCard.vue'
import { useForm, useNavigation, useNotification } from '@/Composables'
import { formatTurkishPhone } from '@/utils'

const props = defineProps({
  roles: {
    type: Array,
    default: () => []
  }
})

// Composable'ları başlat
const navigation = useNavigation('panel.users')
const { showSuccess, showError } = useNotification()

// Form
const { form, errors, processing, submit, validate, reset } = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  address: '',
  password: '',
  password_confirmation: '',
  role_id: ''
}, {
  rules: {
    first_name: ['required'],
    last_name: ['required'],
    email: ['required', 'email'],
    phone: ['nullable', 'phone'],
    password: ['required', 'min:6'],
    password_confirmation: ['required']
  },
  route: 'panel.users.store',
  onSuccess: () => {
    showSuccess('Kullanıcı başarıyla oluşturuldu!')
    navigation.goToIndex()
  },
  onError: (serverErrors) => {
    showError('Kullanıcı oluşturulurken hata oluştu.')
    // Server validation errors will be displayed in the form
  }
})

// Errors are reactive and will update the form validation

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

const saveUser = async () => {
  // Custom validation for password confirmation
  if (form.password !== form.password_confirmation) {
    showError('Şifre tekrarı eşleşmiyor.')
    return
  }
  
  // Submit form using our composable
  await submit()
}

</script> 