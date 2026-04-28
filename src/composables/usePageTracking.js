import { onMounted, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'

const STORAGE_KEY = 'tera_tracking_session'
const SESSION_DURATION_MS = 30 * 60 * 1000

const PAGE_LABELS = {
  '/': 'Halaman Utama',
  '/galeri': 'Halaman Galeri',
  '/events': 'Halaman Event',
  '/tentang': 'Halaman Tentang',
}

function getPageLabel(path) {
  if (PAGE_LABELS[path]) return PAGE_LABELS[path]
  if (path.startsWith('/events/')) return 'Halaman Detail Event'
  return 'Halaman ' + path.replace(/^\//, '')
}

function getOrCreateSessionId() {
  const stored = localStorage.getItem(STORAGE_KEY)
  if (stored) {
    try {
      const data = JSON.parse(stored)
      if (data.exp > Date.now()) {
        data.exp = Date.now() + SESSION_DURATION_MS
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data))
        return data.id
      }
    } catch {
      // corrupted, recreate
    }
  }
  const id = 'sess_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2, 8)
  localStorage.setItem(STORAGE_KEY, JSON.stringify({ id, exp: Date.now() + SESSION_DURATION_MS }))
  return id
}

function getApiBase() {
  return import.meta.env.VITE_API_BASE_URL || 'http://localPA.test/Teras-Samarinda/backend/public'
}

function sendBeaconOrFetch(endpoint, payload) {
  const url = `${getApiBase()}/page-views${endpoint}`
  const body = JSON.stringify(payload)

  if (navigator.sendBeacon) {
    const blob = new Blob([body], { type: 'application/json' })
    const sent = navigator.sendBeacon(url, blob)
    if (sent) return
  }

  try {
    const xhr = new XMLHttpRequest()
    xhr.open('PUT', url, false)
    xhr.setRequestHeader('Content-Type', 'application/json')
    xhr.send(body)
  } catch {
    // best effort
  }
}

export function usePageTracking() {
  const route = useRoute()
  let startTime = null
  let sessionId = null
  let pageUrl = ''
  let tracked = false

  const recordView = async () => {
    sessionId = getOrCreateSessionId()
    pageUrl = getPageLabel(route.path)
    startTime = Date.now()
    tracked = true

    try {
      await fetch(`${getApiBase()}/page-views`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          session_id: sessionId,
          page_url: pageUrl,
          referrer: document.referrer || null,
        }),
      })
    } catch {
      // best effort
    }
  }

  const sendDurationUpdate = () => {
    if (!tracked || !sessionId) return
    const duration = Math.round((Date.now() - startTime) / 1000)
    const isBounce = duration < 10 ? 1 : 0

    sendBeaconOrFetch('', {
      session_id: sessionId,
      page_url: pageUrl,
      duration_seconds: duration,
      is_bounce: isBounce,
    })

    tracked = false
  }

  const handleVisibilityChange = () => {
    if (document.visibilityState === 'hidden') {
      sendDurationUpdate()
    }
  }

  onMounted(() => {
    recordView()
    document.addEventListener('visibilitychange', handleVisibilityChange)
    window.addEventListener('beforeunload', sendDurationUpdate)
  })

  onBeforeUnmount(() => {
    sendDurationUpdate()
    document.removeEventListener('visibilitychange', handleVisibilityChange)
    window.removeEventListener('beforeunload', sendDurationUpdate)
  })
}
