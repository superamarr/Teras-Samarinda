import api from './index'

export const analyticsService = {
  getOverview: (period = 'all') => {
    return api.get('/analytics', {
      params: { period }
    })
  },

  recordView: (data) => {
    return api.post('/page-views', data)
  },

  updateDuration: (data) => {
    return api.put('/page-views', data)
  }
}
