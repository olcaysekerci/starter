import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'

export function useForm(initialData = {}, options = {}) {
  // Form state
  const form = reactive({ ...initialData })
  const errors = ref({})
  const processing = ref(false)
  const isDirty = ref(false)
  const originalData = ref({ ...initialData })

  // Options
  const {
    rules = {},
    route = null,
    method = 'post',
    preserveState = true,
    preserveScroll = true,
    onSuccess = null,
    onError = null,
    onFinish = null
  } = options

  // Computed
  const hasErrors = computed(() => Object.keys(errors.value).length > 0)
  const isValid = computed(() => !hasErrors.value && isDirty.value)
  const isChanged = computed(() => {
    return JSON.stringify(form) !== JSON.stringify(originalData.value)
  })

  // Validation rules
  const validationRules = {
    required: (value) => value !== null && value !== undefined && value !== '',
    email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
    min: (value, min) => value && value.length >= min,
    max: (value, max) => value && value.length <= max,
    numeric: (value) => !isNaN(value) && !isNaN(parseFloat(value)),
    phone: (value) => /^[\+]?[0-9\s\-\(\)]+$/.test(value),
    password: (value) => /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(value)
  }

  // Methods
  const validate = (field = null) => {
    const newErrors = {}
    
    if (field) {
      // Single field validation
      const fieldRules = rules[field] || []
      const value = form[field]
      
      for (const rule of fieldRules) {
        if (typeof rule === 'string') {
          if (rule === 'required' && !validationRules.required(value)) {
            newErrors[field] = `${field} alanı zorunludur.`
            break
          } else if (rule === 'email' && !validationRules.email(value)) {
            newErrors[field] = 'Geçerli bir e-posta adresi giriniz.'
            break
          } else if (rule.startsWith('min:')) {
            const min = parseInt(rule.split(':')[1])
            if (!validationRules.min(value, min)) {
              newErrors[field] = `${field} en az ${min} karakter olmalıdır.`
              break
            }
          } else if (rule.startsWith('max:')) {
            const max = parseInt(rule.split(':')[1])
            if (!validationRules.max(value, max)) {
              newErrors[field] = `${field} en fazla ${max} karakter olmalıdır.`
              break
            }
          }
        }
      }
    } else {
      // Full form validation
      for (const [fieldName, fieldRules] of Object.entries(rules)) {
        const value = form[fieldName]
        
        for (const rule of fieldRules) {
          if (typeof rule === 'string') {
            if (rule === 'required' && !validationRules.required(value)) {
              newErrors[fieldName] = `${fieldName} alanı zorunludur.`
              break
            } else if (rule === 'email' && !validationRules.email(value)) {
              newErrors[fieldName] = 'Geçerli bir e-posta adresi giriniz.'
              break
            } else if (rule.startsWith('min:')) {
              const min = parseInt(rule.split(':')[1])
              if (!validationRules.min(value, min)) {
                newErrors[fieldName] = `${fieldName} en az ${min} karakter olmalıdır.`
                break
              }
            } else if (rule.startsWith('max:')) {
              const max = parseInt(rule.split(':')[1])
              if (!validationRules.max(value, max)) {
                newErrors[fieldName] = `${fieldName} en fazla ${max} karakter olmalıdır.`
                break
              }
            }
          }
        }
      }
    }

    errors.value = { ...errors.value, ...newErrors }
    return Object.keys(newErrors).length === 0
  }

  const clearErrors = (field = null) => {
    if (field) {
      delete errors.value[field]
    } else {
      errors.value = {}
    }
  }

  const setError = (field, message) => {
    errors.value[field] = message
  }

  const setField = (field, value) => {
    form[field] = value
    isDirty.value = true
    clearErrors(field)
  }

  const reset = () => {
    Object.assign(form, originalData.value)
    clearErrors()
    isDirty.value = false
    processing.value = false
  }

  const update = (data) => {
    Object.assign(form, data)
    isDirty.value = true
  }

  // Submit methods
  const submit = async (customRoute = null, customMethod = null) => {
    if (!validate()) {
      return false
    }

    processing.value = true
    let submitRoute = customRoute || route
    const submitMethod = customMethod || method

    if (!submitRoute) {
      processing.value = false
      return false
    }

    // Route string ise Ziggy ile çözümle
    if (typeof submitRoute === 'string' && !submitRoute.startsWith('/')) {
      try {
        submitRoute = window.route(submitRoute)
      } catch (error) {
        processing.value = false
        return false
      }
    }

    try {
      await router[submitMethod](submitRoute, form, {
        preserveState,
        preserveScroll,
        onSuccess: (page) => {
          if (onSuccess) onSuccess(page)
          isDirty.value = false
          originalData.value = { ...form }
        },
        onError: (serverErrors) => {
          if (onError) onError(serverErrors)
          errors.value = serverErrors || {}
        },
        onFinish: () => {
          processing.value = false
          if (onFinish) onFinish()
        }
      })
      return true
    } catch (error) {
      processing.value = false
      return false
    }
  }

  const post = (route) => submit(route, 'post')
  const put = (route) => submit(route, 'put')
  const patch = (route) => submit(route, 'patch')
  const deleteMethod = (route) => submit(route, 'delete')

  // File upload
  const uploadFile = async (file, field = 'file') => {
    const formData = new FormData()
    formData.append(field, file)
    
    processing.value = true
    
    try {
      await router.post('/upload', formData, {
        onSuccess: (response) => {
          form[field] = response.data.url
        },
        onError: (errors) => {
          setError(field, errors[field])
        }
      })
    } finally {
      processing.value = false
    }
  }

  return {
    // State
    form,
    errors,
    processing,
    isDirty,
    hasErrors,
    isValid,
    isChanged,
    
    // Methods
    validate,
    clearErrors,
    setError,
    setField,
    reset,
    update,
    submit,
    post,
    put,
    patch,
    deleteMethod,
    uploadFile
  }
} 