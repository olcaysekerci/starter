<template>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Yetki
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Modül
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Açıklama
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Roller
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Kullanıcılar
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Durum
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Oluşturulma
          </th>
          <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            İşlemler
          </th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
        <tr v-for="permission in permissions.data" :key="permission.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center">
              <div class="flex-shrink-0 h-10 w-10">
                <div class="h-10 w-10 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                  <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                  {{ permission.display_name || permission.name }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  {{ permission.name }}
                </div>
              </div>
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <span 
              v-if="permission.module"
              class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400"
            >
              {{ permission.module }}
            </span>
            <span v-else class="text-sm text-gray-500 dark:text-gray-400">-</span>
          </td>
          <td class="px-6 py-4">
            <div class="text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate">
              {{ permission.description || 'Açıklama yok' }}
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-900 dark:text-gray-100">
              {{ permission.roles?.length || 0 }} rol
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
              {{ getRoleNames(permission.roles) }}
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-900 dark:text-gray-100">
              {{ permission.users_count || 0 }} kullanıcı
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <span 
              :class="[
                'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                permission.is_active 
                  ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                  : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
              ]"
            >
              {{ permission.is_active ? 'Aktif' : 'Pasif' }}
            </span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
            {{ formatDate(permission.created_at) }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex items-center justify-end space-x-2">
              <button
                @click="$emit('view', permission)"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                title="Görüntüle"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>
              <button
                @click="$emit('edit', permission)"
                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                title="Düzenle"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </button>
              <button
                v-if="!isSystemPermission(permission)"
                @click="$emit('delete', permission)"
                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                title="Sil"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    
    <!-- Empty State -->
    <div v-if="!permissions.data || permissions.data.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Yetki bulunamadı</h3>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        Henüz hiç yetki oluşturulmamış.
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  permissions: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['edit', 'delete', 'view'])

// Methods
const getRoleNames = (roles) => {
  if (!roles || roles.length === 0) {
    return 'Rol yok'
  }
  
  const names = roles.map(r => r.display_name || r.name).slice(0, 3)
  if (roles.length > 3) {
    names.push(`+${roles.length - 3} daha`)
  }
  
  return names.join(', ')
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('tr-TR')
}

const isSystemPermission = (permission) => {
  return ['super-admin', 'admin'].includes(permission.name)
}
</script> 