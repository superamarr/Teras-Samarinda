import api from './index'

export const galleryService = {
  getAll: (params) => api.get('/gallery', { params }),
  getById: (id) => api.get(`/gallery/${id}`),
  create: (formData) => {
    return api.post('/gallery', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },
  update: (id, formData) => {
    return api.post(`/gallery/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },
  delete: (id) => api.delete(`/gallery/${id}`),
  toggleFeatured: (id) => api.post(`/gallery/${id}?action=toggle-featured`),
  getSettings: () => api.get('/gallery?action=settings'),
  updateSettings: (data) => {
    if (data instanceof FormData) {
      return api.post('/gallery?action=settings', data, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })
    }
    return api.post('/gallery?action=settings', data)
  },
}
