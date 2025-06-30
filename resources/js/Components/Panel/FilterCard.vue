<template>
    <div v-if="show" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Filtreler</h3>
                    <span v-if="activeFilterCount > 0" class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-blue-100 bg-blue-600 rounded-full">
                        {{ activeFilterCount }}
                    </span>
                </div>
                <button 
                    @click="$emit('close')"
                    class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="overflow-hidden">
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Durum</label>
                            <select 
                                :value="filters.status" 
                                @input="updateFilter('status', $event.target.value)"
                                class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:border-blue-500 text-sm"
                            >
                                <option value="">Tümü</option>
                                <option value="active">Aktif</option>
                                <option value="pending">Bekleyen</option>
                            </select>
                        </div>

                        <!-- Date From Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Başlangıç Tarihi</label>
                            <input
                                type="date"
                                :value="filters.dateFrom"
                                @input="updateFilter('dateFrom', $event.target.value)"
                                class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:border-blue-500 text-sm"
                            >
                        </div>

                        <!-- Date To Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bitiş Tarihi</label>
                            <input
                                type="date"
                                :value="filters.dateTo"
                                @input="updateFilter('dateTo', $event.target.value)"
                                class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:border-blue-500 text-sm"
                            >
                        </div>

                        <!-- Email Domain Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">E-posta Domain</label>
                            <input
                                type="text"
                                :value="filters.emailDomain"
                                @input="updateFilter('emailDomain', $event.target.value)"
                                class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:border-blue-500 text-sm"
                                placeholder="Örn: gmail.com"
                            >
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <ActionButton variant="secondary" @click="$emit('clear-filters')" size="sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Temizle
                        </ActionButton>
                        <ActionButton variant="primary" @click="$emit('apply-filters')" size="sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
                            </svg>
                            Filtrele
                        </ActionButton>
                    </div>
                </div>
            </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import ActionButton from './Actions/ActionButton.vue'

const props = defineProps({
    filters: {
        type: Object,
        required: true
    },
    show: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update-filter', 'apply-filters', 'clear-filters', 'close'])

const activeFilterCount = computed(() => {
    return Object.values(props.filters).filter(value => value !== '').length
})

const updateFilter = (key, value) => {
    emit('update-filter', { key, value })
}
</script> 