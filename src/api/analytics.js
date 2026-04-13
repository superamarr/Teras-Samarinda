import api from './index'

export const analyticsService = {
  getOverview: (period = 'all') => {
    return api.get('/analytics', {
      params: { period }
    })
  }
}
