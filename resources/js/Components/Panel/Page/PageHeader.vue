<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-start">
            <div class="flex-grow min-w-0">
                <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ title }}</h1>
                
                <!-- Description -->
                <p v-if="description" class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ description }}
                </p>
            </div>
            
            <!-- Actions -->
            <div v-if="$slots.actions" class="flex items-center space-x-2 mt-4 lg:mt-0 lg:ml-6 lg:w-auto lg:flex-shrink-0">
                <slot name="actions" />
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: null
    }
})
</script> 