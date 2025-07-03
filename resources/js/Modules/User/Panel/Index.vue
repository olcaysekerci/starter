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
          @click="toggle('showStats')" 
          variant="ghost" 
          size="sm"
          :class="{ 'bg-gray-100 dark:bg-gray-700': toggles.showStats }"
          title="İstatistikleri Göster/Gizle"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
        </ActionButton>
        <ActionButton 
          @click="toggle('showFilters')" 
          variant="ghost" 
          size="sm"
          :class="{ 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700': toggles.showFilters || hasActiveFilters }"
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
    <div v-if="toggles.showStats" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
      <InPageStatCard
        title="Toplam Kullanıcı"
        :value="stats.totalUsers"
        color="blue"
        :icon="UserIcon"
      />
      
      <InPageStatCard
        title="Aktif Kullanıcılar"
        :value="stats.activeUsers"
        color="emerald"
        change="+8%"
        changeType="increase"
        :icon="CheckCircleIcon"
      />
      
      <InPageStatCard
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
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Kullanıcı Listesi</h3>
          <div class="flex items-center space-x-3">
            <SearchInput
              :model-value="searchQuery"
              @update:model-value="updateSearch"
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
      
      <UserList :users="filteredUsers" @view="viewUser" @edit="editUser" @delete="deleteUser" />
    </div>

    <!-- Pagination -->
    <Pagination :links="users.links" @navigate="goToPage" />

    <!-- Delete Modal -->
    <DeleteModal
      :show="showDeleteModal"
      :title="deleteConfig.title"
      :description="deleteConfig.description"
      :additional-info="deleteConfig.additionalInfo"
      :loading="deleteLoading"
      @close="closeDeleteModal"
      @confirm="confirmDelete"
    />
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
import InPageStatCard from '@/Components/Panel/InPageStatCard.vue'
import FilterCard from '@/Components/Panel/FilterCard.vue'
import UserList from '@/Components/Panel/User/UserList.vue'
import Pagination from '@/Components/Panel/Shared/Pagination.vue'
import DeleteModal from '@/Components/Panel/DeleteModal.vue'
import { 
  useSearch, 
  useToggle, 
  useExport, 
  useNotification,
  useDeleteModal 
} from '@/Composables'

const props = defineProps({ 
  users: Object,
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
      totalUsers: 1234,
      activeUsers: 1189,
      pendingUsers: 45,
      newUsersThisMonth: 156
    })
  }
})

// Composable'ları başlat
const { showSuccess, showError } = useNotification()
const { exportData } = useExport()

// Toggle states
const { toggles, toggle } = useToggle(['showFilters', 'showStats'])

// Search functionality
const { 
  searchQuery, 
  filters, 
  showFilters, 
  showStats, 
  updateSearch, 
  updateFilter, 
  applyFilters, 
  clearFilters,
  activeFilterCount,
  hasActiveFilters,
  totalResults
} = useSearch(props.users.data || [], {
  searchFields: ['first_name', 'last_name', 'email', 'phone'],
  filterConfig: [
    {
      key: 'status',
      label: 'Durum',
      type: 'select',
      options: [
        { value: '', label: 'Tümü' },
        { value: 'active', label: 'Aktif' },
        { value: 'inactive', label: 'Pasif' }
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
      placeholder: 'örn: gmail.com'
    }
  ],
  route: 'panel.users.index'
})

// Delete modal
const { 
  showDeleteModal, 
  deleteLoading, 
  deleteConfig, 
  openDeleteModal, 
  closeDeleteModal, 
  confirmDelete 
} = useDeleteModal()

// Filter configuration for FilterCard component
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
  return props.users.data || []
})

// Methods
const goToPage = (url) => { 
  router.visit(url) 
}

const viewUser = (user) => { 
  router.visit(`/panel/users/${user.id}`)
}

const editUser = (user) => { 
  router.visit(`/panel/users/${user.id}/edit`)
}

const deleteUser = (user) => {
  openDeleteModal(user, {
    title: 'Kullanıcı Silme Onayı',
    description: `"${user.full_name}" kullanıcısını silmek istediğinizden emin misiniz?`,
    additionalInfo: 'Bu işlem geri alınamaz ve kullanıcının tüm verileri kalıcı olarak silinecektir.',
    route: 'panel.users.destroy'
  })
}

const addUser = () => { 
  router.visit('/panel/users/create')
}

const exportExcel = async () => { 
  try {
    await exportData(filteredUsers.value, 'excel', 'kullanicilar.xlsx')
    showSuccess('Kullanıcı listesi başarıyla export edildi!')
  } catch (error) {
    showError('Export işlemi sırasında hata oluştu.')
  }
}
</script> 