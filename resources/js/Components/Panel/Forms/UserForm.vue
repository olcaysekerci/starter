<template>
  <BaseForm
    :title="isEditing ? 'Kullanıcı Düzenle' : 'Yeni Kullanıcı'"
    :description="isEditing ? 'Kullanıcı bilgilerini düzenleyin.' : 'Sisteme yeni bir kullanıcı hesabı ekleyin.'"
    :submit-text="isEditing ? 'Güncelle' : 'Kullanıcı Oluştur'"
    :initial-data="initialData"
    :rules="validationRules"
    :route="formRoute"
    :method="formMethod"
    @submit="handleFormSubmit"
    @success="handleSuccess"
    @error="handleError"
  >
    <template #default="{ form, errors }">
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

          <FormGroup v-if="!isEditing" label="Şifre" required>
            <TextInput
              v-model="form.password"
              type="password"
              :error="errors.password"
              placeholder="Güçlü bir şifre girin"
              required
              :minlength="6"
            />
          </FormGroup>

          <FormGroup v-if="!isEditing" label="Şifre Tekrarı" required>
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
    </template>
  </BaseForm>
</template>

<script setup>
import { computed } from 'vue'
import BaseForm from '@/Components/Panel/Forms/BaseForm.vue'
import FormGroup from '@/Components/Panel/Forms/FormGroup.vue'
import TextInput from '@/Components/Panel/Forms/TextInput.vue'
import TextArea from '@/Components/Panel/Forms/TextArea.vue'
import Select from '@/Components/Panel/Forms/Select.vue'

const props = defineProps({
  user: {
    type: Object,
    default: null
  },
  roles: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['success', 'error'])

const isEditing = computed(() => !!props.user)

const initialData = computed(() => {
  if (isEditing.value) {
    return {
      first_name: props.user.first_name || '',
      last_name: props.user.last_name || '',
      email: props.user.email || '',
      phone: props.user.phone || '',
      address: props.user.address || '',
      role_id: props.user.roles?.[0]?.id || ''
    }
  }
  
  return {
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    address: '',
    password: '',
    password_confirmation: '',
    role_id: ''
  }
})

const validationRules = computed(() => {
  const rules = {
    first_name: ['required'],
    last_name: ['required'],
    email: ['required', 'email'],
    phone: ['nullable', 'phone']
  }

  if (!isEditing.value) {
    rules.password = ['required', 'min:6']
    rules.password_confirmation = ['required']
  }

  return rules
})

const formRoute = computed(() => {
  return isEditing.value ? `panel.users.update` : 'panel.users.store'
})

const formMethod = computed(() => {
  return isEditing.value ? 'put' : 'post'
})

const roleOptions = computed(() => {
  return props.roles.map(role => ({
    value: role.id,
    label: role.display_name || role.name,
    description: role.description
  }))
})

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
  
  // Note: We need to update the form through the parent component
  // This will be handled in the parent component
}

const handleFormSubmit = (form) => {
  // Custom validation for password confirmation (for create mode)
  if (!isEditing.value && form.password !== form.password_confirmation) {
    // This validation should be handled by the form validation rules
    return false
  }
  
  return true
}

const handleSuccess = (data) => {
  emit('success', data)
}

const handleError = (errors) => {
  emit('error', errors)
}
</script>