<template>
  <PanelLayout 
    title="Yetki Yönetimi" 
    page-title="Yetki Yönetimi"
    :breadcrumbs="[
      { title: 'Dashboard', url: '/dashboard' },
      { title: 'Yetki Yönetimi' }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Yetki Yönetimi"
      description="Sistem yetkilerini görüntüleyin, düzenleyin ve yönetin. Yeni yetkiler oluşturabilir, mevcut yetkileri güncelleyebilirsiniz."
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
          @click="addPermission" 
          variant="primary" 
          size="sm"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Yeni Yetki
        </ActionButton>
      </template>
    </PageHeader>

    <!-- Stats Cards -->
    <div v-if="showStats" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
      <StatItem
        title="Toplam Yetki"
        :value="stats.total"
        color="blue"
        :icon="KeyIcon"
      />
      
      <StatItem
        title="Aktif Yetkiler"
        :value="stats.active"
        color="emerald"
        :icon="CheckCircleIcon"
      />
      
      <StatItem
        title="Modül Sayısı"
        :value="stats.modules"
        color="purple"
        :icon="CogIcon"
      />
      
      <StatItem
        title="Atanmamış Yetkiler"
        :value="stats.unassigned"
        color="orange"
        :icon="ExclamationTriangleIcon"
      />
    </div>

    <!-- Search and Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Yetki Listesi</h3>
          <div class="flex items-center space-x-3">
            <SearchInput
              v-model="searchQuery"
              placeholder="Yetki ara..."
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
      
      <PermissionList :permissions="permissions" @edit="editPermission" @delete="deletePermission" @view="viewPermission" />
    </div>

    <!-- Pagination -->
    <Pagination :links="permissions.links" @navigate="goToPage" />
  </PanelLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { 
  KeyIcon, 
  CheckCircleIcon, 
  CogIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import SearchInput from '@/Components/Panel/Actions/SearchInput.vue'
import StatItem from '@/Components/Panel/InPageStatCard.vue'
import PermissionList from '@/Components/Panel/Permission/PermissionList.vue'
import Pagination from '@/Components/Panel/Shared/Pagination.vue'

const props = defineProps({ 
  permissions: Object,
  stats: {
    type: Object,
    default: () => ({
      total: 0,
      active: 0,
      modules: 0,
      unassigned: 0
    })
  },
  searchQuery: {
    type: String,
    default: ''
  }
})

// Reactive data
const searchQuery = ref(props.searchQuery || '')
const showStats = ref(false)

// Computed
const filteredPermissions = computed(() => {
  return props.permissions.data || []
})

// Methods
const addPermission = () => {
  router.visit(route('panel.permissions.create'))
}

const editPermission = (permission) => {
  router.visit(route('panel.permissions.edit', permission.id))
}

const viewPermission = (permission) => {
  router.visit(route('panel.permissions.show', permission.id))
}

const deletePermission = (permission) => {
  if (confirm(`"${permission.display_name}" yetkisini silmek istediğinizden emin misiniz?`)) {
    router.delete(route('panel.permissions.destroy', permission.id), {
      onSuccess: () => {
        // Başarı mesajı göster
      }
    })
  }
}

const exportExcel = () => {
  // Excel export işlemi
  console.log('Excel export')
}

const goToPage = (url) => {
  router.visit(url)
}

// Search handler
const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.visit(route('panel.permissions.search'), {
      data: { query: searchQuery.value }
    })
  }
}
</script> 