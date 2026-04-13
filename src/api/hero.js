import api from './index'

export const heroService = {
  get: () => api.get('/hero'),
  update: (formData, onProgress) => {
    return api.post('/hero', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      onUploadProgress: (progressEvent) => {
        if (onProgress && progressEvent.total) {
          const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
          onProgress(percentCompleted)
        }
      },
    })
  },
  deleteMedia: (type) => api.delete(`/hero?action=delete-media&type=${type}`),
}
