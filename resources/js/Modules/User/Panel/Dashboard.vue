<template>
  <PanelLayout 
    title="Kullanıcı Yönetimi" 
    page-title="Kullanıcı Yönetimi"
    :breadcrumbs="[
      { title: 'Dashboard', url: '/dashboard' },
      { title: 'Kullanıcı Yönetimi' }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Kullanıcı Yönetimi"
      description="Kullanıcı hesaplarını görüntüleyin, düzenleyin ve yönetin. Yeni kullanıcı ekleyebilir, mevcut kullanıcıları güncelleyebilirsiniz."
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
          @click="addUser" 
          variant="primary" 
          size="sm"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Yeni Kullanıcı
        </ActionButton>
      </template>
    </PageHeader>

    <!-- Stats Cards -->
    <div v-if="showStats" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
      <StatItem
        title="Toplam Kullanıcı"
        :value="stats.totalUsers"
        color="blue"
        :icon="UserIcon"
      />
      
      <StatItem
        title="Aktif Kullanıcılar"
        :value="stats.activeUsers"
        color="emerald"
        change="+8%"
        changeType="increase"
        :icon="CheckCircleIcon"
      />
      
      <StatItem
        title="Bekleyen Kullanıcılar"
        :value="stats.pendingUsers"
        color="orange"
        change="+3%"
        changeType="increase"
        :icon="ClockIcon"
      />
    </div>

    <!-- Filter Card -->
    <FilterCard 
      :show="showFilters"
      :filters="filters"
      :filter-config="filterConfig"
      @update-filter="updateFilter"
      @apply-filters="applyFilters"
      @clear-filters="clearFilters"
      @close="showFilters = false"
    />

    <!-- Search and Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Kullanıcı Listesi</h3>
          <div class="flex items-center space-x-3">
            <SearchInput
              v-model="searchQuery"
              placeholder="Kullanıcı ara..."
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
      
      <UserList :users="filteredUsers" @edit="editUser" @delete="deleteUser" />
    </div>

    <!-- Pagination -->
    <Pagination :links="users.links" @navigate="goToPage" />
  </PanelLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { 
  UserIcon, 
  CheckCircleIcon, 
  ClockIcon 
} from '@heroicons/vue/24/outline'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import SearchInput from '@/Components/Panel/Actions/SearchInput.vue'
import StatItem from '@/Components/Panel/InPageStatCard.vue'
import FilterCard from '@/Components/Panel/FilterCard.vue'
import UserList from '@/Components/Panel/User/UserList.vue'
import Pagination from '@/Components/Panel/Shared/Pagination.vue'

const props = defineProps({ 
  users: Object,
  stats: {
    type: Object,
    default: () => ({
      totalUsers: 1234,
      activeUsers: 1189,
      pendingUsers: 45,
      newUsersThisMonth: 156
    })
  }
})

// Reactive data
const searchQuery = ref('')
const showFilters = ref(false)
const showStats = ref(false) // Default olarak kapalı
const filters = ref({
  status: '',
  dateFrom: '',
  dateTo: '',
  emailDomain: ''
})

// Filter configuration
const filterConfig = [
  {
    key: 'status',
    label: 'Durum',
    type: 'select',
    placeholder: 'Tümü',
    options: [
      { value: 'active', label: 'Aktif' },
      { value: 'inactive', label: 'Pasif' },
      { value: 'pending', label: 'Bekleyen' }
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
    key: 'emailDomain',
    label: 'E-posta Domain',
    type: 'text',
    placeholder: 'Örn: gmail.com'
  }
]

// Computed
const filteredUsers = computed(() => {
  let filtered = props.users.data || []
  
  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(user => 
      user.name.toLowerCase().includes(query) ||
      user.email.toLowerCase().includes(query)
    )
  }
  
  return filtered
})

const activeFilterCount = computed(() => {
  return Object.values(filters.value).filter(value => value !== '').length
})

const hasActiveFilters = computed(() => {
  return activeFilterCount.value > 0
})

// Methods
const goToPage = (url) => { 
  router.visit(url) 
}

const editUser = (user) => { 
  router.visit(`/panel/users/${user.id}/edit`) 
}

const deleteUser = (user) => {
  if (confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')) {
    router.delete(`/panel/users/${user.id}`)
  }
}

const addUser = () => { 
  router.visit('/panel/users/create') 
}

const exportExcel = () => { 
  // Excel export işlemi
  console.log('Excel export')
}

const updateFilter = (key, value) => {
  filters.value[key] = value
}

const applyFilters = () => {
  // Filter uygulama işlemi
  console.log('Filters applied:', filters.value)
}

const clearFilters = () => {
  filters.value = {
    status: '',
    dateFrom: '',
    dateTo: '',
    emailDomain: ''
  }
}
</script> 