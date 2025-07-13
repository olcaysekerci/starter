<template>
  <PanelLayout 
    title="Yeni Rol Oluştur" 
    page-title="Yeni Rol Oluştur"
    :breadcrumbs="[
      { title: 'Roller', url: '/panel/roles' },
      { title: 'Yeni Rol Oluştur' }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Yeni Rol Oluştur"
      description="Sistem için yeni bir rol oluşturun ve yetkilerini belirleyin."
    >
      <template #actions>
        <ActionButton 
          @click="submitForm" 
          variant="primary" 
          size="sm"
          :loading="processing"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          Rol Oluştur
        </ActionButton>
      </template>
    </PageHeader>

    <!-- Form -->
    <FormCard
      title="Rol Bilgileri"
      description="Rol için temel bilgileri ve yetkileri belirleyin."
      submit-text="Rol Oluştur"
      :processing="processing"
      @submit="submitForm"
    >
      <div class="space-y-6">
        <!-- Basic Information -->
        <div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Temel Bilgiler
          </h3>
          <div class="grid grid-cols-1 gap-6">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Rol Adı *
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="Örn: Editör, Yönetici, Moderatör"
                required
              />
            </div>
          </div>

          <div class="mt-6">
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Açıklama
            </label>
            <textarea
              id="description"
              v-model="form.description"
              :rows="3"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
              placeholder="Rolün amacını ve kapsamını açıklayın..."
            ></textarea>
          </div>
        </div>

        <!-- Permissions -->
        <div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Yetkiler
          </h3>
          
          <!-- Error message for permissions -->
          <div v-if="$page.props.errors.permissions" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-3 mb-4">
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
                    <li v-for="(error, index) in $page.props.errors.permissions" :key="index">
                      {{ error }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="space-y-4">
            <div v-for="(modulePermissions, moduleName) in permissions" :key="moduleName" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
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
          </div>
        </div>
      </div>
    </FormCard>
  </PanelLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import FormCard from '@/Components/Panel/Forms/FormCard.vue'
import { useNotification } from '@/Composables'

const props = defineProps({
  permissions: {
    type: Object,
    required: true
  }
})

// Form data
const form = reactive({
  name: '',
  description: '',
  guard_name: 'web',
  permissions: []
})

// Composables
const { showSuccess, showError } = useNotification()

const processing = ref(false)

// Form submission
function submitForm() {
  processing.value = true
  
  router.post(route('panel.roles.store'), form, {
    onSuccess: () => {
      processing.value = false
      showSuccess('Rol başarıyla oluşturuldu')
      // Notification'ın görünmesi için kısa bir gecikme
      setTimeout(() => {
        router.visit(route('panel.roles.index'))
      }, 1500)
    },
    onError: (errors) => {
      processing.value = false
      showError('Rol oluşturulurken bir hata oluştu')
    }
  })
}

// Modül bazında tümünü seç/kaldır
function isAllModuleSelected(modulePermissions) {
  return modulePermissions.every(p => form.permissions.includes(p.id))
}

function toggleModulePermissions(modulePermissions) {
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