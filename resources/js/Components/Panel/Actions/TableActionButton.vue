<template>
    <button
        @click="$emit('click')"
        :class="buttonClasses"
        class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none"
    >
        <slot />
    </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'destructive', 'outline', 'secondary', 'ghost', 'link', 'info', 'warning', 'success'].includes(value)
    },
    size: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'sm', 'lg', 'icon'].includes(value)
    }
})

const emit = defineEmits(['click'])

const buttonClasses = computed(() => {
    const baseClasses = 'inline-flex items-center justify-center rounded-md text-sm font-medium transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none'
    
    const variantClasses = {
        default: 'border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:border-gray-500',
        destructive: 'border border-gray-200 text-red-600 hover:bg-red-50 hover:border-gray-300 dark:border-gray-600 dark:text-red-400 dark:hover:bg-red-900/20 dark:hover:border-gray-500',
        outline: 'border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:border-gray-500',
        secondary: 'border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:border-gray-500',
        ghost: 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800',
        link: 'text-indigo-600 underline-offset-4 hover:underline dark:text-indigo-400',
        info: 'border border-gray-200 text-blue-600 hover:bg-blue-50 hover:border-gray-300 dark:border-gray-600 dark:text-blue-400 dark:hover:bg-blue-900/20 dark:hover:border-gray-500',
        warning: 'border border-gray-200 text-yellow-600 hover:bg-yellow-50 hover:border-gray-300 dark:border-gray-600 dark:text-yellow-400 dark:hover:bg-yellow-900/20 dark:hover:border-gray-500',
        success: 'border border-gray-200 text-green-600 hover:bg-green-50 hover:border-gray-300 dark:border-gray-600 dark:text-green-400 dark:hover:bg-green-900/20 dark:hover:border-gray-500'
    }
    
    const sizeClasses = {
        default: 'h-8 px-3 py-1',
        sm: 'h-7 px-2 py-1 text-xs',
        lg: 'h-9 px-4 py-2',
        icon: 'h-8 w-8'
    }
    
    return `${baseClasses} ${variantClasses[props.variant]} ${sizeClasses[props.size]}`
})
</script> 