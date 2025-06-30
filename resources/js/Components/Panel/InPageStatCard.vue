<template>
    <div class="flex items-center space-x-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-3 hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
        <!-- Icon -->
        <div :class="[
            'flex items-center justify-center w-8 h-8 rounded-lg',
            iconBgColor
        ]">
            <component :is="icon" :class="['w-4 h-4', iconColor]" />
        </div>
        
        <!-- Content -->
        <div class="flex-1 min-w-0">
            <div class="flex items-baseline space-x-2">
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ formattedValue }}</p>
                <p v-if="change" :class="[
                    'text-xs font-medium',
                    changeType === 'increase' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'
                ]">
                    {{ change }}
                </p>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ title }}</p>
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
    }
})

const iconBgColor = computed(() => {
    const colors = {
        blue: 'bg-blue-50 dark:bg-blue-900/20',
        green: 'bg-emerald-50 dark:bg-emerald-900/20',
        yellow: 'bg-amber-50 dark:bg-amber-900/20',
        purple: 'bg-purple-50 dark:bg-purple-900/20',
        red: 'bg-red-50 dark:bg-red-900/20',
        gray: 'bg-gray-50 dark:bg-gray-700',
        indigo: 'bg-indigo-50 dark:bg-indigo-900/20',
        pink: 'bg-pink-50 dark:bg-pink-900/20',
        emerald: 'bg-emerald-50 dark:bg-emerald-900/20',
        orange: 'bg-orange-50 dark:bg-orange-900/20'
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

const formattedValue = computed(() => {
    if (typeof props.value === 'number') {
        return props.value.toLocaleString('tr-TR')
    }
    return props.value
})
</script> 