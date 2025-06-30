<template>
  <AppLayout title="Profil">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Profil
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <InputLabel for="name" value="Ad Soyad" />
                  <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                  />
                  <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div>
                  <InputLabel for="email" value="E-posta" />
                  <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                  />
                  <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <div>
                  <InputLabel for="phone" value="Telefon" />
                  <TextInput
                    id="phone"
                    v-model="form.phone"
                    type="text"
                    class="mt-1 block w-full"
                  />
                  <InputError :message="form.errors.phone" class="mt-2" />
                </div>

                <div>
                  <InputLabel for="address" value="Adres" />
                  <textarea
                    id="address"
                    v-model="form.address"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    rows="3"
                  ></textarea>
                  <InputError :message="form.errors.address" class="mt-2" />
                </div>
              </div>

              <div class="mt-6 flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                  Güncelle
                </PrimaryButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { defineProps } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputLabel from '@/Components/Shared/InputLabel.vue'
import TextInput from '@/Components/Shared/TextInput.vue'
import InputError from '@/Components/Shared/InputError.vue'
import PrimaryButton from '@/Components/Shared/PrimaryButton.vue'

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  phone: props.user.phone || '',
  address: props.user.address || '',
})

const submit = () => {
  form.put(route('users.profile'))
}
</script> 