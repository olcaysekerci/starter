<template>
  <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 flex flex-col items-center text-center">
    <div class="w-16 h-16 rounded-full bg-indigo-500 flex items-center justify-center mb-2">
      <span class="text-white font-bold text-xl">{{ initials }}</span>
    </div>
    <div class="font-semibold text-gray-900 dark:text-white">{{ user.full_name }}</div>
    <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ user.email }}</div>
    <div class="flex gap-2 mt-2">
      <ActionButton variant="secondary" @click="$emit('edit', user)">Düzenle</ActionButton>
      <ActionButton variant="danger" @click="$emit('delete', user)">Sil</ActionButton>
    </div>
  </div>
</template>
<script setup>
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
const props = defineProps({ user: Object })
const initials = (props.user.full_name || '').split(' ').map(n => n[0]).join('').toUpperCase().slice(0,2)
</script> 