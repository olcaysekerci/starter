import { computed, ref } from 'vue'

/**
 * Optimized computed property for expensive calculations
 * Uses caching and dependency tracking to minimize recalculations
 */
export function useOptimizedComputed(computeFn, dependencies = []) {
  const cache = ref(new Map())
  const lastDependencies = ref([])

  return computed(() => {
    // Create a dependency key for caching
    const depKey = JSON.stringify(dependencies.map(dep => 
      typeof dep === 'function' ? dep() : dep
    ))

    // Check if dependencies have changed
    const depsChanged = depKey !== JSON.stringify(lastDependencies.value)
    
    if (!cache.value.has(depKey) || depsChanged) {
      cache.value.set(depKey, computeFn())
      lastDependencies.value = dependencies.map(dep => 
        typeof dep === 'function' ? dep() : dep
      )
    }

    return cache.value.get(depKey)
  })
}

/**
 * Debounced computed property for search and filter operations
 */
export function useDebouncedComputed(computeFn, delay = 300) {
  const result = ref(null)
  const timeoutId = ref(null)

  return computed(() => {
    if (timeoutId.value) {
      clearTimeout(timeoutId.value)
    }

    timeoutId.value = setTimeout(() => {
      result.value = computeFn()
    }, delay)

    return result.value
  })
}

/**
 * Async computed property for data fetching
 */
export function useAsyncComputed(asyncFn, defaultValue = null) {
  const loading = ref(false)
  const error = ref(null)
  const data = ref(defaultValue)

  const computedValue = computed(() => {
    loading.value = true
    error.value = null

    asyncFn()
      .then(result => {
        data.value = result
      })
      .catch(err => {
        error.value = err
        data.value = defaultValue
      })
      .finally(() => {
        loading.value = false
      })

    return data.value
  })

  return {
    value: computedValue,
    loading,
    error
  }
}