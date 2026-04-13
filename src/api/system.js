import api from './index'

export const systemService = {
  getSettings() {
    return api.get('/system')
  },
  updateSettings(data) {
    return api.post('/system', data)
  }
}
