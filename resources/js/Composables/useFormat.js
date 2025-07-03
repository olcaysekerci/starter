import { computed } from 'vue'

export function useFormat() {
  // Date formatting
  const formatDate = (date, options = {}) => {
    if (!date) return '-'
    
    const defaultOptions = {
      locale: 'tr-TR',
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      ...options
    }
    
    try {
      return new Date(date).toLocaleDateString(defaultOptions.locale, defaultOptions)
    } catch (error) {
      return '-'
    }
  }

  const formatDateTime = (date, options = {}) => {
    if (!date) return '-'
    
    const defaultOptions = {
      locale: 'tr-TR',
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
      ...options
    }
    
    try {
      return new Date(date).toLocaleString(defaultOptions.locale, defaultOptions)
    } catch (error) {
      return '-'
    }
  }

  const formatTime = (date, options = {}) => {
    if (!date) return '-'
    
    const defaultOptions = {
      locale: 'tr-TR',
      hour: '2-digit',
      minute: '2-digit',
      ...options
    }
    
    try {
      return new Date(date).toLocaleTimeString(defaultOptions.locale, defaultOptions)
    } catch (error) {
      return '-'
    }
  }

  const formatRelativeTime = (date) => {
    if (!date) return '-'
    
    const now = new Date()
    const targetDate = new Date(date)
    const diffInSeconds = Math.floor((now - targetDate) / 1000)
    
    if (diffInSeconds < 60) {
      return 'Az önce'
    } else if (diffInSeconds < 3600) {
      const minutes = Math.floor(diffInSeconds / 60)
      return `${minutes} dakika önce`
    } else if (diffInSeconds < 86400) {
      const hours = Math.floor(diffInSeconds / 3600)
      return `${hours} saat önce`
    } else if (diffInSeconds < 2592000) {
      const days = Math.floor(diffInSeconds / 86400)
      return `${days} gün önce`
    } else {
      return formatDate(date)
    }
  }

  // User formatting
  const getUserInitials = (name, maxLength = 2) => {
    if (!name) return '?'
    
    const words = name.trim().split(' ')
    const initials = words.map(word => word.charAt(0)).join('').toUpperCase()
    
    return initials.slice(0, maxLength)
  }

  const formatFullName = (firstName, lastName) => {
    if (!firstName && !lastName) return '-'
    return [firstName, lastName].filter(Boolean).join(' ')
  }

  const formatPhone = (phone) => {
    if (!phone) return '-'
    
    // Remove all non-digit characters
    const cleaned = phone.replace(/\D/g, '')
    
    // Format Turkish phone numbers
    if (cleaned.length === 10) {
      return `(${cleaned.slice(0, 3)}) ${cleaned.slice(3, 6)} ${cleaned.slice(6, 8)} ${cleaned.slice(8)}`
    } else if (cleaned.length === 11 && cleaned.startsWith('0')) {
      return `(${cleaned.slice(1, 4)}) ${cleaned.slice(4, 7)} ${cleaned.slice(7, 9)} ${cleaned.slice(9)}`
    } else if (cleaned.length === 12 && cleaned.startsWith('90')) {
      return `+90 (${cleaned.slice(2, 5)}) ${cleaned.slice(5, 8)} ${cleaned.slice(8, 10)} ${cleaned.slice(10)}`
    }
    
    return phone
  }

  // Number formatting
  const formatNumber = (number, options = {}) => {
    if (number === null || number === undefined) return '-'
    
    const defaultOptions = {
      locale: 'tr-TR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 2,
      ...options
    }
    
    try {
      return Number(number).toLocaleString(defaultOptions.locale, defaultOptions)
    } catch (error) {
      return number.toString()
    }
  }

  const formatCurrency = (amount, currency = 'TRY', options = {}) => {
    if (amount === null || amount === undefined) return '-'
    
    const defaultOptions = {
      locale: 'tr-TR',
      style: 'currency',
      currency: currency,
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
      ...options
    }
    
    try {
      return Number(amount).toLocaleString(defaultOptions.locale, defaultOptions)
    } catch (error) {
      return `${amount} ${currency}`
    }
  }

  const formatPercentage = (value, options = {}) => {
    if (value === null || value === undefined) return '-'
    
    const defaultOptions = {
      locale: 'tr-TR',
      minimumFractionDigits: 1,
      maximumFractionDigits: 2,
      ...options
    }
    
    try {
      return Number(value).toLocaleString(defaultOptions.locale, {
        ...defaultOptions,
        style: 'percent'
      })
    } catch (error) {
      return `${value}%`
    }
  }

  // Text formatting
  const formatText = (text, options = {}) => {
    if (!text) return '-'
    
    const {
      maxLength = null,
      ellipsis = '...',
      capitalize = false,
      lowercase = false,
      uppercase = false
    } = options
    
    let formatted = text.toString()
    
    if (capitalize) {
      formatted = formatted.charAt(0).toUpperCase() + formatted.slice(1).toLowerCase()
    } else if (lowercase) {
      formatted = formatted.toLowerCase()
    } else if (uppercase) {
      formatted = formatted.toUpperCase()
    }
    
    if (maxLength && formatted.length > maxLength) {
      formatted = formatted.slice(0, maxLength) + ellipsis
    }
    
    return formatted
  }

  const formatSlug = (text) => {
    if (!text) return ''
    
    return text
      .toLowerCase()
      .trim()
      .replace(/[ğ]/g, 'g')
      .replace(/[ü]/g, 'u')
      .replace(/[ş]/g, 's')
      .replace(/[ı]/g, 'i')
      .replace(/[ö]/g, 'o')
      .replace(/[ç]/g, 'c')
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '')
  }

  // File size formatting
  const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
  }

  // Status formatting
  const formatStatus = (status, statusMap = {}) => {
    const defaultStatusMap = {
      active: { label: 'Aktif', class: 'bg-green-100 text-green-800' },
      inactive: { label: 'Pasif', class: 'bg-red-100 text-red-800' },
      pending: { label: 'Bekleyen', class: 'bg-yellow-100 text-yellow-800' },
      draft: { label: 'Taslak', class: 'bg-gray-100 text-gray-800' },
      published: { label: 'Yayında', class: 'bg-blue-100 text-blue-800' },
      ...statusMap
    }
    
    return defaultStatusMap[status] || { label: status, class: 'bg-gray-100 text-gray-800' }
  }

  // Event formatting
  const formatEvent = (event) => {
    const eventMap = {
      created: 'Oluşturuldu',
      updated: 'Güncellendi',
      deleted: 'Silindi',
      restored: 'Geri Yüklendi',
      login: 'Giriş Yapıldı',
      logout: 'Çıkış Yapıldı',
      profile_updated: 'Profil Güncellendi',
      password_changed: 'Şifre Değiştirildi'
    }
    
    return eventMap[event] || event
  }

  // Age calculation
  const getAge = (birthDate) => {
    if (!birthDate) return '-'
    
    const today = new Date()
    const birth = new Date(birthDate)
    let age = today.getFullYear() - birth.getFullYear()
    const monthDiff = today.getMonth() - birth.getMonth()
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
      age--
    }
    
    return age
  }

  const getAccountAge = (createdAt) => {
    if (!createdAt) return '-'
    
    const created = new Date(createdAt)
    const now = new Date()
    const diffTime = Math.abs(now - created)
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    
    if (diffDays === 1) return '1 gün'
    if (diffDays < 30) return `${diffDays} gün`
    if (diffDays < 365) return `${Math.floor(diffDays / 30)} ay`
    
    return `${Math.floor(diffDays / 365)} yıl`
  }

  return {
    // Date formatting
    formatDate,
    formatDateTime,
    formatTime,
    formatRelativeTime,
    
    // User formatting
    getUserInitials,
    formatFullName,
    formatPhone,
    
    // Number formatting
    formatNumber,
    formatCurrency,
    formatPercentage,
    
    // Text formatting
    formatText,
    formatSlug,
    
    // File formatting
    formatFileSize,
    
    // Status formatting
    formatStatus,
    formatEvent,
    
    // Age calculations
    getAge,
    getAccountAge
  }
} 