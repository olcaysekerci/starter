<template>
  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ user.id }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ user.name }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ user.email }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ formatDate(user.created_at) }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ user.last_login || '-' }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-right">
      <ActionButton variant="secondary" @click="$emit('edit', user)">Düzenle</ActionButton>
      <ActionButton variant="danger" @click="$emit('delete', user)">Sil</ActionButton>
    </td>
  </tr>
</template>
<script setup>
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
const props = defineProps({ user: Object })
const formatDate = (date) => date ? new Date(date).toLocaleDateString('tr-TR') : '-'
</script> 