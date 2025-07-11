<template>
  <PanelLayout 
    title="Kullanıcı Düzenle" 
    page-title="Kullanıcı Düzenle"
    :breadcrumbs="[
      { title: 'Dashboard', url: '/dashboard' },
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
            <TextInput
              v-model="form.password"
              type="password"
              :error="form.errors.password"
              placeholder="Değiştirmek istiyorsanız yeni şifre girin"
              minlength="6"
            />
          </FormGroup>

          <FormGroup label="Şifre Tekrarı">
            <TextInput
              v-model="form.password_confirmation"
              type="password"
              :error="form.errors.password_confirmation"
              placeholder="Yeni şifreyi tekrar girin"
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
  user: {
    type: Object,
    required: true
  },
  roles: {
    type: Array,
    default: () => []
  }
})

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
  let value = event.target.value
  
  // Sadece rakamları al
  value = value.replace(/[^0-9]/g, '')
  
  // Türkiye telefon numarası formatı
  if (value.length > 0) {
    // +90 ile başlıyorsa, 90'ı kaldır
    if (value.startsWith('90') && value.length > 10) {
      value = value.substring(2)
    }
    // Başında 0 varsa kaldır
    else if (value.startsWith('0')) {
      value = value.substring(1)
    }
    
    // 10 haneli format
    if (value.length <= 3) {
      value = value
    } else if (value.length <= 6) {
      value = value.substring(0, 3) + ' ' + value.substring(3)
    } else if (value.length <= 8) {
      value = value.substring(0, 3) + ' ' + value.substring(3, 6) + ' ' + value.substring(6)
    } else {
      value = value.substring(0, 3) + ' ' + value.substring(3, 6) + ' ' + value.substring(6, 8) + ' ' + value.substring(8, 10)
    }
  }
  
  form.phone = value
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
      // Başarılı olduğunda yapılacak işlemler
    },
    onError: (errors) => {
      // Hata durumunda yapılacak işlemler
      // Server side validation errors will be shown in the form
    }
  })
}

</script> 