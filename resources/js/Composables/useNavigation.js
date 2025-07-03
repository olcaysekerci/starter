// Inertia router'ı import et
import { router } from '@inertiajs/vue3'

// Route helper'ını global olarak kullanabilmek için
const route = window.route || ((name, params = {}) => {
  console.warn(`Route '${name}' not found. Using fallback route generation.`)
  // Fallback route generation
  if (name.includes('.')) {
    const [prefix, action] = name.split('.')
    if (action === 'index') return `/${prefix}`
    if (action === 'create') return `/${prefix}/create`
    if (action === 'show') return `/${prefix}/${params.id}`
    if (action === 'edit') return `/${prefix}/${params.id}/edit`
    if (action === 'destroy') return `/${prefix}/${params.id}`
  }
  return `/${name}`
})

// Fallback router if Inertia router is not available
const safeRouter = router || window.Inertia || {
  visit: (url, options = {}) => {
    if (options.replace) {
      window.location.replace(url)
    } else {
      window.location.href = url
    }
  },
  delete: (url) => {
    if (confirm('Bu öğeyi silmek istediğinizden emin misiniz?')) {
      window.location.href = url
    }
  }
}

// Debug: Check if router and route are available
console.log('Router available:', typeof router)
console.log('Safe router available:', typeof safeRouter)
console.log('Route function available:', typeof window.route)
console.log('Fallback route function:', typeof route)

