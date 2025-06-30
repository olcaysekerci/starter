<template>
  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ user.id }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ user.name }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ user.email }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ formatDate(user.created_at) }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ formatDateTime(user.last_login) }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-right">
      <div class="flex items-center space-x-2">
        <TableActionButton variant="info" @click="$emit('edit', user)">
          Düzenle
        </TableActionButton>
        <TableActionButton variant="destructive" @click="$emit('delete', user)">
          Sil
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