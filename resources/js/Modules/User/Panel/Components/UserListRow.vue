<template>
  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">#{{ user.id }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ user.first_name || '-' }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ user.last_name || '-' }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ user.email }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ user.formatted_phone || '-' }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ formatDate(user.created_at) }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ formatDateTime(user.last_login) }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-right">
      <div class="flex items-center justify-end space-x-1">
        <TableActionButton variant="info" size="sm" @click="$emit('view', user)" title="Görüntüle">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
        </TableActionButton>
        <TableActionButton variant="warning" size="sm" @click="$emit('edit', user)" title="Düzenle">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
        </TableActionButton>
        <TableActionButton variant="destructive" size="sm" @click="$emit('delete', user)" title="Sil">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </TableActionButton>
      </div>
    </td>
  </tr>
</template>

<script setup>
import TableActionButton from '@/Components/Panel/Actions/TableActionButton.vue'

const props = defineProps({ user: Object })

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('tr-TR')
}

const formatDateTime = (date) => {
  if (!date) return '-'
  const d = new Date(date)
  return d.toLocaleDateString('tr-TR') + ' ' + d.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' })
}
</script> 