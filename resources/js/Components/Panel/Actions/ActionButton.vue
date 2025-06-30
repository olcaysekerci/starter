<template>
  <button
    :type="type"
    :disabled="disabled"
    :class="[
      // Base styles
      'inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed',
      
      // Size variants
      sizeClasses,
      
      // Variant styles
      variantClasses,
      
      // Loading state
      { 'cursor-wait': loading }
    ]"
    @click="$emit('click', $event)"
  >
    <!-- Loading spinner -->
    <svg 
      v-if="loading" 
      class="animate-spin -ml-1 mr-2 h-4 w-4" 
      fill="none" 
      viewBox="0 0 24 24"
    >
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    
    <!-- Left icon slot -->
    <slot name="icon-left" />
    
    <!-- Button text -->
    <slot />
    
    <!-- Right icon slot -->
    <slot name="icon-right" />
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'button'
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => [
      'primary', 'secondary', 'success', 'warning', 'danger', 'info', 'ghost', 'outline'
    ].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  disabled: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  rounded: {
    type: String,
    default: 'lg',
    validator: (value) => ['none', 'sm', 'md', 'lg', 'xl', 'full'].includes(value)
  }
})

defineEmits(['click'])

const sizeClasses = computed(() => {
  const sizes = {
    xs: 'px-2 py-1 text-xs rounded-md',
    sm: 'px-3 py-1.5 text-sm rounded-md',
    md: 'px-4 py-2 text-sm rounded-lg',
    lg: 'px-5 py-2.5 text-base rounded-lg',
    xl: 'px-6 py-3 text-lg rounded-xl'
  }
  return sizes[props.size]
})

const variantClasses = computed(() => {
  const variants = {
    primary: 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white shadow-sm border border-transparent dark:bg-blue-600 dark:hover:bg-blue-700',
    secondary: 'bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-900 shadow-sm border border-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100 dark:border-gray-600',
    success: 'bg-green-600 hover:bg-green-700 active:bg-green-800 text-white shadow-sm border border-transparent dark:bg-green-600 dark:hover:bg-green-700',
    warning: 'bg-yellow-500 hover:bg-yellow-600 active:bg-yellow-700 text-white shadow-sm border border-transparent dark:bg-yellow-600 dark:hover:bg-yellow-700',
    danger: 'bg-red-600 hover:bg-red-700 active:bg-red-800 text-white shadow-sm border border-transparent dark:bg-red-600 dark:hover:bg-red-700',
    info: 'bg-cyan-600 hover:bg-cyan-700 active:bg-cyan-800 text-white shadow-sm border border-transparent dark:bg-cyan-600 dark:hover:bg-cyan-700',
    ghost: 'bg-transparent hover:bg-gray-50 active:bg-gray-100 text-gray-600 hover:text-gray-900 border border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 dark:hover:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-100',
    outline: 'bg-white hover:bg-gray-50 active:bg-gray-100 text-gray-700 border border-gray-300 shadow-sm dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-100 dark:border-gray-600'
  }
  return variants[props.variant]
})
</script> 