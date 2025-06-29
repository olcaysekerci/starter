<template>
  <div 
    class="inline-flex items-center justify-center rounded-full"
    :class="[
      sizeClasses[size],
      colorClasses[color]
    ]"
  >
    <span 
      class="font-semibold text-white"
      :class="textSizeClasses[size]"
    >
      {{ userInitials }}
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
  },
  color: {
    type: String,
    default: 'indigo',
    validator: (value) => ['indigo', 'blue', 'green', 'red', 'yellow', 'purple'].includes(value)
  }
})

const userInitials = computed(() => {
  if (!props.user?.name) return '?'
  
  return props.user.name
    .split(' ')
    .map(name => name.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

const sizeClasses = {
  sm: 'w-8 h-8',
  md: 'w-12 h-12',
  lg: 'w-16 h-16',
  xl: 'w-20 h-20'
}

const textSizeClasses = {
  sm: 'text-sm',
  md: 'text-lg',
  lg: 'text-xl',
  xl: 'text-2xl'
}

const colorClasses = {
  indigo: 'bg-indigo-500',
  blue: 'bg-blue-500',
  green: 'bg-green-500',
  red: 'bg-red-500',
  yellow: 'bg-yellow-500',
  purple: 'bg-purple-500'
}
</script> 