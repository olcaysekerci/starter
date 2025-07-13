<template>
  <PanelLayout 
    title="Rol Düzenle" 
    page-title="Rol Düzenle"
    :breadcrumbs="[
      { title: 'Roller', url: '/panel/roles' },
      { title: role.display_name || role.name }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Rol Düzenle"
      :description="`${role.display_name || role.name} rolünün bilgilerini ve izinlerini güncelleyin.`"
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
      :description="`${role.display_name || role.name} rolünün bilgilerini güncelleyin.`"
      submit-text="Değişiklikleri Kaydet"
      :processing="form.processing"
      @submit="saveRole"
    >
      <div class="space-y-6">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 gap-6">
          <FormGroup label="Rol Adı" required>
            <TextInput
              v-model="form.name"
              :error="form.errors.name"
              placeholder="Örn: Admin, Editor, Viewer"
              required
            />
          </FormGroup>
        </div>

        <FormGroup label="Açıklama">
          <TextArea
            v-model="form.description"
            :error="form.errors.description"
            placeholder="Bu rolün ne için kullanıldığını açıklayın..."
            :rows="3"
          />
        </FormGroup>

        <!-- Permissions Section -->
        <div>
          <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
              Yetkiler
            </h3>
            <div class="space-y-4">
              <div v-for="(modulePermissions, moduleName) in groupedPermissions" :key="moduleName" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                  <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">
                    {{ moduleName }}
                  </h4>
                  <button type="button"
                    class="text-xs px-3 py-1 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800/50 transition"
                    @click="toggleModulePermissions(modulePermissions)">
                    {{ isAllModuleSelected(modulePermissions) ? 'Tümünü Kaldır' : 'Tümünü Seç' }}
                  </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                  <label 
                    v-for="permission in modulePermissions" 
                    :key="permission.id"
                    class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                  >
                    <input
                      v-model="form.permissions"
                      :value="permission.id"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    />
                    <div class="ml-3">
                      <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ permission.display_name || permission.name }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ permission.description }}
                      </div>
                    </div>
                  </label>
                </div>
              </div>

              <!-- Error message for permissions -->
              <div v-if="form.errors.permissions" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-3 mb-4">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                      Yetki Seçimi Hatası
                    </h3>
                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                      <ul class="list-disc pl-5 space-y-1">
                        <li v-for="(error, index) in form.errors.permissions" :key="index">
                          {{ error }}
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
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
import FormCard from '@/Components/Panel/Forms/FormCard.vue'
import FormGroup from '@/Components/Panel/Forms/FormGroup.vue'
import TextInput from '@/Components/Panel/Forms/TextInput.vue'
import TextArea from '@/Components/Panel/Forms/TextArea.vue'
import { useNotification } from '@/Composables'

const props = defineProps({
  role: {
    type: Object,
    required: true
  },
  permissions: {
    type: [Array, Object],
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
const groupedPermissions = computed(() => {
  if (!props.permissions || props.permissions.length === 0) return {}
  
  // If permissions is already grouped (object), return as is
  if (typeof props.permissions === 'object' && !Array.isArray(props.permissions)) {
    return props.permissions
  }
  
  // If permissions is array, group by module name
  const grouped = {}
  props.permissions.forEach(permission => {
    // Extract module name from permission name (e.g., "user.create" -> "User")
    const moduleName = permission.name.split('.')[0]
    const moduleDisplayName = moduleName.charAt(0).toUpperCase() + moduleName.slice(1)
    
    if (!grouped[moduleDisplayName]) {
      grouped[moduleDisplayName] = []
    }
    grouped[moduleDisplayName].push(permission)
  })
  
  return grouped
})

// Methods
const saveRole = () => {
  form.put(`/panel/roles/${props.role.id}`, {
    onSuccess: () => {
      showSuccess('Rol başarıyla güncellendi')
      router.visit(route('panel.roles.index'))
    },
    onError: (errors) => {
      // Validation hataları form üzerinde gösterilecek
      // Sadece genel hatalar için notification göster
      if (Object.keys(errors).length === 0) {
        showError('Rol güncellenirken bir hata oluştu')
      }
    }
  })
}

// Modül bazında tümünü seç/kaldır
const isAllModuleSelected = (modulePermissions) => {
  return modulePermissions.every(p => form.permissions.includes(p.id))
}

const toggleModulePermissions = (modulePermissions) => {
  const allSelected = isAllModuleSelected(modulePermissions)
  if (allSelected) {
    // Kaldır
    form.permissions = form.permissions.filter(p => !modulePermissions.some(mp => mp.id === p))
  } else {
    // Ekle
    const toAdd = modulePermissions.map(p => p.id).filter(p => !form.permissions.includes(p))
    form.permissions = [...form.permissions, ...toAdd]
  }
}
</script>