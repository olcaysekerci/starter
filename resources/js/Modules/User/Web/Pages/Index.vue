<template>
  <AppLayout title="Kullanıcılar">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Kullanıcılar
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <UserCard
                v-for="user in users"
                :key="user.id"
                :user="user"
                @click="viewUser(user.id)"
              />
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
import UserCard from '../Components/UserCard.vue'

const props = defineProps({
  users: {
    type: Array,
    required: true
  }
})

const viewUser = (userId) => {
  router.visit(route('users.show', userId))
}
</script> 