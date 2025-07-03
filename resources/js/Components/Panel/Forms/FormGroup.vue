<template>
  <div class="space-y-2">
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <slot />
    <p v-if="help" class="text-sm text-gray-500 dark:text-gray-400">{{ help }}</p>
  </div>
</template>

<script setup>
defineProps({
  label: {
    type: String,
    default: ''
  },
  id: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  help: {
    type: String,
    default: ''
  }
})
</script> 