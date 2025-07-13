<template>
  <PanelLayout 
    title="Rol Yönetimi" 
    page-title="Rol Yönetimi"
    :breadcrumbs="[
      { title: 'Rol Yönetimi' }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Rol Yönetimi"
      description="Sistem rollerini görüntüleyin, düzenleyin ve yönetin. Yeni roller oluşturabilir, mevcut rolleri güncelleyebilirsiniz."
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
          @click="addRole" 
          variant="primary" 
          size="sm"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Yeni Rol
        </ActionButton>
      </template>
    </PageHeader>

    <!-- Stats Cards -->
    <div v-if="showStats" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
      <StatItem
        title="Toplam Rol"
        :value="stats.total"
        color="blue"
        :icon="ShieldCheckIcon"
      />
      
      <StatItem
        title="Aktif Roller"
        :value="stats.active"
        color="emerald"
        :icon="CheckCircleIcon"
      />
      
      <StatItem
        title="Sistem Rolleri"
        :value="stats.system"
        color="purple"
        :icon="CogIcon"
      />
      
      <StatItem
        title="Özel Roller"
        :value="stats.custom"
        color="orange"
        :icon="UserGroupIcon"
      />
    </div>

    <!-- Search and Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Rol Listesi</h3>
          <div class="flex items-center space-x-3">
            <SearchInput
              v-model="searchQuery"
              placeholder="Rol ara..."
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
      
      <RoleList :roles="roles" @edit="editRole" @delete="deleteRole" @view="viewRole" />
    </div>

    <!-- Pagination -->
    <Pagination :links="roles.links" @navigate="goToPage" />

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
  ShieldCheckIcon, 
  CheckCircleIcon, 
  CogIcon,
  UserGroupIcon
} from '@heroicons/vue/24/outline'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import SearchInput from '@/Components/Panel/Actions/SearchInput.vue'
import StatItem from '@/Components/Panel/InPageStatCard.vue'
import RoleList from '@/Components/Panel/Role/RoleList.vue'
import Pagination from '@/Components/Panel/Shared/Pagination.vue'
import DeleteModal from '@/Components/Panel/DeleteModal.vue'
import { useDeleteModal } from '@/Composables/useDeleteModal'

const props = defineProps({ 
  roles: Object,
  stats: {
    type: Object,
    default: () => ({
      total: 0,
      active: 0,
      system: 0,
      custom: 0
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

// Delete modal
const { 
  showDeleteModal, 
  deleteLoading, 
  deleteConfig, 
  openDeleteModal, 
  closeDeleteModal, 
  confirmDelete 
} = useDeleteModal()

// Computed
const filteredRoles = computed(() => {
  return props.roles.data || []
})

// Methods
const addRole = () => {
  router.visit(route('panel.roles.create'))
}

const editRole = (role) => {
  router.visit(route('panel.roles.edit', role.id))
}

const viewRole = (role) => {
  router.visit(route('panel.roles.show', role.id))
}

const deleteRole = (role) => {
  openDeleteModal(role, {
    title: 'Rol Silme Onayı',
    description: `"${role.display_name}" rolünü silmek istediğinizden emin misiniz?`,
    additionalInfo: 'Bu rolü silmek, bu role sahip tüm kullanıcıları etkileyecektir.',
    route: 'panel.roles.destroy'
  })
}

const exportExcel = () => {
  // Excel export işlemi
  // TODO: Implement Excel export functionality
}

const goToPage = (url) => {
  router.visit(url)
}

// Search handler
const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.visit(route('panel.roles.search'), {
      data: { query: searchQuery.value }
    })
  }
}
</script> 