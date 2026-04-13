export function resolveMediaUrl(path) {
  if (!path) return ''
  if (path.startsWith('http') || path.startsWith('blob:') || path.startsWith('data:')) return path

  // Detect and return static public images (e.g., from /images/ directory)
  const publicMatch = path.match(/\/?images\/[^'"]+/);
  if (publicMatch) {
    // Return relative to root, ensuring it starts with /
    return publicMatch[0].startsWith('/') ? publicMatch[0] : `/${publicMatch[0]}`;
  }

  const backendUploads = 'http://localhost/TeraSamarinda/backend/uploads'
  return `${backendUploads}/${path.replace(/^\//, '')}`
}
