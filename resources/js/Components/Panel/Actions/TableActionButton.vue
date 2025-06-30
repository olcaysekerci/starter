<template>
    <button
        :type="type"
        :disabled="disabled"
        :class="[
            'inline-flex items-center px-2 py-1 text-xs font-medium transition-colors focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed',
            variantClasses
        ]"
        @click="$emit('click', $event)"
    >
        <slot />
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
            'primary', 'secondary', 'success', 'warning', 'danger', 'info', 'ghost'
        ].includes(value)
    },
    disabled: {
        type: Boolean,
        default: false
    }
})

defineEmits(['click'])

const variantClasses = computed(() => {
    const variants = {
        primary: 'text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 border border-indigo-200 dark:border-indigo-700 rounded hover:bg-indigo-50 dark:hover:bg-indigo-900/20',
        secondary: 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-800',
        success: 'text-emerald-600 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300 border border-emerald-200 dark:border-emerald-700 rounded hover:bg-emerald-50 dark:hover:bg-emerald-900/20',
        warning: 'text-amber-600 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 border border-amber-200 dark:border-amber-700 rounded hover:bg-amber-50 dark:hover:bg-amber-900/20',
        danger: 'text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 border border-red-200 dark:border-red-700 rounded hover:bg-red-50 dark:hover:bg-red-900/20',
        info: 'text-cyan-600 dark:text-cyan-400 hover:text-cyan-900 dark:hover:text-cyan-300 border border-cyan-200 dark:border-cyan-700 rounded hover:bg-cyan-50 dark:hover:bg-cyan-900/20',
        ghost: 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-800'
    }
    return variants[props.variant]
})
</script> 