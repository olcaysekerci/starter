<template>
  <PanelLayout 
    title="Rol Düzenle" 
    page-title="Rol Düzenle"
    :breadcrumbs="[
      { title: 'Dashboard', url: '/dashboard' },
      { title: 'Kullanıcı Yönetimi', url: '/panel/users' },
      { title: 'Roller', url: '/panel/users/roles' },
      { title: role.name }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Rol Düzenle"
      :description="`${role.name} rolünün bilgilerini ve izinlerini güncelleyin.`"
    >
      <template #actions>
        <ActionButton 
          @click="saveRole" 
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
      title="Rol Bilgileri"
      :description="`${role.name} rolünün bilgilerini güncelleyin.`"
      submit-text="Değişiklikleri Kaydet"
      :processing="form.processing"
      @submit="saveRole"
    >
      <div class="space-y-6">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <FormInput
              v-model="form.name"
              label="Rol Adı"
              name="name"
              placeholder="Örn: Admin, Editor, Viewer"
              required
              :error="form.errors.name"
              description="Rolün benzersiz adı"
            />
          </div>

          <div>
            <FormInput
              v-model="form.guard_name"
              label="Guard Name"
              name="guard_name"
              placeholder="web"
              required
              :error="form.errors.guard_name"
              description="Laravel guard adı (genellikle 'web')"
            />
          </div>
        </div>

        <div>
          <FormTextarea
            v-model="form.description"
            label="Açıklama"
            name="description"
            rows="3"
            placeholder="Bu rolün ne için kullanıldığını açıklayın..."
            :error="form.errors.description"
            description="Rolün ne için kullanıldığına dair açıklama"
          />
        </div>

        <!-- Permissions Section -->
        <div>
          <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
              İzinler
              <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                Bu role hangi izinlerin verileceğini seçin
              </span>
            </h3>

            <div class="space-y-4">
              <!-- Select All Checkbox -->
              <div class="flex items-center">
                <input
                  id="select-all"
                  type="checkbox"
                  :checked="allPermissionsSelected"
                  @change="toggleAllPermissions"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                >
                <label for="select-all" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                  Tüm İzinleri Seç/Kaldır
                </label>
              </div>

              <!-- Permissions Grid -->
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                  v-for="permission in permissions"
                  :key="permission.id"
                  class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800"
                >
                  <input
                    :id="`permission-${permission.id}`"
                    v-model="form.permissions"
                    :value="permission.id"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <label
                    :for="`permission-${permission.id}`"
                    class="ml-3 text-sm cursor-pointer"
                  >
                    <div class="font-medium text-gray-900 dark:text-gray-100">
                      {{ permission.name }}
                    </div>
                    <div v-if="permission.description" class="text-xs text-gray-500 dark:text-gray-400">
                      {{ permission.description }}
                    </div>
                  </label>
                </div>
              </div>

              <!-- Error message for permissions -->
              <div v-if="form.errors.permissions" class="text-red-600 text-sm">
                {{ form.errors.permissions }}
              </div>

              <!-- Selected permissions count -->
              <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ form.permissions.length }} izin seçildi
              </div>
            </div>
          </div>
        </div>
      </div>
    </FormCard>
  </PanelLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import FormCard from '@/Components/Panel/Form/FormCard.vue'
import FormInput from '@/Components/Panel/Form/FormInput.vue'
import FormTextarea from '@/Components/Panel/Form/FormTextarea.vue'
import { useNotification } from '@/Composables'

const props = defineProps({
  role: {
    type: Object,
    required: true
  },
  permissions: {
    type: Array,
    default: () => []
  }
})

// Composables
const { showSuccess, showError } = useNotification()

// Form
const form = useForm({
  name: props.role.name || '',
  guard_name: props.role.guard_name || 'web',
  description: props.role.description || '',
  permissions: props.role.permissions ? props.role.permissions.map(p => p.id) : []
})

// Computed
const allPermissionsSelected = computed(() => {
  return props.permissions.length > 0 && form.permissions.length === props.permissions.length
})

// Methods

const saveRole = () => {
  form.put(`/panel/users/roles/${props.role.id}`, {
    onSuccess: () => {
      showSuccess('Rol başarıyla güncellendi')
      router.visit('/panel/users/roles')
    },
    onError: (errors) => {
      console.error('Form errors:', errors)
      showError('Rol güncellenirken bir hata oluştu')
    }
  })
}

const toggleAllPermissions = (event) => {
  if (event.target.checked) {
    form.permissions = props.permissions.map(p => p.id)
  } else {
    form.permissions = []
  }
}
</script>