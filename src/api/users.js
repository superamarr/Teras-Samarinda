import api from './index'

export const userService = {
  getAll() {
    return api.get('/users')
  },
  create(data) {
    return api.post('/users', data)
  },
  update(id, data) {
    data._method = 'PUT'
    return api.post(`/users/${id}`, data)
  },
  delete(id) {
    return api.delete(`/users/${id}`)
  }
}
