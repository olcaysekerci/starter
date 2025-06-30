<template>
  <AppLayout :title="`Kullanıcı: ${user.name}`">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Kullanıcı Detayı: {{ user.name }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <!-- Kullanıcı Bilgileri -->
              <div class="lg:col-span-2">
                <div class="bg-gray-50 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Kullanıcı Bilgileri</h3>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

                <div class="mt-6 bg-gray-50 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Hesap Bilgileri</h3>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Kullanıcı ID</label>
                      <p class="mt-1 text-sm text-gray-900">{{ user.id }}</p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">E-posta Doğrulandı</label>
                      <p class="mt-1 text-sm text-gray-900">
                        <span v-if="user.email_verified_at" class="text-green-600">✓ Doğrulandı</span>
                        <span v-else class="text-red-600">✗ Doğrulanmadı</span>
                      </p>
                    </div>
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

              <!-- Yan Panel -->
              <div class="lg:col-span-1">
                <div class="bg-gray-50 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Hızlı İşlemler</h3>
                  <div class="space-y-3">
                    <PrimaryButton @click="editUser" class="w-full">
                      Kullanıcıyı Düzenle
                    </PrimaryButton>
                    <SecondaryButton @click="sendEmail" class="w-full">
                      E-posta Gönder
                    </SecondaryButton>
                    <DangerButton @click="deleteUser" class="w-full">
                      Kullanıcıyı Sil
                    </DangerButton>
                  </div>
                </div>

                <div class="mt-6 bg-gray-50 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">İstatistikler</h3>
                  <div class="space-y-3">
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-600">Hesap Yaşı</span>
                      <span class="text-sm font-medium text-gray-900">{{ getAccountAge(user.created_at) }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-600">Durum</span>
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        Aktif
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-8 flex space-x-4">
              <SecondaryButton @click="goBack">
                Geri Dön
              </SecondaryButton>
              <PrimaryButton @click="editUser">
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
import { defineProps } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PrimaryButton from '@/Components/Legacy/PrimaryButton.vue'
import SecondaryButton from '@/Components/Legacy/SecondaryButton.vue'
import DangerButton from '@/Components/Legacy/DangerButton.vue'

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('tr-TR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getAccountAge = (createdAt) => {
  if (!createdAt) return '-'
  const created = new Date(createdAt)
  const now = new Date()
  const diffTime = Math.abs(now - created)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  return `${diffDays} gün`
}

const goBack = () => {
  router.visit(route('panel.users.index'))
}

const editUser = () => {
  router.visit(route('panel.users.edit', props.user.id))
}

const deleteUser = () => {
  if (confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')) {
    router.delete(route('panel.users.destroy', props.user.id))
  }
}

const sendEmail = () => {
  // E-posta gönderme işlemi
  alert('E-posta gönderme özelliği henüz implement edilmedi.')
}
</script> 