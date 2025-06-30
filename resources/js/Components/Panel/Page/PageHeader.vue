<template>
  <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
    <div>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ title }}</h1>
      <p v-if="description" class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ description }}</p>
    </div>
    <div class="flex items-center gap-2">
      <slot name="actions" />
    </div>
  </div>
</template>
<script setup>
defineProps({
  title: { type: String, required: true },
  description: { type: String, default: '' }
})
</script> 