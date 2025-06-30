<template>
  <PanelLayout>
    <template #header>
      <PageHeader title="Mail Bildirimleri">
        <template #actions>
          <Button @click="showTestModal = true" variant="primary">
            <EnvelopeIcon class="w-4 h-4 mr-2" />
            Test Mail Gönder
          </Button>
          <Button @click="retryFailedMails" variant="secondary" :disabled="stats.failed === 0">
            <ArrowPathIcon class="w-4 h-4 mr-2" />
            Başarısız Mailleri Yeniden Dene
          </Button>
        </template>
      </PageHeader>
    </template>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
      <InPageStatCard
        title="Toplam Mail"
        :value="stats.total"
        icon="EnvelopeIcon"
        color="blue"
      />
      <InPageStatCard
        title="Gönderildi"
        :value="stats.sent"
        icon="CheckCircleIcon"
        color="green"
      />
      <InPageStatCard
        title="Başarısız"
        :value="stats.failed"
        icon="XCircleIcon"
        color="red"
      />
      <InPageStatCard
        title="Bugün"
        :value="stats.today"
        icon="CalendarIcon"
        color="purple"
      />
    </div>

    <!-- Filtre Kartı -->
    <FilterCard class="mb-6">
      <template #title>
        <div class="flex items-center">
          <FunnelIcon class="w-5 h-5 mr-2" />
          Filtreler
        </div>
      </template>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <SearchInput
          v-model="filters.search"
          placeholder="Ara..."
          @update:model-value="applyFilters"
        />
        
        <Select
          v-model="filters.status"
          :options="filterOptions.status"
          placeholder="Durum"
          @update:model-value="applyFilters"
        />
        
        <Select
          v-model="filters.type"
          :options="filterOptions.type"
          placeholder="Tür"
          @update:model-value="applyFilters"
        />
        
        <TextInput
          v-model="filters.recipient"
          placeholder="Alıcı"
          @update:model-value="applyFilters"
        />
        
        <TextInput
          v-model="filters.date_from"
          type="date"
          placeholder="Başlangıç"
          @update:model-value="applyFilters"
        />
        
        <TextInput
          v-model="filters.date_to"
          type="date"
          placeholder="Bitiş"
          @update:model-value="applyFilters"
        />
      </div>
    </FilterCard>

    <!-- Mail Logları Tablosu -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
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
                <div class="flex space-x-2">
                  <TableActionButton
                    @click="viewMail(mailLog.id)"
                    variant="info"
                    size="sm"
                  >
                    <EyeIcon class="w-4 h-4" />
                  </TableActionButton>
                  <TableActionButton
                    v-if="mailLog.status === 'failed' && mailLog.can_retry"
                    @click="retrySingleMail(mailLog.id)"
                    variant="warning"
                    size="sm"
                  >
                    <ArrowPathIcon class="w-4 h-4" />
                  </TableActionButton>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination
        v-if="mailLogs.links"
        :links="mailLogs.links"
        class="px-6 py-3"
      />
    </div>

    <!-- Test Mail Modal -->
    <Modal v-model="showTestModal" title="Test Mail Gönder">
      <form @submit.prevent="sendTestMail" class="space-y-4">
        <FormGroup label="E-posta Adresi" required>
          <TextInput
            v-model="testMailForm.email"
            type="email"
            placeholder="test@example.com"
            required
          />
        </FormGroup>
        
        <FormGroup label="Konu">
          <TextInput
            v-model="testMailForm.subject"
            placeholder="Test Mail - Sistem Kontrolü"
          />
        </FormGroup>
      </form>

      <template #footer>
        <div class="flex justify-end space-x-3">
          <Button @click="showTestModal = false" variant="secondary">
            İptal
          </Button>
          <Button @click="sendTestMail" variant="primary" :loading="sendingTestMail">
            Gönder
          </Button>
        </div>
      </template>
    </Modal>
  </PanelLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import InPageStatCard from '@/Components/Panel/Page/InPageStatCard.vue'
import FilterCard from '@/Components/Panel/FilterCard.vue'
import DataTable from '@/Components/Panel/DataTable.vue'
import Pagination from '@/Components/Panel/Shared/Pagination.vue'
import Modal from '@/Components/Global/Modal.vue'
import Button from '@/Components/Global/Button.vue'
import SearchInput from '@/Components/Panel/Actions/SearchInput.vue'
import Select from '@/Components/Global/Forms/Select.vue'
import TextInput from '@/Components/Global/Forms/TextInput.vue'
import FormGroup from '@/Components/Global/Forms/FormGroup.vue'
import TableActionButton from '@/Components/Panel/Actions/TableActionButton.vue'
import {
  EnvelopeIcon,
  CheckCircleIcon,
  XCircleIcon,
  CalendarIcon,
  FunnelIcon,
  EyeIcon,
  ArrowPathIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  mailLogs: Object,
  stats: Object,
  filters: Object,
  filterOptions: Object,
})

// Reactive data
const showTestModal = ref(false)
const sendingTestMail = ref(false)
const testMailForm = reactive({
  email: '',
  subject: 'Test Mail - Sistem Kontrolü'
})

// Methods
const applyFilters = () => {
  router.get(route('panel.mail-notifications.index'), filters, {
    preserveState: true,
    preserveScroll: true,
  })
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

const formatDate = (date) => {
  return new Date(date).toLocaleString('tr-TR')
}
</script> 