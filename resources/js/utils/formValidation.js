/**
 * Common form validation utilities
 */

export const validationRules = {
  required: (value) => {
    return value !== null && value !== undefined && value !== ''
  },
  
  email: (value) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return emailRegex.test(value)
  },
  
  phone: (value) => {
    if (!value) return true // nullable
    const phoneRegex = /^[\+]?[0-9\s\-\(\)]+$/
    return phoneRegex.test(value)
  },
  
  min: (value, min) => {
    if (!value) return true
    return value.length >= min
  },
  
  max: (value, max) => {
    if (!value) return true
    return value.length <= max
  },
  
  numeric: (value) => {
    if (!value) return true
    return !isNaN(value) && !isNaN(parseFloat(value))
  },
  
  password: (value) => {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 digit, 1 special char
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/
    return passwordRegex.test(value)
  },

  url: (value) => {
    if (!value) return true
    try {
      new URL(value)
      return true
    } catch {
      return false
    }
  },

  date: (value) => {
    if (!value) return true
    return !isNaN(Date.parse(value))
  },

  between: (value, min, max) => {
    if (!value) return true
    const num = parseFloat(value)
    return num >= min && num <= max
  }
}

export const errorMessages = {
  required: (field) => `${field} alanı zorunludur.`,
  email: () => 'Geçerli bir e-posta adresi giriniz.',
  phone: () => 'Geçerli bir telefon numarası giriniz.',
  min: (field, min) => `${field} en az ${min} karakter olmalıdır.`,
  max: (field, max) => `${field} en fazla ${max} karakter olmalıdır.`,
  numeric: (field) => `${field} sayısal bir değer olmalıdır.`,
  password: () => 'Şifre en az 8 karakter olmalı ve büyük harf, küçük harf, rakam ve özel karakter içermelidir.',
  url: () => 'Geçerli bir URL giriniz.',
  date: () => 'Geçerli bir tarih giriniz.',
  between: (field, min, max) => `${field} ${min} ile ${max} arasında olmalıdır.`,
  custom: (message) => message
}

/**
 * Validate a single field against its rules
 */
export function validateField(value, rules, fieldName) {
  const errors = []
  
  for (const rule of rules) {
    if (typeof rule === 'string') {
      // Simple rule
      if (rule === 'required' && !validationRules.required(value)) {
        errors.push(errorMessages.required(fieldName))
        break
      } else if (rule === 'email' && !validationRules.email(value)) {
        errors.push(errorMessages.email())
        break
      } else if (rule === 'phone' && !validationRules.phone(value)) {
        errors.push(errorMessages.phone())
        break
      } else if (rule === 'numeric' && !validationRules.numeric(value)) {
        errors.push(errorMessages.numeric(fieldName))
        break
      } else if (rule === 'password' && !validationRules.password(value)) {
        errors.push(errorMessages.password())
        break
      } else if (rule === 'url' && !validationRules.url(value)) {
        errors.push(errorMessages.url())
        break
      } else if (rule === 'date' && !validationRules.date(value)) {
        errors.push(errorMessages.date())
        break
      } else if (rule.includes(':')) {
        // Rule with parameter
        const [ruleName, param] = rule.split(':')
        const paramValue = parseInt(param)
        
        if (ruleName === 'min' && !validationRules.min(value, paramValue)) {
          errors.push(errorMessages.min(fieldName, paramValue))
          break
        } else if (ruleName === 'max' && !validationRules.max(value, paramValue)) {
          errors.push(errorMessages.max(fieldName, paramValue))
          break
        }
      }
    } else if (typeof rule === 'function') {
      // Custom validation function
      const result = rule(value)
      if (result !== true) {
        errors.push(typeof result === 'string' ? result : errorMessages.custom('Geçersiz değer'))
        break
      }
    } else if (typeof rule === 'object') {
      // Rule object with custom message
      const { validator, message } = rule
      if (typeof validator === 'function' && !validator(value)) {
        errors.push(message || errorMessages.custom('Geçersiz değer'))
        break
      }
    }
  }
  
  return errors
}

/**
 * Validate an entire form
 */
export function validateForm(formData, validationRules) {
  const errors = {}
  
  for (const [fieldName, rules] of Object.entries(validationRules)) {
    const fieldErrors = validateField(formData[fieldName], rules, fieldName)
    if (fieldErrors.length > 0) {
      errors[fieldName] = fieldErrors[0] // Show only first error
    }
  }
  
  return errors
}

/**
 * Check if form has any errors
 */
export function hasValidationErrors(errors) {
  return Object.keys(errors).length > 0
}

/**
 * Format phone number for Turkey
 */
export function formatTurkishPhone(value) {
  if (!value) return value
  
  // Remove all non-digits
  let cleaned = value.replace(/[^0-9]/g, '')
  
  // Handle international format
  if (cleaned.startsWith('90') && cleaned.length > 10) {
    cleaned = cleaned.substring(2)
  }
  
  // Remove leading zero
  if (cleaned.startsWith('0')) {
    cleaned = cleaned.substring(1)
  }
  
  // Format as XXX XXX XX XX
  if (cleaned.length <= 3) {
    return cleaned
  } else if (cleaned.length <= 6) {
    return cleaned.substring(0, 3) + ' ' + cleaned.substring(3)
  } else if (cleaned.length <= 8) {
    return cleaned.substring(0, 3) + ' ' + cleaned.substring(3, 6) + ' ' + cleaned.substring(6)
  } else if (cleaned.length <= 10) {
    return cleaned.substring(0, 3) + ' ' + cleaned.substring(3, 6) + ' ' + cleaned.substring(6, 8) + ' ' + cleaned.substring(8, 10)
  }
  
  return cleaned.substring(0, 10)
}