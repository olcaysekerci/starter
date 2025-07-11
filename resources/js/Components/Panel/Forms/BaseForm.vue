<template>
  <FormCard
    :title="title"
    :description="description"
    :submit-text="submitText"
    :processing="processing"
    @submit="handleSubmit"
  >
    <slot :form="form" :errors="errors" />
  </FormCard>
</template>

<script setup>
import { computed } from 'vue'
import FormCard from '@/Components/Panel/Forms/FormCard.vue'
import { useForm } from '@/Composables'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: ''
  },
  submitText: {
    type: String,
    default: 'Kaydet'
  },
  initialData: {
    type: Object,
    default: () => ({})
  },
  rules: {
    type: Object,
    default: () => ({})
  },
  route: {
    type: String,
    required: true
  },
  method: {
    type: String,
    default: 'post'
  },
  onSuccess: {
    type: Function,
    default: null
  },
  onError: {
    type: Function,
    default: null
  }
})

const emit = defineEmits(['submit', 'success', 'error'])

const { form, errors, processing, submit } = useForm(props.initialData, {
  rules: props.rules,
  route: props.route,
  method: props.method,
  onSuccess: (data) => {
    if (props.onSuccess) props.onSuccess(data)
    emit('success', data)
  },
  onError: (serverErrors) => {
    if (props.onError) props.onError(serverErrors)
    emit('error', serverErrors)
  }
})

const handleSubmit = async () => {
  emit('submit', form)
  await submit()
}

defineExpose({
  form,
  errors,
  processing,
  submit
})
</script>