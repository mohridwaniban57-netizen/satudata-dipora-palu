import axios from 'axios'

const API_BASE_URL = import.meta.env.VITE_API_URL || '/api'

export const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  timeout: 10000,
})

// Request Interceptor: Attach Sanctum Bearer token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => Promise.reject(error)
)

// Response Interceptor: Handle auth & validation errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      const { status } = error.response
      if (status === 401) {
        // Token expired or invalid
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        if (window.location.pathname.startsWith('/admin') && window.location.pathname !== '/admin/login') {
          window.location.href = '/admin/login?expired=1'
        }
      } else if (status === 422) {
        console.warn('Validasi gagal:', error.response.data)
      } else if (status === 429) {
        console.warn('Terlalu banyak permintaan (Rate limited). Tunggu beberapa saat.')
      }
    }
    return Promise.reject(error)
  }
)

export default api
