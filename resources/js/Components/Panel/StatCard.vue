<template>
    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <!-- Background Gradient -->
        <div :class="[
            'absolute inset-0 rounded-xl opacity-5',
            gradientBg
        ]"></div>
        
        <!-- Header -->
        <div class="relative flex items-center justify-between mb-3">
            <div :class="[
                'flex items-center justify-center w-10 h-10 rounded-lg shadow-sm',
                iconBgColor
            ]">
                <component :is="icon" :class="['w-5 h-5', iconColor]" />
            </div>
            
            <!-- Trend indicator -->
            <div v-if="change" :class="[
                'flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                changeType === 'increase' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400'
            ]">
                <svg 
                    :class="[
                        'w-3 h-3 mr-0.5',
                        changeType === 'increase' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'
                    ]" 
                    fill="currentColor" 
                    viewBox="0 0 20 20"
                >
                    <path 
                        v-if="changeType === 'increase'"
                        fill-rule="evenodd" 
                        d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" 
                        clip-rule="evenodd"
                    />
                    <path 
                        v-else
                        fill-rule="evenodd" 
                        d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" 
                        clip-rule="evenodd"
                    />
                </svg>
                {{ change }}
            </div>
        </div>
        
        <!-- Content -->
        <div class="relative">
            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ title }}</h3>
            <div class="flex items-baseline space-x-2">
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ formattedValue }}</p>
                <p v-if="unit" class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ unit }}</p>
            </div>
            
            <!-- Change label -->
            <p v-if="change" class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ changeLabel }}</p>
        </div>
        
        <!-- Optional action button -->
        <div v-if="$slots.action" class="relative mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
            <slot name="action" />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [String, Number],
        required: true
    },
    icon: {
        type: [String, Object],
        required: true
    },
    color: {
        type: String,
        default: 'blue',
        validator: (value) => ['blue', 'green', 'yellow', 'purple', 'red', 'gray', 'indigo', 'pink', 'emerald', 'orange'].includes(value)
    },
    change: {
        type: String,
        default: null
    },
    changeType: {
        type: String,
        default: 'increase',
        validator: (value) => ['increase', 'decrease'].includes(value)
    },
    changeLabel: {
        type: String,
        default: 'Son 30 gün'
    },
    description: {
        type: String,
        default: null
    },
    unit: {
        type: String,
        default: null
    }
})

const iconBgColor = computed(() => {
    const colors = {
        blue: 'bg-blue-100 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800',
        green: 'bg-emerald-100 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800',
        yellow: 'bg-amber-100 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800',
        purple: 'bg-purple-100 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-800',
        red: 'bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800',
        gray: 'bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600',
        indigo: 'bg-indigo-100 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800',
        pink: 'bg-pink-100 dark:bg-pink-900/30 border border-pink-200 dark:border-pink-800',
        emerald: 'bg-emerald-100 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800',
        orange: 'bg-orange-100 dark:bg-orange-900/30 border border-orange-200 dark:border-orange-800'
    }
    return colors[props.color]
})

const iconColor = computed(() => {
    const colors = {
        blue: 'text-blue-600 dark:text-blue-400',
        green: 'text-emerald-600 dark:text-emerald-400',
        yellow: 'text-amber-600 dark:text-amber-400',
        purple: 'text-purple-600 dark:text-purple-400',
        red: 'text-red-600 dark:text-red-400',
        gray: 'text-gray-600 dark:text-gray-400',
        indigo: 'text-indigo-600 dark:text-indigo-400',
        pink: 'text-pink-600 dark:text-pink-400',
        emerald: 'text-emerald-600 dark:text-emerald-400',
        orange: 'text-orange-600 dark:text-orange-400'
    }
    return colors[props.color]
})

const gradientBg = computed(() => {
    const gradients = {
        blue: 'bg-gradient-to-br from-blue-500 to-blue-600',
        green: 'bg-gradient-to-br from-emerald-500 to-emerald-600',
        yellow: 'bg-gradient-to-br from-amber-500 to-amber-600',
        purple: 'bg-gradient-to-br from-purple-500 to-purple-600',
        red: 'bg-gradient-to-br from-red-500 to-red-600',
        gray: 'bg-gradient-to-br from-gray-500 to-gray-600',
        indigo: 'bg-gradient-to-br from-indigo-500 to-indigo-600',
        pink: 'bg-gradient-to-br from-pink-500 to-pink-600',
        emerald: 'bg-gradient-to-br from-emerald-500 to-emerald-600',
        orange: 'bg-gradient-to-br from-orange-500 to-orange-600'
    }
    return gradients[props.color]
})

const formattedValue = computed(() => {
    if (typeof props.value === 'number') {
        return props.value.toLocaleString('tr-TR')
    }
    return props.value
})
</script> 