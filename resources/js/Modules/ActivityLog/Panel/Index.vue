<template>
  <PanelLayout 
    title="Aktivite Logları" 
    page-title="Aktivite Logları"
    :breadcrumbs="[
      { title: 'Dashboard', url: '/dashboard' },
      { title: 'Aktivite Logları' }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Aktivite Logları"
      description="Sistemdeki tüm kullanıcı aktivitelerini, model değişikliklerini ve sistem olaylarını görüntüleyin."
    >
      <template #actions>
        <ActionButton 
          @click="showStats = !showStats" 
          variant="ghost" 
          size="sm"
          :class="{ 'bg-gray-100 dark:bg-gray-700': showStats }"
          title="İstatistikleri Göster/Gizle"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
        </ActionButton>
        <ActionButton 
          @click="showFilters = !showFilters" 
          variant="ghost" 
          size="sm"
          :class="{ 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700': showFilters || hasActiveFilters }"
          title="Filtreleri Göster/Gizle"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
          </svg>
          <span v-if="activeFilterCount > 0" class="ml-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-blue-100 bg-blue-600 rounded-full">
            {{ activeFilterCount }}
          </span>
        </ActionButton>
        <ActionButton 
          @click="showCleanupModal = true" 
          variant="warning" 
          size="sm"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
          Temizle
        </ActionButton>
      </template>
    </PageHeader>

    <!-- Stats Cards -->
    <div v-if="showStats" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
      <InPageStatCard
        title="Toplam Log"
        :value="stats.total"
        color="blue"
        :icon="DocumentIcon"
      />
      
      <InPageStatCard
        title="Bugün"
        :value="stats.today"
        color="green"
        :icon="CalendarIcon"
      />
      
      <InPageStatCard
        title="Bu Hafta"
        :value="stats.this_week"
        color="purple"
        :icon="ClockIcon"
      />
      
      <InPageStatCard
        title="Bu Ay"
        :value="stats.this_month"
        color="orange"
        :icon="ChartIcon"
      />
    </div>

    <!-- Filter Card -->
    <FilterCard 
      :show="showFilters"
      :filters="filters"
      title="Log Filtreleri"
      :filter-config="filterConfig"
      @update-filter="updateFilter"
      @apply-filters="applyFilters"
      @clear-filters="clearFilters"
      @close="showFilters = false"
    />

    <!-- Logs Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Log Listesi</h3>
          <div class="flex items-center space-x-3">
            <SearchInput
              :model-value="searchQuery"
              @update:model-value="updateSearch"
              placeholder="Log ara..."
              clearable
              class="w-full sm:w-64"
            />
            <ActionButton variant="secondary" @click="exportExcel" size="sm">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              Excel
            </ActionButton>
          </div>
        </div>
      </div>
      
      <LogList :logs="logs.data" @view="viewLog" />
    </div>

    <!-- Pagination -->
    <Pagination :links="logs.links" @navigate="goToPage" />

    <!-- Cleanup Modal -->
    <Modal :show="showCleanupModal" @close="showCleanupModal = false">
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Eski Logları Temizle</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
          Belirtilen günden eski logları kalıcı olarak sileceksiniz. Bu işlem geri alınamaz.
        </p>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Kaç günden eski loglar silinsin?
            </label>
            <input
              v-model="cleanupDays"
              type="number"
              min="1"
              max="365"
              class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:border-blue-500"
              placeholder="30"
            >
          </div>
          
          <div class="flex items-center justify-end space-x-3">
            <ActionButton variant="secondary" @click="showCleanupModal = false">
              İptal
            </ActionButton>
            <ActionButton variant="warning" @click="cleanupLogs" :loading="cleanupLoading">
              Temizle
            </ActionButton>
          </div>
        </div>
      </div>
    </Modal>

    <!-- Log Detail Modal -->
    <Modal :show="showLogModal" @close="closeLogModal">
      <div v-if="selectedLog" class="p-6 max-w-lg mx-auto">
        <h3 class="text-lg font-bold mb-2 text-gray-900 dark:text-gray-100 flex items-center">
          <span class="mr-2">#{{ selectedLog.id }}</span>
          <span class="text-xs px-2 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 ml-2">{{ selectedLog.model_name }}</span>
        </h3>
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-300">
          <span class="font-semibold">İşlem:</span> {{ selectedLog.formatted_description }}<br>
          <span class="font-semibold">Açıklama:</span> {{ selectedLog.description }}<br>
          <span class="font-semibold">Tarih:</span> {{ formatDate(selectedLog.created_at) }} {{ formatTime(selectedLog.created_at) }}<br>
          <span class="font-semibold">Kullanıcı:</span> {{ selectedLog.causer?.full_name || selectedLog.user_name || 'Sistem' }}<br>
          <span class="font-semibold">IP:</span> {{ selectedLog.ip_address || '-' }}<br>
          <span class="font-semibold">User Agent:</span> {{ selectedLog.user_agent || '-' }}
        </div>
        <div v-if="selectedLog.formatted_changes && selectedLog.formatted_changes.length" class="mb-4">
          <div class="font-semibold mb-1">Değişiklikler:</div>
          <ul class="text-xs text-gray-700 dark:text-gray-200 list-disc ml-5">
            <li v-for="change in selectedLog.formatted_changes" :key="change.field">
              <span class="font-bold">{{ change.field_name || change.field }}:</span>
              <span class="line-through text-red-500">{{ change.old }}</span>
              <span class="mx-1">→</span>
              <span class="text-green-600">{{ change.new }}</span>
            </li>
          </ul>
        </div>
        <div class="flex justify-end mt-6">
          <ActionButton variant="secondary" @click="closeLogModal">Kapat</ActionButton>
        </div>
      </div>
    </Modal>
  </PanelLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { 
  DocumentIcon, 
  CalendarIcon, 
  ClockIcon, 
  ChartBarIcon as ChartIcon 
} from '@heroicons/vue/24/outline'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import SearchInput from '@/Components/Panel/Actions/SearchInput.vue'
import InPageStatCard from '@/Components/Panel/InPageStatCard.vue'
import FilterCard from '@/Components/Panel/FilterCard.vue'
import LogList from '@/Components/Panel/ActivityLog/LogList.vue'
import Pagination from '@/Components/Panel/Shared/Pagination.vue'
import Modal from '@/Components/Panel/Modal.vue'

