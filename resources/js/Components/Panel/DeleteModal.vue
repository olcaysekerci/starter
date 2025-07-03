<template>
  <Modal :show="show" @close="$emit('close')">
    <div class="p-6">
      <!-- Icon ve Başlık -->
      <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 dark:bg-red-900/20 rounded-full">
        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
        </svg>
      </div>

      <!-- Başlık -->
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center mb-2">
        {{ title }}
      </h3>

      <!-- Açıklama -->
      <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-6">
        {{ description }}
      </p>

      <!-- Ek Bilgi (opsiyonel) -->
      <div v-if="additionalInfo" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3 mb-6">
        <div class="flex items-start">
          <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
          <p class="text-sm text-yellow-800 dark:text-yellow-200">
            {{ additionalInfo }}
          </p>
        </div>
      </div>

      <!-- Butonlar -->
      <div class="flex items-center justify-center space-x-3">
        <ActionButton 
          @click="$emit('close')" 
          variant="secondary" 
          size="sm"
          :disabled="loading"
        >
          İptal
        </ActionButton>
        
        <ActionButton 
          @click="$emit('confirm')" 
          variant="danger" 
          size="sm"
          :loading="loading"
        >
          <svg v-if="!loading" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
          {{ loading ? 'Siliniyor...' : 'Sil' }}
        </ActionButton>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import Modal from './Modal.vue'
import ActionButton from './Actions/ActionButton.vue'

defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Silme Onayı'
  },
  description: {
    type: String,
    default: 'Bu öğeyi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.'
  },
  additionalInfo: {
    type: String,
    default: ''
  },
  loading: {
    type: Boolean,
    default: false
  }
})

defineEmits(['close', 'confirm'])
</script> 