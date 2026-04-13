import api from './index'

export const authService = {
  login: (username, password) => api.post('/auth?action=login', { username, password }),
  logout: () => api.post('/auth?action=logout'),
  check: () => api.get('/auth?action=check'),
  me: () => api.get('/auth'),
}
