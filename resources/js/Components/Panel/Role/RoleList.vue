<template>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            ID
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Rol
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Açıklama
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Yetkiler
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Kullanıcılar
          </th>
          <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            İşlemler
          </th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
        <tr v-for="role in roles.data" :key="role.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            #{{ role.id }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            {{ role.display_name || role.name }}
          </td>
          <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 max-w-xs truncate">
            {{ role.description || '-' }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            {{ role.permissions?.length || 0 }} yetki
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
            {{ role.users_count || 0 }} kullanıcı
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex items-center justify-end space-x-1">
              <TableActionButton
                @click="$emit('view', role)"
                variant="info"
                size="sm"
                title="Görüntüle"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </TableActionButton>
              <TableActionButton
                @click="$emit('edit', role)"
                variant="warning"
                size="sm"
                title="Düzenle"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </TableActionButton>
              <TableActionButton
                v-if="!isSystemRole(role)"
                @click="$emit('delete', role)"
                variant="destructive"
                size="sm"
                title="Sil"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </TableActionButton>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    
    <!-- Empty State -->
    <div v-if="!roles.data || roles.data.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Rol bulunamadı</h3>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        Henüz hiç rol oluşturulmamış.
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import TableActionButton from '@/Components/Panel/Actions/TableActionButton.vue'

const props = defineProps({
  roles: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['edit', 'delete', 'view'])

// Methods

const isSystemRole = (role) => {
  return ['super-admin', 'admin'].includes(role.name)
}
</script> 