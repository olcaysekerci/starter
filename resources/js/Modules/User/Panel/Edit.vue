<template>
    <PanelLayout>
        <template #header>
            <PageHeader title="Kullanıcı Düzenle" />
        </template>

        <FormCard>
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Ad -->
                    <FormGroup label="Ad" required>
                        <TextInput
                            v-model="form.first_name"
                            type="text"
                            :error="form.errors.first_name"
                            required
                        />
                    </FormGroup>

                    <!-- Soyad -->
                    <FormGroup label="Soyad" required>
                        <TextInput
                            v-model="form.last_name"
                            type="text"
                            :error="form.errors.last_name"
                            required
                        />
                    </FormGroup>

                    <!-- E-posta -->
                    <FormGroup label="E-posta" required>
                        <TextInput
                            v-model="form.email"
                            type="email"
                            :error="form.errors.email"
                            required
                        />
                    </FormGroup>

                    <!-- Telefon -->
                    <FormGroup label="Telefon">
                        <TextInput
                            v-model="form.phone"
                            type="tel"
                            :error="form.errors.phone"
                        />
                    </FormGroup>

                    <!-- Şifre -->
                    <FormGroup label="Şifre">
                        <TextInput
                            v-model="form.password"
                            type="password"
                            :error="form.errors.password"
                            minlength="6"
                            placeholder="Değiştirmek istemiyorsanız boş bırakın"
                        />
                    </FormGroup>

                    <!-- Şifre Tekrarı -->
                    <FormGroup label="Şifre Tekrarı">
                        <TextInput
                            v-model="form.password_confirmation"
                            type="password"
                            :error="form.errors.password_confirmation"
                            minlength="6"
                            placeholder="Şifre değiştirilecekse tekrar girin"
                        />
                    </FormGroup>
                </div>

                <!-- Adres -->
                <FormGroup label="Adres" class="mt-6">
                    <TextArea
                        v-model="form.address"
                        :error="form.errors.address"
                        rows="3"
                    />
                </FormGroup>

                <!-- Roller -->
                <FormGroup label="Roller" class="mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Checkbox
                            v-for="role in roles"
                            :key="role.id"
                            :id="`role-${role.id}`"
                            v-model:checked="form.roles"
                            :value="role.id"
                            :label="role.display_name || role.name"
                        />
                    </div>
                    <InputError :message="form.errors.roles" class="mt-2" />
                </FormGroup>

                <!-- İzinler -->
                <FormGroup label="İzinler" class="mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Checkbox
                            v-for="permission in permissions"
                            :key="permission.id"
                            :id="`permission-${permission.id}`"
                            v-model:checked="form.permissions"
                            :value="permission.id"
                            :label="permission.display_name || permission.name"
                        />
                    </div>
                    <InputError :message="form.errors.permissions" class="mt-2" />
                </FormGroup>

                <div class="flex justify-end mt-6">
                    <PrimaryButton
                        type="submit"
                        :disabled="form.processing"
                        class="ml-3"
                    >
                        {{ form.processing ? 'Güncelleniyor...' : 'Kullanıcı Güncelle' }}
                    </PrimaryButton>
                </div>
            </form>
        </FormCard>
    </PanelLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import PanelLayout from '@/Layouts/PanelLayout.vue'
import PageHeader from '@/Components/Panel/Page/PageHeader.vue'
import FormCard from '@/Components/Panel/Forms/FormCard.vue'
import FormGroup from '@/Components/Panel/Forms/FormGroup.vue'
import TextInput from '@/Components/Panel/Forms/TextInput.vue'
import TextArea from '@/Components/Panel/Forms/TextArea.vue'
import Checkbox from '@/Components/Panel/Forms/Checkbox.vue'
import PrimaryButton from '@/Components/Shared/PrimaryButton.vue'
import InputError from '@/Components/Shared/InputError.vue'

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    roles: {
        type: Array,
        default: () => []
    },
    permissions: {
        type: Array,
        default: () => []
    }
})

const form = useForm({
    first_name: props.user.first_name || '',
    last_name: props.user.last_name || '',
    email: props.user.email || '',
    phone: props.user.phone || '',
    address: props.user.address || '',
    password: '',
    password_confirmation: '',
    roles: props.user.roles?.map(role => role.id) || [],
    permissions: props.user.permissions?.map(permission => permission.id) || []
})

const submit = () => {
    form.put(route('panel.users.update', props.user.id))
}
</script> 