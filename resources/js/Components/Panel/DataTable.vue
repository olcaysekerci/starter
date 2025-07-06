<template>
  <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
    <!-- Tablo Başlığı -->
    <div v-if="title || $slots.header" class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center justify-between">
        <h3 v-if="title" class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ title }}</h3>
        <slot name="header" />
      </div>
    </div>

    <!-- Arama ve Filtreler -->
    <div v-if="showSearch || $slots.filters" class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
      <div class="flex flex-col sm:flex-row gap-4">
        <div v-if="showSearch" class="flex-1">
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="searchPlaceholder"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            @input="handleSearch"
          />
        </div>
        <slot name="filters" />
      </div>
    </div>

    <!-- Tablo -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              :class="[
                'px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider',
                column.class || ''
              ]"
            >
              {{ column.label }}
            </th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
          <tr
            v-for="(item, index) in items"
            :key="item.id || index"
            class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 cursor-pointer"
            @click="$emit('row-click', item)"
          >
            <td
              v-for="column in columns"
              :key="column.key"
              :class="[
                'px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100',
                column.class || ''
              ]"
            >
              <slot :name="`cell-${column.key}`" :item="item" :value="item[column.key]">
                {{ formatCellValue(item[column.key], column) }}
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Boş Durum -->
    <div v-if="items.length === 0" class="px-6 py-12 text-center">
      <div class="text-gray-500 dark:text-gray-400">
        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Veri bulunamadı</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ emptyMessage }}</p>
      </div>
    </div>

    <!-- Sayfalama -->
    <div v-if="pagination && pagination.links" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
      <Pagination
        :links="pagination.links"
        :total="pagination.total || 0"
        :from="pagination.from || 0"
        :to="pagination.to || 0"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import Pagination from '@/Components/Shared/Pagination.vue'

const props = defineProps({
  title: {
    type: String,
    default: ''
  },
  columns: {
    type: Array,
    required: true
  },
  items: {
    type: Array,
    required: true
  },
  pagination: {
    type: Object,
    default: null
  },
  showSearch: {
    type: Boolean,
    default: false
  },
  searchPlaceholder: {
    type: String,
    default: 'Ara...'
  },
  emptyMessage: {
    type: String,
    default: 'Henüz veri bulunmuyor.'
  }
})

const emit = defineEmits(['search', 'row-click'])

const searchQuery = ref('')

const handleSearch = () => {
  emit('search', searchQuery.value)
}

const formatCellValue = (value, column) => {
  if (column.formatter) {
    return column.formatter(value)
  }
  
  if (value === null || value === undefined) {
    return '-'
  }
  
  if (typeof value === 'boolean') {
    return value ? 'Evet' : 'Hayır'
  }
  
  if (value instanceof Date) {
    return value.toLocaleDateString('tr-TR')
  }
  
  return value
}
</script> 