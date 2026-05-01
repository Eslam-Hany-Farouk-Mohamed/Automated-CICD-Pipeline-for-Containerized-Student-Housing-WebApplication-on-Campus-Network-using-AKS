import { defineStore } from 'pinia'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('user') || 'null'),
        token: localStorage.getItem('token') || null,
        loading: false,
        error: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        isStudent: (state) => state.user?.role?.name === 'student',
        isOwner: (state) => state.user?.role?.name === 'owner',
        isBroker: (state) => state.user?.role?.name === 'broker',
        canManageListings: (state) => ['owner', 'broker'].includes(state.user?.role?.name),
        userRole: (state) => state.user?.role?.name || null,
    },

    actions: {
        async register(userData) {
            this.loading = true
            this.error = null
            try {
                const response = await api.post('/register', userData)
                this.setAuthData(response.data)
                return response.data
            } catch (error) {
                // Handle Laravel validation errors
                if (error.response?.status === 422 && error.response?.data?.errors) {
                    const errors = error.response.data.errors
                    const errorMessages = Object.entries(errors)
                        .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
                        .join('\n')
                    this.error = errorMessages
                } else {
                    this.error = error.response?.data?.message || 'Registration failed'
                }
                throw error
            } finally {
                this.loading = false
            }
        },

        async login(credentials) {
            this.loading = true
            this.error = null
            try {
                const response = await api.post('/login', credentials)
                this.setAuthData(response.data)
                return response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Login failed'
                throw error
            } finally {
                this.loading = false
            }
        },

        async logout() {
            try {
                await api.post('/logout')
            } catch (error) {
                console.error('Logout error:', error)
            } finally {
                this.clearAuthData()
            }
        },

        async fetchUser() {
            if (!this.token) return
            try {
                const response = await api.get('/user')
                this.user = response.data.user
                localStorage.setItem('user', JSON.stringify(this.user))
            } catch (error) {
                this.clearAuthData()
            }
        },

        setAuthData(data) {
            this.user = data.user
            this.token = data.token
            localStorage.setItem('user', JSON.stringify(data.user))
            localStorage.setItem('token', data.token)
        },

        clearAuthData() {
            this.user = null
            this.token = null
            localStorage.removeItem('user')
            localStorage.removeItem('token')
        },
    },
})
