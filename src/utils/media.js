export function resolveMediaUrl(path) {
  if (!path) return ''
  if (path.startsWith('http') || path.startsWith('blob:') || path.startsWith('data:')) return path

  // Detect and return static public images (e.g., from /images/ directory)
  const publicMatch = path.match(/\/?images\/[^'"]+/)
  if (publicMatch) {
    // Return relative to root, ensuring it starts with /
    return publicMatch[0].startsWith('/') ? publicMatch[0] : `/${publicMatch[0]}`
  }

  const apiRoot = import.meta.env.VITE_API_BASE_URL || 'http://localPA.test/Teras-Samarinda/backend/public'
  let backendUploads = '/uploads'
  if (apiRoot !== '') {
    if (/\/api\/?$/.test(apiRoot)) {
      backendUploads = '/uploads'
    } else if (/\/public\/?$/.test(apiRoot)) {
      backendUploads = `${apiRoot.replace(/\/$/, '')}/uploads`
    } else {
      backendUploads = `${apiRoot.replace(/\/$/, '')}/uploads`
    }
  }
  return `${backendUploads.replace(/\/$/, '')}/${path.replace(/^\//, '')}`
}
