import { ref } from 'vue'

export function useNotification() {
  // State
  const notifications = ref([])
  const maxNotifications = 5
  const defaultDuration = 5000

  // Notification types
  const types = {
    success: {
      icon: '✓',
      class: 'bg-green-500 text-white',
      title: 'Başarılı'
    },
    error: {
      icon: '✕',
      class: 'bg-red-500 text-white',
      title: 'Hata'
    },
    warning: {
      icon: '⚠',
      class: 'bg-yellow-500 text-white',
      title: 'Uyarı'
    },
    info: {
      icon: 'ℹ',
      class: 'bg-blue-500 text-white',
      title: 'Bilgi'
    }
  }

  // Methods
  const addNotification = (type, message, options = {}) => {
    const {
      title = null,
      duration = defaultDuration,
      persistent = false,
      action = null,
      dismissible = true
    } = options

    const id = Date.now() + Math.random()
    const notification = {
      id,
      type,
      message,
      title: title || types[type]?.title || type,
      icon: types[type]?.icon || '•',
      class: types[type]?.class || 'bg-gray-500 text-white',
      duration,
      persistent,
      action,
      dismissible,
      timestamp: new Date()
    }

    notifications.value.unshift(notification)

    // Limit notifications
    if (notifications.value.length > maxNotifications) {
      notifications.value = notifications.value.slice(0, maxNotifications)
    }

    // Auto remove if not persistent
    if (!persistent && duration > 0) {
      setTimeout(() => {
        removeNotification(id)
      }, duration)
    }

    return id
  }

  const removeNotification = (id) => {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index > -1) {
      notifications.value.splice(index, 1)
    }
  }

  const clearNotifications = () => {
    notifications.value = []
  }

  const clearByType = (type) => {
    notifications.value = notifications.value.filter(n => n.type !== type)
  }

  // Convenience methods
  const showSuccess = (message, options = {}) => {
    return addNotification('success', message, options)
  }

  const showError = (message, options = {}) => {
    return addNotification('error', message, options)
  }

  const showWarning = (message, options = {}) => {
    return addNotification('warning', message, options)
  }

  const showInfo = (message, options = {}) => {
    return addNotification('info', message, options)
  }

  // Toast methods (shorter duration)
  const toast = (message, type = 'info', duration = 3000) => {
    return addNotification(type, message, { duration, persistent: false })
  }

  const toastSuccess = (message, duration = 3000) => {
    return toast(message, 'success', duration)
  }

  const toastError = (message, duration = 5000) => {
    return toast(message, 'error', duration)
  }

  const toastWarning = (message, duration = 4000) => {
    return toast(message, 'warning', duration)
  }

  const toastInfo = (message, duration = 3000) => {
    return toast(message, 'info', duration)
  }

  // Alert methods (persistent)
  const alert = (message, type = 'info', options = {}) => {
    return addNotification(type, message, { ...options, persistent: true })
  }

  const alertSuccess = (message, options = {}) => {
    return alert(message, 'success', options)
  }

  const alertError = (message, options = {}) => {
    return alert(message, 'error', options)
  }

  const alertWarning = (message, options = {}) => {
    return alert(message, 'warning', options)
  }

  const alertInfo = (message, options = {}) => {
    return alert(message, 'info', options)
  }

  // Confirmation dialog
  const confirm = (message, options = {}) => {
    return new Promise((resolve) => {
      const {
        title = 'Onay',
        confirmText = 'Evet',
        cancelText = 'Hayır',
        type = 'warning'
      } = options

      const id = addNotification(type, message, {
        title,
        persistent: true,
        dismissible: false,
        action: {
          confirm: {
            text: confirmText,
            class: 'bg-green-500 hover:bg-green-600 text-white',
            onClick: () => {
              removeNotification(id)
              resolve(true)
            }
          },
          cancel: {
            text: cancelText,
            class: 'bg-gray-500 hover:bg-gray-600 text-white',
            onClick: () => {
              removeNotification(id)
              resolve(false)
            }
          }
        }
      })
    })
  }

  // Computed
  const hasNotifications = () => notifications.value.length > 0
  const notificationCount = () => notifications.value.length
  const getNotificationsByType = (type) => notifications.value.filter(n => n.type === type)

  return {
    // State
    notifications,
    types,
    
    // Methods
    addNotification,
    removeNotification,
    clearNotifications,
    clearByType,
    
    // Success methods
    showSuccess,
    toastSuccess,
    alertSuccess,
    
    // Error methods
    showError,
    toastError,
    alertError,
    
    // Warning methods
    showWarning,
    toastWarning,
    alertWarning,
    
    // Info methods
    showInfo,
    toastInfo,
    alertInfo,
    
    // Generic methods
    toast,
    alert,
    confirm,
    
    // Computed
    hasNotifications,
    notificationCount,
    getNotificationsByType
  }
} 