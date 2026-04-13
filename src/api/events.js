import api from './index'

export const eventService = {
  getAll: (params = {}) => api.get('/events', { params }),
  getById: (id) => api.get(`/events/${id}`),
  create: (formData) => {
    return api.post('/events', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },
  update: (id, formData) => {
    return api.post(`/events/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },
  delete: (id) => api.delete(`/events/${id}`),
  getSettings: () => api.get('/events?action=settings'),
  updateSettings: (formData) => {
    return api.post('/events?action=settings', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },
}
