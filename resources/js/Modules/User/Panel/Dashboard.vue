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
        <ActionButton variant="secondary" @click="showFilters = !showFilters" size="sm">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
          </svg>
          Filtreler
        </ActionButton>
        <ActionButton variant="success" @click="exportExcel" size="sm">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Excel
        </ActionButton>
        <ActionButton variant="primary" @click="addUser" size="sm">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Yeni Kullanıcı
        </ActionButton>
      </template>
    </PageHeader>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <StatCard
        title="Toplam Kullanıcı"
        :value="stats.totalUsers"
        color="blue"
        change="+12%"
        change-type="increase"
        :icon="UserIcon"
      />
      
      <StatCard
        title="Aktif Kullanıcılar"
        :value="stats.activeUsers"
        color="green"
        change="+8%"
        change-type="increase"
        :icon="CheckIcon"
      />
      
      <StatCard
        title="Bekleyen Kullanıcılar"
        :value="stats.pendingUsers"
        color="yellow"
        change="+3%"
        change-type="increase"
        :icon="ClockIcon"
      />
      
      <StatCard
        title="Bu Ay Kayıt"
        :value="stats.newUsersThisMonth"
        color="purple"
        change="+15%"
        change-type="increase"
        :icon="PlusIcon"
      />
    </div>

    <!-- Filter Card -->
    <FilterCard 
      :show="showFilters"
      :filters="filters"
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
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import SearchInput from '@/Components/Panel/Actions/SearchInput.vue'
import StatCard from '@/Components/Panel/StatCard.vue'
import FilterCard from '@/Components/Panel/FilterCard.vue'
import UserList from '@/Components/Panel/User/UserList.vue'
import Pagination from '@/Components/Panel/Shared/Pagination.vue'

// Icon components
const UserIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
  </svg>`
}

const CheckIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
  </svg>`
}

const ClockIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
  </svg>`
}

const PlusIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
  </svg>`
}

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
const filters = ref({
  status: '',
  dateFrom: '',
  dateTo: '',
  emailDomain: ''
})

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

const updateFilter = ({ key, value }) => {
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