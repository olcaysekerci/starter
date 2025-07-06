<template>
  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">#{{ log.id }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
      <div class="flex items-center">
        <div class="flex-shrink-0 h-8 w-8">
          <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
              {{ getUserInitials(log.causer?.full_name || log.user_name || 'Sistem') }}
            </span>
          </div>
        </div>
        <div class="ml-3">
          <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
            {{ log.causer?.full_name || log.user_name || 'Sistem' }}
          </div>
          <div v-if="log.causer?.email" class="text-sm text-gray-500 dark:text-gray-400">
            {{ log.causer.email }}
          </div>
        </div>
      </div>
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
      <span :class="getEventBadgeClass(log.event)" class="px-2 py-1 rounded text-xs font-semibold">
        {{ getEventLabel(log.event) }}
      </span>
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
        {{ log.model_name }}
      </span>
    </td>
    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 max-w-xs truncate">
      {{ log.description }}
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
      <div class="text-sm text-gray-900 dark:text-gray-100">
        {{ formatDate(log.created_at) }}
      </div>
      <div class="text-xs text-gray-500 dark:text-gray-400">
        {{ formatTime(log.created_at) }}
      </div>
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-right">
      <div class="flex items-center justify-end space-x-1">
        <TableActionButton variant="info" size="sm" @click="$emit('view', log)" title="Detayları Görüntüle">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
        </TableActionButton>
      </div>
    </td>
  </tr>
</template>

<script setup>
import TableActionButton from '@/Components/Panel/Actions/TableActionButton.vue'

const props = defineProps({ log: Object })
defineEmits(['view'])

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('tr-TR')
}

const formatTime = (date) => {
  if (!date) return '-'
  const d = new Date(date)
  return d.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' })
}

const getUserInitials = (name) => {
  if (!name || name === 'Sistem') return 'S'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

const getEventLabel = (event) => {
  const labels = {
    'created': 'Oluşturuldu',
    'updated': 'Güncellendi',
    'deleted': 'Silindi',
    'restored': 'Geri Yüklendi',
    'login': 'Giriş',
    'logout': 'Çıkış',
    'password_changed': 'Şifre Değişti',
    'email_changed': 'E-posta Değişti'
  }
  return labels[event] || event
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