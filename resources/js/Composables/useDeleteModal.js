import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

export function useDeleteModal() {
  const showDeleteModal = ref(false)
  const deleteLoading = ref(false)
  const deleteItem = ref(null)
  const deleteConfig = ref({
    title: 'Silme Onayı',
    description: 'Bu öğeyi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.',
    additionalInfo: '',
    route: null,
    routeParams: {}
  })

  const openDeleteModal = (item, config = {}) => {
    deleteItem.value = item
    deleteConfig.value = {
      title: config.title || 'Silme Onayı',
      description: config.description || 'Bu öğeyi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.',
      additionalInfo: config.additionalInfo || '',
      route: config.route,
      routeParams: config.routeParams || {}
    }
    showDeleteModal.value = true
  }

  const closeDeleteModal = () => {
    showDeleteModal.value = false
    deleteItem.value = null
    deleteLoading.value = false
  }

  const confirmDelete = () => {
    if (!deleteConfig.value.route) {
      return
    }

    deleteLoading.value = true

    // Route parametrelerini hazırla
    const routeParams = { ...deleteConfig.value.routeParams }
    
    // Eğer item ID'si varsa ve routeParams'da id yoksa ekle
    if (deleteItem.value?.id && !routeParams.id) {
      routeParams.id = deleteItem.value.id
    }

    router.delete(route(deleteConfig.value.route, routeParams), {
      onSuccess: () => {
        closeDeleteModal()
      },
      onError: (errors) => {
        deleteLoading.value = false
      },
      onFinish: () => {
        deleteLoading.value = false
      }
    })
  }

  return {
    showDeleteModal,
    deleteLoading,
    deleteItem,
    deleteConfig,
    openDeleteModal,
    closeDeleteModal,
    confirmDelete
  }
} 