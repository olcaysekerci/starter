<template>
  <div class="relative">
    <input
      :type="type"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :placeholder="placeholder"
      :class="[
        'pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 rounded-lg focus:outline-none focus:border-blue-500 transition-colors',
        size === 'sm' ? 'text-sm w-full sm:w-48' : size === 'lg' ? 'text-base w-full sm:w-80' : 'text-sm w-full sm:w-64'
      ]"
    >
    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
      <SearchIcon v-if="!icon || icon === 'search'" class="h-4 w-4 text-gray-400 dark:text-gray-500" />
      <component v-else :is="icon" class="h-4 w-4 text-gray-400 dark:text-gray-500" />
    </div>
    <div v-if="clearable && modelValue" 
         @click="$emit('update:modelValue', '')"
         class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
      <svg class="h-4 w-4 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </div>
  </div>
</template>

<script setup>
import SearchIcon from '../Icons/SearchIcon.vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Ara...'
  },
  type: {
    type: String,
    default: 'text'
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  icon: {
    type: [String, Object],
    default: 'search'
  },
  clearable: {
    type: Boolean,
    default: false
  }
})

defineEmits(['update:modelValue'])
</script> 