const props = defineProps({ 
  logs: Object,
  stats: {
    type: Object,
    default: () => ({
      total: 0,
      today: 0,
      this_week: 0,
      this_month: 0
    })
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

// Reactive data
const searchQuery = ref(props.filters.search || '')
const showFilters = ref(false)
const showStats = ref(false)
const showCleanupModal = ref(false)
const cleanupDays = ref(30)
const cleanupLoading = ref(false)

// State
const selectedLog = ref(null)
const showLogModal = ref(false)

// Filter configuration
const filterConfig = [
  {
    key: 'log_name',
    label: 'Log Türü',
    type: 'select',
    placeholder: 'Tümü',
    options: [
      { value: 'user', label: 'Kullanıcı' },
      { value: 'mail', label: 'Mail' },
      { value: 'system', label: 'Sistem' }
    ]
  },
  {
    key: 'dateFrom',
    label: 'Başlangıç Tarihi',
    type: 'date'
  },
  {
    key: 'dateTo',
    label: 'Bitiş Tarihi',
    type: 'date'
  },
  {
    key: 'causer_id',
    label: 'Kullanıcı ID',
    type: 'text',
    placeholder: 'Kullanıcı ID girin'
  }
]

// Computed
const activeFilterCount = computed(() => {
  return Object.values(props.filters).filter(value => value !== '').length
})

const hasActiveFilters = computed(() => {
  return activeFilterCount.value > 0
})

// Methods
const goToPage = (url) => { 
  router.visit(url) 
}

const updateSearch = (value) => {
  searchQuery.value = value
  router.get(route('panel.activity-logs.index'), { search: value }, { preserveState: true })
}

const exportExcel = () => { 
  console.log('Excel export')
}

const updateFilter = (key, value) => {
  props.filters[key] = value
}

const applyFilters = () => {
  router.get(route('panel.activity-logs.index'), props.filters, { preserveState: true })
}

const clearFilters = () => {
  router.get(route('panel.activity-logs.index'), {}, { preserveState: true })
}

const viewLog = (log) => {
  selectedLog.value = log
  showLogModal.value = true
}

const closeLogModal = () => {
  showLogModal.value = false
  selectedLog.value = null
}

const cleanupLogs = async () => {
  cleanupLoading.value = true
  
  try {
    await router.post(route('panel.activity-logs.cleanup'), { days: cleanupDays.value })
    showCleanupModal.value = false
  } catch (error) {
    console.error('Cleanup error:', error)
  } finally {
    cleanupLoading.value = false
  }
}

// Helper functions (assuming these are defined elsewhere or will be added)
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString()
}

const formatTime = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleTimeString()
}
</script> 