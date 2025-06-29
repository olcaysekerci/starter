<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Hoş Geldiniz!</h3>
            <p class="text-gray-600 mb-4">
              Bu Laravel + Inertia + Vue 3 projesinin dashboard sayfasıdır.
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-medium text-blue-900 mb-2">Web Tarafı</h4>
                <p class="text-blue-700 text-sm mb-3">
                  Kullanıcı tarafı işlemleri için aşağıdaki linkleri kullanabilirsiniz.
                </p>
                <div class="space-y-2">
                  <Link 
                    :href="route('users.index')"
                    class="block text-blue-600 hover:text-blue-800 text-sm"
                  >
                    → Kullanıcı Listesi
                  </Link>
                  <Link 
                    :href="route('users.profile')"
                    class="block text-blue-600 hover:text-blue-800 text-sm"
                  >
                    → Profil Düzenleme
                  </Link>
                </div>
              </div>
              
              <div class="bg-green-50 p-4 rounded-lg">
                <h4 class="font-medium text-green-900 mb-2">Panel Tarafı</h4>
                <p class="text-green-700 text-sm mb-3">
                  Admin paneli işlemleri için aşağıdaki linkleri kullanabilirsiniz.
                </p>
                <div class="space-y-2">
                  <Link 
                    :href="route('panel.users.index')"
                    class="block text-green-600 hover:text-green-800 text-sm"
                  >
                    → Kullanıcı Yönetimi
                  </Link>
                  <Link 
                    :href="route('panel.users.show', 1)"
                    class="block text-green-600 hover:text-green-800 text-sm"
                  >
                    → Kullanıcı Detayı
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
</script> 