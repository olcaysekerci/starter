<template>
  <PanelLayout 
    title="Kullanıcı Düzenle" 
    page-title="Kullanıcı Düzenle"
    :breadcrumbs="[
      { title: 'Dashboard', url: '/dashboard' },
      { title: 'Kullanıcı Yönetimi', url: '/panel/users' },
      { title: user.name }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Kullanıcı Düzenle"
      :description="`${user.name} kullanıcısının bilgilerini güncelleyin.`"
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
          Değişiklikleri Kaydet
        </ActionButton>
    </template>
    </PageHeader>

    <!-- Form Card -->
    <FormCard
      title="Kullanıcı Bilgileri"
      :description="`${user.name} kullanıcısının bilgilerini güncelleyin.`"
      submit-text="Değişiklikleri Kaydet"
      :processing="form.processing"
      @submit="saveUser"
      @cancel="goBack"
    >
      <div class="space-y-6">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <FormGroup label="Ad Soyad" required>
            <TextInput
              v-model="form.name"
              :error="form.errors.name"
              placeholder="Kullanıcının adı ve soyadı"
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

          <FormGroup label="Yeni Şifre">
            <TextInput
              v-model="form.password"
              type="password"
              :error="form.errors.password"
              placeholder="Değiştirmek istiyorsanız yeni şifre girin"
            />
          </FormGroup>

          <FormGroup label="Şifre Tekrarı">
            <TextInput
              v-model="form.password_confirmation"
              type="password"
              :error="form.errors.password_confirmation"
              placeholder="Yeni şifreyi tekrar girin"
            />
          </FormGroup>

          <FormGroup label="Telefon">
            <TextInput
              v-model="form.phone"
              :error="form.errors.phone"
              placeholder="+90 555 123 45 67"
            />
          </FormGroup>

          <FormGroup label="Aktif Durumu">
            <Checkbox
              v-model="form.is_active"
              :error="form.errors.is_active"
              label="Kullanıcı aktif"
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

        <!-- Roles -->
        <FormGroup label="Roller">
          <div class="space-y-2">
            <div v-for="role in roles" :key="role.id" class="flex items-center">
              <Checkbox
                :id="`role-${role.id}`"
                :value="role.name"
                v-model="form.roles"
                :label="role.name"
                :description="role.description"
              />
            </div>
            <div v-if="form.errors.roles" class="text-sm text-red-600 dark:text-red-400">
              {{ form.errors.roles }}
            </div>
          </div>
        </FormGroup>
      </div>
    </FormCard>
  </PanelLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import FormGroup from '@/Components/Panel/Forms/FormGroup.vue'
import TextInput from '@/Components/Panel/Forms/TextInput.vue'
import TextArea from '@/Components/Panel/Forms/TextArea.vue'
import Checkbox from '@/Components/Panel/Forms/Checkbox.vue'
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
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  phone: props.user.phone || '',
  address: props.user.address || '',
  is_active: props.user.is_active ?? true,
  roles: props.user.roles?.map(role => role.name) || []
})

// Methods
const saveUser = () => {
  // Client-side validation
  if (!form.name.trim()) {
    form.setError('name', 'Ad alanı zorunludur.')
    return
  }
  
  if (!form.email.trim()) {
    form.setError('email', 'E-posta alanı zorunludur.')
    return
  }
  
  // Şifre değişikliği varsa kontrol et
  if (form.password && form.password !== form.password_confirmation) {
    form.setError('password_confirmation', 'Şifre tekrarı eşleşmiyor.')
    return
  }
  
  form.put(`/panel/users/${props.user.id}`, {
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