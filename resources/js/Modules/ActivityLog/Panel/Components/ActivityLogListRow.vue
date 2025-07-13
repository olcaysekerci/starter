<template>
  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">#{{ log.id }}</td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
      {{ log.causer?.full_name || log.user_name || 'Sistem' }}
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
      <span :class="getEventBadgeClass(log.resolved_event || log.event || log.description)" class="px-2 py-1 rounded text-xs font-semibold">
        {{ getEventLabel(log.resolved_event || log.event, log.description) }}
      </span>
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
        {{ log.model_name }}
      </span>
    </td>
    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-200 max-w-xs truncate">
      {{ log.formatted_description || log.description || 'İşlem detayı bulunamadı' }}
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
      {{ formatDateTime(log.created_at) }}
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
import { useFormat } from '@/Composables/useFormat'

const props = defineProps({ log: Object })
defineEmits(['view'])

const { formatDateTime } = useFormat()

const getEventLabel = (event, description) => {
  // Eğer event boşsa, description'dan event'i çıkar
  if (!event && description) {
    if (description.includes('Giriş yaptı')) return 'Giriş'
    if (description.includes('Çıkış yaptı')) return 'Çıkış'
    if (description.includes('Başarısız giriş denemesi')) return 'Başarısız Giriş'
    if (description.includes('Şifre sıfırlandı')) return 'Şifre Sıfırlandı'
    if (description.includes('Kayıt oldu')) return 'Kayıt Oldu'
    if (description.includes('E-posta doğrulandı')) return 'E-posta Doğrulandı'
    if (description.includes('oluşturdu')) return 'Oluşturuldu'
    if (description.includes('güncelledi')) return 'Güncellendi'
    if (description.includes('sildi')) return 'Silindi'
    if (description.includes('geri yükledi')) return 'Geri Yüklendi'
  }

  const labels = {
    'created': 'Oluşturuldu',
    'updated': 'Güncellendi',
    'deleted': 'Silindi',
    'restored': 'Geri Yüklendi',
    'login': 'Giriş',
    'logout': 'Çıkış',
    'failed_login': 'Başarısız Giriş',
    'password_reset': 'Şifre Sıfırlandı',
    'registered': 'Kayıt Oldu',
    'email_verified': 'E-posta Doğrulandı',
    'password_changed': 'Şifre Değişti',
    'email_changed': 'E-posta Değişti',
    'profile_updated': 'Profil Güncellendi',
    'Giriş yaptı': 'Giriş',
    'Çıkış yaptı': 'Çıkış',
    'Başarısız giriş denemesi': 'Başarısız Giriş',
    'Şifre sıfırlandı': 'Şifre Sıfırlandı',
    'Kayıt oldu': 'Kayıt Oldu',
    'E-posta doğrulandı': 'E-posta Doğrulandı'
  }
  return labels[event] || event || 'Bilinmeyen İşlem'
}

const getEventBadgeClass = (event) => {
  const classes = {
    'created': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'updated': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'deleted': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    'restored': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    'login': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
    'logout': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
    'failed_login': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    'password_reset': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    'registered': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'email_verified': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'password_changed': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    'email_changed': 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
    'profile_updated': 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
    'Giriş yaptı': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
    'Çıkış yaptı': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
    'Başarısız giriş denemesi': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    'Şifre sıfırlandı': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    'Kayıt oldu': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'E-posta doğrulandı': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'Giriş': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
    'Çıkış': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
    'Başarısız Giriş': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    'Şifre Sıfırlandı': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    'Kayıt Oldu': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'E-posta Doğrulandı': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'Oluşturuldu': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'Güncellendi': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'Silindi': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    'Geri Yüklendi': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
  }
  return classes[event] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
}
</script> 