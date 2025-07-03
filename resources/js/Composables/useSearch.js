import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'

export function useSearch(items = [], options = {}) {
  // Options
  const {
    searchFields = [],
    filterConfig = [],
    debounceMs = 300,
    route = null,
    preserveState = true,
    preserveScroll = true
  } = options

  // State
  const searchQuery = ref('')
  const filters = ref({})
  const showFilters = ref(false)
  const showStats = ref(false)
  const isSearching = ref(false)

  // Initialize filters from config
  filterConfig.forEach(filter => {
    if (!filters.value.hasOwnProperty(filter.key)) {
      filters.value[filter.key] = ''
    }
  })

  // Computed
  const filteredItems = computed(() => {
    let filtered = items || []
    
    // Search filter
    if (searchQuery.value.trim()) {
      const query = searchQuery.value.toLowerCase().trim()
      filtered = filtered.filter(item => {
        return searchFields.some(field => {
          const value = getNestedValue(item, field)
          return value && value.toString().toLowerCase().includes(query)
        })
      })
    }
    
    // Apply custom filters
    Object.entries(filters.value).forEach(([key, value]) => {
      if (value && value !== '') {
        const filterConfig = filterConfig.find(f => f.key === key)
        if (filterConfig && filterConfig.filter) {
          filtered = filtered.filter(item => filterConfig.filter(item, value))
        }
      }
    })
    
    return filtered
  })

  const activeFilterCount = computed(() => {
    return Object.values(filters.value).filter(value => value && value !== '').length
  })

  const hasActiveFilters = computed(() => {
    return activeFilterCount.value > 0 || searchQuery.value.trim() !== ''
  })

  const totalResults = computed(() => filteredItems.value.length)

  // Helper function to get nested object values
  const getNestedValue = (obj, path) => {
    return path.split('.').reduce((current, key) => {
      return current && current[key] !== undefined ? current[key] : null
    }, obj)
  }

  // Methods
  const updateSearch = (query) => {
    searchQuery.value = query
    isSearching.value = true
    
    // Debounce search
    setTimeout(() => {
      isSearching.value = false
      if (route) {
        performSearch()
      }
    }, debounceMs)
  }

  const updateFilter = (key, value) => {
    filters.value[key] = value
    if (route) {
      performSearch()
    }
  }

  const applyFilters = () => {
    if (route) {
      performSearch()
    }
  }

  const clearFilters = () => {
    filters.value = {}
    searchQuery.value = ''
    if (route) {
      performSearch()
    }
  }

  const clearSearch = () => {
    searchQuery.value = ''
    if (route) {
      performSearch()
    }
  }

  const toggleFilters = () => {
    showFilters.value = !showFilters.value
  }

  const toggleStats = () => {
    showStats.value = !showStats.value
  }

  const performSearch = () => {
    if (!route) return

    const searchData = {
      search: searchQuery.value,
      ...filters.value
    }

    // Remove empty values
    Object.keys(searchData).forEach(key => {
      if (!searchData[key] || searchData[key] === '') {
        delete searchData[key]
      }
    })

    router.get(route, searchData, {
      preserveState,
      preserveScroll,
      onStart: () => {
        isSearching.value = true
      },
      onFinish: () => {
        isSearching.value = false
      }
    })
  }

  const reset = () => {
    searchQuery.value = ''
    filters.value = {}
    showFilters.value = false
    showStats.value = false
    isSearching.value = false
  }

  // Export functionality
  const exportData = (format = 'excel') => {
    const data = filteredItems.value
    const filename = `export-${new Date().toISOString().split('T')[0]}.${format}`
    
    // Basic export implementation
    if (format === 'csv') {
      exportToCSV(data, filename)
    } else if (format === 'json') {
      exportToJSON(data, filename)
    } else {
      // Default to Excel-like format
      exportToExcel(data, filename)
    }
  }

  const exportToCSV = (data, filename) => {
    if (data.length === 0) return
    
    const headers = Object.keys(data[0])
    const csvContent = [
      headers.join(','),
      ...data.map(row => headers.map(header => `"${row[header] || ''}"`).join(','))
    ].join('\n')
    
    downloadFile(csvContent, filename, 'text/csv')
  }

  const exportToJSON = (data, filename) => {
    const jsonContent = JSON.stringify(data, null, 2)
    downloadFile(jsonContent, filename, 'application/json')
  }

  const exportToExcel = (data, filename) => {
    // Basic Excel export - in real implementation you'd use a library like xlsx
    const csvContent = exportToCSV(data, filename.replace('.xlsx', '.csv'))
  }

  const downloadFile = (content, filename, mimeType) => {
    const blob = new Blob([content], { type: mimeType })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = filename
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    URL.revokeObjectURL(url)
  }

  return {
    // State
    searchQuery,
    filters,
    showFilters,
    showStats,
    isSearching,
    
    // Computed
    filteredItems,
    activeFilterCount,
    hasActiveFilters,
    totalResults,
    
    // Methods
    updateSearch,
    updateFilter,
    applyFilters,
    clearFilters,
    clearSearch,
    toggleFilters,
    toggleStats,
    performSearch,
    reset,
    exportData,
    exportToCSV,
    exportToJSON,
    exportToExcel
  }
} 