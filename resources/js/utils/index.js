// Form validation utilities
export * from './formValidation'

// Data formatting utilities
export function formatDate(date, locale = 'tr-TR') {
  if (!date) return '-'
  return new Date(date).toLocaleDateString(locale)
}

export function formatDateTime(date, locale = 'tr-TR') {
  if (!date) return '-'
  return new Date(date).toLocaleString(locale)
}

export function formatCurrency(amount, currency = 'TRY', locale = 'tr-TR') {
  if (amount === null || amount === undefined) return '-'
  return new Intl.NumberFormat(locale, {
    style: 'currency',
    currency: currency
  }).format(amount)
}

export function formatNumber(number, locale = 'tr-TR') {
  if (number === null || number === undefined) return '-'
  return new Intl.NumberFormat(locale).format(number)
}

// String utilities
export function capitalize(str) {
  if (!str) return ''
  return str.charAt(0).toUpperCase() + str.slice(1)
}

export function truncate(str, length = 100, suffix = '...') {
  if (!str) return ''
  if (str.length <= length) return str
  return str.substring(0, length) + suffix
}

export function slugify(str) {
  if (!str) return ''
  return str
    .toLowerCase()
    .replace(/[^a-z0-9 -]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .trim('-')
}

// Array utilities
export function groupBy(array, key) {
  return array.reduce((groups, item) => {
    const group = item[key]
    groups[group] = groups[group] || []
    groups[group].push(item)
    return groups
  }, {})
}

export function sortBy(array, key, direction = 'asc') {
  return [...array].sort((a, b) => {
    const aVal = a[key]
    const bVal = b[key]
    
    if (direction === 'asc') {
      return aVal > bVal ? 1 : -1
    } else {
      return aVal < bVal ? 1 : -1
    }
  })
}

export function unique(array, key = null) {
  if (!key) {
    return [...new Set(array)]
  }
  
  const seen = new Set()
  return array.filter(item => {
    const value = item[key]
    if (seen.has(value)) {
      return false
    }
    seen.add(value)
    return true
  })
}

// Object utilities
export function pick(obj, keys) {
  const result = {}
  keys.forEach(key => {
    if (key in obj) {
      result[key] = obj[key]
    }
  })
  return result
}

export function omit(obj, keys) {
  const result = { ...obj }
  keys.forEach(key => {
    delete result[key]
  })
  return result
}

export function isEmpty(value) {
  if (value === null || value === undefined) return true
  if (typeof value === 'string') return value.trim() === ''
  if (Array.isArray(value)) return value.length === 0
  if (typeof value === 'object') return Object.keys(value).length === 0
  return false
}

// URL utilities
export function buildQueryString(params) {
  const searchParams = new URLSearchParams()
  
  Object.entries(params).forEach(([key, value]) => {
    if (value !== null && value !== undefined && value !== '') {
      searchParams.append(key, value)
    }
  })
  
  return searchParams.toString()
}

export function parseQueryString(search) {
  const params = new URLSearchParams(search)
  const result = {}
  
  for (const [key, value] of params.entries()) {
    result[key] = value
  }
  
  return result
}

// File utilities
export function formatFileSize(bytes) {
  if (bytes === 0) return '0 Bytes'
  
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

export function getFileExtension(filename) {
  if (!filename) return ''
  return filename.split('.').pop().toLowerCase()
}

export function isImageFile(filename) {
  const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']
  return imageExtensions.includes(getFileExtension(filename))
}

// Color utilities
export function hexToRgb(hex) {
  const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex)
  return result ? {
    r: parseInt(result[1], 16),
    g: parseInt(result[2], 16),
    b: parseInt(result[3], 16)
  } : null
}

export function rgbToHex(r, g, b) {
  return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1)
}

// Debounce utility
export function debounce(func, wait, immediate = false) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      timeout = null
      if (!immediate) func(...args)
    }
    const callNow = immediate && !timeout
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
    if (callNow) func(...args)
  }
}

// Throttle utility
export function throttle(func, limit) {
  let inThrottle
  return function executedFunction(...args) {
    if (!inThrottle) {
      func.apply(this, args)
      inThrottle = true
      setTimeout(() => inThrottle = false, limit)
    }
  }
}