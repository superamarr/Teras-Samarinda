import api from './index'

export const contactService = {
  get: () => api.get('/contact'),
  update: (data) => api.put('/contact', data),
}
