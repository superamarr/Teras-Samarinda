export function resolveMediaUrl(path) {
  if (!path) return ''
  if (path.startsWith('http') || path.startsWith('blob:') || path.startsWith('data:')) return path

  // Detect and return static public images (e.g., from /images/ directory)
  const publicMatch = path.match(/\/?images\/[^'"]+/);
  if (publicMatch) {
    // Return relative to root, ensuring it starts with /
    return publicMatch[0].startsWith('/') ? publicMatch[0] : `/${publicMatch[0]}`;
  }

  const raw = import.meta.env.VITE_API_BASE_URL
  const devFallback = 'http://localhost/pa/Teras-Samarinda/backend/public'
  const apiRoot =
    raw != null && raw !== ''
      ? raw.replace(/\/$/, '')
      : import.meta.env.DEV
        ? devFallback
        : ''
  const backendUploads =
    apiRoot === '' ? '/uploads' : apiRoot.replace(/\/public\/?$/, '') + '/uploads'
  return `${backendUploads.replace(/\/$/, '')}/${path.replace(/^\//, '')}`
}
