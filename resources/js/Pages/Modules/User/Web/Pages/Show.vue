<template>
  <AppLayout :title="user.name">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ user.name }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Kullanıcı Bilgileri</h3>
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Ad Soyad</label>
                    <p class="mt-1 text-sm text-gray-900">{{ user.name }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">E-posta</label>
                    <p class="mt-1 text-sm text-gray-900">{{ user.email }}</p>
                  </div>
                  <div v-if="user.phone">
                    <label class="block text-sm font-medium text-gray-700">Telefon</label>
                    <p class="mt-1 text-sm text-gray-900">{{ user.phone }}</p>
                  </div>
                  <div v-if="user.address">
                    <label class="block text-sm font-medium text-gray-700">Adres</label>
                    <p class="mt-1 text-sm text-gray-900">{{ user.address }}</p>
                  </div>
                </div>
              </div>
              
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Hesap Bilgileri</h3>
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Kayıt Tarihi</label>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(user.created_at) }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Son Güncelleme</label>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(user.updated_at) }}</p>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="mt-8 flex space-x-4">
              <SecondaryButton @click="goBack">
                Geri Dön
              </SecondaryButton>
              <PrimaryButton v-if="canEdit" @click="editUser">
                Düzenle
              </PrimaryButton>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { defineProps, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

const canEdit = computed(() => {
  return props.user.id === usePage().props.auth.user?.id
})

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('tr-TR')
}

const goBack = () => {
  router.visit(route('users.index'))
}

const editUser = () => {
  router.visit(route('users.profile'))
}
</script> 