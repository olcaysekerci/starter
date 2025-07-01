<template>
  <PanelLayout 
    title="Mail Bildirimleri" 
    page-title="Mail Bildirimleri"
    :breadcrumbs="[
      { title: 'Dashboard', url: '/dashboard' },
      { title: 'Mail Bildirimleri' }
    ]"
  >
    <!-- Page Header -->
    <PageHeader
      title="Mail Bildirimleri"
      description="Sistemdeki tüm mail gönderimlerini, durumlarını ve loglarını görüntüleyin."
    >
      <template #actions>
        <ActionButton 
          @click="showTestModal = true" 
          variant="primary" 
          size="sm"
        >
          <EnvelopeIcon class="w-4 h-4 mr-2" />
          Test Mail
        </ActionButton>
        <ActionButton 
          @click="showStats = !showStats" 
          variant="ghost" 
          size="sm"
          :class="{ 'bg-gray-100 dark:bg-gray-700': showStats }"
          title="İstatistikleri Göster/Gizle"
        >
          <ChartBarIcon class="w-4 h-4" />
        </ActionButton>
        <ActionButton 
          @click="showFilters = !showFilters" 
          variant="ghost" 
          size="sm"
          :class="{ 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700': showFilters || hasActiveFilters }"
          title="Filtreleri Göster/Gizle"
        >
          <FunnelIcon class="w-4 h-4" />
          <span v-if="activeFilterCount > 0" class="ml-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-blue-100 bg-blue-600 rounded-full">
            {{ activeFilterCount }}
          </span>
        </ActionButton>
        <ActionButton 
          @click="retryFailedMails" 
          variant="warning" 
          size="sm"
          :disabled="stats.failed === 0"
        >
          <ArrowPathIcon class="w-4 h-4 mr-2" />
          Yeniden Dene
        </ActionButton>
      </template>
    </PageHeader>

    <!-- Stats Cards -->
    <div v-if="showStats" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
      <InPageStatCard
        title="Toplam Mail"
        :value="stats.total"
        color="blue"
        :icon="EnvelopeIcon"
      />
      <InPageStatCard
        title="Gönderildi"
        :value="stats.sent"
        color="green"
        :icon="CheckCircleIcon"
      />
      <InPageStatCard
        title="Başarısız"
        :value="stats.failed"
        color="red"
        :icon="XCircleIcon"
      />
      <InPageStatCard
        title="Bugün"
        :value="stats.today"
        color="purple"
        :icon="CalendarIcon"
      />
    </div>

    <!-- Filter Card -->
    <FilterCard 
      :show="showFilters"
      :filters="filters"
      title="Mail Filtreleri"
      :filter-config="filterConfig"
      @update-filter="updateFilter"
      @apply-filters="applyFilters"
      @clear-filters="clearFilters"
      @close="showFilters = false"
    />

    <!-- Mail Logs Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Mail Logları</h3>
          <div class="flex items-center space-x-3">
            <SearchInput
              v-model="searchQuery"
              placeholder="Mail ara..."
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
      
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Alıcı
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Konu
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Tür
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Durum
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Tarih
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                İşlemler
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="mailLog in mailLogs.data" :key="mailLog.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                {{ mailLog.recipient }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                <div class="max-w-xs truncate" :title="mailLog.subject">
                  {{ mailLog.subject }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                  {{ mailLog.type }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="mailLog.status_badge_class" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                  {{ mailLog.status_label }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                {{ formatDate(mailLog.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex items-center justify-end space-x-1">
                  <TableActionButton
                    @click="viewMail(mailLog.id)"
                    variant="info"
                    size="sm"
                    title="Görüntüle"
                  >
                    <EyeIcon class="w-4 h-4" />
                  </TableActionButton>
                  <TableActionButton
                    v-if="mailLog.status === 'failed' && mailLog.can_retry"
                    @click="retrySingleMail(mailLog.id)"
                    variant="warning"
                    size="sm"
                    title="Yeniden Dene"
                  >
                    <ArrowPathIcon class="w-4 h-4" />
                  </TableActionButton>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <Pagination :links="mailLogs.links" @navigate="goToPage" />

    <!-- Test Mail Modal -->
    <Modal :show="showTestModal" @close="showTestModal = false">
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Test Mail Gönder</h3>
        
        <form @submit.prevent="sendTestMail" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              E-posta Adresi
            </label>
            <input
              v-model="testMailForm.email"
              type="email"
              required
              class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:border-blue-500"
              placeholder="test@example.com"
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Konu
            </label>
            <input
              v-model="testMailForm.subject"
              type="text"
              class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:border-blue-500"
              placeholder="Test Mail - Sistem Kontrolü"
            >
          </div>
          
          <div class="flex items-center justify-end space-x-3">
            <ActionButton variant="secondary" @click="showTestModal = false">
              İptal
            </ActionButton>
            <ActionButton variant="primary" @click="sendTestMail" :loading="sendingTestMail">
              Gönder
            </ActionButton>
          </div>
        </form>
      </div>
    </Modal>
  </PanelLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import {
  EnvelopeIcon,
  CheckCircleIcon,
  XCircleIcon,
  CalendarIcon,
  FunnelIcon,
  EyeIcon,
  ArrowPathIcon,
  ChartBarIcon
} from '@heroicons/vue/24/outline'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import TableActionButton from '@/Components/Panel/Actions/TableActionButton.vue'
import SearchInput from '@/Components/Panel/Actions/SearchInput.vue'
import InPageStatCard from '@/Components/Panel/InPageStatCard.vue'
import FilterCard from '@/Components/Panel/FilterCard.vue'
import Pagination from '@/Components/Panel/Shared/Pagination.vue'
import Modal from '@/Components/Panel/Modal.vue'

// Props
const props = defineProps({
  mailLogs: Object,
  stats: Object,
  filters: Object,
  filterOptions: Object,
})

// Reactive data
const searchQuery = ref('')
const showFilters = ref(false)
const showStats = ref(true)
const showTestModal = ref(false)
const sendingTestMail = ref(false)
const testMailForm = reactive({
  email: '',
  subject: 'Test Mail - Sistem Kontrolü'
})

// Filter configuration
const filterConfig = [
  {
    key: 'status',
    label: 'Durum',
    type: 'select',
    placeholder: 'Tümü',
    options: [
      { value: 'sent', label: 'Gönderildi' },
      { value: 'failed', label: 'Başarısız' },
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
const hasActiveFilters = computed(() => {
  return Object.values(props.filters).some(value => value && value !== '')
})

const activeFilterCount = computed(() => {
  return Object.values(props.filters).filter(value => value && value !== '').length
})

// Methods
const updateFilter = (key, value) => {
  props.filters[key] = value
}

const applyFilters = () => {
  router.get(route('panel.mail-notifications.index'), props.filters, {
    preserveState: true,
    preserveScroll: true,
  })
}

const clearFilters = () => {
  Object.keys(props.filters).forEach(key => {
    props.filters[key] = ''
  })
  applyFilters()
}

const goToPage = (url) => {
  router.visit(url)
}

const viewMail = (id) => {
  router.get(route('panel.mail-notifications.show', id))
}

const sendTestMail = async () => {
  sendingTestMail.value = true
  
  try {
    await router.post(route('panel.mail-notifications.test'), testMailForm, {
      onSuccess: () => {
        showTestModal.value = false
        testMailForm.email = ''
        testMailForm.subject = 'Test Mail - Sistem Kontrolü'
      }
    })
  } finally {
    sendingTestMail.value = false
  }
}

const retryFailedMails = async () => {
  await router.post(route('panel.mail-notifications.retry'))
}

const retrySingleMail = async (id) => {
  await router.post(route('panel.mail-notifications.retry-single', id))
}

const exportExcel = () => {
  // Excel export işlemi
  console.log('Excel export')
}

const formatDate = (date) => {
  return new Date(date).toLocaleString('tr-TR')
}
</script> 