import { defineStore } from 'pinia'
import api from '@/services/api'

export const useListingsStore = defineStore('listings', {
    state: () => ({
        listings: [],
        currentListing: null,
        myListings: [],
        favorites: [],
        cities: [],
        loading: false,
        error: null,
        pagination: {
            currentPage: 1,
            lastPage: 1,
            total: 0,
        },
        filters: {
            city_id: null,
            region_id: null,
            type: null,
            price_min: null,
            price_max: null,
            search: '',
        },
    }),

    actions: {
        async fetchListings(page = 1) {
            this.loading = true
            try {
                const params = { page, ...this.filters }
                // Remove null/empty values
                Object.keys(params).forEach(key => {
                    if (params[key] === null || params[key] === '') delete params[key]
                })

                const response = await api.get('/listings', { params })
                this.listings = response.data.data
                this.pagination = {
                    currentPage: response.data.current_page,
                    lastPage: response.data.last_page,
                    total: response.data.total,
                }
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch listings'
            } finally {
                this.loading = false
            }
        },

        async fetchListing(id) {
            this.loading = true
            try {
                const response = await api.get(`/listings/${id}`)
                this.currentListing = response.data.listing
                return response.data.listing
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch listing'
                throw error
            } finally {
                this.loading = false
            }
        },

        async fetchMyListings(page = 1) {
            this.loading = true
            try {
                const response = await api.get('/my-listings', { params: { page } })
                this.myListings = response.data.data
                return response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch my listings'
                throw error
            } finally {
                this.loading = false
            }
        },

        async createListing(formData) {
            this.loading = true
            try {
                const response = await api.post('/listings', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                })
                return response.data.listing
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to create listing'
                throw error
            } finally {
                this.loading = false
            }
        },

        async updateListing(id, data) {
            this.loading = true
            try {
                const response = await api.put(`/listings/${id}`, data)
                return response.data.listing
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to update listing'
                throw error
            } finally {
                this.loading = false
            }
        },

        async deleteListing(id) {
            this.loading = true
            try {
                await api.delete(`/listings/${id}`)
                this.myListings = this.myListings.filter(l => l.id !== id)
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to delete listing'
                throw error
            } finally {
                this.loading = false
            }
        },

        async fetchCities() {
            try {
                const response = await api.get('/cities')
                this.cities = response.data.cities
            } catch (error) {
                console.error('Failed to fetch cities:', error)
            }
        },

        async fetchFavorites() {
            this.loading = true
            try {
                const response = await api.get('/favorites')
                this.favorites = response.data.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch favorites'
            } finally {
                this.loading = false
            }
        },

        async toggleFavorite(listingId) {
            const isFavorite = this.favorites.some(f => f.id === listingId)
            try {
                if (isFavorite) {
                    await api.delete(`/favorites/${listingId}`)
                    this.favorites = this.favorites.filter(f => f.id !== listingId)
                } else {
                    await api.post(`/favorites/${listingId}`)
                    // Refetch to get full listing data
                    await this.fetchFavorites()
                }
            } catch (error) {
                console.error('Failed to toggle favorite:', error)
                throw error
            }
        },

        setFilters(filters) {
            this.filters = { ...this.filters, ...filters }
        },

        clearFilters() {
            this.filters = {
                city_id: null,
                region_id: null,
                type: null,
                price_min: null,
                price_max: null,
                search: '',
            }
        },
    },
})
