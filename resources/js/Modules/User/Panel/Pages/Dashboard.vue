<template>
  <AppLayout title="Kullanıcı Yönetimi">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Kullanıcı Yönetimi
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <!-- Arama ve Filtreler -->
            <div class="mb-6">
              <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                  <TextInput
                    v-model="searchQuery"
                    type="text"
                    placeholder="Kullanıcı ara..."
                    class="w-full"
                    @input="searchUsers"
                  />
                </div>
                <div class="flex gap-2">
                  <PrimaryButton @click="refreshUsers">
                    Yenile
                  </PrimaryButton>
                </div>
              </div>
            </div>

            <!-- Kullanıcı Tablosu -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Kullanıcı
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      İletişim
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Kayıt Tarihi
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Durum
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      İşlemler
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                          <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">
                              {{ getUserInitials(user.name) }}
                            </span>
                          </div>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">
                            {{ user.name }}
                          </div>
                          <div class="text-sm text-gray-500">
                            ID: {{ user.id }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ user.email }}</div>
                      <div v-if="user.phone" class="text-sm text-gray-500">
                        {{ user.phone }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(user.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        Aktif
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex justify-end space-x-2">
                        <SecondaryButton @click="viewUser(user.id)" size="sm">
                          Görüntüle
                        </SecondaryButton>
                        <PrimaryButton @click="editUser(user.id)" size="sm">
                          Düzenle
                        </PrimaryButton>
                        <DangerButton @click="deleteUser(user.id)" size="sm">
                          Sil
                        </DangerButton>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Sayfalama -->
            <div v-if="users.links" class="mt-6">
              <Pagination :links="users.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import TextInput from '@/Components/Shared/TextInput.vue'
import PrimaryButton from '@/Components/Legacy/PrimaryButton.vue'
import SecondaryButton from '@/Components/Legacy/SecondaryButton.vue'
import DangerButton from '@/Components/Legacy/DangerButton.vue'
import Pagination from '@/Components/Shared/Pagination.vue'

const props = defineProps({
  users: {
    type: Object,
    required: true
  }
})

const searchQuery = ref('')

const searchUsers = () => {
  router.get(route('panel.users.index'), { search: searchQuery.value }, {
    preserveState: true,
    preserveScroll: true
  })
}

const refreshUsers = () => {
  router.visit(route('panel.users.index'))
}

const viewUser = (userId) => {
  router.visit(route('panel.users.show', userId))
}

const editUser = (userId) => {
  router.visit(route('panel.users.edit', userId))
}

const deleteUser = (userId) => {
  if (confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')) {
    router.delete(route('panel.users.destroy', userId))
  }
}

const getUserInitials = (name) => {
  return name
    .split(' ')
    .map(name => name.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('tr-TR')
}
</script> 