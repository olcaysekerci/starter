import { ref, reactive, computed } from 'vue'

export function useLoading(loadingKeys = []) {
  // State
  const loadingStates = reactive({})
  const globalLoading = ref(false)
  const loadingQueue = ref([])

  // Initialize loading states
  loadingKeys.forEach(key => {
    loadingStates[key] = false
  })

  // Methods
  const setLoading = (key, state = true) => {
    if (loadingKeys.includes(key)) {
      loadingStates[key] = state
    } else {
      // Dynamic key
      loadingStates[key] = state
    }
  }

  const startLoading = (key) => {
    setLoading(key, true)
  }

  const stopLoading = (key) => {
    setLoading(key, false)
  }

  const toggleLoading = (key) => {
    setLoading(key, !loadingStates[key])
  }

  const withLoading = async (key, asyncFunction) => {
    try {
      startLoading(key)
      const result = await asyncFunction()
      return result
    } finally {
      stopLoading(key)
    }
  }

  const withGlobalLoading = async (asyncFunction) => {
    try {
      globalLoading.value = true
      const result = await asyncFunction()
      return result
    } finally {
      globalLoading.value = false
    }
  }

  const addToQueue = (key) => {
    if (!loadingQueue.value.includes(key)) {
      loadingQueue.value.push(key)
    }
  }

  const removeFromQueue = (key) => {
    const index = loadingQueue.value.indexOf(key)
    if (index > -1) {
      loadingQueue.value.splice(index, 1)
    }
  }

  const clearQueue = () => {
    loadingQueue.value = []
  }

  const isLoading = (key) => {
    return loadingStates[key] || false
  }

  const isAnyLoading = computed(() => {
    return Object.values(loadingStates).some(state => state) || globalLoading.value
  })

  const isLoadingInQueue = (key) => {
    return loadingQueue.value.includes(key)
  }

  const getLoadingKeys = computed(() => {
    return Object.keys(loadingStates).filter(key => loadingStates[key])
  })

  const getQueueKeys = computed(() => {
    return [...loadingQueue.value]
  })

  const reset = (key = null) => {
    if (key) {
      setLoading(key, false)
      removeFromQueue(key)
    } else {
      // Reset all
      Object.keys(loadingStates).forEach(k => {
        setLoading(k, false)
      })
      globalLoading.value = false
      clearQueue()
    }
  }

  const resetAll = () => {
    reset()
  }

  // Batch operations
  const startMultiple = (keys) => {
    keys.forEach(key => startLoading(key))
  }

  const stopMultiple = (keys) => {
    keys.forEach(key => stopLoading(key))
  }

  const withMultipleLoading = async (keys, asyncFunction) => {
    try {
      startMultiple(keys)
      const result = await asyncFunction()
      return result
    } finally {
      stopMultiple(keys)
    }
  }

  // Loading groups
  const createLoadingGroup = (groupName, keys) => {
    return {
      start: () => startMultiple(keys),
      stop: () => stopMultiple(keys),
      withLoading: (asyncFunction) => withMultipleLoading(keys, asyncFunction),
      isLoading: () => keys.some(key => isLoading(key))
    }
  }

  // Progress tracking
  const progressStates = reactive({})

  const setProgress = (key, progress) => {
    progressStates[key] = Math.min(100, Math.max(0, progress))
  }

  const getProgress = (key) => {
    return progressStates[key] || 0
  }

  const resetProgress = (key = null) => {
    if (key) {
      delete progressStates[key]
    } else {
      Object.keys(progressStates).forEach(k => {
        delete progressStates[k]
      })
    }
  }

  // Loading with progress
  const withProgress = async (key, asyncFunction, progressCallback = null) => {
    try {
      startLoading(key)
      setProgress(key, 0)
      
      const result = await asyncFunction((progress) => {
        setProgress(key, progress)
        if (progressCallback) {
          progressCallback(progress)
        }
      })
      
      setProgress(key, 100)
      return result
    } finally {
      stopLoading(key)
      setTimeout(() => resetProgress(key), 500) // Keep progress visible briefly
    }
  }

  return {
    // State
    loadingStates,
    globalLoading,
    loadingQueue,
    progressStates,
    
    // Methods
    setLoading,
    startLoading,
    stopLoading,
    toggleLoading,
    withLoading,
    withGlobalLoading,
    withProgress,
    
    // Queue methods
    addToQueue,
    removeFromQueue,
    clearQueue,
    
    // Check methods
    isLoading,
    isAnyLoading,
    isLoadingInQueue,
    
    // Get methods
    getLoadingKeys,
    getQueueKeys,
    getProgress,
    
    // Reset methods
    reset,
    resetAll,
    resetProgress,
    
    // Batch methods
    startMultiple,
    stopMultiple,
    withMultipleLoading,
    
    // Group methods
    createLoadingGroup
  }
} 