<template>
  <PanelLayout title="Kullanıcı Yönetimi" current-page="Kullanıcılar">
    <template #breadcrumb>
      <Breadcrumb :items="breadcrumbItems" />
    </template>
    <PageHeader
      title="Kullanıcı Yönetimi"
      description="Kullanıcı hesaplarını görüntüleyin, düzenleyin ve yönetin. Yeni kullanıcı ekleyebilir, mevcut kullanıcıları güncelleyebilirsiniz."
    >
      <template #actions>
        <ExportButton @click="exportExcel" />
        <ActionButton icon="plus" @click="addUser">Yeni Kullanıcı</ActionButton>
      </template>
    </PageHeader>
    <div class="flex items-center justify-between mb-4">
      <SearchInput v-model="searchQuery" placeholder="Kullanıcı ara..." />
      <!-- İsteğe bağlı filtreler buraya eklenebilir -->
    </div>
    <UserList :users="users.data" @edit="editUser" @delete="deleteUser" />
    <Pagination :links="users.links" @navigate="goToPage" />
  </PanelLayout>
</template>

<script setup>
import PanelLayout from '@/Layouts/PanelLayout.vue'
import Breadcrumb from '@/Components/Panel/Header/Breadcrumb.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import ActionButton from '@/Components/Panel/Actions/ActionButton.vue'
import ExportButton from '@/Components/Panel/Actions/ExportButton.vue'
import SearchInput from '@/Components/Panel/Actions/SearchInput.vue'
import UserList from '@/Components/Panel/User/UserList.vue'
import Pagination from '@/Components/Panel/Shared/Pagination.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({ users: Object })
const searchQuery = ref('')
const breadcrumbItems = [
  { label: 'Panel', href: '/dashboard' },
  { label: 'Kullanıcılar', href: '/panel/users' }
]
const goToPage = (url) => { router.visit(url) }
const editUser = (user) => { router.visit(`/panel/users/${user.id}/edit`) }
const deleteUser = (user) => {
  if (confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')) {
    router.delete(`/panel/users/${user.id}`)
  }
}
const addUser = () => { router.visit('/panel/users/create') }
const exportExcel = () => { /* Excel export işlemi */ }
</script> 