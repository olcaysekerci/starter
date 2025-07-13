<template>
  <div 
    class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer border border-gray-200"
    @click="$emit('click')"
  >
    <div class="flex items-center space-x-4">
      <div class="flex-shrink-0">
        <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center">
          <span class="text-white font-semibold text-lg">
            {{ userInitials }}
          </span>
        </div>
      </div>
      
      <div class="flex-1 min-w-0">
        <h3 class="text-lg font-medium text-gray-900 truncate">
          {{ user.full_name }}
        </h3>
        <p class="text-sm text-gray-500 truncate">
          {{ user.email }}
        </p>
        <div v-if="user.phone" class="mt-1">
          <p class="text-xs text-gray-400">
            📞 {{ user.phone }}
          </p>
        </div>
      </div>
    </div>
    
    <div class="mt-4 pt-4 border-t border-gray-100">
      <div class="flex justify-between text-xs text-gray-500">
        <span>Kayıt: {{ formatDate(user.created_at) }}</span>
        <span class="text-indigo-600 font-medium">Detaylar →</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useFormat } from '@/Composables/useFormat'

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

const userInitials = computed(() => {
  return props.user.full_name
    .split(' ')
    .map(name => name.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

const { formatDate } = useFormat()
</script> 