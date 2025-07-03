import { ref } from 'vue'

export function useExport() {
  // State
  const exportLoading = ref(false)
  const exportProgress = ref(0)
  const exportError = ref(null)

  // Methods
  const exportToCSV = async (data, filename = null, options = {}) => {
    const {
      headers = null,
      delimiter = ',',
      includeHeaders = true
    } = options

    try {
      exportLoading.value = true
      exportError.value = null
      exportProgress.value = 0

      if (!data || data.length === 0) {
        throw new Error('Export edilecek veri bulunamadı')
      }

      // Auto-generate headers if not provided
      const csvHeaders = headers || Object.keys(data[0])
      
      // Build CSV content
      const csvContent = []
      
      if (includeHeaders) {
        csvContent.push(csvHeaders.join(delimiter))
        exportProgress.value = 20
      }

      // Add data rows
      data.forEach((row, index) => {
        const csvRow = csvHeaders.map(header => {
          const value = getNestedValue(row, header)
          return `"${escapeCSVValue(value)}"`
        }).join(delimiter)
        
        csvContent.push(csvRow)
        exportProgress.value = 20 + ((index + 1) / data.length) * 60
      })

      const csvString = csvContent.join('\n')
      const finalFilename = filename || `export-${new Date().toISOString().split('T')[0]}.csv`
      
      exportProgress.value = 90
      downloadFile(csvString, finalFilename, 'text/csv')
      exportProgress.value = 100

      return { success: true, filename: finalFilename }
    } catch (error) {
      exportError.value = error.message
      throw error
    } finally {
      exportLoading.value = false
      setTimeout(() => {
        exportProgress.value = 0
      }, 1000)
    }
  }

  const exportToJSON = async (data, filename = null, options = {}) => {
    const {
      pretty = true,
      includeMetadata = true
    } = options

    try {
      exportLoading.value = true
      exportError.value = null
      exportProgress.value = 0

      if (!data) {
        throw new Error('Export edilecek veri bulunamadı')
      }

      let exportData = data

      if (includeMetadata) {
        exportData = {
          metadata: {
            exportedAt: new Date().toISOString(),
            totalRecords: Array.isArray(data) ? data.length : 1,
            version: '1.0'
          },
          data: data
        }
      }

      const jsonString = pretty ? JSON.stringify(exportData, null, 2) : JSON.stringify(exportData)
      const finalFilename = filename || `export-${new Date().toISOString().split('T')[0]}.json`
      
      exportProgress.value = 90
      downloadFile(jsonString, finalFilename, 'application/json')
      exportProgress.value = 100

      return { success: true, filename: finalFilename }
    } catch (error) {
      exportError.value = error.message
      throw error
    } finally {
      exportLoading.value = false
      setTimeout(() => {
        exportProgress.value = 0
      }, 1000)
    }
  }

  const exportToExcel = async (data, filename = null, options = {}) => {
    const {
      sheetName = 'Sheet1',
      headers = null
    } = options

    try {
      exportLoading.value = true
      exportError.value = null
      exportProgress.value = 0

      if (!data || data.length === 0) {
        throw new Error('Export edilecek veri bulunamadı')
      }

      // For now, we'll use CSV format as Excel can open CSV files
      // In a real implementation, you'd use a library like xlsx
      const csvResult = await exportToCSV(data, filename?.replace('.xlsx', '.csv'), {
        headers,
        delimiter: ','
      })

      exportProgress.value = 100
      return { success: true, filename: csvResult.filename, note: 'CSV formatında export edildi (Excel ile açılabilir)' }
    } catch (error) {
      exportError.value = error.message
      throw error
    } finally {
      exportLoading.value = false
      setTimeout(() => {
        exportProgress.value = 0
      }, 1000)
    }
  }

  const exportToPDF = async (data, filename = null, options = {}) => {
    const {
      title = 'Export Raporu',
      orientation = 'portrait',
      format = 'a4'
    } = options

    try {
      exportLoading.value = true
      exportError.value = null
      exportProgress.value = 0

      // This is a placeholder - in real implementation you'd use a PDF library
      // For now, we'll create a simple HTML table and suggest printing
      const htmlContent = generatePDFHTML(data, title)
      const finalFilename = filename || `export-${new Date().toISOString().split('T')[0]}.html`
      
      exportProgress.value = 90
      downloadFile(htmlContent, finalFilename, 'text/html')
      exportProgress.value = 100

      return { 
        success: true, 
        filename: finalFilename, 
        note: 'HTML formatında export edildi. Tarayıcıda açıp PDF olarak yazdırabilirsiniz.' 
      }
    } catch (error) {
      exportError.value = error.message
      throw error
    } finally {
      exportLoading.value = false
      setTimeout(() => {
        exportProgress.value = 0
      }, 1000)
    }
  }

  const exportData = async (data, format = 'csv', filename = null, options = {}) => {
    switch (format.toLowerCase()) {
      case 'csv':
        return await exportToCSV(data, filename, options)
      case 'json':
        return await exportToJSON(data, filename, options)
      case 'excel':
      case 'xlsx':
        return await exportToExcel(data, filename, options)
      case 'pdf':
        return await exportToPDF(data, filename, options)
      default:
        throw new Error(`Desteklenmeyen format: ${format}`)
    }
  }

  // Helper functions
  const getNestedValue = (obj, path) => {
    return path.split('.').reduce((current, key) => {
      return current && current[key] !== undefined ? current[key] : null
    }, obj)
  }

  const escapeCSVValue = (value) => {
    if (value === null || value === undefined) return ''
    return value.toString().replace(/"/g, '""')
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

  const generatePDFHTML = (data, title) => {
    if (!data || data.length === 0) return '<p>Veri bulunamadı</p>'

    const headers = Object.keys(data[0])
    const tableRows = data.map(row => 
      `<tr>${headers.map(header => `<td>${getNestedValue(row, header) || ''}</td>`).join('')}</tr>`
    ).join('')

    return `
      <!DOCTYPE html>
      <html>
      <head>
        <meta charset="utf-8">
        <title>${title}</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; }
          table { border-collapse: collapse; width: 100%; margin-top: 20px; }
          th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
          th { background-color: #f2f2f2; font-weight: bold; }
          h1 { color: #333; }
          .export-info { color: #666; font-size: 12px; margin-bottom: 20px; }
        </style>
      </head>
      <body>
        <h1>${title}</h1>
        <div class="export-info">
          Export Tarihi: ${new Date().toLocaleString('tr-TR')}<br>
          Toplam Kayıt: ${data.length}
        </div>
        <table>
          <thead>
            <tr>${headers.map(header => `<th>${header}</th>`).join('')}</tr>
          </thead>
          <tbody>
            ${tableRows}
          </tbody>
        </table>
      </body>
      </html>
    `
  }

  const clearError = () => {
    exportError.value = null
  }

  const reset = () => {
    exportLoading.value = false
    exportProgress.value = 0
    exportError.value = null
  }

  return {
    // State
    exportLoading,
    exportProgress,
    exportError,
    
    // Methods
    exportData,
    exportToCSV,
    exportToJSON,
    exportToExcel,
    exportToPDF,
    clearError,
    reset
  }
} 