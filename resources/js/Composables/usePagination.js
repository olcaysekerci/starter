import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

export function usePagination(data = {}, options = {}) {
  // Options
  const {
    route = null,
    preserveState = true,
    preserveScroll = true,
    defaultPerPage = 15
  } = options

  // State
  const currentPage = ref(data.current_page || 1)
  const perPage = ref(data.per_page || defaultPerPage)
  const total = ref(data.total || 0)
  const from = ref(data.from || 0)
  const to = ref(data.to || 0)
  const lastPage = ref(data.last_page || 1)
  const links = ref(data.links || [])

  // Computed
  const hasPages = computed(() => lastPage.value > 1)
  const hasMorePages = computed(() => currentPage.value < lastPage.value)
  const hasPreviousPages = computed(() => currentPage.value > 1)
  const isFirstPage = computed(() => currentPage.value === 1)
  const isLastPage = computed(() => currentPage.value === lastPage.value)

  const pageInfo = computed(() => ({
    current: currentPage.value,
    total: lastPage.value,
    from: from.value,
    to: to.value,
    perPage: perPage.value,
    totalItems: total.value
  }))

  const paginationRange = computed(() => {
    const range = []
    const delta = 2
    const left = currentPage.value - delta
    const right = currentPage.value + delta + 1

    for (let i = 1; i <= lastPage.value; i++) {
      if (i === 1 || i === lastPage.value || (i >= left && i < right)) {
        range.push(i)
      } else if (range[range.length - 1] !== '...') {
        range.push('...')
      }
    }

    return range
  })

  // Methods
  const goToPage = (page) => {
    if (page < 1 || page > lastPage.value || page === currentPage.value) {
      return
    }

    currentPage.value = page
    if (route) {
      performNavigation()
    }
  }

  const nextPage = () => {
    if (hasMorePages.value) {
      goToPage(currentPage.value + 1)
    }
  }

  const previousPage = () => {
    if (hasPreviousPages.value) {
      goToPage(currentPage.value - 1)
    }
  }

  const firstPage = () => {
    goToPage(1)
  }

  const lastPageMethod = () => {
    goToPage(lastPage.value)
  }

  const setPerPage = (newPerPage) => {
    if (newPerPage !== perPage.value) {
      perPage.value = newPerPage
      currentPage.value = 1 // Reset to first page
      if (route) {
        performNavigation()
      }
    }
  }

  const updateData = (newData) => {
    currentPage.value = newData.current_page || 1
    perPage.value = newData.per_page || defaultPerPage
    total.value = newData.total || 0
    from.value = newData.from || 0
    to.value = newData.to || 0
    lastPage.value = newData.last_page || 1
    links.value = newData.links || []
  }

  const performNavigation = () => {
    if (!route) return

    const params = {
      page: currentPage.value,
      per_page: perPage.value
    }

    router.get(route, params, {
      preserveState,
      preserveScroll,
      onStart: () => {
        // Optional: Add loading state
      },
      onFinish: () => {
        // Optional: Remove loading state
      }
    })
  }

  const reset = () => {
    currentPage.value = 1
    perPage.value = defaultPerPage
    total.value = 0
    from.value = 0
    to.value = 0
    lastPage.value = 1
    links.value = []
  }

  // Navigation helpers
  const navigateToLink = (link) => {
    if (link.url && !link.active) {
      router.visit(link.url, {
        preserveState,
        preserveScroll
      })
    }
  }

  const canNavigateTo = (link) => {
    return link.url && !link.active
  }

  // Per page options
  const perPageOptions = [
    { value: 10, label: '10' },
    { value: 15, label: '15' },
    { value: 25, label: '25' },
    { value: 50, label: '50' },
    { value: 100, label: '100' }
  ]

  return {
    // State
    currentPage,
    perPage,
    total,
    from,
    to,
    lastPage,
    links,
    
    // Computed
    hasPages,
    hasMorePages,
    hasPreviousPages,
    isFirstPage,
    isLastPage,
    pageInfo,
    paginationRange,
    
    // Methods
    goToPage,
    nextPage,
    previousPage,
    firstPage,
    lastPage: lastPageMethod,
    setPerPage,
    updateData,
    performNavigation,
    reset,
    navigateToLink,
    canNavigateTo,
    
    // Options
    perPageOptions
  }
} 