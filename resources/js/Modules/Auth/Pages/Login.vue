<template>
  <GuestLayout>
    <Head title="Log in" />

    <AuthenticationCard>
      <template #logo>
        <AuthenticationCardLogo />
      </template>

      <ValidationErrors class="mb-4" />

      <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
        {{ status }}
      </div>

      <form @submit.prevent="submit">
        <div>
          <InputLabel for="email" value="Email" />
          <TextInput
            id="email"
            v-model="form.email"
            type="email"
            class="mt-1 block w-full"
            required
            autofocus
            autocomplete="username"
          />
          <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div class="mt-4">
          <InputLabel for="password" value="Password" />
          <TextInput
            id="password"
            v-model="form.password"
            type="password"
            class="mt-1 block w-full"
            required
            autocomplete="current-password"
          />
          <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="mt-4">
          <label class="flex items-center">
            <Checkbox v-model:checked="form.remember" name="remember" />
            <span class="ml-2 text-sm text-gray-600">Remember me</span>
          </label>
        </div>

        <div class="flex items-center justify-end mt-4">
          <Link
            v-if="canResetPassword"
            :href="route('password.request')"
            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Forgot your password?
          </Link>

          <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Log in
          </PrimaryButton>
        </div>
      </form>
    </AuthenticationCard>
  </GuestLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticationCard from '@/Components/Auth/AuthenticationCard.vue'
import AuthenticationCardLogo from '@/Components/Auth/AuthenticationCardLogo.vue'
import Checkbox from '@/Components/Web/Checkbox.vue'
import InputError from '@/Components/Web/InputError.vue'
import InputLabel from '@/Components/Web/InputLabel.vue'
import PrimaryButton from '@/Components/Web/PrimaryButton.vue'
import TextInput from '@/Components/Web/TextInput.vue'
import ValidationErrors from '@/Components/Web/ValidationErrors.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'

defineProps({
  canResetPassword: {
    type: Boolean,
  },
  status: {
    type: String,
  },
})

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script> 