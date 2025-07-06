<template>
  <PanelLayout>
    <!-- Page Header -->
    <PageHeader
      title="Aktivite Logları"
      description="Sistem aktivitelerini takip edin ve izleyin"
      :breadcrumbs="[
        { name: 'Panel', href: route('panel.dashboard') },
        { name: 'Aktivite Logları', href: route('panel.activity-logs.index') }
      ]"
    >
      <template #actions>
        <ActionButton variant="secondary" @click="exportExcel" size="sm">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Excel
        </ActionButton>
      </template>
    </PageHeader>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
      <InPageStatCard
        title="Toplam Log"
        :value="stats.totalLogs"
        icon="ChartBarIcon"
        trend="up"
        :trend-value="12"
        color="blue"
      />
      <InPageStatCard
        title="Bugün"
        :value="stats.todayLogs"
        icon="ClockIcon"
        trend="up"
        :trend-value="8"
        color="green"
      />
      <InPageStatCard
        title="Bu Hafta"
        :value="stats.thisWeekLogs"
        icon="CalendarIcon"
        trend="down"
        :trend-value="3"
        color="yellow"
      />
      <InPageStatCard
        title="Bu Ay"
        :value="stats.thisMonthLogs"
        icon="ChartBarIcon"
        trend="up"
        :trend-value="15"
        color="purple"
      />
    </div>

    <!-- Filter Card -->
    <FilterCard 
      :show="toggles.showFilters"
      :filters="filters"
      :filter-config="filterConfig"
      @update-filter="updateFilter"
      @apply-filters="applyFilters"
      @clear-filters="clearFilters"
      @close="toggle('showFilters')"
    />

    <!-- Search and Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Aktivite Logları</h3>
          <div class="flex items-center space-x-3">
            <SearchInput
              :model-value="searchQuery"
              @update:model-value="updateSearch"
              placeholder="Log ara..."
              clearable
              class="w-full sm:w-64"
            />
            <ActionButton variant="secondary" @click="toggle('showFilters')" size="sm">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
              </svg>
              Filtrele
            </ActionButton>
          </div>
        </div>
      </div>
      
      <ActivityLogList :logs="filteredLogs" @view="viewLog" />
    </div>

    <!-- Pagination -->
    <Pagination :links="logs.links" @navigate="goToPage" />
  </PanelLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { 
  ChartBarIcon, 
  ClockIcon, 
  CalendarIcon, 
  DocumentTextIcon 
} from '@heroicons/vue/24/outline'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import SearchInput from '@/Components/Panel/Actions/SearchInput.vue'
import InPageStatCard from '@/Components/Panel/InPageStatCard.vue'
import FilterCard from '@/Components/Panel/FilterCard.vue'
import ActivityLogList from '@/Components/Panel/ActivityLog/ActivityLogList.vue'
import Pagination from '@/Components/Panel/Shared/Pagination.vue'
import { 
  useSearch, 
  useToggle, 
  useExport, 
  useNotification
} from '@/Composables'

const props = defineProps({ 
  logs: Object,
  filters: {
    type: Object,
    default: () => ({
      search: '',
      per_page: 15
    })
  },
  stats: {
    type: Object,
    default: () => ({
      totalLogs: 1234,
      todayLogs: 45,
      thisWeekLogs: 234,
      thisMonthLogs: 892
    })
  }
})

// Composable'ları başlat
const { showSuccess, showError } = useNotification()
const { exportData } = useExport()

// Toggle states
const { toggles, toggle } = useToggle(['showFilters'])

// Search functionality
const { 
  searchQuery, 
  filters, 
  updateSearch, 
  updateFilter, 
  applyFilters, 
  clearFilters,
  activeFilterCount,
  hasActiveFilters,
  totalResults
} = useSearch(props.logs.data || [], {
  searchFields: ['description', 'user_name', 'event', 'subject_type'],
  filterConfig: [
    {
      key: 'event',
      label: 'Olay Tipi',
      type: 'select',
      options: [
        { value: '', label: 'Tümü' },
        { value: 'created', label: 'Oluşturuldu' },
        { value: 'updated', label: 'Güncellendi' },
        { value: 'deleted', label: 'Silindi' }
      ]
    },
    {
      key: 'subject_type',
      label: 'Model Tipi',
      type: 'select',
      options: [
        { value: '', label: 'Tümü' },
        { value: 'App\\Models\\User', label: 'Kullanıcı' },
        { value: 'App\\Modules\\User\\Models\\Role', label: 'Rol' },
        { value: 'App\\Modules\\User\\Models\\Permission', label: 'İzin' },
        { value: 'App\\Modules\\Settings\\Models\\Setting', label: 'Ayar' }
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
    }
  ],
  route: 'panel.activity-logs.index'
})

// Filter configuration for FilterCard component
const filterConfig = [
  {
    key: 'event',
    label: 'Olay Tipi',
    type: 'select',
    placeholder: 'Tümü',
    options: [
      { value: 'created', label: 'Oluşturuldu' },
      { value: 'updated', label: 'Güncellendi' },
      { value: 'deleted', label: 'Silindi' }
    ]
  },
  {
    key: 'subject_type',
    label: 'Model Tipi',
    type: 'select',
    placeholder: 'Tümü',
    options: [
      { value: 'App\\Models\\User', label: 'Kullanıcı' },
      { value: 'App\\Modules\\User\\Models\\Role', label: 'Rol' },
      { value: 'App\\Modules\\User\\Models\\Permission', label: 'İzin' },
      { value: 'App\\Modules\\Settings\\Models\\Setting', label: 'Ayar' }
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
  }
]

// Computed
const filteredLogs = computed(() => {
  return props.logs.data || []
})

// Methods
const viewLog = (log) => {
  router.visit(route('panel.activity-logs.show', log.id))
}

const exportExcel = async () => {
  try {
    await exportData('activity-logs', {
      filters: filters.value,
      search: searchQuery.value
    })
    showSuccess('Excel dosyası başarıyla indirildi')
  } catch (error) {
    showError('Excel dosyası indirilirken hata oluştu')
  }
}

const goToPage = (url) => {
  router.visit(url, {
    data: {
      ...filters.value,
      search: searchQuery.value
    }
  })
}
</script> 