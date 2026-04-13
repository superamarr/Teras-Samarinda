import api from './index'

export const statsService = {
  getDashboard: () => api.get('/stats/dashboard'),
  getAnalytics: (filter) => api.get(`/stats/analytics?filter=${filter}`),
}
