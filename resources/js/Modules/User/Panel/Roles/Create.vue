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
      </template>
    </PageHeader>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
      <form @submit.prevent="submitForm">
        <div class="p-6 space-y-6">
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
                rows="3"
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
                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">
                  {{ moduleName }}
                </h4>
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

        <!-- Form Actions -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 flex items-center justify-between">
          <ActionButton 
            @click="goBack" 
            variant="secondary" 
            size="sm"
          >
            İptal
          </ActionButton>
          
          <div class="flex items-center space-x-3">
            <ActionButton 
              @click="saveAsDraft" 
              variant="outline" 
              size="sm"
              :disabled="processing"
            >
              Taslak Olarak Kaydet
            </ActionButton>
            
            <ActionButton 
              type="submit" 
              variant="primary" 
              size="sm"
              :disabled="processing"
            >
              <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ processing ? 'Kaydediliyor...' : 'Rol Oluştur' }}
            </ActionButton>
          </div>
        </div>
      </form>
    </div>
  </PanelLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'

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

// Methods
const submitForm = () => {
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

const saveAsDraft = () => {
  form.is_active = false
  submitForm()
}

const goBack = () => {
  router.visit(route('panel.roles.index'))
}
</script> 