export function useNavigation(baseRoute = null) {
  // Methods
  const goTo = (route, params = {}, options = {}) => {
    const {
      preserveState = true,
      preserveScroll = true,
      replace = false
    } = options

    try {
      safeRouter.visit(route, {
        preserveState,
        preserveScroll,
        replace
      })
    } catch (error) {
      console.error('Navigation error:', error)
      // Fallback to window.location
      window.location.href = route
    }
  }

  const goBack = (fallbackRoute = null) => {
    if (window.history.length > 1) {
      window.history.back()
    } else if (fallbackRoute) {
      router.visit(fallbackRoute)
    }
  }

  const goForward = () => {
    if (window.history.length > 1) {
      window.history.forward()
    }
  }

  const refresh = (options = {}) => {
    const {
      preserveState = false,
      preserveScroll = false
    } = options

    router.reload({
      preserveState,
      preserveScroll
    })
  }

  // CRUD operations
  const goToIndex = (routeName = null, params = {}, options = {}) => {
    const targetRoute = routeName || `${baseRoute}.index`
    try {
      goTo(route(targetRoute), params, options)
    } catch (error) {
      console.error(`Route '${targetRoute}' not found:`, error)
      // Fallback to direct URL
      goTo(`/${baseRoute}`, params, options)
    }
  }

  const goToCreate = (routeName = null, params = {}, options = {}) => {
    const targetRoute = routeName || `${baseRoute}.create`
    try {
      goTo(route(targetRoute), params, options)
    } catch (error) {
      console.error(`Route '${targetRoute}' not found:`, error)
      // Fallback to direct URL
      goTo(`/${baseRoute}/create`, params, options)
    }
  }

  const goToShow = (id, routeName = null, params = {}, options = {}) => {
    const targetRoute = routeName || `${baseRoute}.show`
    try {
      goTo(route(targetRoute, { id }), params, options)
    } catch (error) {
      console.error(`Route '${targetRoute}' not found:`, error)
      // Fallback to direct URL
      goTo(`/${baseRoute}/${id}`, params, options)
    }
  }

  const goToEdit = (id, routeName = null, params = {}, options = {}) => {
    const targetRoute = routeName || `${baseRoute}.edit`
    try {
      goTo(route(targetRoute, { id }), params, options)
    } catch (error) {
      console.error(`Route '${targetRoute}' not found:`, error)
      // Fallback to direct URL
      goTo(`/${baseRoute}/${id}/edit`, params, options)
    }
  }

  const goToDelete = (id, routeName = null, params = {}, options = {}) => {
    const targetRoute = routeName || `${baseRoute}.destroy`
    try {
      safeRouter.delete(route(targetRoute, { id }), params, options)
    } catch (error) {
      console.error(`Route '${targetRoute}' not found:`, error)
      // Fallback to direct URL
      safeRouter.delete(`/${baseRoute}/${id}`, params, options)
    }
  }

  // Specific navigation patterns
  const goToDashboard = (options = {}) => {
    goTo(route('dashboard'), {}, options)
  }

  const goToProfile = (options = {}) => {
    goTo(route('profile.show'), {}, options)
  }

  const goToSettings = (section = null, options = {}) => {
    const routeName = section ? `panel.settings.${section}` : 'panel.settings.index'
    goTo(route(routeName), {}, options)
  }

  const goToUsers = (options = {}) => {
    goTo(route('panel.users.index'), {}, options)
  }

  const goToRoles = (options = {}) => {
    goTo(route('panel.roles.index'), {}, options)
  }

  const goToPermissions = (options = {}) => {
    goTo(route('panel.permissions.index'), {}, options)
  }

  const goToActivityLogs = (options = {}) => {
    goTo(route('panel.activity-logs.index'), {}, options)
  }

  const goToMailNotifications = (options = {}) => {
    goTo(route('panel.mail-notifications.index'), {}, options)
  }

  // External navigation
  const openExternal = (url, target = '_blank') => {
    window.open(url, target)
  }

  const openInNewTab = (url) => {
    openExternal(url, '_blank')
  }

  const openInSameTab = (url) => {
    window.location.href = url
  }

  // Navigation with confirmation
  const goToWithConfirmation = async (route, params = {}, options = {}) => {
    const {
      message = 'Bu sayfaya gitmek istediğinizden emin misiniz?',
      confirmText = 'Evet',
      cancelText = 'Hayır'
    } = options

    const confirmed = await confirm(message)
    if (confirmed) {
      goTo(route, params, options)
    }
  }

  // Navigation with data preservation
  const goToPreservingData = (route, params = {}, data = {}) => {
    goTo(route, params, {
      preserveState: true,
      preserveScroll: true,
      data
    })
  }

  // Navigation with query parameters
  const goToWithQuery = (route, queryParams = {}, options = {}) => {
    goTo(route, queryParams, {
      preserveState: true,
      preserveScroll: true,
      ...options
    })
  }

  // Navigation with replace (no history entry)
  const replaceTo = (route, params = {}, options = {}) => {
    goTo(route, params, {
      replace: true,
      ...options
    })
  }

  // Navigation with scroll to top
  const goToTop = (route, params = {}, options = {}) => {
    goTo(route, params, {
      preserveState: false,
      preserveScroll: false,
      ...options
    })
  }

  // Navigation with loading state
  const goToWithLoading = async (route, params = {}, options = {}) => {
    const {
      loadingKey = 'navigation',
      setLoading = null
    } = options

    try {
      if (setLoading) {
        setLoading(loadingKey, true)
      }
      
      await new Promise((resolve) => {
        goTo(route, params, {
          ...options,
          onFinish: () => {
            if (options.onFinish) {
              options.onFinish()
            }
            resolve()
          }
        })
      })
    } finally {
      if (setLoading) {
        setLoading(loadingKey, false)
      }
    }
  }

  // Navigation history
  const getCurrentRoute = () => {
    return window.location.pathname
  }

  const getCurrentUrl = () => {
    return window.location.href
  }

  const getQueryParams = () => {
    const urlParams = new URLSearchParams(window.location.search)
    const params = {}
    for (const [key, value] of urlParams) {
      params[key] = value
    }
    return params
  }

  const setQueryParam = (key, value) => {
    const url = new URL(window.location.href)
    url.searchParams.set(key, value)
    window.history.replaceState({}, '', url)
  }

  const removeQueryParam = (key) => {
    const url = new URL(window.location.href)
    url.searchParams.delete(key)
    window.history.replaceState({}, '', url)
  }

  // Navigation guards
  const beforeNavigate = (callback) => {
    // This would integrate with Inertia's before event
    // For now, it's a placeholder
    return callback
  }

  const afterNavigate = (callback) => {
    // This would integrate with Inertia's after event
    // For now, it's a placeholder
    return callback
  }

  return {
    // Basic navigation
    goTo,
    goBack,
    goForward,
    refresh,
    
    // CRUD navigation
    goToIndex,
    goToCreate,
    goToShow,
    goToEdit,
    goToDelete,
    
    // Specific routes
    goToDashboard,
    goToProfile,
    goToSettings,
    goToUsers,
    goToRoles,
    goToPermissions,
    goToActivityLogs,
    goToMailNotifications,
    
    // External navigation
    openExternal,
    openInNewTab,
    openInSameTab,
    
    // Advanced navigation
    goToWithConfirmation,
    goToPreservingData,
    goToWithQuery,
    replaceTo,
    goToTop,
    goToWithLoading,
    
    // Route information
    getCurrentRoute,
    getCurrentUrl,
    getQueryParams,
    setQueryParam,
    removeQueryParam,
    
    // Navigation guards
    beforeNavigate,
    afterNavigate
  }
} 