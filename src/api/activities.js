import api from './index'

export const activityService = {
  getAll: () => api.get('/activities'),
  getById: (id) => api.get(`/activities/${id}`),
  create: (formData) => {
    return api.post('/activities', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },
  update: (id, formData) => {
    return api.post(`/activities/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },
  delete: (id) => api.delete(`/activities/${id}`),
  deleteMedia: (id) => api.delete(`/activities/${id}?action=delete-media`),
  sort: (items) => api.post(`/activities?action=reorder`, { items }),
  getSettings: () => api.get('/activities?action=settings'),
  updateSettings: (formData) => {
    return api.post('/activities?action=settings', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },
}
