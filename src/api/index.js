import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost/TeraSamarinda/backend/public',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
  },
  withCredentials: true,
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      console.warn('Unauthorized request - user not logged in')
    }
    return Promise.reject(error)
  },
)

export default api
