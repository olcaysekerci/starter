<template>
  <PanelLayout 
    title="Yeni Rol Oluştur" 
    page-title="Yeni Rol Oluştur"
    :breadcrumbs="[
      { title: 'Dashboard', url: '/dashboard' },
      { title: 'Rol Yönetimi', url: route('panel.roles.index') },
      { title: 'Yeni Rol Oluştur' }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Yeni Rol Oluştur"
      description="Sistem için yeni bir rol oluşturun ve yetkilerini belirleyin."
    >
      <template #actions>
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
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Rol Adı *
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="Örn: editor"
                required
              />
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Benzersiz rol adı (küçük harfler, tire ile ayrılmış)
              </p>
            </div>

            <div>
              <label for="display_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Görünen Ad *
              </label>
              <input
                id="display_name"
                v-model="form.display_name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="Örn: Editör"
                required
              />
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kullanıcıların göreceği rol adı
              </p>
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

          <div class="mt-6">
            <label class="flex items-center">
              <input
                v-model="form.is_active"
                type="checkbox"
                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
              />
              <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                Rol aktif
              </span>
            </label>
          </div>
        </div>

        <!-- Permissions -->
        <div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Yetkiler
          </h3>
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
                    :value="permission.name"
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

const props = defineProps({
  permissions: {
    type: Object,
    required: true
  }
})

// Form data
const form = reactive({
  name: '',
  display_name: '',
  description: '',
  guard_name: 'web',
  is_active: true,
  permissions: []
})

const processing = ref(false)

// Form submission
function submitForm() {
  processing.value = true
  
  router.post(route('panel.roles.store'), form, {
    onSuccess: () => {
      processing.value = false
    },
    onError: () => {
      processing.value = false
    }
  })
}

// Navigation

// Modül bazında tümünü seç/kaldır
function isAllModuleSelected(modulePermissions) {
  return modulePermissions.every(p => form.permissions.includes(p.name))
}

function toggleModulePermissions(modulePermissions) {
  const allSelected = isAllModuleSelected(modulePermissions)
  if (allSelected) {
    // Kaldır
    form.permissions = form.permissions.filter(p => !modulePermissions.some(mp => mp.name === p))
  } else {
    // Ekle
    const toAdd = modulePermissions.map(p => p.name).filter(p => !form.permissions.includes(p))
    form.permissions = [...form.permissions, ...toAdd]
  }
}
</script> 