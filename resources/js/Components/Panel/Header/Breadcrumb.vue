<template>
    <nav class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm text-gray-600 dark:text-gray-400 overflow-hidden">        
        <!-- Breadcrumb Items -->
        <template v-for="(crumb, index) in visibleBreadcrumbItems" :key="index">
            <!-- Separator -->
            <svg v-if="index > 0" class="w-3 h-3 sm:w-4 sm:h-4 text-gray-400 dark:text-gray-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            
            <!-- Breadcrumb Link or Text -->
            <Link 
                v-if="crumb.url" 
                :href="crumb.url" 
                class="hover:text-gray-900 dark:hover:text-gray-100 transition-colors truncate"
            >
                {{ crumb.title }}
            </Link>
            <span 
                v-else 
                class="text-gray-900 dark:text-gray-100 font-medium truncate"
            >
                {{ crumb.title }}
            </span>
        </template>
        
        <!-- Show more indicator on mobile -->
        <div v-if="isMobile && breadcrumbItems.length > 2" class="flex items-center space-x-1 sm:hidden">
            <svg class="w-3 h-3 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <span class="text-gray-400 dark:text-gray-500">...</span>
        </div>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed, ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
        validator: (items) => {
            // Her item'ın title property'si olmalı
            return items.every(item => item.title)
        }
    }
})

// Mobile detection
const isMobile = ref(false)

const checkMobile = () => {
    isMobile.value = window.innerWidth < 640
}

// Reactive breadcrumb items
const breadcrumbItems = computed(() => {
    return props.items
})

// Show only last 2 items on mobile
const visibleBreadcrumbItems = computed(() => {
    if (isMobile.value && breadcrumbItems.value.length > 2) {
        return breadcrumbItems.value.slice(-2)
    }
    return breadcrumbItems.value
})

onMounted(() => {
    checkMobile()
    window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
    window.removeEventListener('resize', checkMobile)
})
</script> 