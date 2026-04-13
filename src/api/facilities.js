import api from './index'

export const facilityService = {
  getAll: () => api.get('/facilities'),
  getById: (id) => api.get(`/facilities/${id}`),
  create: (formData) => {
    return api.post('/facilities', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },
  update: (id, formData) => {
    return api.post(`/facilities/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },
  delete: (id) => api.delete(`/facilities/${id}`),
  deleteMedia: (id) => api.delete(`/facilities/${id}?action=delete-media`),
  sort: (items) => api.post(`/facilities?action=reorder`, { items }),
  getSettings: () => api.get('/facilities?action=settings'),
  updateSettings: (formData) => {
    return api.post('/facilities?action=settings', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },
}
