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
    />

    <!-- Form -->
    <FormCard
      title="Yetki Bilgileri"
      :description="`${permission.display_name} yetkisinin bilgilerini güncelleyin.`"
      submit-text="Değişiklikleri Kaydet"
      :processing="form.processing"
      @submit="submitForm"
    >
      <div class="space-y-6">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Yetki Adı *
            </label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
              placeholder="Örn: user.create"
              required
            />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Benzersiz yetki adı (küçük harfler, nokta ile ayrılmış)
            </p>
            <div v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
              {{ form.errors.name }}
            </div>
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
              placeholder="Örn: Kullanıcı Oluşturma"
              required
            />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Kullanıcıların göreceği yetki adı
            </p>
            <div v-if="form.errors.display_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
              {{ form.errors.display_name }}
            </div>
          </div>
        </div>

        <div>
          <label for="module" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Modül
          </label>
          <select
            id="module"
            v-model="form.module"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
          >
            <option value="">Modül Seçin</option>
            <option v-for="module in modules" :key="module" :value="module">
              {{ module }}
            </option>
          </select>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Yetkinin ait olduğu modül (opsiyonel)
          </p>
          <div v-if="form.errors.module" class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ form.errors.module }}
          </div>
        </div>

        <div>
          <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Açıklama
          </label>
          <textarea
            id="description"
            v-model="form.description"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
            placeholder="Yetkinin amacını ve kapsamını açıklayın..."
          ></textarea>
          <div v-if="form.errors.description" class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ form.errors.description }}
          </div>
        </div>

        <div>
          <label class="flex items-center">
            <input
              v-model="form.is_active"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
            />
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
              Yetki aktif
            </span>
          </label>
          <div v-if="form.errors.is_active" class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ form.errors.is_active }}
          </div>
        </div>
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
import FormCard from '@/Components/Panel/Forms/FormCard.vue'

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