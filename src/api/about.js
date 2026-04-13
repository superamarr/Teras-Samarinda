import api from './index'

export const aboutService = {
  get: () => api.get('/about'),
  update: (formData) => {
    return api.post('/about', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },
  deleteMedia: (type) => api.delete(`/about?type=${type}`),
}
