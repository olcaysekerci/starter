<template>
  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer" @click="$emit('view', log)">
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200 font-semibold">#{{ log.id }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
      {{ log.causer?.full_name || log.user_name || 'Sistem' }}
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-sm">
      <span :class="getEventBadgeClass(log.event)" class="px-2 py-0.5 rounded text-xs font-semibold">
        {{ log.formatted_description }}
      </span>
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
      {{ log.model_name }}
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">
      {{ log.description }}
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
      {{ formatDate(log.created_at) }} {{ formatTime(log.created_at) }}
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400 font-mono">
      {{ log.ip_address || '-' }}
    </td>
  </tr>
</template>

<script setup>
const props = defineProps({ 
  log: {
    type: Object,
    required: true
  }
})

defineEmits(['view'])

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('tr-TR')
}

const formatTime = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleTimeString('tr-TR', { 
    hour: '2-digit', 
    minute: '2-digit' 
  })
}

const getEventBadgeClass = (event) => {
  const classes = {
    'created': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'updated': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'deleted': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    'restored': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    'login': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
    'logout': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
    'password_changed': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    'email_changed': 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
  }
  return classes[event] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
}
</script> 