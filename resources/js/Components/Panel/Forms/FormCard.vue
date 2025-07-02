<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <!-- Header -->
    <div v-if="title || description" class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
      <h3 v-if="title" class="text-lg font-medium text-gray-900 dark:text-gray-100">
        {{ title }}
      </h3>
      <p v-if="description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        {{ description }}
      </p>
    </div>

    <!-- Body -->
    <div class="p-6">
      <slot />
    </div>

    <!-- Footer -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end space-x-3">
      <ActionButton 
        v-if="showCancel"
        @click="$emit('cancel')" 
        variant="secondary" 
        size="sm"
      >
        {{ cancelText }}
      </ActionButton>
      
      <ActionButton 
        @click="$emit('submit')" 
        variant="primary" 
        size="sm"
        :disabled="processing"
      >
        <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ processing ? 'Kaydediliyor...' : submitText }}
      </ActionButton>
    </div>
  </div>
</template>

<script setup>
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'

defineProps({
  title: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  submitText: {
    type: String,
    default: 'Kaydet'
  },
  cancelText: {
    type: String,
    default: 'İptal'
  },
  processing: {
    type: Boolean,
    default: false
  },
  showCancel: {
    type: Boolean,
    default: true
  }
})

defineEmits(['submit', 'cancel'])
</script> 