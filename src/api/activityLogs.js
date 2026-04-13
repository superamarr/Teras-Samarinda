import api from './index'

export const activityLogService = {
  getRecent: (limit = 10) => {
    return api.get('/activity-logs', {
      params: { limit },
    })
  },
}
