<template>
  <div class="relative w-full">
    <input
      v-model="modelValue"
      :placeholder="placeholder"
      type="text"
      class="block w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
      @input="$emit('update:modelValue', modelValue)"
    />
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4-4m0 0A7 7 0 1110 3a7 7 0 017 7z" />
      </svg>
    </div>
  </div>
</template>
<script setup>
import { ref, watch } from 'vue'
const props = defineProps({
  modelValue: String,
  placeholder: { type: String, default: 'Ara...' }
})
const modelValue = ref(props.modelValue)
watch(() => props.modelValue, v => modelValue.value = v)
</script> 