<template>
  <PanelLayout 
    title="Yetki Düzenle" 
    page-title="Yetki Düzenle"
    :breadcrumbs="[
      { title: 'Dashboard', url: '/dashboard' },
      { title: 'Yetki Yönetimi', url: route('panel.permissions.index') },
      { title: permission.display_name || permission.name }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Yetki Düzenle"
      :description="`${permission.display_name || permission.name} yetkisini düzenleyin.`"
    >
      <template #actions>
        <ActionButton 
          @click="$router.back()" 
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
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Yetki Bilgileri</h3>
      </div>
      
      <form @submit.prevent="submitForm" class="p-6 space-y-6">
        <!-- Yetki Adı -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Yetki Adı <span class="text-red-500">*</span>
          </label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            :class="[
              'w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors',
              form.errors.name 
                ? 'border-red-300 dark:border-red-600 bg-red-50 dark:bg-red-900/20' 
                : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700'
            ]"
            placeholder="örn: user.create"
            required
            :disabled="isSystemPermission"
          >
          <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ form.errors.name }}
          </p>
          <p v-if="isSystemPermission" class="mt-1 text-xs text-orange-600 dark:text-orange-400">
            Sistem yetkileri düzenlenemez.
          </p>
        </div>

        <!-- Görünen Ad -->
        <div>
          <label for="display_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Görünen Ad <span class="text-red-500">*</span>
          </label>
          <input
            id="display_name"
            v-model="form.display_name"
            type="text"
            :class="[
              'w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors',
              form.errors.display_name 
                ? 'border-red-300 dark:border-red-600 bg-red-50 dark:bg-red-900/20' 
                : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700'
            ]"
            placeholder="örn: Kullanıcı Oluşturma"
            required
          >
          <p v-if="form.errors.display_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ form.errors.display_name }}
          </p>
        </div>

        <!-- Modül -->
        <div>
          <label for="module" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Modül
          </label>
          <select
            id="module"
            v-model="form.module"
            :class="[
              'w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors',
              form.errors.module 
                ? 'border-red-300 dark:border-red-600 bg-red-50 dark:bg-red-900/20' 
                : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700'
            ]"
          >
            <option value="">Modül Seçin</option>
            <option v-for="module in modules" :key="module" :value="module">
              {{ module }}
            </option>
          </select>
          <p v-if="form.errors.module" class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ form.errors.module }}
          </p>
        </div>

        <!-- Açıklama -->
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Açıklama
          </label>
          <textarea
            id="description"
            v-model="form.description"
            rows="3"
            :class="[
              'w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors',
              form.errors.description 
                ? 'border-red-300 dark:border-red-600 bg-red-50 dark:bg-red-900/20' 
                : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700'
            ]"
            placeholder="Bu yetkinin ne işe yaradığını açıklayın..."
          ></textarea>
          <p v-if="form.errors.description" class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ form.errors.description }}
          </p>
        </div>

        <!-- Durum -->
        <div>
          <label class="flex items-center">
            <input
              v-model="form.is_active"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
            >
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</span>
          </label>
          <p v-if="form.errors.is_active" class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ form.errors.is_active }}
          </p>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
          <ActionButton 
            @click="$router.back()" 
            variant="secondary" 
            type="button"
          >
            İptal
          </ActionButton>
          <ActionButton 
            type="submit" 
            variant="primary"
            :disabled="form.processing"
          >
            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ form.processing ? 'Güncelleniyor...' : 'Yetki Güncelle' }}
          </ActionButton>
        </div>
      </form>
    </div>
  </PanelLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'

const props = defineProps({
  permission: {
    type: Object,
    required: true
  },
  modules: {
    type: Array,
    default: () => []
  }
})

const form = useForm({
  name: props.permission.name,
  display_name: props.permission.display_name,
  description: props.permission.description,
  module: props.permission.module,
  is_active: props.permission.is_active,
  guard_name: props.permission.guard_name
})

const isSystemPermission = computed(() => {
  return ['super-admin', 'admin'].includes(props.permission.name)
})

const submitForm = () => {
  form.put(route('panel.permissions.update', props.permission.id), {
    onSuccess: () => {
      // Başarı mesajı göster
    }
  })
}
</script> 